<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return apply_filters( 'wc_awepay_settings',
	array(
		'enabled' => array(
			'title'       => __( 'Enable/Disable', 'woocommerce-gateway-awepay' ),
			'label'       => __( 'Enable Awepay', 'woocommerce-gateway-awepay' ),
			'type'        => 'checkbox',
			'description' => '',
			'default'     => 'no'
		),
		'title' => array(
			'title'       => __( 'Title', 'woocommerce-gateway-awepay' ),
			'type'        => 'text',
			'description' => __( 'This controls the title which the user sees during checkout.', 'woocommerce-gateway-awepay' ),
			'default'     => __( 'P2P (Awepay)', 'woocommerce-gateway-awepay' ),
			'desc_tip'    => true,
		),
		'description' => array(
			'title'       => __( 'Description', 'woocommerce-gateway-awepay' ),
			'type'        => 'text',
			'description' => __( 'This controls the description which the user sees during checkout.', 'woocommerce-gateway-awepay' ),
			'default'     => __( 'P2P payment via Awepay.', 'woocommerce-gateway-awepay'),
			'desc_tip'    => true,
		),
		'sid' => array(
			'title'       => __( 'SID', 'woocommerce-gateway-awepay' ),
			'type'        => 'text',
			'description' => __( 'The SID from your awepay account.', 'woocommerce-gateway-awepay' ),
			'default'     => '',
			'desc_tip'    => true,
		),
		'rcode' => array(
			'title'       => __( 'RCode', 'woocommerce-gateway-awepay' ),
			'type'        => 'text',
			'description' => __( 'The RCode from your awepay account.', 'woocommerce-gateway-awepay' ),
			'default'     => '',
			'desc_tip'    => true,
		),
	)
);
