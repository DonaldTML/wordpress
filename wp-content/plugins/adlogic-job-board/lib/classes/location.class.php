<?php 
class Location {
	
	private $oSoapClient;
	var $advertiserId;
	var $recruiterId;
	var $onlyFirstLevel = false;
	var $withAdCount = false;

	public function __construct($oSoapClient, $advertiserId, $recruiterId = null) {
		if ((get_class($oSoapClient) == 'SoapClient') || (get_class($oSoapClient) == 'nusoap_client')) {
			// Set variables
			$this->oSoapClient = $oSoapClient;
			$this->advertiserId = $advertiserId;
			$this->recruiterId = $recruiterId;
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

	public function get($returnXML = false) {
		// Build Parameters Array
		if (!empty($this->recruiterId)) {
			$locationOptions = array(
						'advertiserId'		=> $this->advertiserId, 
						'recruiterId'		=> $this->recruiterId, 
						'onlyFirstLevel'	=> $this->onlyFirstLevel,
						'withAdCount'		=> $this->withAdCount
					);

			switch(get_class($this->oSoapClient)) {
				case 'nusoap_client':
					$results = $this->oSoapClient->call('getAllLocationsFlatListForRecruiter', array($locationOptions));
					if ($this->oSoapClient->fault) {
						$this->addError($this->oSoapClient->getError());
						return false;
					}
					break;
				case 'SoapClient':
				default:
					try {
						$results = $this->oSoapClient->getAllLocationsFlatListForRecruiter($locationOptions);
					} catch (Exception $e) {
						$this->addError($e);
						return false;
					}
					break;
			}

		} else {
			$locationOptions = array('advertiserId' => $this->advertiserId, 'onlyFirstLevel' => $this->onlyFirstLevel);
			
			switch(get_class($this->oSoapClient)) {
				case 'nusoap_client':
					$results = $this->oSoapClient->call('getAllLocationsFlatList', array($locationOptions));
					if ($this->oSoapClient->fault) {
						$this->addError($this->oSoapClient->getError());
						return false;
					} else {
						if ($returnXML) {
							return $results['return'];
						}
					}
					break;
				case 'SoapClient':
				default:
					try {
						$results = $this->oSoapClient->getAllLocationsFlatList($locationOptions);
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

	}

	public function getFromXML($xmlString) {
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

	private function addError($errorMsg) {
		error_log($errorMsg);
		die('Unable to connect to server');
	}

	public function __isset($att) {
		$props = get_object_vars($this);
		return true;
		//return array_key_exists($att, $props);
	}
}
?>