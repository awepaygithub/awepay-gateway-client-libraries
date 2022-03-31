<?php
/**
 * 
 */
namespace AwepayPayout\Base;

class Deactivate 
{
	function deactivate(){
		flush_rewrite_rules();
	}
}