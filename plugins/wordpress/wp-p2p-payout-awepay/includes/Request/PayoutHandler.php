<?php
/**
 * 
 */
namespace AwepayPayout\Request;
use AwepayPayout\Base\BaseController;
use AwepayPayout\Utility\UtilPayout;
use AwepayPayout\Validation\PayoutValidity;

class PayoutHandler extends BaseController 
{
	public static $payout_response="";
	
	public static function getPayoutRequest(){

		$failureurl= esc_attr( get_option( 'failureurl' ) );
		$postbackurl= esc_attr( get_option( 'postbackurl' ) );
		$successurl= esc_attr( get_option( 'successurl' ) );
		
		$rcode= esc_attr( get_option( 'rcode' ) );
		$sid= esc_attr( get_option( 'sid' ) );
		$redirect_url="";

		if(isset($_POST['submit'])){
			
			global $wpdb;
			global $table_prefix;	
			$table_name=$table_prefix."payouts";

			$firstname = $_POST['firstname'];
			$lastname = $_POST['lastname'];
			$email = $_POST['email'];
			$card_type = CARD_TYPE;
			$tx_action = TX_ACTION;
			$amount = $_POST['amount'];
			$bank_code= $_POST['bank_code'];
			$currency = $_POST['currency'];
			$bank_province = empty($_POST['bank_province'])?"ICBC":$_POST['bank_province'];
			$bank_city = empty($_POST['bank_city'])?"ICBC":$_POST['bank_city'];
			$bank_branch = empty($_POST['bank_branch'])?"ICBC":$_POST['bank_branch'];
			$response = ' response text';
			$status = 'Pending';
			$account_name = $_POST['account_name'];
			$account_number = $_POST['account_number'];	
			$tid = $currency.$sid.UtilPayout::getUniqueNumber();


			$payout_api_array = array(
				'sid'=> $sid,
				'tid'=> $tid,
				'card_type'=> $card_type ,
				'tx_action'=>$tx_action ,
				'firstname'=>$firstname,
				'city'=>$bank_city,
				'lastname'=> $lastname,
				'email'=>$email,
				'amount'=>$amount,
				'currency'=>$currency,
				'bank_city'=>$bank_city,
				'bank_branch'=>$bank_branch,
				'bank_province'=>$bank_province,
				'bank_code'=>$bank_code,
				'account_name'=>$account_name,
				'account_number'=>$account_number,
				'successurl'=>$successurl,
				'failureurl'=>$failureurl,
				'postback_url'=>$postbackurl
			);

			$validity_api_array=PayoutValidity::payoutApiRequestValidation($payout_api_array);
			
			$txid="";
			if(empty($validity_api_array)){
				$response=self::payoutApiRequest($payout_api_array);
				if(!empty($response)){
					$response_arr=json_decode($response);

					if(isset($response_arr->error)){
						$txid=$response_arr->txid;
						$error=$response_arr->error;
						if(empty($error->response)){
							$status="Pending";
							$redirect_url=$successurl;
						}else{
							$status=$error->response;
							$redirect_url=$failureurl;
						}
					}else{
						$status="CURL Error";
					}
				}
				
			}else{
				self::$payout_response=$validity_api_array;
			}

			

			if(empty($validity_api_array)){
			
			$form_data_array=array(
				'firstname' => $firstname,
				'lastname' => $lastname,
				'tid' => $tid,
				'txid' => $txid,
				'email' => $email,
				'tx_action' => $tx_action,
				'card_type' => $card_type,
				'amount' => $amount,
				'currency' => $currency,
				'bank_province' => $bank_province,
				'bank_city' => $bank_city,
				'bank_branch' => $bank_branch,
				'bank_code' => $bank_code,
				'response' => $response,
				'account_name' => $account_name,
				'account_number' => $account_number,
				'status' => $status
			);


			$validity_form_array=PayoutValidity::payoutFormRequestValidation($form_data_array);

			if(empty($validity_form_array)){

			
				$data_insert=$wpdb->insert("$table_name", $form_data_array, array( 
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s', 
				'%s', 
				'%s', 
				'%s', 
				'%s', 
				'%s', 
				'%s', 
				'%s', 
				'%s', 
				'%s', 
				'%s', 
				'%s'
				) 
				);
				
				//var_dump($form_data_array);
				
				//die("sss");
			self::$payout_response="Payout Request Created Successfully!".$table_name;
			if(!empty($redirect_url))	
			wp_redirect($redirect_url);

			}
		}else{
			
			if(empty($validity_api_array))
			self::$payout_response=$validity_form_array;
			else
			self::$payout_response=$validity_api_array;			
		}

		}
	}

	private static function payoutApiRequest($payout_data){
		

		$curl = curl_init();


		curl_setopt_array($curl, array(
		CURLOPT_URL =>PAYOUT_API_URL,
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_ENCODING => "",
		CURLOPT_TIMEOUT => 30,
		CURLOPT_SSL_VERIFYHOST => 0,
		CURLOPT_SSL_VERIFYPEER => 0,
		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		CURLOPT_CUSTOMREQUEST => "POST",
		CURLOPT_POSTFIELDS => http_build_query($payout_data),
		CURLOPT_HTTPHEADER => array(
			"content-type: application/x-www-form-urlencoded"
		),
		));
		$response = curl_exec($curl);
		$err = curl_error($curl);

		curl_close($curl);

		if ($err) {
			return  "cURL Error #:" . $err;
		} else {
			return $response;
		}
	}
}