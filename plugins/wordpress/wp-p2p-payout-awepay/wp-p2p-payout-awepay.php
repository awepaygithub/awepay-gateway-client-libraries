<?php

/*
 * Plugin Name: WP Awepay Gateway P2P Payout 
 * Description: P2P Payout on your store using Awepay.
 * copyright         Awepay Asia SDN BHD
 * Author: Md Mahabubur Rahman
 * Author URI:        https://github.com/plycoder
 * Version: 1.0.1
 * Text Domain: wp-p2p-payout-awepay
 */

if (!defined('ABSPATH')) {
	echo "You can not access this file<br/>";
	exit;
}
define("CARD_TYPE","P2P");
define("TX_ACTION","PAYOUT");
define("PAYOUT_API_URL","https://secure.awepay.com/txHandlerPayout.php");
define("TRANSACTION_URL","https://admin.awepay.com/transaction.php?txid=");

if(file_exists(dirname(__FILE__)."/vendor/autoload.php")){
	require_once dirname(__FILE__)."/vendor/autoload.php";
}

function activate_payout_plugin(){
	AwepayPayout\Base\Activate::activate();
}

register_activation_hook(__FILE__,'activate_payout_plugin');


function deactivate_payout_plugin(){
	AwepayPayout\Base\Deactivate::deactivate();
}

register_deactivation_hook(__FILE__,'deactivate_payout_plugin');

if(class_exists('AwepayPayout\\Init')){
	AwepayPayout\Init::register_services();

}