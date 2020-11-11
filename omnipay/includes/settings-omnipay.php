<?php

defined('ABSPATH') || exit;

$settings = array(
    'title' => array(
        'title' => __('Title', 'woocommerce'),
        'type' => 'text',
        'description' => __('The title that is displayed when the user checkout.', 'woocommerce'),
        'default' => __('OmniPay Credit Card', 'woocommerce'),
        'desc_tip' => true,
    ),
    'description' => array(
        'title' => __('Description', 'woocommerce'),
        'type' => 'text',
        'desc_tip' => true,
        'description' => __('Description shown at checkout.', 'woocommerce'),
        'default' => __("Credit Card payment", 'woocommerce'),
    ),
    'instructions'    => array(
        'title'       => __( 'Instruksi', 'woocommerce' ),
        'type'        => 'textarea',
        'description' => __( 'Instructions on how to pay will be shown on the "thank you" and "email" pages.', 'woocommerce' ),
        'default'     => '',
        'desc_tip'    => true,
    ),
    'fee' => array(
        'title' => __('Fee %', 'woocommerce'),
        'type' => 'decimal',
        'desc_tip' => true,
        'description' => __('Fee (in percent) to be added to the total order', 'woocommerce'),
        'default' => 0,
    ),
//    'expiry_minutes' => array(
//        'title' => __('Expired (minute)', 'woocommerce'),
//        'type' => 'number',
//        'description' => __('Waktu dalam menit sampai dengan nomor virtual account tidak berlaku lagi (1 hari adalah 1440 menit)', 'woocommerce'),
//        'default' => 2880,
//        'desc_tip' => true,
//    ),
    'merchant_id' => array(
        'title' => __('Merchant ID', 'woocommerce'),
        'type' => 'text',
        'description' => __('Merchant ID anda.', 'woocommerce'),
        'default' => __('', 'woocommerce'),
        'desc_tip' => true,
    ),
    'verify_key' => array(
        'title' => __('Verify Key', 'woocommerce'),
        'type' => 'password',
        'description' => __('Verify Key is required for payment notification security', 'woocommerce'),
        'default' => __('', 'woocommerce'),
        'desc_tip' => true,
    ),
);

return $settings;
