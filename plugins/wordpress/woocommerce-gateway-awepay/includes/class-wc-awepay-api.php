<?php

if (!defined('ABSPATH')) {
  exit;
}

class WC_Awepay_API {

  const ENDPOINT = 'https://admin.awepay.com/soap/tx3.php?wsdl';

  protected static $sid = '';
  protected static $rcode = '';

  public static function setSid($sid) {
    self::$sid = $sid;
  }

  public static function setRcode($rcode) {
    self::$rcode = $rcode;
  }

  public static function getSid() {
    if (!self::$sid) {
      $options = get_option('woocommerce_awepay_settings');

      if (isset($options['sid'])) {
        self::setSid($options['sid']);
      }
    }
    return self::$sid;
  }

  public static function getRcode() {
    if (!self::$rcode) {
      $options = get_option('woocommerce_awepay_settings');

      if (isset($options['rcode'])) {
        self::setRcode($options['rcode']);
      }
    }
    return self::$rcode;
  }

  public static function request($request, $function = 'processPayment') {
    $client = new SoapClient(self::ENDPOINT, array('trace' => true, 'exceptions' => true));
    $response = $client->__soapCall($function, $request);

    if (empty($response->status)) {
      return new WP_Error('awepay_error', __('There was a problem connecting to the payment gateway.', 'woocommerce-gateway-awepay'));
    }

    if (!in_array($response->status, array('OK', 'REQ', 'UREQ', 'REDIRECT'))) {
      if (!empty($response->error->code)) {
        $code = $response->error->code;
      } else {
        $code = 'awepay_error';
      }
      return new WP_Error($code, $response->error->msg . ($response->error->info ? ' - ' . $response->error->info : ''));
    } else {
      return $response;
    }
  }

}
