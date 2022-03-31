<?php

if(!defined("WP_UNINSTALL_PLUGIN")){
	die();
}
global $wpdp;
global $table_prefix;

$table_name=$table_prefix."payouts";

$wpdp->query("drop table $table_name");

