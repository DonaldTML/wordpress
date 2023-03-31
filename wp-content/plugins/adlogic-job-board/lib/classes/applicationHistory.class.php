<?php

class AppliedHistory {

	var $recruiterId;
	var $advertiserId;
	var $sessionHash;
	public $getJobsCriteria = array();
	var $template;

	
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

	public function countHistoricJobs() {
		$xml = AJB_PLUGIN_PATH . '/lib/classes/test.xml';
		
		try {
			$loadXML = simplexml_load_file($xml, 'SimpleXMLElement');

		} catch (Exception $e) {
			$this->addError($e);
			return false;
		}
		$Job = $loadXML->Advertiser->JobPostings;
		echo $Job->attributes()->count;
		
	}

	public function HistoricJob_Pagination() {
		$xml = AJB_PLUGIN_PATH . '/lib/classes/test.xml';

		try {
			$loadXML = simplexml_load_file($xml, 'SimpleXMLElement');

		} catch (Exception $e) {
			$this->addError($e);
			return false;
		}
		if ($returnXML) {
			return $loadXML->return;
		}

		$searchSettings = get_option('adlogic_search_settings');
		$resultsPerPage = $searchSettings['adlogic_search_results_per_page'];
		$from = 0;
		$JobCount = $loadXML->Advertiser->JobPostings->attributes()->count;
		?>
		<div class="pagination">
			<ul>
			<?php
				if(($JobCount > 0) && ($JobCount > $resultsPerPage)) {
					$i = 1;
					while($i < $resultsPerPage)
					{
						echo '<li class="page-item"><a href="#page='. $i . '">' . $i . '</a></li>';
						$i++;
					}
				
				}
			?>
			</ul>
		</div>
		<?php


	}

	public function getHistoricJobs($getJobsCriteria) {
		

		global $wp_rewrite;
		$xml = AJB_PLUGIN_PATH . '/lib/classes/test.xml';
		
		try {
			$loadXML = simplexml_load_file($xml, 'SimpleXMLElement');

		} catch (Exception $e) {
			$this->addError($e);
			return false;
		}
		if ($returnXML) {
			return $loadXML->return;
		}
		return $loadXML;

		// Can be used later when we need to use the WSDL.
		/* Uncomment for WSDL

		$getJobsCriteria = array(
			// Session Hash
			"arg0"	=>	$getJobsCriteria['sessionHash'],

			// Recruiter ID
			"arg1"	=>	$getJobsCriteria['recruiterId'],

			// Advertiser ID
			"arg2"	=>	$getJobsCriteria['advertiserId'],

			// From
			"arg3"	=>	"1",

			// To
			"arg4"	=>	"5",

			// Expired Ads?
			"arg5"	=>	"1"

		);
		
		switch(get_class($this->oSoapClient)) {
			case 'nusoap_client':
				$results = $this->oSoapClient->call('getSavedAds', array($getJobsCriteria));
				if ($this->oSoapClient->fault) {
					$this->addError($this->oSoapClient->getError());
					return false;
				} else {
					if ($returnXML) {
						return $results['return'];
					} else {
						if ($results['return'] != 'Authentication failed') {
							// Return Results Object
							$resultsObject = simplexml_load_string($results['return'], 'SimpleXMLElement');
							return $this->cleanJobSimpleXML($resultsObject);
						}
					}
				}
				break;
			case 'SoapClient':
			default:
				try {
					$results = $this->oSoapClient->getSavedAds($getJobsCriteria);
				} catch (Exception $e) {
					$this->addError($e);
					return false;
				}

				if ($returnXML) {
					return $results->return;
				} else {
					if ($results->return != 'Authentication failed') {
						// Return Results Object
						$resultsObject = simplexml_load_string($results->return, 'SimpleXMLElement');
				
						return $this->cleanJobSimpleXML($resultsObject);
					} else {
						return false;
					}
				}
				break;
		}

		// Local Database storage stub
		
		return $aAppliedJobs;
		*/
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

	private function addError($errorMsg) {
		error_log($errorMsg);
		die('Unable to connect to server');
	}

}