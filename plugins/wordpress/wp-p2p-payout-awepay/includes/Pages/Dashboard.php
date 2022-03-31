<?php
/**
 * 
 */
namespace AwepayPayout\Pages;

use AwepayPayout\Api\SettingsApi;
use AwepayPayout\Base\BaseController;
use AwepayPayout\Api\Callbacks\AdminCallbacks;

class Dashboard extends BaseController
{
	public $settings;
	public $pages;
	public $subpages;
	public $callbacks;

	public function register(){
		$this->settings = new SettingsApi();
		$this->callbacks= new AdminCallbacks();
		$this->setPages();
		$this->setSubPages();
		
		$this->setSettings();
		$this->setSections();
		$this->setFields();
		
		$this->settings->addPages($this->pages)->withSubPage('Settings')->addSubPages($this->subpages)->register();

		
	}



	public function setPages(){
		$this->pages=[
			[
			'page_title' => 'Awepay Payout Plugin',
			'menu_title' => 'Awepay Payout',
			'capability' => 'manage_options',
			'menu_slug' => 'awepay_payout',
			'callback' => array($this->callbacks,"settingsPage"),
			'icon_url' => 'dashicons-money-alt',
			'position' => 110
			]
		];
	}

	public function setSubPages(){
			
		$this->subpages=[
			[
				'parent_slug' => 'awepay_payout',
				'page_title' => 'Payout Settings',
				'menu_title' => 'Request Payout',
				'capability' => 'manage_options',
				'menu_slug' => 'awepay_payout_request_payout',
				'callback' => array($this->callbacks,"requestPayout")
			],
			[
				'parent_slug' => 'awepay_payout',
				'page_title' => 'Transactions List',
				'menu_title' => 'Transactions',
				'capability' => 'manage_options',
				'menu_slug' => 'awepay_payout_transactions',
				'callback' => array($this->callbacks,"payoutList")
			]
		];
	}

	public function setSettings()
	{
		$args = array(
			array(
				'option_group' => 'awepay_payout_settings_options_group',
				'option_name' => 'sid'
			),
			array(
				'option_group' => 'awepay_payout_settings_options_group',
				'option_name' => 'bank_code'
			),
			array(
				'option_group' => 'awepay_payout_settings_options_group',
				'option_name' => 'rcode'
			),array(
				'option_group' => 'awepay_payout_settings_options_group',
				'option_name' => 'successurl'
			),array(
				'option_group' => 'awepay_payout_settings_options_group',
				'option_name' => 'failureurl'
			),array(
				'option_group' => 'awepay_payout_settings_options_group',
				'option_name' => 'postbackurl'
			),
			array(
				'option_group' => 'awepay_payout_options_group',
				'option_name' => 'first_name'
			),
			array(
				'option_group' => 'awepay_payout_options_group',
				'option_name' => 'last_name'
			)
		);

		$this->settings->setSettings( $args );
	}

	public function setSections()
	{
		$args = array(
			array(
				'id' => 'payout_request_admin_index',
				'title' => 'Settings',
				'callback' => array( $this->callbacks, 'awepayPayoutAddSettingsSection' ),
				'page' => 'awepay_payout_settings'
			),array(
				'id' => 'payout_request_admin_index',
				'title' => 'Settings',
				'callback' => array( $this->callbacks, 'awepayPayoutAddPayoutRequestSection' ),
				'page' => 'awepay_payout_request'
			),
		);
		
		$this->settings->setSections( $args );
	}

	public function setFields()
	{
		$args = array(
			
			
			array(
				'id' => 'firstname',
				'title' => 'First Name *',
				'callback' => array( $this->callbacks, 'awepayPayoutAddFirstName' ),
				'page' => 'awepay_payout_request',
				'section' => 'payout_request_admin_index',
				'args' => array(
					'label_for' => 'firstname',
					'class' => 'firstname-class'
				)
			),
			array(
				'id' => 'lastname',
				'title' => 'Last Name *',
				'callback' => array( $this->callbacks, 'awepayPayoutAddLastName' ),
				'page' => 'awepay_payout_request',
				'section' => 'payout_request_admin_index',
				'args' => array(
					'label_for' => 'lastname',
					'class' => 'lastname-class'
				)
			),

			array(
				'id' => 'email',
				'title' => 'Email *',
				'callback' => array( $this->callbacks, 'awepayPayoutAddEmail' ),
				'page' => 'awepay_payout_request',
				'section' => 'payout_request_admin_index',
				'args' => array(
					'label_for' => 'email',
					'class' => 'email-class'
				)
			),
			array(
				'id' => 'amount',
				'title' => 'Amount *',
				'callback' => array( $this->callbacks, 'awepayPayoutAddAmount' ),
				'page' => 'awepay_payout_request',
				'section' => 'payout_request_admin_index',
				'args' => array(
					'label_for' => 'amount',
					'class' => 'amount-class'
				)
			),
			array(
				'id' => 'currency',
				'title' => 'Currency*',
				'callback' => array( $this->callbacks, 'awepayPayoutAddCurrency' ),
				'page' => 'awepay_payout_request',
				'section' => 'payout_request_admin_index',
				'args' => array(
					'label_for' => 'currency',
					'class' => 'currency-class'
				)
			),
			
			array(
				'id' => 'account_name',
				'title' => 'Account Name *',
				'callback' => array( $this->callbacks, 'awepayPayoutAddAccountName' ),
				'page' => 'awepay_payout_request',
				'section' => 'payout_request_admin_index',
				'args' => array(
					'label_for' => 'account_name',
					'class' => 'account-name-class'
				)
			),
			array(
				'id' => 'account_number',
				'title' => 'Account Number *',
				'callback' => array( $this->callbacks, 'awepayPayoutAddAccountNumber' ),
				'page' => 'awepay_payout_request',
				'section' => 'payout_request_admin_index',
				'args' => array(
					'label_for' => 'account_number',
					'class' => 'account-number-class'
				)
			),
			
			array(
				'id' => 'bank_code',
				'title' => 'Bank Code*',
				'callback' => array( $this->callbacks, 'awepayPayoutAddBankCode' ),
				'page' => 'awepay_payout_request',
				'section' => 'payout_request_admin_index',
				'args' => array(
					'label_for' => 'bank_code',
					'class' => 'bank-code-class'
				)
			) ,
			
			array(
				'id' => 'bank_city',
				'title' => 'Bank City',
				'callback' => array( $this->callbacks, 'awepayPayoutAddBankCity' ),
				'page' => 'awepay_payout_request',
				'section' => 'payout_request_admin_index',
				'args' => array(
					'label_for' => 'bank_city',
					'class' => 'bank-city-class'
				)
			),

			array(
				'id' => 'bank_branch',
				'title' => 'Bank Branch',
				'callback' => array( $this->callbacks, 'awepayPayoutAddBankBranch' ),
				'page' => 'awepay_payout_request',
				'section' => 'payout_request_admin_index',
				'args' => array(
					'label_for' => 'bank_branch',
					'class' => 'bank-branch-class'
				)
			),
			array(
				'id' => 'bank_province',
				'title' => 'Bank Province',
				'callback' => array( $this->callbacks, 'awepayPayoutAddBankProvince' ),
				'page' => 'awepay_payout_request',
				'section' => 'payout_request_admin_index',
				'args' => array(
					'label_for' => 'bank_province',
					'class' => 'bank-province-class'
				)
			),

			array(
				'id' => 'sid',
				'title' => 'SID*',
				'callback' => array( $this->callbacks, 'awepayPayoutAddSid' ),
				'page' => 'awepay_payout_settings',
				'section' => 'payout_request_admin_index',
				'args' => array(
					'label_for' => 'sid',
					'class' => 'sid-class'
				)
			),
			array(
				'id' => 'rcode',
				'title' => 'Rcode*',
				'callback' => array( $this->callbacks, 'awepayPayoutAddRcode' ),
				'page' => 'awepay_payout_settings',
				'section' => 'payout_request_admin_index',
				'args' => array(
					'label_for' => 'rcode',
					'class' => 'rcode-class'
				)
			),
			
			array(
				'id' => 'successurl',
				'title' => 'Success URL',
				'callback' => array( $this->callbacks, 'awepayPayoutAddSuccessUrl' ),
				'page' => 'awepay_payout_settings',
				'section' => 'payout_request_admin_index',
				'args' => array(
					'label_for' => 'successurl',
					'class' => 'successurl-class'
				)
			) 
			,
			array(
				'id' => 'failureurl',
				'title' => 'Failure URL',
				'callback' => array( $this->callbacks, 'awepayPayoutAddFailureUrl' ),
				'page' => 'awepay_payout_settings',
				'section' => 'payout_request_admin_index',
				'args' => array(
					'label_for' => 'failureurl',
					'class' => 'failureurl-class'
				)
			) 
			,
			array(
				'id' => 'postbackurl',
				'title' => 'Postback URL*',
				'callback' => array( $this->callbacks, 'awepayPayoutAddPostBackUrl'),
				'page' => 'awepay_payout_settings',
				'section' => 'payout_request_admin_index',
				'args' => array(
					'label_for' => 'postbackurl',
					'class' => 'postbackurl-class'
				)
			) 

		);

		$this->settings->setFields( $args );
	}

	
}