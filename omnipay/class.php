<?php

namespace WooOmniPayIDCC;

if (!defined('ABSPATH')) {
    exit;
}

// aneh ini dipanggil berkali-kali
if (!class_exists('\WooOmniPayIDCC\OmnipayCC')) {


    class OmnipayCC extends \WC_Payment_Gateway
    {

        /**
         * Whether or not logging is enabled
         *
         * @var bool
         */
        public static $log_enabled = false;

        /**
         * Logger instance
         *
         * @var WC_Logger
         */
        public static $log = false;

        protected $returnHandler = null;

        /**
         * Constructor for the gateway.
         */
        public function __construct()
        {
            $this->id = 'omnipay-cc';
            $this->has_fields = true;

            $this->method_title = __('OmniPay Credit Card', 'woocommerce');
            /* translators: %s: Link to WC system status page */
            $this->method_description = __('OmniPay Credit Card Payment Method.', 'woocommerce');
            $this->supports = array(
                'products',
                //'refunds',
            );

            // Load the settings.
            $this->init_form_fields();
            $this->init_settings();

            // Define user set variables.
            $this->title = $this->get_option('title');
            $this->description = $this->get_option('description');

            add_action('admin_enqueue_scripts', array($this, 'admin_scripts'));
            add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this, 'process_admin_options'));

            add_action('woocommerce_order_status_on-hold_to_processing', array($this, 'capture_payment'));
            add_action('woocommerce_order_status_on-hold_to_completed', array($this, 'capture_payment'));

            if (!$this->is_valid_for_use()) {
                $this->enabled = 'no';
            } else {
                include_once __DIR__ . '/includes/return-handler.php';
                $this->returnHandler = new ReturnHandler($this->settings['verify_key'], $this->settings['merchant_id']);
            }

            // shows CC Link on THANK YOU page..
            add_action('woocommerce_thankyou_' . $this->id, array($this, 'thankyou'));
            // shows CC Link on EMAIL
            add_action('woocommerce_email_before_order_table', array($this, 'email_instructions'), 10, 3);
        }

        /**
         * Return whether or not this gateway still requires setup to function.
         *
         * When this gateway is toggled on via AJAX, if this returns true a
         * redirect will occur to the settings page instead.
         *
         * @since 3.4.0
         * @return bool
         */
        public function needs_setup()
        {
            return !($this->settings['merchant_id'] && $this->settings['verify_key']);
        }

        /**
         * Logging method.
         *
         * @param string $message Log message.
         * @param string $level Optional. Default 'info'. Possible values:
         *                      emergency|alert|critical|error|warning|notice|info|debug.
         */
        public static function log($message, $level = 'info')
        {
            if (self::$log_enabled) {
                if (empty(self::$log)) {
                    self::$log = wc_get_logger();
                }
                self::$log->log($level, $message, array('source' => 'omnipay.id-cc'));
            }
        }

        /**
         * Processes and saves options.
         * If there is an error thrown, will continue to save and validate fields, but will leave the erroring field out.
         *
         * @return bool was anything saved?
         */
        public function process_admin_options()
        {
            $saved = parent::process_admin_options();

            // Maybe clear logs.
            if ('yes' !== $this->get_option('debug', 'no')) {
                if (empty(self::$log)) {
                    self::$log = wc_get_logger();
                }
                self::$log->clear('omnipay.id-cc');
            }

            return $saved;
        }

        /**
         * Get gateway icon.
         *
         * @return string
         */
        public function get_icon()
        {
            $icon_html = '';
            return apply_filters('woocommerce_gateway_icon', $icon_html, $this->id);
        }

        /**
         * Check if this gateway is enabled and available in the user's country.
         *
         * @return bool
         */
        public function is_valid_for_use()
        {
            return in_array(
                get_woocommerce_currency(),
                array('IDR'),
                true
            );
        }

        /**
         * Admin Panel Options.
         * - Options for bits like 'title' and availability on a country-by-country basis.
         *
         * @since 1.0.0
         */
        public function admin_options()
        {
            if ($this->is_valid_for_use()) {
                parent::admin_options();
            } else {
                ?>
                <div class="inline error">
                    <p>
                        <strong><?php esc_html_e('Gateway disabled', 'woocommerce'); ?></strong>: <?php esc_html_e('OmniPay does not support your store currency.', 'woocommerce'); ?>
                    </p>
                </div>
                <?php
            }
        }

        /**
         * Initialise Gateway Settings Form Fields.
         */
        public function init_form_fields()
        {
            $this->form_fields = include 'includes/settings-omnipay.php';
        }

        public function payment_fields()
        {
            $description = $this->get_description();
            if ($description) {
                echo wpautop(wptexturize($description)); // @codingStandardsIgnoreLine.
            }

            ?>
            <p><?= $description ?></p>
            <?php
        }

        /**
         * Process the payment and return the result.
         *
         * @param  int $order_id Order ID.
         * @return array
         */
        public function process_payment($order_id)
        {
            $order = wc_get_order($order_id);

            $amount = $order->get_total() * (1.0 + (($this->settings['fee'] ? $this->settings['fee'] : 0) / 100.0));
            $invoiceid = $order->get_id();
            $bill_name = $order->get_formatted_billing_full_name();
            $bill_email = $order->get_billing_email();
            $bill_mobile = $order->get_billing_phone();
            $bill_desc = get_bloginfo('name') . ' Order: ' . $order_id;
            $expiry_minute = $this->settings['expiry_minutes'] ? $this->settings['expiry_minutes'] : 2880; // default to two days..

            $omnipay_cc_link = '';

            // save the va numbers..
            update_post_meta($order->get_id(), 'omnipay_cc_link', $omnipay_cc_link);

            if ($order->get_total() > 0) {
                // Mark as on-hold (we're awaiting the payment).
                $order->update_status('on-hold', __('Awaiting OmniPay Credit Card payment', 'woocommerce'));
            } else {
                $order->payment_complete();
            }

            // Remove cart.
            WC()->cart->empty_cart();

            // Return redirect to payment page..
            return array(
                'result' => 'success',
                'redirect' => $omnipay_cc_link
            );
        }

        public function can_refund_order($order)
        {
            return false;
        }

        /**
         * Add content to the WC emails.
         *
         * @param WC_Order $order Order object.
         * @param bool $sent_to_admin Sent to admin.
         * @param bool $plain_text Email format: plain text or HTML.
         */
        public function email_instructions($order, $sent_to_admin, $plain_text = false)
        {

            if (!$sent_to_admin && $this->id === $order->get_payment_method() && $order->has_status('on-hold')) {
                $instruction = $this->settings['instructions'];
                if ($instruction) {
                    echo wp_kses_post(wpautop(wptexturize(wp_kses_post($instruction))));
                }
                $this->cc_details($order->get_id(), $instruction);
            }

        }

        public function thankyou($order_id)
        {
            $order = wc_get_order($order_id);
            if ($order && $order->get_payment_method() == $this->id) {
                $instruction = $this->settings['instructions'];
                if ($instruction) {
                    echo wp_kses_post(wpautop(wptexturize(wp_kses_post($instruction))));
                }
                $this->cc_details($order_id, $instruction);
            }
        }

        public function cc_details($order_id, $instruction)
        {
            // Get order and store in $order.
            $order = wc_get_order($order_id);

            $omnipay_cc_link = get_post_meta($order->get_id(), 'omnipay_cc_link');

            ?>
            <section>
                <h2>Credit Card Payment</h2>
                <p>Please Click the link bellow to make the payment</p>
                <p>
                    <a href="<?=$omnipay_cc_link?>">Pay Now</a>
                </p>
            </section>
            <?php

        }

        public function admin_scripts()
        {
            $screen = get_current_screen();
            $screen_id = $screen ? $screen->id : '';

            if ('woocommerce_page_wc-settings' !== $screen_id) {
                return;
            }

            wp_enqueue_script('woocommerce_omnipay-cc_admin', plugins_url('/woocommerce-omnipay.id-cc/omnipay/assets/js/omnipay-cc-admin.js'), array(), WC_VERSION, true);
        }
    }

}