<?php
/*
Plugin Name: ADVcash woocomerce payment
Description: Credit Card Payment Gateway ADVcash for WooCommerce
Author: Nicolas Kowenski
Version: 1.0.0
Author URI: https://flag5.com
URL:
*/

namespace WooOmniPayIDCC;

\add_filter('woocommerce_payment_gateways', 'WooOmniPayIDCC\OmniPayIDCC_init');

function OmniPayIDCC_init($gateways) {
    require 'omnipay/class.php';

    $gateways[] = 'WooOmniPayIDCC\OmnipayCC';
    return $gateways;
}
