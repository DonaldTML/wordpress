<?php 
class ApplicantWS {

	// Job Information
	public $JobAdId = '';
	public $advId = '';
	public $JobId = '';
	// Personal Information
	public $Source = '';
	public $SubSourceId = '';
	public $Name = '';
	public $Surname = '';
	public $Phone = '';
	public $StreetAddress = '';
	public $Email = '';
	public $PlatformId = '';
	public $UserAgent = '';

	// Cover Letter
	public $CoverLetterContent = '';
	public $CoverLetterFileName = '';
	public $CoverLetterData = '';
	// Resume
	public $ResumeFileName = '';
	public $ResumeData = '';

	// Retention Consent
	public $RetentionConsent = false;

	// Answers
	public $JobCriteriaAnswers = array();

	// Custom Fields Answers
	public $CustomFieldsAnswers = array();

	// Additional Documents
	public $ApplicantDocuments = array();
	
	// Referrer URL
	public $ReferrerUrl = '';

	// Tracking Code
	public $TrackingCode = '';

	private $oSoapClient;
	
	public function __construct($oSoapClient) {
		if ((get_class($oSoapClient) == 'SoapClient') || (get_class($oSoapClient) == 'nusoap_client')) {
			// Set variables
			$this->oSoapClient = $oSoapClient;
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

	public function submitJobId() {
		$apiSettings = get_option('adlogic_api_settings');
		$applicantDetails = array(
			'Source'				=> $this->Source,
			'SubSourceId'			=> $this->advId,
			'Name'					=> $this->Name,
			'Surname'				=> $this->Surname,
			'Phone'					=> $this->Phone,
			'StreetAddress'			=> $this->StreetAddress,
			'Email'					=> $this->Email,
			'PlatformId'			=> $this->PlatformId,
			'UserAgent'				=> $this->UserAgent,
			'CoverLetterContent'	=> $this->CoverLetterContent,
			'CoverLetterFileName'	=> $this->CoverLetterFileName,
			'CoverLetterData'		=> $this->CoverLetterData,
			'ResumeFileName'		=> $this->ResumeFileName,
			'ResumeData'			=> $this->ResumeData,
			'RetentionConsent'		=> $this->RetentionConsent,
			'JobCriteriaAnswers'	=> $this->JobCriteriaAnswers,
			'CustomFieldsAnswers'	=> $this->CustomFieldsAnswers,
			'ApplicantDocuments'	=> $this->ApplicantDocuments,
			'TrackingCode'			=> $this->TrackingCode,
			'ReferrerUrl'			=> $this->ReferrerUrl
		);
		
		$additionalApplicantOptions = array(
			'applicantWS' => $applicantDetails,
			'advId'					=>	$this->advId,
			'jobId'					=>	$this->JobId
		);

		switch(get_class($this->oSoapClient)) {
			case 'nusoap_client':
				$results = $this->oSoapClient->call('addApplicantOnlyWithJobId', $additionalApplicantOptions);
				if ($this->oSoapClient->fault) {
					$this->addError($this->oSoapClient->getError());
					return false;
				}
				break;
			case 'SoapClient':
			default:
				try {
					$results = $this->oSoapClient->addApplicantOnlyWithJobId($additionalApplicantOptions);
				} catch (Exception $e) {
					$this->addError($e);
					return false;
				}
				break;
		}

		// Return Results Object
		if (is_object($results)) {
			if (($results->return == 'OK') || (stripos($results->return, 'added successfully') != false)) {
				return array('result' => true);
			} else {
				return array('result' => false, 'message' => $results->return);
			}
		} else if (is_array($results)) {
			if (($results['return'] == 'OK') || (stripos($results['return'], 'added successfully') != false)) {
				return array('result' => true);
			} else {
				return array('result' => false, 'message' => $results['return']);
			}
		}
	}
	public function submit() {
		$apiSettings = get_option('adlogic_api_settings');
			$applicantDetails = array(
				'JobAdId'				=> $this->JobAdId,
				'Source'				=> $this->Source,
				'SubSourceId'			=> $this->SubSourceId,
				'Name'					=> $this->Name,
				'Surname'				=> $this->Surname,
				'Phone'					=> $this->Phone,
				'StreetAddress'			=> $this->StreetAddress,
				'Email'					=> $this->Email,
				'PlatformId'			=> $this->PlatformId,
				'UserAgent'				=> $this->UserAgent,
				'CoverLetterContent'	=> $this->CoverLetterContent,
				'CoverLetterFileName'	=> $this->CoverLetterFileName,
				'CoverLetterData'		=> $this->CoverLetterData,
				'ResumeFileName'		=> $this->ResumeFileName,
				'ResumeData'			=> $this->ResumeData,
				'RetentionConsent'		=> $this->RetentionConsent,
				'JobCriteriaAnswers'	=> $this->JobCriteriaAnswers,
				'CustomFieldsAnswers'	=> $this->CustomFieldsAnswers,
				'ApplicantDocuments'	=> $this->ApplicantDocuments,
				'TrackingCode'			=> $this->TrackingCode,
				'ReferrerUrl'			=> $this->ReferrerUrl
			);
			print_r("<textarea>".json_encode($applicantDetails)."</textarea>");
			switch(get_class($this->oSoapClient)) {
				case 'nusoap_client':
					$results = $this->oSoapClient->call('addApplicant', array('applicantWS' => $applicantDetails));
					if ($this->oSoapClient->fault) {
						$this->addError($this->oSoapClient->getError());
						return false;
					}
					break;
				case 'SoapClient':
				default:
					try {
						$results = $this->oSoapClient->addApplicant(array('applicantWS' => $applicantDetails));
					} catch (Exception $e) {
						$this->addError($e);
						return false;
					}
					break;
			}

			// Return Results Object
			if (is_object($results)) {
				if (($results->return == 'OK') || (stripos($results->return, 'added successfully') != false)) {
					return array('result' => true);
				} else {
					return array('result' => false, 'message' => $results->return);
				}
			} else if (is_array($results)) {
				if (($results['return'] == 'OK') || (stripos($results['return'], 'added successfully') != false)) {
					return array('result' => true);
				} else {
					return array('result' => false, 'message' => $results['return']);
				}
			}
		
		
	}

	public function getCriteriaForJobId($returnXML = false, $archivedAds = false) {

			$jobApplicationOptions = array('jobId' => $this->JobId, 'showArchived' => $archivedAds);
			switch(get_class($this->oSoapClient)) {
				case 'nusoap_client':
					$results = $this->oSoapClient->call('getJobCriteriasForJob', array($jobApplicationOptions));
					if ($this->oSoapClient->fault) {
						$this->addError($this->oSoapClient->getError());
						return false;
					}
					break;
				case 'SoapClient':
				default:
					try {
						$results = $this->oSoapClient->getJobCriteriasForJob($jobApplicationOptions);
					} catch (Exception $e) {
						$this->addError($e);
						return false;
					}
					break;
			}

			if ($returnXML) {
				if (is_object($results)) {
					// Remove ampersand from criteria questions as it breaks the page
					$results->return = str_replace('&', '&amp;', $results->return);
					return $results->return;
				} else if (is_array($results)) {
					// Remove ampersand from criteria questions as it breaks the page
					$results->return = str_replace('&', '&amp;', $results->return);
					return $results['return'];
				}
			} else {
				// Suppress all xml parsing errors from being displayed to output
				libxml_use_internal_errors(true);

				// Return Results Object
				if (is_object($results)) {
					// Remove ampersand from criteria questions as it breaks the page
					$results->return = str_replace('&', '&amp;', $results->return);
					$resultsObject = simplexml_load_string($results->return, 'SimpleXMLElement');
				} else if (is_array($results)) {
					// Remove ampersand from criteria questions as it breaks the page
					$results->return = str_replace('&', '&amp;', $results->return);
					$resultsObject = simplexml_load_string($results['return'], 'SimpleXMLElement');
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

	public function getCriteria($returnXML = false, $archivedAds = false) {
		$apiSettings = get_option('adlogic_api_settings');
		// Build Parameters Array
		
			$jobApplicationOptions = array('jobAdId' => $this->JobAdId, 'showArchived' => $archivedAds);
			switch(get_class($this->oSoapClient)) {
				case 'nusoap_client':
					$results = $this->oSoapClient->call('getJobCriteriasForAd', array($jobApplicationOptions));
					if ($this->oSoapClient->fault) {
						$this->addError($this->oSoapClient->getError());
						return false;
					}
					break;
				case 'SoapClient':
				default:
					try {
						$results = $this->oSoapClient->getJobCriteriasForAd($jobApplicationOptions);
					} catch (Exception $e) {
						$this->addError($e);
						return false;
					}
					break;
			}

			if ($returnXML) {
				if (is_object($results)) {
					// Remove ampersand from criteria questions as it breaks the page
					$results->return = str_replace('&', '&amp;', $results->return);
					return $results->return;
				} else if (is_array($results)) {
					// Remove ampersand from criteria questions as it breaks the page
					$results->return = str_replace('&', '&amp;', $results->return);
					return $results['return'];
				}
			} else {
				// Suppress all xml parsing errors from being displayed to output
				libxml_use_internal_errors(true);

				// Return Results Object
				if (is_object($results)) {
					// Remove ampersand from criteria questions as it breaks the page
					$results->return = str_replace('&', '&amp;', $results->return);
					$results->return = htmlspecialchars_decode($results->return);
					$resultsObject = simplexml_load_string($results->return, 'SimpleXMLElement');
				} else if (is_array($results)) {
					// Remove ampersand from criteria questions as it breaks the page
					$results->return = str_replace('&', '&amp;', $results->return);
					$resultsObject = simplexml_load_string($results['return'], 'SimpleXMLElement');
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
		if (isset($oSimpleXML->jobCriteria )) {
			for ($i = 0; $i < count($oSimpleXML->jobCriteria); $i++) {
				$oSimpleXML->jobCriteria[$i]->question = (string) $oSimpleXML->jobCriteria[$i]->question;

				if (isset($oSimpleXML->jobCriteria[$i]->answer->answerOption)) {
					for ($j = 0; $j < count($oSimpleXML->jobCriteria[$i]->answer->answerOption); $j++) {
						$oSimpleXML->jobCriteria[$i]->answer->answerOption[$j] = (string) $oSimpleXML->jobCriteria[$i]->answer->answerOption[$j];
					}
				}
			}
		}
		return $oSimpleXML;
	}

	// Sort the Job Questions using quicksort
	public function sortQuestions($aJobCriteria) {
		if (count($aJobCriteria) <2) {
			return $aJobCriteria;
		} else {
			// If receiving a job criteria object, first place into an array structure
			if (is_object($aJobCriteria)) {
				$oJobCriteria = $aJobCriteria;
				$aJobCriteria = array();
				for ($i = 0; $i < count($oJobCriteria); $i++) {
					$aJobCriteria[] = $oJobCriteria[$i];
				}
			}

			// Create low and high arrays
			$low = $high = array();

			// Reset array
			reset($aJobCriteria);

			// Create pivot key
			$pivot = array_shift($aJobCriteria);
			$pivotCriterionAttributes  = $pivot->attributes();
			$pivotKey = $pivotCriterionAttributes->sortIndex;

			// Get array length
			foreach ($aJobCriteria as $i => $oJobCriterion) {
				$criterionAttributes  = $oJobCriterion->attributes();
				if ((int) $criterionAttributes->sortIndex <= $pivotCriterionAttributes->sortIndex) {
					$low[] = $aJobCriteria[$i];
				} else {
					$high[] = $aJobCriteria[$i];
				}
			}

			return array_merge($this->sortQuestions($low), array($pivot), $this->sortQuestions($high));
		}
	}

	private function addError($errorMsg) {
		error_log($errorMsg);
		die('Unable to connect to server');
	}
}
?>