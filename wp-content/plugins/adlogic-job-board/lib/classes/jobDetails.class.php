<?php
class JobDetails {
	
	private $oSoapClient;
	var $jobAdId;
	var $webToken;
	var $applicationFormUrl;
	var $templateId;
	var $isJob;
	var $forceUTF8Encoding = false;

	public function __construct($oSoapClient, $jobAdId, $webToken, $isJob) {
		if ((get_class($oSoapClient) == 'SoapClient') || (get_class($oSoapClient) == 'nusoap_client')) {
			// Set variables
			$this->oSoapClient = $oSoapClient;
			$this->jobAdId = $jobAdId;
			$this->webToken = $webToken;
			$this->isJob = $isJob;
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

	public function getHTML($returnXML = false, $subSourceId = null, $platformId = AJB_PLATFORM_ID_DESKTOP) {
		$method = 'viewAd';
		$param = 'jobAdId';
		if($this->isJob) {
			$method = 'viewJob';
			$param = 'jobId';
		}

		// Set Custom Parameters
		$searchSettings = get_option('adlogic_search_settings');
		 $jobDetailscustomDesign = $searchSettings['adlogic_job_details_page_design'];
		 if(empty($jobDetailscustomDesign) || $jobDetailscustomDesign == 'false'){
			$jobDetailscustomDesign = 'false';
		}
		 else{
			$jobDetailscustomDesign = 'true';
		 }
		$aParams = array(
			'subSourceId' => $subSourceId,	// Source of the application
			'platformId' => $platformId,	// Platform the application was made from
			'format' => 'HTML',				// Return HTML or XML Format,
			'showExpiredAds' => true,
            'simpleTemplateOnly' => $jobDetailscustomDesign    // Returns HTML without buttons
		);

		// Set custom application form url (if applicable)
		if (!empty($this->applicationFormUrl)) {
			$aParams['applicationUrl'] = $this->applicationFormUrl;
		}
		if (!empty($this->templateId)) {
			$aParams['templateId'] = $this->templateId;
		}
		if (!empty($this->webToken)) {
			$aParams['webLinkToken'] = $this->webToken;
			if(empty($this->jobAdId)) {
				$this->jobAdId = 0;
			}
		}
		// Build Parameters Array 
		$jobDetailOptions = array($param => $this->jobAdId, 'params' => json_encode($aParams));
		if (!Adlogic_job_board::shouldUseNewAPI()||$this->isJob) {
		switch(get_class($this->oSoapClient)) {
			case 'nusoap_client':
				$results = $this->oSoapClient->call($method, array($jobDetailOptions));
				if ($this->oSoapClient->fault) {
					$this->addError($this->oSoapClient->getError());
					return false;
				}
				break;
			case 'SoapClient':
			default:
				try {
					$results = $this->oSoapClient->$method($jobDetailOptions);
				} catch (Exception $e) {
					echo $this->oSoapClient->__getLastResponse();
					$this->addError($e);
					return false;
				}
				break;
		} } elseif (!$this->isJob&&Adlogic_Job_Board::shouldUseNewAPI()) {
			try {
				
				$apiSettings = get_option('adlogic_api_settings');
				$apibase = $apiSettings['adlogic_rest_server']; 
				$authorization = $apiSettings['adlogic_rest_api_key'];
				$apiMethod = 'ads/'.$method;
				
				$url = $apibase . 
				$apiMethod .  
				'?jobAdId=' . $this->jobAdId . 
				'&params=' . urlencode(json_encode($aParams)) ;
				$header = array();
				
				$header[] = 'Authorization:' . base64_encode($authorization);
				$curl = curl_init($url);
				curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
				curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
				curl_setopt($curl, CURLOPT_HTTPHEADER,$header);
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
			if($this->forceUTF8Encoding) {
				$resultsObject = simplexml_load_string(utf8_encode($results), 'SimpleXMLElement', LIBXML_NOCDATA);
			} else {
				$resultsObject = simplexml_load_string($results, 'SimpleXMLElement', LIBXML_NOCDATA);
			}
			return $resultsObject[0];

		}

		if ($returnXML) {
			if (is_object($results)) {
				return $results->return;
			} else if (is_array($results)) {
				return $results['return'];
			}
		} else {
			// Suppress all xml parsing errors from being displayed to output
			libxml_use_internal_errors(true);

			// Return Results Object
			if (is_object($results)) {
				if($this->forceUTF8Encoding) {
					$resultsObject = simplexml_load_string(utf8_encode($results->return), 'SimpleXMLElement', LIBXML_NOCDATA);
				} else {
					$resultsObject = simplexml_load_string($results->return, 'SimpleXMLElement', LIBXML_NOCDATA);
				}
			} else if (is_array($results)) {
				if($this->forceUTF8Encoding) {
					$resultsObject = simplexml_load_string(utf8_encode($results['return']), 'SimpleXMLElement', LIBXML_NOCDATA);
				} else {
					$resultsObject = simplexml_load_string($results['return'], 'SimpleXMLElement', LIBXML_NOCDATA);
				}
			}

			// TODO: Error logging for XML Parsing
			foreach (libxml_get_errors() as $error) {
				// Still to implement
			}

			// Clear XML Parsing Errors
			libxml_clear_errors();
			// un-suppress xml parsing errors
			libxml_use_internal_errors(false);

			return $resultsObject[0];
		}
	}

	public function getDetails($returnXML = false, $subSourceId = null, $platformId = AJB_PLATFORM_ID_DESKTOP) {
		$method = 'viewAd';
		$param = 'jobAdId';
		if($this->isJob) {
			$method = 'viewJob';
			$param = 'jobId';
		}

		// Set Custom Parameters
		$aParams = array(
				//'subSourceId' => $subSourceId,	// Source of the application
				//'platformId' => $platformId,	// Platform the application was made from
				'format' => 'XML'				// Return HTML or XML Format
			);

		// Set custom application form url (if applicable)
		if (!empty($this->applicationFormUrl)) {
			$aParams['applicationUrl'] = $this->applicationFormUrl;
		}
		if (!empty($this->webToken)) {
			$aParams['webLinkToken'] = $this->webToken;
			if(empty($this->jobAdId)) {
				$this->jobAdId = 0;
			}
		}
		// Build Parameters Array
		$jobDetailOptions = array($param => $this->jobAdId, 'params' => json_encode($aParams));
		if (!Adlogic_job_board::shouldUseNewAPI()||$this->isJob) {
		switch(get_class($this->oSoapClient)) {
			case 'nusoap_client':
				$results = $this->oSoapClient->call($method, array($jobDetailOptions));
				if ($this->oSoapClient->fault) {
					$this->addError($this->oSoapClient->getError());
					return false;
				}
				break;
			case 'SoapClient':
			default:
				try {
					$results = $this->oSoapClient->$method($jobDetailOptions);
				} catch (Exception $e) {
					$this->addError($e);
					return false;
				}
				break;
		} } elseif (!$this->isJob&&Adlogic_Job_Board::shouldUseNewAPI()) {
			try {
				
				$apiSettings = get_option('adlogic_api_settings');
				$apibase = $apiSettings['adlogic_rest_server']; 
				$authorization = $apiSettings['adlogic_rest_api_key'];
				$apiMethod = 'ads/'.$method;
				
				$url = $apibase . 
				$apiMethod .  
				'?jobAdId=' . $this->jobAdId . 
				'&params=' . urlencode(json_encode($aParams)) ;
				$header = array();
				
				$header[] = 'Authorization:' . base64_encode($authorization);
				$curl = curl_init($url);
				curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
				curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
				curl_setopt($curl, CURLOPT_HTTPHEADER,$header);
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
			if($this->forceUTF8Encoding) {
				$resultsObject = simplexml_load_string(utf8_encode($results), 'SimpleXMLElement', LIBXML_NOCDATA);
			} else {
				$resultsObject = simplexml_load_string($results, 'SimpleXMLElement', LIBXML_NOCDATA);
			}
			return $resultsObject[0];

		}

		if ($returnXML) {
			if (is_object($results)) {
				return $results->return;
			} else if (is_array($results)) {
				return $results['return'];
			}
		} else {
			// Suppress all xml parsing errors from being displayed to output
			libxml_use_internal_errors(true);

			// Return Results Object
			if (is_object($results)) {
				if($this->forceUTF8Encoding) {
					$resultsObject = simplexml_load_string(utf8_encode($results->return), 'SimpleXMLElement', LIBXML_NOCDATA);
				} else {
					$resultsObject = simplexml_load_string($results->return, 'SimpleXMLElement', LIBXML_NOCDATA);
				}
			} else if (is_array($results)) {
				if($this->forceUTF8Encoding) {
					$resultsObject = simplexml_load_string(utf8_encode($results['return']), 'SimpleXMLElement', LIBXML_NOCDATA);
				} else {
					$resultsObject = simplexml_load_string($results['return'], 'SimpleXMLElement', LIBXML_NOCDATA);
				}
			}

			// TODO: Error logging for XML Parsing
			foreach (libxml_get_errors() as $error) {
				// Still to implement
			}

			// Clear XML Parsing Errors
			libxml_clear_errors();
			// un-suppress xml parsing errors
			libxml_use_internal_errors(false);

			return $this->cleanJobSimpleXML($resultsObject);
		}

	}

	public function cleanJobSimpleXML($oSimpleXML) {
		// UnCDATA-ise the XML (as json_encode does not like it)
		$oJobPosting = $oSimpleXML;
		// Job Title
		$oSimpleXML->JobTitle = (string) $oJobPosting->JobTitle;
		// Ad Footer
		$oSimpleXML->adFooter = (string) $oJobPosting->adFooter;
		// Job Description
		$oSimpleXML->JobDescription = (string) $oJobPosting->JobDescription;
		// Search Headline
		$oSimpleXML->SearchHeadline = (string) $oJobPosting->SearchHeadline;
		// Search Summary
		$oSimpleXML->SearchSummary = (string) $oJobPosting->SearchSummary;
		// Logo URL
		$oSimpleXML->standOut->logoURL = (string) $oJobPosting->standOut->logoURL;
		// Consultant Details
		$oSimpleXML->Enquiry->ConsultantImage = (string) $oJobPosting->Enquiry->ConsultantImage;
		// Bulletpoints
		for ($j = 0; $j < count($oSimpleXML->standOut->BulletPoints->BulletPoint); $j++) {
			$oBulletPoint = $oJobPosting->standOut->BulletPoints->BulletPoint[$j];
			$oSimpleXML->standOut->BulletPoints->BulletPoint[$j] = (string) $oBulletPoint;
		}
		// Bulletpoints
		for ($j = 0; $j < count($oSimpleXML->BulletPoints->BulletPoint); $j++) {
			$oBulletPoint = $oJobPosting->BulletPoints->BulletPoint[$j];
			$oSimpleXML->BulletPoints->BulletPoint[$j] = (string) $oBulletPoint;
		}

		// Locations
		for ($j = 0; $j < count($oSimpleXML->locations->location); $j++) {
			$oLocation =$oJobPosting->locations->location[$j];
			$oSimpleXML->locations->location[$j]->addChild('value', str_replace('&', '&amp;', (string) $oLocation));
		}
		// Classifications
		for ($j = 0; $j < count($oSimpleXML->classifications->classification); $j++) {
			$oClassification =$oJobPosting->classifications->classification[$j];
			$oSimpleXML->classifications->classification[$j]->addChild('value', str_replace('&', '&amp;', (string) $oClassification));
		}

		// Salary Details - minimum
		$oSimpleXML->Salary->salaryMinimum = (string) $oJobPosting->Salary->salaryMinimum;
		// Salary Details - maximum
		$oSimpleXML->Salary->salaryMaximum = (string) $oJobPosting->Salary->salaryMaximum;
		// Salary Details - rate
		$oSimpleXML->Salary->salaryRate = (string) $oJobPosting->Salary->salaryRate;
		// Salary Details - Additional Text
		$oSimpleXML->Salary->salaryAdditionalText = (string) $oJobPosting->Salary->salaryAdditionalText;

		// WorkType Details
		$workTypeValue = (string) $oSimpleXML->workType;
		$oSimpleXML->workType = null;
		$oSimpleXML->workType->addChild('value', $workTypeValue);

		// AdStatus
		$oSimpleXML->AdStatus = (string) $oSimpleXML->AdStatus;

		return $oSimpleXML;
	}

	public function __isset($att) {
		$props = get_object_vars($this);
		return true;
		//return array_key_exists($att, $props);
	}

	private function addError($errorMsg) {
		error_log($errorMsg);
	}
}
?>
