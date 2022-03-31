<?php
/**
 * 
 */
namespace AwepayPayout\Base;

use AwepayPayout\Base\BaseController;

class Activate 
{
	
	public static function activate(){
		flush_rewrite_rules();
	}
}