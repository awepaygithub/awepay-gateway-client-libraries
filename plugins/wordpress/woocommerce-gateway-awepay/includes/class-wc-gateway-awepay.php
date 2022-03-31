<?php

if (!defined('ABSPATH')) {
  exit;
}

class WC_Gateway_Awepay extends WC_Gateway_COD {
  public $sid;
  public $rcode;

  public function __construct() {
    $this->id = 'awepay';
    $this->method_title = __('Awepay', 'woocommerce-gateway-awepay');
    $this->method_description = __('Awepay works by adding P2P fields on the checkout and then sending the details to Awepay for verification.', 'woocommerce-gateway-awepay');
    $this->has_fields = true;
    $this->view_transaction_url = 'https://admin.awepay.com/transaction.php?txid=%s';
    $this->supports = array();

    $this->initFormFields();
    $this->init_settings();

    $this->title = $this->get_option('title');
    $this->description = $this->get_option('description');
    $this->enabled = $this->get_option('enabled');
    $this->sid = $this->get_option('sid');
    $this->rcode = $this->get_option('rcode');

    WC_Awepay_API::setSid($this->sid);
    WC_Awepay_API::setRcode($this->rcode);

    add_action('wp_enqueue_scripts', array($this, 'paymentScripts'));
    add_action('admin_notices', array($this, 'adminNotices'));
    add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this, 'process_admin_options'));
  }

  public function get_icon() {
    $ext = version_compare(WC()->version, '2.6', '>=') ? '.svg' : '.png';
    $style = version_compare(WC()->version, '2.6', '>=') ? 'style="margin-left: 0.3em"' : '';

    $icon = '';

    return apply_filters('woocommerce_gateway_icon', $icon, $this->id);
  }

  public function get_awepay_amount($total, $currency = '') {
    return sprintf('%01.2f', $total);
  }

  public function adminNotices() {
    if ('no' === $this->enabled) {
      return;
    }

    if (!$this->sid) {
      echo '<div class="error"><p>' . sprintf(__('Awepay error: Please enter your sid <a href="%s">here</a>', 'woocommerce-gateway-awepay'), admin_url('admin.php?page=wc-settings&tab=checkout&section=wc_gateway_awepay')) . '</p></div>';
      return;
    } elseif (!$this->rcode) {
      echo '<div class="error"><p>' . sprintf(__('Awepay error: Please enter your rcode <a href="%s">here</a>', 'woocommerce-gateway-awepay'), admin_url('admin.php?page=wc-settings&tab=checkout&section=wc_gateway_awepay')) . '</p></div>';
      return;
    }

    if ($this->sid == $this->rcode) {
      echo '<div class="error"><p>' . sprintf(__('Awepay error: Your sid and rcode are identical. Please check and re-enter. <a href="%s">here</a>', 'woocommerce-gateway-awepay'), admin_url('admin.php?page=wc-settings&tab=checkout&section=wc_gateway_awepay')) . '</p></div>';
      return;
    }
  }

  public function is_available() {
    if ('yes' === $this->enabled) {
      if (!$this->sid || !$this->rcode) {
        return false;
      }
      return true;
    }
    return false;
  }

  public function initFormFields() {
    $this->form_fields = include 'settings-awepay.php';
  }

  public function payment_fields() {
    $user = wp_get_current_user();
    $total = WC()->cart->total;

    if (isset($_GET['pay_for_order']) && isset($_GET['key'])) {
      $order = wc_get_order(wc_get_order_id_by_order_key(wc_clean($_GET['key'])));
      $total = $order->get_total();
    }

    if ($user->ID) {
      $user_email = get_user_meta($user->ID, 'billing_email', true);
      $user_email = $user_email ? $user_email : $user->user_email;
    } else {
      $user_email = '';
    }

    if (is_add_payment_method_page()) {
      $pay_button_text = __('Add Card', 'woocommerce-gateway-awepay');
    } else {
      $pay_button_text = '';
    }

    echo '<div
			id="awepay-payment-data"
			data-panel-label="' . esc_attr($pay_button_text) . '"
			data-description=""
			data-email="' . esc_attr($user_email) . '"
			data-amount="' . esc_attr($this->get_awepay_amount($total)) . '"
			data-name="' . esc_attr(get_bloginfo('name', 'display')) . '"
			data-currency="' . esc_attr(strtolower(get_woocommerce_currency())) . '">';

    if ($this->description) {
      echo apply_filters('wc_awepay_description', wpautop(wp_kses_post($this->description)));
    }


    echo '</div>';
  }

  public function get_localized_messages() {
    return apply_filters('wc_awepay_localized_messages', array(
      
      
      'processing_error' => __('An error occurred while processing the P2P payment.', 'woocommerce-gateway-awepay'),
      'invalid_request_error' => __('Could not find payment information.', 'woocommerce-gateway-awepay'),
    ));
  }

  public function paymentScripts() {
    wp_enqueue_script('woocommerce_awepay', plugins_url('assets/js/awepay.js', WC_TXPROCESS_MAIN_FILE), array('jquery-payment', 'awepay'), WC_TXPROCESS_VERSION, true);

    $awepay_params = array(
      'sid' => $this->sid,
      'rcode' => $this->rcode,
      'i18n_terms' => __('Please accept the terms and conditions first', 'woocommerce-gateway-awepay'),
      'i18n_required_fields' => __('Please fill in required checkout fields first', 'woocommerce-gateway-awepay'),
    );

    if (isset($_GET['pay_for_order']) && 'true' === $_GET['pay_for_order']) {
      $order_id = wc_get_order_id_by_order_key(urldecode($_GET['key']));
      $order = wc_get_order($order_id);

      $awepay_params['billing_first_name'] = $order->billing_first_name;
      $awepay_params['billing_last_name'] = $order->billing_last_name;
      $awepay_params['billing_address_1'] = $order->billing_address_1;
      $awepay_params['billing_address_2'] = $order->billing_address_2;
      $awepay_params['billing_state'] = $order->billing_state;
      $awepay_params['billing_city'] = $order->billing_city;
      $awepay_params['billing_postcode'] = $order->billing_postcode;
      $awepay_params['billing_country'] = $order->billing_country;
    }

    $awepay_params['awepay_checkout_require_billing_address'] = apply_filters('wc_awepay_checkout_require_billing_address', false) ? 'yes' : 'no';

    $awepay_params = array_merge($awepay_params, $this->get_localized_messages());

    wp_localize_script('woocommerce_awepay', 'wc_awepay_params', apply_filters('wc_awepay_params', $awepay_params));
  }

  protected function generate_payment_request($order, $source) {
    $post_data = array();
    $post_data['currency'] = strtolower($order->get_order_currency() ? $order->get_order_currency() : get_woocommerce_currency());
    $post_data['amount'] = $this->get_awepay_amount($order->get_total(), $post_data['currency']);
    $post_data['description'] = sprintf(__('%s - Order %s', 'woocommerce-gateway-awepay'), wp_specialchars_decode(get_bloginfo('name'), ENT_QUOTES), $order->get_order_number());

    if ($source->customer) {
      $post_data['customer'] = $source->customer;
    }

    if ($source->source) {
      $post_data['source'] = $source->source;
    }

    $request = array(
      'sid' => $this->sid,
      'rcode' => $this->rcode,
      'udetails' => array(
        'firstname' => $post_data['customer']['billing_first_name'],
        'lastname' => $post_data['customer']['billing_last_name'],
        'email' => $post_data['customer']['billing_email'],
        'phone' => $post_data['customer']['billing_phone'],
        'address' => $post_data['customer']['billing_address_1'] . ' ' . $post_data['customer']['billing_address_2'],
        'suburb_city' => $post_data['customer']['billing_city'],
        'state' => $post_data['customer']['billing_state'],
        'country' => $post_data['customer']['billing_country'],
        'postcode' => $post_data['customer']['billing_postcode'],
        'ship_firstname' => $post_data['customer']['shipping_first_name'],
        'ship_lastname' => $post_data['customer']['shipping_last_name'],
        'ship_email' => $post_data['customer']['shipping_email'],
        'ship_phone' => $post_data['customer']['shipping_phone'],
        'ship_address' => $post_data['customer']['shipping_address_1'] . ' ' . $post_data['customer']['shipping_address_2'],
        'ship_suburb_city' => $post_data['customer']['shipping_city'],
        'ship_state' => $post_data['customer']['shipping_state'],
        'ship_country' => $post_data['customer']['shipping_country'],
        'ship_postcode' => $post_data['customer']['shipping_postcode'],
        'uip' => $_SERVER['REMOTE_ADDR'],
      ),
      'paydetails' => array(
        'payby' => 'P2P',
        'useragent' => $_SERVER['HTTP_USER_AGENT'],
        'browseragent' => $_SERVER['HTTP_ACCEPT'],
        'md' => uniqid(),
        'redirecturl' => plugins_url('pages/3dreturn.php', dirname(__FILE__)),
      ),
      'cart' => array(
        'summary' => array(
          'amount_purchase' => $post_data['amount'],
          'amount_shipping' => 0.00,
          'currency_code' => strtoupper($post_data['currency']),
        ),
        'items' => array(
          array(
            'item_desc' => $post_data['customer']['description'],
            'quantity' => 1,
            'amount_unit' => $post_data['amount'],
          ),
        ),
      ),
      'txparams' => array(
		'tid' => strtoupper($post_data['currency']).$this->sid.$this->getUniqueNumber(), 
        'successurl' => plugins_url('pages/3dreturn.php', dirname(__FILE__)),
        'failureurl' => plugins_url('pages/3dreturn.php', dirname(__FILE__)),
      ),
    );

    return apply_filters('wc_awepay_generate_payment_request', $request, $order, $source);
  }
  
  public function getUniqueNumber()
  {
        $timestamp = microtime(true)*10000;		
		return $timestamp.rand(1,100000);
  }

  protected function get_source($user_id, $force_customer = false) {
    return (object) array(
      'token_id' => false,
      'customer' => $_POST,
      'source' => false,
    );
  }

  protected function get_order_source($order = null) {
    return (object) array(
      'token_id' => false,
      'customer' => false,
      'source' => false,
    );
  }

  public function process_payment($order_id, $retry = true, $force_customer = false) {
    try {
      $order = wc_get_order($order_id);
      $source = $this->get_source(get_current_user_id(), $force_customer);

      if (empty($source->source) && empty($source->customer)) {
        $error_msg = __('Please enter your card details to make a payment.', 'woocommerce-gateway-awepay');
        $error_msg .= ' ' . __('Developers: Please make sure that you are including jQuery and there are no JavaScript errors on the page.', 'woocommerce-gateway-awepay');
        throw new Exception($error_msg);
      }

      $this->save_source($order, $source);

      if ($order->get_total() > 0) {
        $response = WC_Awepay_API::request($this->generate_payment_request($order, $source), 'processPayment');
        if (is_wp_error($response)) {
          if ('customer' === $response->get_error_code() && $retry) {
            delete_user_meta(get_current_user_id(), '_awepay_customer_id');
            return $this->process_payment($order_id, false, $force_customer);
          } elseif ('source' === $response->get_error_code() && $source->token_id) {
            $token = WC_Payment_Tokens::get($source->token_id);
            $token->delete();
            throw new Exception(__('This card is no longer available and has been removed.', 'woocommerce-gateway-awepay'));
          }
          $localized_messages = $this->get_localized_messages();
          throw new Exception((isset($localized_messages[$response->get_error_code()]) ? $localized_messages[$response->get_error_code()] : $response->get_error_message()));
        }
        if ($response->status == 'REQ' || $response->status == 'UREQ' || $response->status == 'REDIRECT') {
          update_post_meta($order->id, '_awepay_redirect_id', $response);
          update_post_meta($response->txid, '_awepay_txid_order_id', $order->id);
          return array(
            'result' => 'success',
            'redirect' => plugins_url('pages/redirect.php?id=' . $order->id, dirname(__FILE__)),
          );
        }
        $this->process_response($response, $order);
      } else {
        $order->payment_complete();
      }

      WC()->cart->empty_cart();
      do_action('wc_gateway_awepay_process_payment', $response, $order);
      return array(
        'result' => 'success',
        'redirect' => $this->get_return_url($order),
      );
    } catch (Exception $e) {
      wc_add_notice($e->getMessage(), 'error');
      if ($order->has_status(array('pending', 'failed'))) {
      }

      do_action('wc_gateway_awepay_process_payment_error', $e, $order);

      return array(
        'result' => 'fail',
        'redirect' => '',
      );
    }
  }

  protected function save_source($order, $source) {
    if ($source->customer) {
      update_post_meta($order->id, '_awepay_customer_id', $source->customer);
    }
    if ($source->source) {
      update_post_meta($order->id, '_awepay_card_id', $source->source);
    }
  }

  public function process_response($response, $order) {
    update_post_meta($order->id, '_awepay_charge_id', $response->txid);
    update_post_meta($order->id, '_awepay_charge_captured', $response->status == 'OK' ? 'yes' : 'no');

    if ($response->status == 'OK') {
      $order->payment_complete($response->txid);
      $message = sprintf(__('Awepay charge complete (Charge ID: %s)', 'woocommerce-gateway-awepay'), $response->txid);
      $order->add_order_note($message);
    } else {
      add_post_meta($order->id, '_transaction_id', $response->txid, true);
      if ($order->has_status(array('pending', 'failed'))) {
        $order->reduce_order_stock();
      }
      $order->update_status('on-hold', sprintf(__('Awepay charge authorized (Charge ID: %s). Process order to take payment, or cancel to remove the pre-authorization.', 'woocommerce-gateway-awepay'), $response->txid));
    }

    return $response;
  }

}
