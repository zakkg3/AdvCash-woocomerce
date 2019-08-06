<?php
/*
Plugin Name: WooCommerce OmniPay.id CC
Description: Credit Card Payment Gateway OmniPay.ID for WooCommerce
Author: PT. Aneka Piranti Perkasa
Version: 1.0.0
Author URI: http://omnipay.co.id/developer
URL:
*/

namespace WooOmniPayIDCC;

\add_filter('woocommerce_payment_gateways', 'WooOmniPayIDCC\OmniPayIDCC_init');

function OmniPayIDCC_init($gateways) {
    require 'omnipay/class.php';

    $gateways[] = 'WooOmniPayIDCC\OmnipayCC';
    return $gateways;
}