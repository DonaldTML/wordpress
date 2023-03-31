<?php
class SavedJobs {
	private $oSoapClient;
	var $recruiterId;
	var $advertiserId;
	var $sessionHash;
	var $jobAdId;
	var $from = 1;
	var $to = 1000;
	var $expiredAds = false;
	var $forceUTF8Encoding = false;

	public function __construct($oSoapClient) {
		// Set variables
		if ((get_class($oSoapClient) == 'SoapClient') || (get_class($oSoapClient) == 'nusoap_client')) {
			$this->oSoapClient = $oSoapClient;
			$apiSettings = get_option('adlogic_api_settings');
			if(!empty($apiSettings['adlogic_api_force_encoding']) && isset($apiSettings['adlogic_api_force_encoding']) && $apiSettings['adlogic_api_force_encoding'] == "true") {
				require_once(AJB_PLUGIN_PATH . '/lib/classes/Utility.class.php');
				$this->forceUTF8Encoding = Adlogic_Job_Board_Utility::isUTF8EncodingAvailable();
			}
		} else {
			trigger_error('Argument 1 passed to ' . __METHOD__ . ' must be an instance of SoapClient or nusoap_client, instance of ' . get_class($oSoapClient) . ' given', E_USER_ERROR);
		}
	}

	public function set($varName, $value) {
		if ($this->__isset($varName)) {
			$this->$varName = $value;
			return true;
		} else {
			return false;
		}
	}

	public function add($jobAdId, $returnXML = false) {
		$aSavedJobsCriteria = array(
			'arg0'	=> $this->sessionHash,
			'arg1'	=> $jobAdId
		);

		switch(get_class($this->oSoapClient)) {
			case 'nusoap_client':
				@$results = $this->oSoapClient->call('addSavedAd', array($aSavedJobsCriteria));
				if ($this->oSoapClient->fault) {
					$this->addError($this->oSoapClient->getError());
					return false;
				} else {
					if ($returnXML) {
						return $results['return'];
					} else {
						if($this->forceUTF8Encoding) {
							$resultsObject = simplexml_load_string(utf8_encode($results['return']), 'SimpleXMLElement');
						} else {
							$resultsObject = simplexml_load_string($results['return'], 'SimpleXMLElement');
						}
		
						if ($resultsObject->status == 'true') {
							return true;
						} else {
							return false;
						}
					}
				}
				break;
			case 'SoapClient':
			default:
				try {
					$results = $this->oSoapClient->addSavedAd($aSavedJobsCriteria);
				} catch (Exception $e) {
					$this->addError($e);
					return false;
				}
		
				if ($returnXML) {
					return $results->return;
				} else {
					if($this->forceUTF8Encoding) {
						$resultsObject = simplexml_load_string(utf8_encode($results->return), 'SimpleXMLElement');
					} else {
						$resultsObject = simplexml_load_string($results->return, 'SimpleXMLElement');
					}

					if ($resultsObject->status == 'true') {
						return true;
					} else {
						return false;
					}
				}
				break;
		}
	}

	public function remove($jobAdId, $returnXML = false) {
		$aSavedJobsCriteria = array(
			'arg0'	=> $this->sessionHash,
			'arg1'	=> $jobAdId
		);

		switch(get_class($this->oSoapClient)) {
			case 'nusoap_client':
				@$results = $this->oSoapClient->call('removeSavedAd', array($aSavedJobsCriteria));
				if ($this->oSoapClient->fault) {
					$this->addError($this->oSoapClient->getError());
					return false;
				} else {
					if ($returnXML) {
						return $results['return'];
					} else {
						if($this->forceUTF8Encoding) {
							$resultsObject = simplexml_load_string(utf8_encode($results['return']), 'SimpleXMLElement');
						} else {
							$resultsObject = simplexml_load_string($results['return'], 'SimpleXMLElement');
						}
		
						if ($resultsObject->status == 'true') {
							return true;
						} else {
							return false;
						}
					}
				}
				break;
			case 'SoapClient':
			default:
				try {
					$results = $this->oSoapClient->removeSavedAd($aSavedJobsCriteria);
				} catch (Exception $e) {
					$this->addError($e);
					return false;
				}
		
				if ($returnXML) {
					return $results->return;
				} else {
					if($this->forceUTF8Encoding) {
						$resultsObject = simplexml_load_string(utf8_encode($results->return), 'SimpleXMLElement');
					} else {
						$resultsObject = simplexml_load_string($results->return, 'SimpleXMLElement');
					}
		
					if ($resultsObject->status == 'true') {
						return true;
					} else {
						return false;
					}
				}
				break;
		}
	}

	public function get($returnXML = false) {
		$aSavedJobsCriteria = array(
			'arg0'		=> $this->sessionHash
		);

		if (!empty($this->recruiterId)) {
			$aSavedJobsCriteria['arg1'] = $this->recruiterId;
			
		}

		if (!empty($this->advertiserId)) {
			$aSavedJobsCriteria['arg2'] = $this->advertiserId;
		}

		if (!empty($this->from)) {
			$aSavedJobsCriteria['arg3'] = $this->from;
		}

		if (!empty($this->to)) {
			$aSavedJobsCriteria['arg4'] = $this->to;
		}

		if (!empty($this->expiredAds)) {
			$aSavedJobsCriteria['arg5'] = $this->expiredAds;
		}

/*		global $wpdb;
		$savedJobs = $wpdb->get_results('select `ajb_saved_jobs`.* from `ajb_saved_jobs` inner join `ajb_sessions` on `ajb_sessions`.`user_id` = `ajb_saved_jobs`.`user_id` where `ajb_sessions`.`hash`="' . $this->sessionHash . '"', OBJECT);
		$aSavedJobs = array();

		foreach ($savedJobs as $i => $savedJob) {
			$aSavedJobs[] = $savedJob->job_ad_id;
		}*/

		switch(get_class($this->oSoapClient)) {
			case 'nusoap_client':
				$results = $this->oSoapClient->call('getSavedAds', array($aSavedJobsCriteria));
				if ($this->oSoapClient->fault) {
					$this->addError($this->oSoapClient->getError());
					return false;
				} else {
					if ($returnXML) {
						return $results['return'];
					} else {
						if ($results['return'] != 'Authentication failed') {
							// Return Results Object
							if($this->forceUTF8Encoding) {
								$resultsObject = simplexml_load_string(utf8_encode($results['return']), 'SimpleXMLElement');
							} else {
								$resultsObject = simplexml_load_string($results['return'], 'SimpleXMLElement');
							}
			
							return $this->cleanJobSimpleXML($resultsObject);
						}
					}
				}
				break;
			case 'SoapClient':
			default:
				try {
					$results = $this->oSoapClient->getSavedAds($aSavedJobsCriteria);
				} catch (Exception $e) {
					$this->addError($e);
					return false;
				}

				if ($returnXML) {
					return $results->return;
				} else {
					if ($results->return != 'Authentication failed') {
						// Return Results Object
						if($this->forceUTF8Encoding) {
							$resultsObject = simplexml_load_string(utf8_encode($results->return), 'SimpleXMLElement');
						} else {
							$resultsObject = simplexml_load_string($results->return, 'SimpleXMLElement');
						}
				
						
						return $this->cleanJobSimpleXML($resultsObject);
					} else {
						return false;
					}
				}
				break;
		}

		// Local Database storage stub
		
		return $aSavedJobs;
	}

	private function addError($errorMsg) {
		error_log($errorMsg);
		die('Unable to connect to server');
	}
	
	public function __isset($att) {
		$props = get_object_vars($this);
		return true;
		//return array_key_exists($att, $props);
	}

	public function cleanJobSimpleXML($oSimpleXML) {
		// UnCDATA-ise the XML (as json_encode does not like it)
		for ($i = 0; $i < count($oSimpleXML->JobPostings->JobPosting); $i++) {
			$oJobPosting = $oSimpleXML->JobPostings->JobPosting[$i];
			// Job Title
			$oSimpleXML->JobPostings->JobPosting[$i]->JobTitle = str_replace('&', '&amp;', (string) $oJobPosting->JobTitle);
			// Job Description
			$oSimpleXML->JobPostings->JobPosting[$i]->JobDescription = str_replace('&', '&amp;', (string) $oJobPosting->JobDescription);
			// Logo URL
			$oSimpleXML->JobPostings->JobPosting[$i]->standOut->logoURL = (string) $oJobPosting->standOut->logoURL;
			// Bulletpoints
			for ($j = 0; $j < count($oSimpleXML->JobPostings->JobPosting[$i]->standOut->BulletPoints->BulletPoint); $j++) {
				$oBulletPoint = $oJobPosting->standOut->BulletPoints->BulletPoint[$j];
				$oSimpleXML->JobPostings->JobPosting[$i]->standOut->BulletPoints->BulletPoint[$j] = str_replace('&', '&amp;', (string) $oBulletPoint);
			}
	
			// Locations
			for ($j = 0; $j < count($oSimpleXML->JobPostings->JobPosting[$i]->locations->location); $j++) {
				$oLocation =$oJobPosting->locations->location[$j];
				$oSimpleXML->JobPostings->JobPosting[$i]->locations->location[$j]->addChild('value', str_replace('&', '&amp;', (string) $oLocation));
			}
			// Classifications
			for ($j = 0; $j < count($oSimpleXML->JobPostings->JobPosting[$i]->classifications->classification); $j++) {
				$oClassification =$oJobPosting->classifications->classification[$j];
				$oSimpleXML->JobPostings->JobPosting[$i]->classifications->classification[$j]->addChild('value',  str_replace('&', '&amp;', (string) $oClassification));
			}
	
			// WorkType Details
			$workTypeValue = (string) $oSimpleXML->JobPostings->JobPosting[$i]->workType;
			$oSimpleXML->JobPostings->JobPosting[$i]->workType = null;
			$oSimpleXML->JobPostings->JobPosting[$i]->workType->addChild('value', str_replace('&', '&amp;', $workTypeValue));
		}
	
		return $oSimpleXML;
	}
}
?>