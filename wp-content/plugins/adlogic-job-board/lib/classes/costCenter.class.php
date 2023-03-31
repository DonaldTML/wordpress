<?php 
class CostCenter {
	
	private $oSoapClient;
	var $recruiterId;

	public function __construct($oSoapClient, $recruiterId = null) {
		if ((get_class($oSoapClient) == 'SoapClient') || (get_class($oSoapClient) == 'nusoap_client')) {
			// Set variables
			$this->oSoapClient = $oSoapClient;
			$this->recruiterId = $recruiterId;
		} else {
			trigger_error('Argument 1 passed to ' . __METHOD__ . ' must be an instance of SoapClient or nusoap_client, instance of ' . get_class($oSoapClient) . ' given', E_USER_ERROR);
		}
	}

	public function get($returnXML = false) {
		// Build Parameters Array
		$costCenterOptions = array('recruiterId' => $this->recruiterId);
		
		switch(get_class($this->oSoapClient)) {
			case 'nusoap_client':
				$results = $this->oSoapClient->call('getCostCenters', array($costCenterOptions));
				if ($this->oSoapClient->fault) {
					$this->addError($this->oSoapClient->getError());
					return false;
				}
				break;
			case 'SoapClient':
			default:
				try {
					$results = $this->oSoapClient->getCostCenters($costCenterOptions);
				} catch (Exception $e) {
					$this->addError($e);
					return false;
				}
				break;
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
				$resultsObject = simplexml_load_string($results->return, 'SimpleXMLElement');
			} else if (is_array($results)) {
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
				
			return $resultsObject;
		}
	}

	private function addError($errorMsg) {
		error_log($errorMsg);
		die('Unable to connect to server');
	}
}
?>