<?php
require_once(dirname(__FILE__).'/library/fedex-common.php');

class fedex_address_validation{
	private $client = false;
	private $request;
	private $errors = array();
	private $min_score = 91;	//bumped up from 81 'cause was letting pass 28401 instead than 28451

	public function __construct($path_to_wsdl = ''){
		if($path_to_wsdl==''){
			$path_to_wsdl=dirname(__FILE__).'/wsdl/AddressValidationService_v2.wsdl';
		}
		ini_set('soap.wsdl_cache_enabled', '0');
		$this->client = new SoapClient($path_to_wsdl, array('trace' => 1)); // Refer to http://us3.php.net/manual/en/ref.soap.php for more information
		$this->request['TransactionDetail'] = array('CustomerTransactionId' => ' *** Address Validation Request v2 using PHP ***');
		$this->request['Version'] = array('ServiceId' => 'aval', 'Major' => '2', 'Intermediate' => '0', 'Minor' => '0');
		$this->request['RequestTimestamp'] = date('c');
		$this->request['Options'] = array('CheckResidentialStatus' => 1,
									 'MaximumNumberOfMatches' => 3,
									 'StreetAccuracy' => 'LOOSE',
									 'DirectionalAccuracy' => 'LOOSE',
									 'CompanyNameAccuracy' => 'LOOSE',
									 'ConvertToUpperCase' => 0,
									 'RecognizeAlternateCityNames' => 1,
									 'ReturnParsedElements' => 1);
	}
	
	public function set_min_score($min_score){
		$this->min_score = $min_score;
	}
	
	public function credential($key, $password, $account_number, $meter_number){
		$this->request['WebAuthenticationDetail'] = array('UserCredential' =>
											  array('Key' => $key, 'Password' => $password)); // Replace 'XXX' and 'YYY' with FedEx provided credentials 
		$this->request['ClientDetail'] = array('AccountNumber' => $account_number, 'MeterNumber' => $meter_number); // Replace 'XXX' with client's account and meter number
	}
	
	public function add_address_to_validate($addressid, $street, $state, $city, $zip, $country, $company){
		$street	= array(trim(htmlspecialchars($street)));
		$this->request['AddressesToValidate'][] = array('AddressId' => $addressid,
														  'Address' => array('StreetLines' => $street,
																	  'StateOrProvinceCode' => trim($state),
																	  'City' => trim($city),
																	  'PostalCode' => trim($zip),
																	  'CountryCode' => trim($country),
																	  'CompanyName' => trim($company)),
												);
	}
	
	public function reset_address_to_validate(){
		$this->request['AddressesToValidate'] = array();
	}
	
	public function get_errors(){
		return $this->errors;
	}
	
	public function get_last_response(){
		return $this->response;
	}
	
	public function validate(){
		$this->errors = array();
		$address = false;
		try {
			$response = $this->client->addressValidation($this->request);
		
			if ($response->HighestSeverity != 'FAILURE' && $response->HighestSeverity != 'ERROR'){
		//        printRequestResponse($client);
				if(!is_array($response->AddressResults)){
					$response->AddressResults = array($response->AddressResults);
				}
				foreach ($response -> AddressResults as $address_result){
					$key				= $address_result->AddressId;
					$prop_address		= $address_result->ProposedAddressDetails;
					if(!is_array($prop_address)){
						$prop_address = array($prop_address);
					}
					foreach($prop_address as $k=>$p_address){
						$address[$key][$k]['valid']					= $p_address->DeliveryPointValidation=='CONFIRMED'?true:($p_address->DeliveryPointValidation=='UNCONFIRMED'?false:NULL);
						$address[$key][$k]['score']					= $p_address->Score;
						$address[$key][$k]['residential']			= $p_address->ResidentialStatus=='RESIDENTIAL'?true:($p_address->ResidentialStatus=='BUSINESS'?false:NULL);
						$address[$key][$k]['changed']				= $this->check_changes($p_address->Changes);

						$address[$key][$k]['address']['street']		= $p_address->Address->StreetLines;
						$address[$key][$k]['address']['city']		= $p_address->Address->City;
						$address[$key][$k]['address']['state']		= $p_address->Address->StateOrProvinceCode;
						$address[$key][$k]['address']['zip']		= $p_address->Address->PostalCode;
						$address[$key][$k]['address']['country']	= $p_address->Address->CountryCode;

						$address[$key][$k]['changes']['street']		= $this->check_changes($p_address->ParsedAddress->ParsedStreetLine->Elements);//[0]->Changes!='NO_CHANGES' || $p_address->ParsedAddress->ParsedStreetLine->Elements[1]->Changes!='NO_CHANGES' || $p_address->ParsedAddress->ParsedStreetLine->Elements[2]->Changes!='NO_CHANGES';
						$address[$key][$k]['changes']['city']		= $this->check_changes($p_address->ParsedAddress->ParsedCity->Elements);//->Changes!='NO_CHANGES';
						$address[$key][$k]['changes']['state']		= $this->check_changes($p_address->ParsedAddress->ParsedStateOrProvinceCode->Elements);//->Changes!='NO_CHANGES';
						$address[$key][$k]['changes']['zip']		= $this->check_changes($p_address->ParsedAddress->ParsedPostalCode->Elements);//[0]->Changes!='NO_CHANGES' || $p_address->ParsedAddress->ParsedPostalCode->Elements[1]->Changes!='NO_CHANGES' || $p_address->ParsedAddress->ParsedPostalCode->Elements[2]->Changes!='NO_CHANGES';
						$address[$key][$k]['changes']['country']	= $this->check_changes($p_address->ParsedAddress->ParsedCountryCode->Elements);//->Changes!='NO_CHANGES';
						
						if($address[$key][$k]['valid']===false || $address[$key][$k]['changed']===true){
							if($address[$key][$k]['score'] > $this->min_score){
								//it passed the minimun score, let this address pass completely
								//not need for adjustments
								$address[$key][$k]['valid']		= true;
								$address[$key][$k]['changed']	= false;
							}
						}
/*
						$address[$key][$k]['was']['street']			= trim($street);
						$address[$key][$k]['was']['city']			= trim($state);
						$address[$key][$k]['was']['state']			= trim($city);
						$address[$key][$k]['was']['zip']			= trim($zip);
						$address[$key][$k]['was']['country']		= trim($country);
*/
					}
				}
			}else{
				foreach ($response->Notifications as $notification){
					if(is_array($response->Notifications)){
						$this->errors[] .= $notification->Severity.':'.$notification->Message;
					}else{
						$this->errors[] .= $notification;
					}
				}
			}
		} catch (SoapFault $exception) {
			printFault($exception, $client);
		}
		$this->reset_address_to_validate();
		$this->response = $response;
		return $address;
	}
	
	private function check_changes($what){
		$return = true;
		if(is_array($what)){
			foreach($what as $key=>$value){
				if(isset($value->Changes)){
					$return = $return && ($value->Changes =='NO_CHANGES' || $value->Changes =='NORMALIZED');
				} else {
					$return = false;
				}
			}
		} else {
			if(isset($what->Changes)){
				$return = ($what->Changes=='NO_CHANGES' || $what->Changes =='NORMALIZED');
			} else {
				$return = ($what=='NO_CHANGES' || $what=='NORMALIZED');
			}
		//$static++;
		}
		return !$return;
	}
}
?>