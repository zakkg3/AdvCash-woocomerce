<?php

defined('ABSPATH') || exit;

$settings = array(
    'title' => array(
        'title' => __('Title', 'woocommerce'),
        'type' => 'text',
        'description' => __('Judul yang ditampilkan saat user melakukan checkout.', 'woocommerce'),
        'default' => __('OmniPay Credit Card', 'woocommerce'),
        'desc_tip' => true,
    ),
    'description' => array(
        'title' => __('Description', 'woocommerce'),
        'type' => 'text',
        'desc_tip' => true,
        'description' => __('Deskripsi yang ditampilkan pada saat checkout.', 'woocommerce'),
        'default' => __("Pembayaran melalui Credit Card", 'woocommerce'),
    ),
    'instructions'    => array(
        'title'       => __( 'Instruksi', 'woocommerce' ),
        'type'        => 'textarea',
        'description' => __( 'Instruksi mengenai cara bayar yang akan diperlihatkan di halaman "thank you" dan "email".', 'woocommerce' ),
        'default'     => '',
        'desc_tip'    => true,
    ),
    'fee' => array(
        'title' => __('Fee %', 'woocommerce'),
        'type' => 'decimal',
        'desc_tip' => true,
        'description' => __('Fee (dalam persen) yang akan ditambahkan pada order total', 'woocommerce'),
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
        'description' => __('Verify Key diperlukan untuk keamanan notifikasi pembayaran', 'woocommerce'),
        'default' => __('', 'woocommerce'),
        'desc_tip' => true,
    ),
);

return $settings;