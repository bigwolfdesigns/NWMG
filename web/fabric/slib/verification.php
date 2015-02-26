<?php

class verification{
	protected $config = array();
	public function __construct(){
		$this->config['address_driver'] = strtolower(lc('config')->load('verification')->get('verification_address_driver', ''));
		if($this->config['address_driver'] == 'fedex'){
			$this->config['address_key']			 = lc('config')->get('verification_fedex_key', '');
			$this->config['address_password']		 = lc('config')->get('verification_fedex_password', '');
			$this->config['address_account_number']	 = lc('config')->get('verification_fedex_account_number', '');
			$this->config['address_meter_number']	 = lc('config')->get('verification_fedex_meter_number', '');
			$this->config['allow_po_boxes']			 = lc('config')->get('allow_po_boxes', '');
		}
		lc('config')->unload('verification');
	}
	public function is_address_po($address){
		//simple regex provided by leeoniya https://gist.github.com/899034
		$return = false;
		if(preg_match('/^box[^a-z]|(p[-. ]?o.?[- ]?|post office )b(.|ox)/i', trim($address))){
			$return = true;
		}
		//more advanced Regexpfrom http://stackoverflow.com/questions/5159535/po-box-validation
		if(!$return && preg_match('/^\s*((?:P(?:OST)?.?\s*(?:O(?:FF(?:ICE)?)?)?.?\s*(?:B(?:IN|OX)?)?)+|(?:B(?:IN|OX)+\s+)+).?\s*\d+/i', trim($address))){
			$return = true;
		}
		return $return;
	}
	public function address($address = array()){
		//do not allow PO boxes
		if(!$this->config['allow_po_boxes'] && isset($address['shipping_address_line_1']) && $this->is_address_po($address['shipping_address_line_1'])){
			return false;
		}
		switch($this->config['address_driver']){
			case 'fedex':
			default:
				//include all the junk to verify address
				//load class (this is very similar to an INCLUDE)
				ll('address_verification'.DIRECTORY_SEPARATOR.'fedex'.DIRECTORY_SEPARATOR.'AddressValidation', false);
				//fedex_address_validation::static_function();
				//if you need to instantiate the class then
				//$validator = ll('address_verification'.DIRECTORY_SEPARATOR.'fedex'.DIRECTORY_SEPARATOR.'fedex_address_validation');
				$return = false;
				//$states = ll('store')->get_states();
				//$countries = ll('store')->get_countries();
				if(isset($address['shipping_state_id'])){
					$state = ll('table_prototype')->get_record($address['shipping_state_id'], 'state')->get('abbreviation');
				}
				$address['shipping_state'] = isset($state)?$state:'';
				if(isset($address['shipping_country_id'])){
					$country = ll('table_prototype')->get_record($address['shipping_country_id'], 'country')->get('abbreviation');
				}
				$address['shipping_country'] = isset($country)?$country:'';
				if(
						!empty($address) &&
						isset($address['shipping_address_line_1']) &&
						$address['shipping_address_line_1'] != '' &&
						$address['shipping_city'] != '' &&
						$address['shipping_state'] != '' &&
						$address['shipping_zip'] != ''
				){
					//check if the address is actually valid
					//using fedex APIs
					//if not, return a tring with the error

					$fav = new fedex_address_validation();
					$fav->credential($this->config['address_key'], $this->config['address_password'], $this->config['address_account_number'], $this->config['address_meter_number']);

					$fav->add_address_to_validate('addr', $address['shipping_address_line_1'].' '.$address['shipping_address_line_2'], $address['shipping_state'], $address['shipping_city'], $address['shipping_zip'], ($address['shipping_country'] != ''?$address['shipping_country']:'US'), $address['shipping_to']);
					$ret	 = $fav->validate();
					$k		 = 0;
					$return	 = true;
					if(is_array($ret['addr'])){
						foreach($ret['addr'] as $key=> $addr){
							if($addr['valid'] === true){
								if($addr['changed'] === true){
									if(is_bool($return)){
										$return = array();
									}
									$return[$k]['shipping_address_line_1']	 = $addr['address']['street'];
									$return[$k]['shipping_address_line_2']	 = '';
									$return[$k]['shipping_state']			 = $addr['address']['state'];
									$return[$k]['shipping_state_id']		 = ($addr['address']['state']);
									$return[$k]['shipping_city']			 = $addr['address']['city'];
									$return[$k]['shipping_zip']				 = $addr['address']['zip'];
									$k++;

									$return['score'] = $addr['score'];
								}
							}else{
								$return = false;
							}
						}
					}
				}
				return $return;
				break;
		}
	}
	/**
	 * RFC compatible function that checks email addresses
	 *
	 * @param string $email
	 * @return boolean
	 */
	public function email($email){
		//copied from
		if(function_exists('filter_var')){ //Introduced in PHP 5.2
			if(filter_var($email, FILTER_VALIDATE_EMAIL) === FALSE){
				return false;
			}else{
				return true;
			}
		}else{
			return (bool)preg_match('/^(?:[\w\!\#\$\%\&\'\*\+\-\/\=\?\^\`\{\|\}\~]+\.)*[\w\!\#\$\%\&\'\*\+\-\/\=\?\^\`\{\|\}\~]+@(?:(?:(?:[a-zA-Z0-9_](?:[a-zA-Z0-9_\-](?!\.)){0,61}[a-zA-Z0-9_-]?\.)+[a-zA-Z0-9_](?:[a-zA-Z0-9_\-](?!$)){0,61}[a-zA-Z0-9_]?)|(?:\[(?:(?:[01]?\d{1,2}|2[0-4]\d|25[0-5])\.){3}(?:[01]?\d{1,2}|2[0-4]\d|25[0-5])\]))$/', $email);
		}
		/**
		 * @link http://www.ilovejackdaniels.com/php/email-address-validation/
		 */
		/**
		  //if it pass this simple test, we accept the email, else we do some extra testing
		  //better 1 extra line of code once in awhile, than lots of code always.

		  if (preg_match('/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/i', $email))
		  return true;
		  // First, we check that there's one @ symbol, and that the lengths are right
		  if (!preg_match('/^[^@]{1,64}@[^@]{1,255}$/', $email)) {
		  // Email invalid because wrong number of characters in one section, or wrong number of @ symbols.
		  return false;
		  }
		  // Split it into sections to make life easier
		  $email_array = explode('@', $email);
		  $local_array = explode('.', $email_array[0]);
		  for ($i = 0; $i < sizeof($local_array); $i++) {
		  if (!preg_match('/^(([A-Za-z0-9!#$%&\'*+\/=?^_`{|}~-][A-Za-z0-9!#$%&\'*+\/=?^_`{|}~\.-]{0,63})|(\"[^(\\|")]{0,62}"))$/', $local_array[$i])) {
		  return false;
		  }
		  }
		  if (!preg_match('/^\[?[0-9\.]+\]?$/', $email_array[1])) { // Check if domain is IP. If not, it should be valid domain name
		  $domain_array = explode('.', $email_array[1]);
		  if (sizeof($domain_array) < 2) {
		  return false; // Not enough parts to domain
		  }
		  for ($i = 0; $i < sizeof($domain_array); $i++) {
		  if (!preg_match('/^(([A-Za-z0-9][A-Za-z0-9-]{0,61}[A-Za-z0-9])|([A-Za-z0-9]+))$/', $domain_array[$i])) {
		  return false;
		  }
		  }
		  }
		  return true;
		 */
	}
	public function url($url){
		/*
		 * for eregi
		  $protocol	= '([[:alpha:]]+:\/\/)+';
		  $domain		= '([[:alpha:]][-[:alnum:]]*[[:alnum:]])(\.[[:alpha:]][-[:alnum:]]*[[:alpha:]])+';
		  $dir		= '(/[[:alpha:]][-[:alnum:]]*[[:alnum:]])*';
		  $page		= '(/[[:alpha:]][-[:alnum:]]*\.[[:alpha:]]{3,5})?';
		  $getstring	= '(\?([[:alnum:]][-_%[:alnum:]]*=[-_%[:alnum:]]+)(&([[:alnum:]][-_%[:alnum:]]*=[-_%[:alnum:]]+))*)?';
		  $pattern	= '^'.$protocol.$domain.$dir.$page.$getstring.'$';
		 */

		//for preg_match
		$protocol	 = '([a-z]+:\/\/)?';
		$domain		 = '([a-z][-a-z0-9]*[a-z0-9])(\.[a-z0-9][-a-z0-9]*[a-z])+';
		$dir		 = '(\/[a-z][-a-z0-9]*[a-z0-9])*';
		$page		 = '(\/[a-z][-a-z0-9]*\.[a-z]{3,5})?';
		$getstring	 = '(\?([a-z0-9][-_%a-z0-9]*=[-_%a-z0-9]+)(&([a-z0-9][-_%a-z0-9]*=[-_%a-z0-9]+))*)?';
		$pattern	 = '/^'.$protocol.$domain.$dir.$page.$getstring.'$/i';

		return (bool)preg_match($pattern, $url);
	}
	public function alphanum($string){
		if(function_exists('ctype_alnum')){
			$return = ctype_alnum($string);
		}else{
			$return = preg_match('/^[a-z0-9]+$/i', $string) > 0;
		}
		return $return;
	}
	public function numeric($number){
		return is_numeric($number);
	}
	public function string($string){
		return is_string($string);
	}
	public function text($string){
		return $this->string($string);
	}
	public function phone($phone){
		$phone = preg_replace('/[^0-9]/', '', $phone);
		return (is_numeric($phone) && strlen($phone) >= 7);
	}
	public function date($year, $month = 0, $day = 0, $future_only = false){
		if(!$this->numeric($year) || !$this->numeric($month) || !$this->numeric($day)) return false;
		$skip_check_day		 = false;
		$skip_check_month	 = false;
		if($year > 99){
			$year = intval(substr($year, -2));
		}
		if($month == 0){
			$month				 = strftime('%m');
			$skip_check_month	 = true;
		}
		if($day == 0){
			$day			 = strftime('%d');
			$skip_check_day	 = true;
			$last_day_month	 = date('t', mktime(0, 0, 1, $month, 1, $year));
			if($day > $last_day_month){
				$day = $last_day_month;
			}
		}
		$mktime			 = mktime(23, 59, 59, $month, $day, $year);
		$correct_day	 = ($skip_check_day || intval(strftime('%d', $mktime)) == intval($day));
		$correct_month	 = ($skip_check_month || intval(strftime('%m', $mktime)) == intval($month));
		$correct_year	 = (intval(strftime('%y', $mktime)) == intval($year));

		if(!$correct_day || !$correct_month || !$correct_year){
			return false;
		}
		if($future_only && $mktime < time()) return false;
		return true;
	}
	public function ip_address($checkip, $jolly_char = ''){
		if($jolly_char == '.')  // dot ins't allowed as jolly char
			$jolly_char = '';

		if($jolly_char != ''){
			$checkip	 = str_replace($jolly_char, '*', $checkip);  // replace the jolly char with an asterisc
			$my_reg_expr = '/^[0-9\*]{1,3}\.[0-9\*]{1,3}\.[0-9\*]{1,3}\.[0-9\*]{1,3}$/';
			$jolly_char	 = '*';
		}else{
			$my_reg_expr = '/^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}$/';
		}

		if(preg_match($my_reg_expr, $checkip)){
			for($i = 1; $i <= 3; $i++){
				if(!(substr($checkip, 0, strpos($checkip, '.')) >= '0' && substr($checkip, 0, strpos($checkip, '.')) <= '255')){
					if($jolly_char != ''){   // if exists, check for the jolly char
						if(substr($checkip, 0, strpos($checkip, '.')) != $jolly_char){
							return false;
						}
					}else{
						return false;
					}
				}
				$checkip = substr($checkip, strpos($checkip, '.') + 1);
			}

			if(!($checkip >= '0' && $checkip <= '255')){  // class D
				if($jolly_char != ''){   // if exists, check for the jolly char
					if($checkip != $jolly_char){
						return false;
					}
				}else{
					return false;
				}
			}
		}else{
			return false;
		}

		return true;
	}
	public function credit_card_number($ccnum, $type = 'auto'){
		$ccnum = preg_replace('/[^0-9]/', '', $ccnum);
		if($type == 'auto' || $type == ''){
			if(substr($ccnum, 0, 1) == '3') $type	 = 'A'; //amex
			if(substr($ccnum, 0, 1) == '4') $type	 = 'V'; //visa
			if(substr($ccnum, 0, 1) == '5') $type	 = 'M'; //mastercard
			if(substr($ccnum, 0, 1) == '6') $type	 = 'D'; //discover
		}
		switch(strtoupper($type)){
			case 'A':
				if(!(preg_match("/^3[47]\d{13}$/", $ccnum))) return false;
				break;
			case 'V':
				if(!preg_match("/^4\d{12}$/", $ccnum) && !preg_match("/^4\d{15}$/", $ccnum)) return false;
				break;
			case 'M':
				if(!preg_match("/^5[1-5]\d{14}$/", $ccnum)) return false;
				break;
			case 'D':
				if(!preg_match("/^6011\d{12}$/", $ccnum)) return false;
				break;
			default:
				//not a recognized card type
				return false;
		}
		if(!$this->_confirm_cc_mod_10($ccnum)) return false;
		return true;
	}
	private function _confirm_cc_mod_10($ccnum){

		// adapted from http://www.zend.com/codex.php?id=31&single=1
		// Reverse and clean the number
		$cc_no	 = strrev($ccnum);
		$digits	 = '';
		$sum	 = 0;

		// VALIDATION ALGORITHM
		// Loop through the number one digit at a time
		// Double the value of every second digit (starting from the right)
		// Concatenate the new values with the unaffected digits
		for($ndx = 0; $ndx < strlen($cc_no); ++$ndx)
			$digits .= ($ndx % 2)?$cc_no[$ndx] * 2:$cc_no[$ndx];

		// Add all of the single digits together
		for($ndx = 0; $ndx < strlen($digits); ++$ndx)
			$sum += $digits[$ndx];

		// Valid card numbers will be transformed into a multiple of 10
		return ($sum % 10)?false:true;
	}
	public function is_image($file_name){
		$allowedExtensions	 = array('jpg', 'jpeg', 'png', 'gif', 'tif', 'bmp',);
		$file_name			 = trim($file_name);
		$return				 = true;
		$ext				 = @end(explode('.', strtolower($file_name))).'';
		if($file_name == '' || !@in_array($ext, $allowedExtensions)){
			$return = false;
		}
		return $return;
	}
	public function password($password, $lenght = 8){
		//this function only test if a password is secure
		$return		 = array(
			'code'			=>0,
			'description'	=>'very weak',
		);
		//check lenght and if it contains all the necessary protected chars
		$strength	 = 0;
		if(strlen($password) >= $lenght){
			//if it is not long enough it should not be accepted !!
			//for example lenght = 8, passowrd 'aA0!'  should return 'very weak'
			$strength++;
			$patterns = array('#[a-z]#', '#[A-Z]#', '#[0-9]#', '/[¬!"£$%^&*()`{}\[\]~;\'#<>?,.\/\\-=_+\|]/');
			foreach($patterns as $pattern){
				if(preg_match($pattern, $password, $matches)){
					$strength++;
				}
			}
		}
		$return['code'] = $strength;
		switch($return['code']){
			case 0:
			case 1:
				$return['description']	 = 'very weak';
				break;
			case 2:
				$return['description']	 = 'weak';
				break;
			case 3:
				$return['description']	 = 'acceptable';
				break;
			case 4:
			case 5:
				$return['description']	 = 'strong';
				break;
			default:
				break;
		}
		return $return;
	}
}

?>