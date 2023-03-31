<?php
class Worktype
{

	private $oSoapClient;
	var $advertiserId;
	var $recruiterId;
	var $onlyFirstLevel = false;
	var $withAdCount = false;

	public function __construct($oSoapClient, $advertiserId, $recruiterId = null)
	{
		if ((get_class($oSoapClient) == 'SoapClient') || (get_class($oSoapClient) == 'nusoap_client')) {
			// Set variables
			$this->oSoapClient = $oSoapClient;
			$this->advertiserId = $advertiserId;
			$this->recruiterId = $recruiterId;
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

	public function get($returnXML = false)
	{
		// Build Parameters Array
		$workTypeOptions = array(
			'advertiserId'		=> $this->advertiserId,
			'recruiterId'		=> $this->recruiterId,
			'onlyFirstLevel'	=> $this->onlyFirstLevel,
			'withAdCount'		=> $this->withAdCount
		);
		if (!adlogic_job_board::shouldUseNewAPI()) {
			if (!empty($this->recruiterId)) {

				switch (get_class($this->oSoapClient)) {
					case 'nusoap_client':
						$results = $this->oSoapClient->call('getWorkTypesForRecruiter', array($workTypeOptions));
						if ($this->oSoapClient->fault) {
							$this->addError($this->oSoapClient->getError());
							return false;
						}
						break;
					case 'SoapClient':
					default:
						try {
							$results = $this->oSoapClient->getWorkTypesForRecruiter($workTypeOptions);
						} catch (Exception $e) {
							$this->addError($e);
							return false;
						}
						break;
				}
			} else {
				$workTypeOptions = array('advertiserId' => $this->advertiserId, 'onlyFirstLevel' => $this->onlyFirstLevel);
				switch (get_class($this->oSoapClient)) {
					case 'nusoap_client':
						$results = $this->oSoapClient->call('getWorkTypes', array($workTypeOptions));
						if ($this->oSoapClient->fault) {
							$this->addError($this->oSoapClient->getError());
							return false;
						}
						break;
					case 'SoapClient':
					default:
						try {
							$results = $this->oSoapClient->getWorkTypes($workTypeOptions);
						} catch (Exception $e) {
							$this->addError($e);
							return false;
						}
						break;
				}
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
					$resultsObject = simplexml_load_string($results->return);
				} else if (is_array($results)) {
					$resultsObject = simplexml_load_string($results['return']);
				}

				// TODO: Error logging for XML Parsing
				foreach (libxml_get_errors() as $error) {
					// Still to implement
				}

				// Clear XML Parsing Errors
				libxml_clear_errors();
				// un-suppress xml parsing errors
				libxml_use_internal_errors(false);

				return $resultsObject;
			}
		} else {
			try {

				$apiSettings = get_option('adlogic_api_settings');
				$apibase = $apiSettings['adlogic_rest_server'];
				$authorization = $apiSettings['adlogic_rest_api_key'];
				$apiMethod = 'ads/getWorkTypesForRecruiter';

				$url = $apibase .
					$apiMethod .
					'?advertiserId=' . $workTypeOptions['advertiserId'] .
					'&recruiterId=' . $workTypeOptions['recruiterId'];
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
				libxml_use_internal_errors(true);

				// Return Results Object
				$resultsObject = simplexml_load_string($results, 'SimpleXMLElement');
				return $resultsObject;
			}
		}
	}

	public function getFromXML($xmlString)
	{
		// Suppress all xml parsing errors from being displayed to output
		libxml_use_internal_errors(true);

		// Return Results Object
		$resultsObject = simplexml_load_string($xmlString);

		// TODO: Error logging for XML Parsing
		foreach (libxml_get_errors() as $error) {
			// Still to implement
		}

		// Clear XML Parsing Errors
		libxml_clear_errors();
		// un-suppress xml parsing errors
		libxml_use_internal_errors(false);

		return $resultsObject;
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
