<?php
class JobSearch
{
	private $oSoapClient;
	var $recruiterId;
	var $advertiserId;
	var $keyword = '';
	var $classificationsCriteria = array();
	var $childrenRecruiterIds = array();
	var $salaryType = '';
	var $salaryMin;
	var $salaryMax;
	var $from = '1';
	var $to = '5';
	var $costCenterId;
	var $orgUnit;
	var $internalExternal = 'IntOrBoth'; // Options - IntOrBoth, ExtOrBoth
	var $passphrase;
	var $geoLocationObject;
	var $forceUTF8Encoding = false;

	public function __construct($oSoapClient, $AdvertiserId, $RecruiterId)
	{
		// Set variables
		if ((get_class($oSoapClient) == 'SoapClient') || (get_class($oSoapClient) == 'nusoap_client')) {
			$this->oSoapClient = $oSoapClient;
			$this->advertiserId = $AdvertiserId;
			$this->recruiterId = $RecruiterId;
			$apiSettings = get_option('adlogic_api_settings');
			if (!empty($apiSettings['adlogic_api_force_encoding']) && isset($apiSettings['adlogic_api_force_encoding']) && $apiSettings['adlogic_api_force_encoding'] == "true") {
				require_once(AJB_PLUGIN_PATH . '/lib/classes/Utility.class.php');
				$this->forceUTF8Encoding = Adlogic_Job_Board_Utility::isUTF8EncodingAvailable();
			}
		} else {
			trigger_error('Argument 1 passed to ' . __METHOD__ . ' must be an instance of SoapClient or nusoap_client, instance of ' . get_class($oSoapClient) . ' given', E_USER_ERROR);
		}
	}

	public function set($varName, $value)
	{
		if ($this->__isset($varName)) {
			$this->$varName = $value;
			return true;
		} else {
			return false;
		}
	}

	public function getArchiveJobs($returnXML = false)
	{
		$aJobSearchCriteria = array(
			'recruiterId' 				=> $this->recruiterId,
			'advertiserId' 				=> $this->advertiserId,
			'from' 						=> $this->from,
			'to' 						=> $this->to
		);

		switch (get_class($this->oSoapClient)) {
			case 'nusoap_client':
				$results = $this->oSoapClient->call('searchArchiveAdsV2', array($aJobSearchCriteria));
				if ($this->oSoapClient->fault) {
					$this->addError($this->oSoapClient->getError());
					return false;
				} else {
					if ($returnXML) {
						return $results['return'];
					} else {
						// Return Results Object
						if ($this->forceUTF8Encoding) {
							$resultsObject = simplexml_load_string(utf8_encode($results['return']), 'SimpleXMLElement');
						} else {
							$resultsObject = simplexml_load_string($results['return'], 'SimpleXMLElement');
						}

						return $this->cleanJobSimpleXML($resultsObject);
					}
				}
				break;
			case 'SoapClient':
			default:
				try {
					$results = $this->oSoapClient->searchArchiveAdsV2($aJobSearchCriteria);
				} catch (Exception $e) {
					$this->addError($e);
					return false;
				}

				if ($returnXML) {
					return $results->return;
				} else {
					// Return Results Object
					if ($this->forceUTF8Encoding) {
						$resultsObject = simplexml_load_string(utf8_encode($results->return), 'SimpleXMLElement');
					} else {
						$resultsObject = simplexml_load_string($results->return, 'SimpleXMLElement');
					}

					return $this->cleanJobSimpleXML($resultsObject);
				}
				break;
		}
	}

	public function getHotJobs($returnXML = false)
	{
		$aJobSearchCriteria = array(
			'recruiterId' 				=> $this->recruiterId,
			'advertiserId' 				=> $this->advertiserId,
			'from' 						=> $this->from,
			'to' 						=> $this->to,
			'costCenterId'				=> $this->costCenterId,
			'orgUnit'					=> $this->orgUnit
		);

		switch (get_class($this->oSoapClient)) {
			case 'nusoap_client':
				$results = $this->oSoapClient->call('searchHotAdsV2', array($aJobSearchCriteria));
				if ($this->oSoapClient->fault) {
					$this->addError($this->oSoapClient->getError());
					return false;
				} else {
					if ($returnXML) {
						return $results['return'];
					} else {
						// Return Results Object
						if ($this->forceUTF8Encoding) {
							$resultsObject = simplexml_load_string(utf8_encode($results['return']), 'SimpleXMLElement');
						} else {
							$resultsObject = simplexml_load_string($results['return'], 'SimpleXMLElement');
						}

						return $this->cleanJobSimpleXML($resultsObject);
					}
				}
				break;
			case 'SoapClient':
			default:
				try {
					$results = $this->oSoapClient->searchHotAdsV2($aJobSearchCriteria);
				} catch (Exception $e) {
					$this->addError($e);
					return false;
				}

				if ($returnXML) {
					return $results->return;
				} else {
					// Return Results Object
					if ($this->forceUTF8Encoding) {
						$resultsObject = simplexml_load_string(utf8_encode($results->return), 'SimpleXMLElement');
					} else {
						$resultsObject = simplexml_load_string($results->return, 'SimpleXMLElement');
					}

					return $this->cleanJobSimpleXML($resultsObject);
				}
				break;
		}
	}

	public function getHotJobsIntranet($returnXML = false)
	{
		$aJobSearchCriteria = array(
			'recruiterId' 				=> $this->recruiterId,
			'advertiserId' 				=> $this->advertiserId,
			'from' 						=> $this->from,
			'to' 						=> $this->to,
			'costCenterId'				=> $this->costCenterId,
			'orgUnit'					=> $this->orgUnit,
			'internalExternal'			=> $this->internalExternal
		);

		switch (get_class($this->oSoapClient)) {
			case 'nusoap_client':
				$results = $this->oSoapClient->call('searchHotAdsForInternetAndIntranetV2', array($aJobSearchCriteria));
				if ($this->oSoapClient->fault) {
					$this->addError($this->oSoapClient->getError());
					return false;
				} else {
					if ($returnXML) {
						return $results['return'];
					} else {
						// Return Results Object
						if ($this->forceUTF8Encoding) {
							$resultsObject = simplexml_load_string(utf8_encode($results['return']), 'SimpleXMLElement');
						} else {
							$resultsObject = simplexml_load_string($results['return'], 'SimpleXMLElement');
						}

						return $this->cleanJobSimpleXML($resultsObject);
					}
				}
				break;
			case 'SoapClient':
			default:
				try {
					$results = $this->oSoapClient->searchHotAdsForInternetAndIntranetV2($aJobSearchCriteria);
				} catch (Exception $e) {
					$this->addError($e);
					return false;
				}

				if ($returnXML) {
					return $results->return;
				} else {
					// Return Results Object
					if ($this->forceUTF8Encoding) {
						$resultsObject = simplexml_load_string(utf8_encode($results->return), 'SimpleXMLElement');
					} else {
						$resultsObject = simplexml_load_string($results->return, 'SimpleXMLElement');
					}

					return $this->cleanJobSimpleXML($resultsObject);
				}
				break;
		}
	}

	public function getIntranet($returnXML = false)
	{
		$aJobSearchCriteria = array(
			'recruiterId'				=> $this->recruiterId,
			'advertiserId'				=> $this->advertiserId,
			'keyword'					=> $this->keyword,
			'classificationsCriteria'	=> $this->classificationsCriteria,
			'salaryType'				=> $this->salaryType,
			'salaryMin'					=> $this->salaryMin,
			'salaryMax'					=> $this->salaryMax,
			'from'						=> $this->from,
			'to'						=> $this->to,
			'costCenterId'				=> $this->costCenterId,
			'orgUnit'					=> $this->orgUnit,
			'internalExternal'			=> $this->internalExternal
		);

		switch (get_class($this->oSoapClient)) {
			case 'nusoap_client':
				$results = $this->oSoapClient->call('searchAdsForInternetAndIntranetV2', array($aJobSearchCriteria));
				if ($this->oSoapClient->fault) {
					$this->addError($this->oSoapClient->getError());
					return false;
				} else {
					if ($returnXML) {
						return $results['return'];
					} else {
						// Return Results Object
						if ($this->forceUTF8Encoding) {
							$resultsObject = simplexml_load_string(utf8_encode($results['return']), 'SimpleXMLElement');
						} else {
							$resultsObject = simplexml_load_string($results['return'], 'SimpleXMLElement');
						}

						return $this->cleanJobSimpleXML($resultsObject);
					}
				}
				break;
			case 'SoapClient':
			default:
				try {
					$results = $this->oSoapClient->searchAdsForInternetAndIntranetV2($aJobSearchCriteria);
				} catch (Exception $e) {
					$this->addError($e);
					return false;
				}


				if ($returnXML) {
					return $results->return;
				} else {
					// Return Results Object
					if ($this->forceUTF8Encoding) {
						$resultsObject = simplexml_load_string(utf8_encode($results->return), 'SimpleXMLElement');
					} else {
						$resultsObject = simplexml_load_string($results->return, 'SimpleXMLElement');
					}
					return $this->cleanJobSimpleXML($resultsObject);
				}
				break;
		}
	}

	public function get($returnXML = false)
	{
		$aJobSearchCriteria = array(
			'recruiterId'				=> $this->recruiterId,
			'advertiserId'				=> $this->advertiserId,
			'keyword'					=> $this->keyword,
			'classificationsCriteria'	=> $this->classificationsCriteria,
			'salaryType'				=> $this->salaryType,
			'salaryMin'					=> $this->salaryMin,
			'salaryMax'					=> $this->salaryMax,
			'from'						=> $this->from,
			'to'						=> $this->to,
			'costCenterId'				=> $this->costCenterId,
			'orgUnit'					=> $this->orgUnit,
			'geoLocationObject'			=> $this->geoLocationObject,
			'suburbIds'					=> $this->suburbIds
		);

		if (!adlogic_job_board::shouldUseNewAPI()) {
			switch (get_class($this->oSoapClient)) {
				case 'nusoap_client':
					$results = $this->oSoapClient->call('searchAdsV2', array($aJobSearchCriteria));
					if ($this->oSoapClient->fault) {
						$this->addError($this->oSoapClient->getError());
						return false;
					} else {
						if ($returnXML) {
							return $results['return'];
						} else {
							// Return Results Object
							if ($results['return'] == 'Not active now.') {
								return $results['return'];
							}
							if ($this->forceUTF8Encoding) {
								$resultsObject = simplexml_load_string(utf8_encode($results['return']), 'SimpleXMLElement');
							} else {
								$resultsObject = simplexml_load_string($results['return'], 'SimpleXMLElement');
							}

							return $this->cleanJobSimpleXML($resultsObject);
						}
					}
					break;
				case 'SoapClient':
				default:
					try {
						$results = $this->oSoapClient->searchAdsV2($aJobSearchCriteria);
					} catch (Exception $e) {
						$this->addError($e);
						return false;
					}

					if ($returnXML) {
						return $results->return;
					} else {
						// Return Results Object
						if ($results->return == 'Not active now.') {
							return $results->return;
						}
						if (!$this->forceUTF8Encoding) {
							$resultsObject = simplexml_load_string(utf8_encode($results->return), 'SimpleXMLElement');
						} else {
							$resultsObject = simplexml_load_string($results->return, 'SimpleXMLElement');
						}

						return $this->cleanJobSimpleXML($resultsObject);
					}
					break;
			}
		} else {

			try {

				$apiSettings = get_option('adlogic_api_settings');
				$apibase = $apiSettings['adlogic_rest_server'];
				$authorization = $apiSettings['adlogic_rest_api_key'];
				$apiMethod = 'ads/searchAdsV2';
				$classif = "";
				if(!empty($aJobSearchCriteria['classificationsCriteria']))
				{
					

					$classif = array();
					$classif = json_encode($aJobSearchCriteria['classificationsCriteria']);
				}
			
				$url = $apibase .
					$apiMethod .
					'?advertiserId=' . $aJobSearchCriteria['advertiserId'] .
					'&recruiterId=' . $aJobSearchCriteria['recruiterId'] .
					'&keyword=' . $aJobSearchCriteria['keyword'] .
					'&classificationsCriteria=' . $classif .
					'&salaryType=' . $aJobSearchCriteria['salaryType'] .
					'&salaryMin=' . $aJobSearchCriteria['salaryMin'] .
					'&salaryMax=' . $aJobSearchCriteria['salaryMax'] .
					'&from=' . $aJobSearchCriteria['from'] .
					'&to=' . $aJobSearchCriteria['to'] .
					'&costCenterId=' . $aJobSearchCriteria['costCenterId'] .
					'&suburbIds=' . $aJobSearchCriteria['suburbIds'];

				$header = array();
				$header[] = 'Authorization:' . base64_encode($authorization);
				$curl = curl_init($url);
				curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
				curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
				curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
				$results = curl_exec($curl);
			} catch (Exception $e) {
				$this->addError($e);
				return false;
			}

			if ($results === false) {
				$info = curl_getinfo($curl);
				curl_close($curl);
				//die('error occured during curl exec. Additioanl info: ' . var_export($info));
			}
			curl_close($curl);

			if ($returnXML) {
				return $results;
			} else {
				// Return Results Object
				if ($results == 'Not active now.') {
					return $results;
				}
				if ($this->forceUTF8Encoding) {
					$resultsObject = simplexml_load_string(utf8_encode($results), 'SimpleXMLElement');
				} else {
					$resultsObject = simplexml_load_string($results, 'SimpleXMLElement');
				}
			}

			return $this->cleanJobSimpleXML($resultsObject);
		}
	}

	public function getFiltered($returnXML = false)
	{
		$aJobSearchCriteria = array(
			'recruiterId'				=> $this->recruiterId,
			'advertiserId'				=> $this->advertiserId,
			'keyword'					=> $this->keyword,
			'classificationsCriteria'	=> $this->classificationsCriteria,
			'salaryType'				=> $this->salaryType,
			'salaryMin'					=> $this->salaryMin,
			'salaryMax'					=> $this->salaryMax,
			'from'						=> $this->from,
			'to'						=> $this->to,
			'costCenterId'				=> $this->costCenterId,
			'orgUnit'					=> $this->orgUnit,
			'childrenRecruiterId'		=> $this->childrenRecruiterIds,
			'geoLocationObject'			=> $this->geoLocationObject
		);

		switch (get_class($this->oSoapClient)) {
			case 'nusoap_client':
				$results = $this->oSoapClient->call('searchAdsWithOrgUnitsFilteredByChildrenRecruiterIdsV2', array($aJobSearchCriteria));
				if ($this->oSoapClient->fault) {
					$this->addError($this->oSoapClient->getError());
					return false;
				} else {
					if ($returnXML) {
						return $results['return'];
					} else {
						// Return Results Object
						if ($this->forceUTF8Encoding) {
							$resultsObject = simplexml_load_string(utf8_encode($results['return']), 'SimpleXMLElement');
						} else {
							$resultsObject = simplexml_load_string($results['return'], 'SimpleXMLElement');
						}

						return $this->cleanJobSimpleXML($resultsObject);
					}
				}
				break;
			case 'SoapClient':
			default:
				try {
					$results = $this->oSoapClient->searchAdsWithOrgUnitsFilteredByChildrenRecruiterIdsV2($aJobSearchCriteria);
				} catch (Exception $e) {
					$this->addError($e);
					return false;
				}

				if ($returnXML) {
					return $results->return;
				} else {
					// Return Results Object
					if ($this->forceUTF8Encoding) {
						$resultsObject = simplexml_load_string(utf8_encode($results->return), 'SimpleXMLElement');
					} else {
						$resultsObject = simplexml_load_string($results->return, 'SimpleXMLElement');
					}

					return $this->cleanJobSimpleXML($resultsObject);
				}
				break;
		}
	}

	public function getForAllRecruiters($returnXML = false, $passphrase = '')
	{
		$aJobSearchCriteria = array(
			'advertiserId'				=> $this->advertiserId,
			'keyword'					=> $this->keyword,
			'classificationsCriteria'	=> $this->classificationsCriteria,
			'salaryType'				=> $this->salaryType,
			'salaryMin'					=> $this->salaryMin,
			'salaryMax'					=> $this->salaryMax,
			'from'						=> $this->from,
			'to'						=> $this->to,
			'costCenterId'				=> $this->costCenterId,
			'orgUnit'					=> $this->orgUnit,
			'passphrase'				=> $this->passphrase
		);

		switch (get_class($this->oSoapClient)) {
			case 'nusoap_client':
				$results = $this->oSoapClient->call('searchAllRecruitersAds', array($aJobSearchCriteria));
				if ($this->oSoapClient->fault) {
					$this->addError($this->oSoapClient->getError());
					return false;
				} else {
					if ($returnXML) {
						return $results['return'];
					} else {
						if ($results['return'] != 'Authentication failed') {
							// Return Results Object
							if ($this->forceUTF8Encoding) {
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
					$results = $this->oSoapClient->searchAllRecruitersAds($aJobSearchCriteria);
				} catch (Exception $e) {
					$this->addError($e);
					return false;
				}


				if ($returnXML) {
					return $results->return;
				} else {
					if ($results->return != 'Authentication failed') {
						// Return Results Object
						if ($this->forceUTF8Encoding) {
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
	}

	public function cleanJobSimpleXML($oSimpleXML)
	{
		/*
		  *	May 25th 2016
		  *	We will now always pass along an empty JobPosting element if there are no results returned from the API.
		  *	To ensure no errors occur, we'll check to see if the JobPosting element is empty - if it is we will simply return the direct response
		  *	instead of appending values i.e. JobTitle, JobDescription, etc.
		  */
		if (empty($oSimpleXML->JobPostings->JobPosting)) {
			return $oSimpleXML;
		}
		// UnCDATA-ise the XML (as json_encode does not like it)
		//
		for ($i = 0; $i < count($oSimpleXML->JobPostings->JobPosting); $i++) {
			$oJobPosting = $oSimpleXML->JobPostings->JobPosting[$i];
			// Job Title
			$oSimpleXML->JobPostings->JobPosting[$i]->JobTitle = (string) $oJobPosting->JobTitle;
			// Job Description
			$oSimpleXML->JobPostings->JobPosting[$i]->JobDescription = (string) $oJobPosting->JobDescription;
			// Logo URL
			$oSimpleXML->JobPostings->JobPosting[$i]->standOut->logoURL = (string) $oJobPosting->standOut->logoURL;
			// Bulletpoints
			for ($j = 0; $j < count($oSimpleXML->JobPostings->JobPosting[$i]->standOut->BulletPoints->BulletPoint); $j++) {
				$oBulletPoint = $oJobPosting->standOut->BulletPoints->BulletPoint[$j];
				$oSimpleXML->JobPostings->JobPosting[$i]->standOut->BulletPoints->BulletPoint[$j] = (string) $oBulletPoint;
			}

			// Locations
			for ($j = 0; $j < count($oSimpleXML->JobPostings->JobPosting[$i]->locations->location); $j++) {
				$oLocation = $oJobPosting->locations->location[$j];
				$oSimpleXML->JobPostings->JobPosting[$i]->locations->location[$j]->addChild('value', htmlspecialchars((string) $oLocation));
			}
			// Classifications
			for ($j = 0; $j < count($oSimpleXML->JobPostings->JobPosting[$i]->classifications->classification); $j++) {
				$oClassification = $oJobPosting->classifications->classification[$j];
				$oSimpleXML->JobPostings->JobPosting[$i]->classifications->classification[$j]->addChild('value', htmlspecialchars((string) $oClassification));
			}

			// WorkType Details
			$workTypeValue = (string) $oSimpleXML->JobPostings->JobPosting[$i]->workType;
			$oSimpleXML->JobPostings->JobPosting[$i]->workType = null;
			$oSimpleXML->JobPostings->JobPosting[$i]->workType->addChild('value', htmlspecialchars($workTypeValue));
		}

		return $oSimpleXML;
	}

	private function addError($errorMsg)
	{
		error_log($errorMsg);
		die('Unable to connect to server');
	}

	public function __isset($att)
	{
		$props = get_object_vars($this);
		return true;
		//return array_key_exists($att, $props);
	}
}
