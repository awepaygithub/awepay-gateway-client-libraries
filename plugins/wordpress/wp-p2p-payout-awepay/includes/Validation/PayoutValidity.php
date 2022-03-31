<?php
/**
 * 
 */
namespace AwepayPayout\Validation;


class PayoutValidity 
{

   public static function payoutFormRequestValidation($params) {
     $form_response=array();
     
     if(empty($params['firstname']) && empty($params['firstname'])){
        $form_response[]="Firstname or lastname required";
     }
     if(empty($params['tid'])){
        $form_response[]="TID required";
     }
     if(empty($params['email'])){
        $form_response[]="Email required";
     }else{
        if (!filter_var($params['email'], FILTER_VALIDATE_EMAIL)) {
            $form_response[]="Enter valid email";
        }
    }

    if(empty($params['bank_code'])){
        $form_response[]="Bank Code required";
     }
     
     if(empty($params['tx_action'])){
        $form_response[]="tx_action required";
     }
    
     if(empty($params['card_type'])){
        $form_response[]="card_type required";
     }
    
     if(empty($params['amount'])){
        $form_response[]="Amount required";
     }
     if(empty($params['currency'])){
        $form_response[]="Currency required";
     }
     if(empty($params['account_name'])){
        $form_response[]="Account name required";
     }
     if(empty($params['account_number'])){
        $form_response[]="Account number required";
     }
     return $form_response;
   }

   public static function payoutApiRequestValidation($params) {


     $api_response=array();
     
     if(empty($params['firstname']) && empty($params['firstname'])){
        $api_response[]="Firstname or lastname required";
     }
     if(empty($params['sid'])){
        $api_response[]="Enter SID in settings page";
     }
     if(empty($params['tid'])){
        $api_response[]="TID required";
     }
     if(empty($params['bank_code'])){
        $api_response[]="Enter Bank Code in settings page";
     }
     
     if(empty($params['postback_url'])){
        $api_response[]="Enter Postback Url in settings page";
     }
     if(empty($params['email'])){
        $api_response[]="Email required";
     }
     if (!filter_var($params['email'], FILTER_VALIDATE_EMAIL)) {
        $api_response[]="Enter valid email";
     }
     
     if(empty($params['tx_action'])){
        $api_response[]="tx_action required";
     }
    
     if(empty($params['card_type'])){
        $api_response[]="card_type required";
     }
    
     if(empty($params['amount'])){
        $api_response[]="Amount required";
     }
     if(empty($params['currency'])){
        $api_response[]="Currency required";
     }
     if(empty($params['account_name'])){
        $api_response[]="Account name required";
     }
     if(empty($params['account_number'])){
        $api_response[]="Account number required";
     }
     return $api_response;

  }
   
	
}