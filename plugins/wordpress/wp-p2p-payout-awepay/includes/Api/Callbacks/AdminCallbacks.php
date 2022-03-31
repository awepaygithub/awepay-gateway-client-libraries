<?php
/**
 * 
 */
namespace AwepayPayout\Api\Callbacks;
use AwepayPayout\Base\BaseController;
use AwepayPayout\Request\PayoutHandler;

class AdminCallbacks extends BaseController
{   
    public function requestPayout(){
       PayoutHandler::getPayoutRequest();
       $payout_response=PayoutHandler::$payout_response;
       return require_once("$this->plugin_path/template/request_payout.php"); 
    }

    public function payoutList(){
        return require_once("$this->plugin_path/template/list.php"); 
    }
    public function settingsPage(){
        return require_once("$this->plugin_path/template/settingpage.php"); 
    }
    

    public function awepayPayoutAddOptionsGroup( $input )
	{
		return $input;
	}

	public function awepayPayoutAddSettingsSection()
	{
		echo 'Payout Setting Section!';
	}
    public function awepayPayoutAddPayoutRequestSection()
	{
		echo 'Payout Request section!';
	}

	public function awepayPayoutAddSid()
	{
		$value = esc_attr( get_option( 'sid' ) );
		echo '<input type="text" class="regular-text" name="sid" value="' . $value . '" placeholder="Write SID Here!">';
	}

	public function awepayPayoutAddRcode()
	{
		$value = esc_attr( get_option( 'rcode' ) );
		echo '<input type="text" class="regular-text" name="rcode" value="' . $value . '" placeholder="Enter Rcode Here!">';
	}


   

    
    public function awepayPayoutAddSuccessUrl()
	{
		$value = esc_attr( get_option( 'successurl' ) );
		echo '<input type="text" class="regular-text" name="successurl" value="' . $value . '" placeholder="Enter Success URL">';
	}

    public function awepayPayoutAddFailureUrl()
	{
		$value = esc_attr( get_option( 'failureurl' ) );
		echo '<input type="text" class="regular-text" name="failureurl" value="' . $value . '" placeholder="Enter Failure URL">';
	}

    public function awepayPayoutAddPostBackUrl()
	{
		$value = esc_attr( get_option( 'postbackurl' ) );
		echo '<input type="text" class="regular-text" name="postbackurl" value="' . $value . '" placeholder="Enter Postback URL">';
	}
    public function awepayPayoutAddFirstName()
	{
		$value ="";
		echo '<input type="text" class="regular-text" name="firstname" value="' . $value . '" placeholder="Write your First Name">';
	}
    public function awepayPayoutAddLastName()
	{
		$value ="";
		echo '<input type="text" class="regular-text" name="lastname" value="' . $value . '" placeholder="Write your Last Name">';
	}
    public function awepayPayoutAddEmail()
	{
		$value ="";
		echo '<input type="text" class="regular-text" name="email" value="' . $value . '" placeholder="Enter Email">';
	}
     public function awepayPayoutAddAmount()
	{
		$value ="";
		echo '<input type="text" class="regular-text" name="amount" value="' . $value . '" placeholder="Enter Amount">';
	}
    public function awepayPayoutAddCurrency()
	{
		$value ="";
		echo '<input type="text" class="regular-text" name="currency" value="' . $value . '" placeholder="Enter currency">';
	}

    public function awepayPayoutAddBankCity()
	{
		$value ="";
		echo '<input type="text" class="regular-text" name="bank_city" value="' . $value . '" placeholder="Bank City">';
	}
    public function awepayPayoutAddBankBranch()
	{
		$value ="";
		echo '<input type="text" class="regular-text" name="bank_branch" value="' . $value . '" placeholder="Bank Branch">';
	}
    
    public function awepayPayoutAddBankProvince()
	{
		$value ="";
		echo '<input type="text" class="regular-text" name="bank_province" value="' . $value . '" placeholder="Enter Bank Province">';
	}
    public function awepayPayoutAddAccountName()
	{
		$value ="";
		echo '<input type="text" class="regular-text" name="account_name" value="' . $value . '" placeholder="Account Name">';
	}

    public function awepayPayoutAddAccountNumber()
	{
		$value ="";
		echo '<input type="text" class="regular-text" name="account_number" value="' . $value . '" placeholder="Account Number">';
	}

    public function awepayPayoutAddBankCode()
	{
		$banks =array(
            "TCB.VN"=>"Techcombank",
            "SCM.VN"=>"Sacombank",
            "VCB.VN"=>"Vietcombank",
            "ACB.VN"=>"Asia Commercial Bank",
            "DAB.VN"=>"DongA Bank",
            "VTB.VN"=>"Vietinbank",
            "BIDV.VN"=>"BIDV Bank",
            "EXIM.VN"=>"Eximbank",
            "VBARD.VN"=>"Agribank",

        );
        $bank_code="<select class='regular-text' name='bank_code'>";
		$bank_code.='<option   value="" > Select Bank Code</option>';
        foreach($banks as $key => $bank){
		    $bank_code.='<option   value="' . $key . '" > '.$bank.' </option>';
        }
        $bank_code.="</select>";
        echo $bank_code;
	}

    
}