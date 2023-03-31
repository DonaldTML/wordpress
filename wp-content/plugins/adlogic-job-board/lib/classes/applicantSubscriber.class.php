<?php 
class ApplicantSubscriberWS {
	//Always pass this as blank ''
	public $ApplicantId = '';
	public $Name = '';
	public $Surname = '';
	public $Phone = '';
	public $Email = '';
	public $State = 'NSW';
	public $Country = 'Australia';

	//Always pass this as false
	public $Indigenous = 'false';
	public $WorkTypeArr = '';
	public $RegionArr = '';
	public $IndustryArr = '';
	public $AdvertiserId = '';
	public $RecruiterId = '';

	//Always pass this as 1
	public $NoticePeriod = 1;

	private $oSoapClient;
	
	public function __construct($oSoapClient, $AdvertiserId, $RecruiterId, $onlyFirstLevel = false) {
		if ((get_class($oSoapClient) == 'SoapClient') || (get_class($oSoapClient) == 'nusoap_client')) {
			// Set variables
			$this->oSoapClient = $oSoapClient;
			$this->AdvertiserId = $AdvertiserId;
			$this->RecruiterId = $RecruiterId;
		} else {
			trigger_error('Argument 1 passed to ' . __METHOD__ . ' must be an instance of SoapClient or nusoap_client, instance of ' . get_class($oSoapClient) . ' given', E_USER_ERROR);
		}
	}
	
	public function set($varName, $value) {
		if (isset($this->$varName)) {
			$this->$varName = $value;
			return true;
		} else {
			return false;
		}
	}
	
	public function subscribe() {
		$subscriberCriteria = array(
								'ApplicantId'		=> $this->ApplicantId,
								'Name'				=> $this->Name,
								'Surname'			=> $this->Surname,
								'Phone'				=> $this->Phone,
								'Email'				=> $this->Email,
								'State'				=> $this->State,
								'Country'			=> $this->Country,
								'Indigenous'		=> $this->Indigenous,
								'AdvertiserId'		=> $this->AdvertiserId,
								'RecruiterId'		=> $this->RecruiterId,
								'NoticePeriod'		=> $this->NoticePeriod,
								'WorkTypeArr'		=> $this->WorkTypeArr,
								'RegionArr'			=> $this->RegionArr,
								'IndustryArr'		=> $this->IndustryArr
							);
		
		switch(get_class($this->oSoapClient)) {
			case 'nusoap_client':
				$results = $this->oSoapClient->call('addSubscriber', array('applicantDTO' => $subscriberCriteria));
				if ($this->oSoapClient->fault) {
					$this->addError($this->oSoapClient->getError());
					return false;
				}
				break;
			case 'SoapClient':
			default:
				try {
					$results = $this->oSoapClient->addSubscriber(array('applicantDTO' => $this));
				} catch (Exception $e) {
					$this->addError($e);
					return false;
				}
				break;
		}

		if (is_object($results)) {
			if ($results->return == 'OK') {
				return array('result' => true);
			} else {
				return array('result' => false, 'message' => $results->return);
			}
		} else if (is_array($results)) {
			if ($results['return'] == 'OK') {
				return array('result' => true);
			} else {
				return array('result' => false, 'message' => $results['return']);
			}
		}
	}
	
	public function getSubscriber() {
		$subscriberCriteria = array(
			'ApplicantId'		=> $this->ApplicantId
		);
		
		switch(get_class($this->oSoapClient)) {
			case 'nusoap_client':
				$results = $this->oSoapClient->call('getSubscriber', array($subscriberCriteria));
				if ($this->oSoapClient->fault) {
					$this->addError($this->oSoapClient->getError());
					return false;
				}
				break;
			case 'SoapClient':
			default:
				try {
					$results = $this->oSoapClient->getSubscriber(array($subscriberCriteria));
				} catch (Exception $e) {
					$this->addError($e);
					return false;
				}
				break;
		}

		if (is_object($results)) {
			if ($results->return == 'OK') {
				return array('result' => true);
			} else {
				return array('result' => false, 'message' => $results->return);
			}
		} else if (is_array($results)) {
			if ($results['return'] == 'OK') {
				return array('result' => true);
			} else {
				return array('result' => false, 'message' => $results['return']);
			}
		}
	}

	private function addError($errorMsg) {
		error_log($errorMsg);
		die('Unable to connect to server');
	}
}
?>