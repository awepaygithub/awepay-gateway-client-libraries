<?php

/*
 * Plugin Name: WooCommerce Awepay Gateway
 * Description: P2P payments on your store using Awepay.
 * @copyright         Awepay Asia SDN BHD
 * Author: MD Mahabubur Rahman
 * Author URI:        https://github.com/plycoder
 * Version: 1.1.1
 * Text Domain: woocommerce-gateway-awepay
 * Domain Path: /languages
 */

if (!defined('ABSPATH')) {
	exit;
}

/**
 * Required minimums and constants
 */
define('WC_TXPROCESS_VERSION', '3.0.7');
define('WC_TXPROCESS_MIN_PHP_VER', '5.3.0');
define('WC_TXPROCESS_MIN_WC_VER', '2.5.0');
define('WC_TXPROCESS_MAIN_FILE', __FILE__);
define('WC_TXPROCESS_PLUGIN_URL', untrailingslashit(plugins_url(basename(plugin_dir_path(__FILE__)), basename(__FILE__))));

if (!class_exists('WC_Awepay')) :

	class WC_Awepay
	{

		protected static $_instance;
		protected static $_log;

		public static function getInstance()
		{
			if (self::$_instance === null) {
				self::$_instance = new self();
			}
			return self::$_instance;
		}

		protected function __clone()
		{
			
		}

		protected function __wakeup()
		{
			
		}

		public $notices = array();

		protected function __construct()
		{
			add_action('admin_init', array($this, 'checkEnvironment'));
			add_action('admin_notices', array($this, 'adminNotices'), 15);
			add_action('plugins_loaded', array($this, 'init'));
		}
		
		

		public function init()
		{
			if (self::getEnvironmentWarning()) {
				return;
			}

			include_once( dirname(__FILE__) . '/includes/class-wc-awepay-api.php' );

			$this->initGateways();

			add_filter('plugin_action_links_' . plugin_basename(__FILE__), array($this, 'plugin_action_links'));
		}

		public function addAdminNotice($slug, $class, $message)
		{
			$this->notices[$slug] = array(
				'class' => $class,
				'message' => $message
			);
		}

		public function checkEnvironment()
		{
			$environmentWarning = self::getEnvironmentWarning();
			if ($environmentWarning && is_plugin_active(plugin_basename(__FILE__))) {
				$this->addAdminNotice('bad_environment', 'error', $environmentWarning);
			}
			if (!class_exists('WC_Awepay_API')) {
				include_once( dirname(__FILE__) . '/includes/class-wc-awepay-api.php' );
			}

			$rcode = WC_Awepay_API::getRcode();
			if (empty($rcode) && !( isset($_GET['page'], $_GET['section']) && 'wc-settings' === $_GET['page'] && 'awepay' === $_GET['section'] )) {
				$settingLink = $this->getSettingLink();
				$this->addAdminNotice('prompt_connect', 'notice notice-warning', sprintf(__('Awepay is almost ready. To get started, <a href="%s">set your Awepay account keys</a>.', 'woocommerce-gateway-awepay'), $settingLink));
			}
		}

		static function getEnvironmentWarning()
		{
			if (version_compare(phpversion(), WC_TXPROCESS_MIN_PHP_VER, '<')) {
				$message = __('WooCommerce Awepay - The minimum PHP version required for this plugin is %1$s. You are running %2$s.', 'woocommerce-gateway-awepay', 'woocommerce-gateway-awepay');
				return sprintf($message, WC_TXPROCESS_MIN_PHP_VER, phpversion());
			}
			if (!defined('WC_VERSION')) {
				return __('WooCommerce Awepay requires WooCommerce to be activated to work.', 'woocommerce-gateway-awepay');
			}
			if (version_compare(WC_VERSION, WC_TXPROCESS_MIN_WC_VER, '<')) {
				$message = __('WooCommerce Awepay - The minimum WooCommerce version required for this plugin is %1$s. You are running %2$s.', 'woocommerce-gateway-awepay', 'woocommerce-gateway-awepay');
				return sprintf($message, WC_TXPROCESS_MIN_WC_VER, WC_VERSION);
			}
			if (!class_exists('SoapClient')) {
				return __('WooCommerce Awepay - PHP-Soap module is not installed.', 'woocommerce-gateway-awepay');
			}
			return false;
		}

		public function plugin_action_links($links)
		{
			$settingLink = $this->getSettingLink();
			$pluginLinks = array(
				sprintf('<a href="%s">%s</a>', $settingLink, __('Settings', 'woocommerce-gateway-awepay')),
				sprintf('<a href="https://docs.woothemes.com/document/awepay/">%s</a>', __('Docs', 'woocommerce-gateway-awepay')),
				sprintf('<a href="http://support.woothemes.com/">%s</a>', __('Support', 'woocommerce-gateway-awepay')),
			);
			return array_merge($pluginLinks, $links);
		}

		public function getSettingLink()
		{
			$useIdAsSection = function_exists('WC') ? version_compare(WC()->version, '2.6', '>=') : false;
			$sectionSlug = $useIdAsSection ? 'awepay' : strtolower('WC_Gateway_Awepay');
			return admin_url('admin.php?page=wc-settings&tab=checkout&section=' . $sectionSlug);
		}

		public function adminNotices()
		{
			foreach ((array) $this->notices as $notice) {
				echo "<div class='" . esc_attr($notice['class']) . "'><p>";
				echo wp_kses($notice['message'], array('a' => array('href' => array())));
				echo "</p></div>";
			}
		}

		public function initGateways()
		{
			if (!class_exists('WC_Payment_Gateway')) {
				return;
			}

			include_once( dirname(__FILE__) . '/includes/class-wc-gateway-awepay.php' );

			load_plugin_textdomain('woocommerce-gateway-awepay', false, plugin_basename(dirname(__FILE__)) . '/languages');
			add_filter('woocommerce_payment_gateways', array($this, 'addGateways'));
		}

		public function addGateways($methods)
		{
			$methods[] = 'WC_Gateway_Awepay';
			return $methods;
		}

		public static function getMinimumAmount()
		{
			return 0;
		}

		public static function log($message)
		{
			if (empty(self::$_log)) {
				self::$_log = new WC_Logger();
			}
			self::$_log->add('woocommerce-gateway-awepay', $message);
			if (defined('WP_DEBUG') && WP_DEBUG) {
				error_log($message);
			}
		}

		public function decrypt($status)
		{
			$rcode = WC_Awepay_API::getRcode();
			return $this->_decrypt($status, $rcode);
		}

		private function _decrypt($string, $key)
		{
			$result = "";
			$string = base64_decode($string);

			for ($i = 0; $i < strlen($string); $i++) {
				$char = substr($string, $i, 1);
				$keychar = substr($key, ($i % strlen($key)) - 1, 1);
				$char = chr(ord($char) - ord($keychar));
				$result.=$char;
			}

			parse_str($result, $result);

			return $result;
		}

	}

	$GLOBALS['wc_awepay'] = WC_Awepay::getInstance();

endif;
