<?php
/**
 * 
 */
namespace AwepayPayout\Base;

use \AwepayPayout\Base\BaseController;

class Actions extends BaseController
{

	function register(){
		add_filter("plugin_action_links_".$this->plugin_name,array($this,'settings_link'));
	}
	
	function settings_link($links){
			$settings_link='<a href="admin.php?page=awepay_payout">Settings</a> ';
			array_push($links,$settings_link);
			return $links;
	}
}