<?php
/**
 * 
 */
namespace AwepayPayout\Database;

class Payout 
{

	function register(){
	    
	   
		$this->createPayoutTable();
	}
	
	function createPayoutTable(){
        global $wpdb;
        global $table_prefix;

        $table_name=$table_prefix."payouts";

        $table_sql_script="
        CREATE TABLE IF NOT EXISTS $table_name (
            `id` int NOT NULL AUTO_INCREMENT,
            `firstname` varchar(200) NOT NULL,
            `lastname` varchar(200) NOT NULL,
            `card_type` varchar(15) NOT NULL,
            `email` varchar(200) NOT NULL,
            `tx_action` varchar(50) NOT NULL,
            `tid` varchar(20) NOT NULL,
            `txid` varchar(20) DEFAULT NULL,
            `amount` decimal(20,2) NOT NULL,
            `currency` varchar(20) NOT NULL,
            `bank_province` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
            `bank_city` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
            `bank_branch` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
            `bank_code` varchar(30) NOT NULL,
            `status` varchar(40) DEFAULT NULL,
            `response` text DEFAULT NULL,
            `account_name` varchar(400) NOT NULL,
            `account_number` varchar(30) NOT NULL,
            PRIMARY KEY (`id`)
          ) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
          $wpdb->query($table_sql_script);
	}
}