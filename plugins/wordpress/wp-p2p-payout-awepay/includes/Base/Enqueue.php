<?php
/**
 * 
 */
namespace AwepayPayout\Base;

use \AwepayPayout\Base\BaseController;

class Enqueue extends BaseController
{

	function register(){
		add_action('admin_enqueue_scripts',array($this,'enqueue'));
	}
	
	function enqueue(){

		wp_enqueue_style( 'payoutplugin',  $this->plugin_url . '/assets/css/payout.css' );  
		wp_enqueue_script( 'payoutplugin', $this->plugin_url . '/assets/js/payout.js' );  
	}
}