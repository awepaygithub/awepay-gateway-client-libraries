<?php

$wp_did_header = true;
require_once( dirname(__FILE__) . '/../../../../wp-load.php' );

$response = $_REQUEST;
if (!isset($response['txid'])) {
	$response = WC_Awepay::getInstance()->decrypt($response['status']);
}

$response = (object) $response;
$response->error = (object) $response->error;

$order_id = get_post_meta($response->txid, '_awepay_txid_order_id', true);
$order = wc_get_order($order_id);

if ($response->status != 'OK') {
	$msg = $response->error->msg . ($response->error->info ? ' - Info: ' . $response->error->info : '');
	$order->update_status('failed', $msg);
	$redirect_url = add_query_arg('wc_error', urlencode($msg), $order->get_checkout_payment_url(true));
} else {
	$gw = new WC_Gateway_Awepay();
	$gw->process_response($response, $order);
	WC()->cart->empty_cart();
	do_action('wc_gateway_awepay_process_payment', $response, $order);
	$redirect_url = $gw->get_return_url($order);
}
wp_redirect($redirect_url);
