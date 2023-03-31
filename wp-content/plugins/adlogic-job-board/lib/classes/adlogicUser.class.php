<?php
class AdlogicUser {
	private $oSoapClient;
	var $user_id;
	var $name;
	var $surname;
	var $email;
	var $facebook_id;
	var $facebook_email;
	var $linkedin_id;
	var $linkedin_email;
	var $google_id;
	var $google_email;
	var $session_hash;
	var $created;
	var $updated;

	public function __construct($oSoapClient) {
		// Set variables
		if ((get_class($oSoapClient) == 'SoapClient') || (get_class($oSoapClient) == 'nusoap_client')) {
			$this->oSoapClient = $oSoapClient;
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

	public function get($sessionHash = null) {
		if (empty($sessionHash) && !empty($this->session_hash)) {
			$sessionHash = $this->session_hash;
		} else if (empty($sessionHash) && empty($this->session_hash)) {
			return false;
		}
		if (empty($this->user_id)) {
			$aSessionCriteria = array();
			
			if (!empty($sessionHash)) {
				$aSessionCriteria['arg0'] = $sessionHash;
			}
			
			switch(get_class($this->oSoapClient)) {
				case 'nusoap_client':
					@$results = $this->oSoapClient->call('getCandidate', array($aSessionCriteria));
					if ($this->oSoapClient->fault) {
						$this->addError($this->oSoapClient->getError());
						return false;
					} else {
						if ($returnXML) {
							return $results['return'];
						} else {
							$resultsObject = simplexml_load_string($results['return'], 'SimpleXMLElement');
			
							if ($resultsObject->status != 'false') {
							$this->user_id			= (string) $resultsObject->id;
							$this->name				= (string) $resultsObject->name;
							$this->surname			= (string) $resultsObject->surname;
							$this->email			= (string) $resultsObject->email;
							$this->facebook_id		= (string) $resultsObject->facebookId;
							$this->facebook_email	= (string) $resultsObject->facebookEmail;
							$this->linkedin_id		= (string) $resultsObject->linkedinId;
							$this->linkedin_email	= (string) $resultsObject->linkedinEmail;
							$this->google_id		= (string) $resultsObject->googleId;
							$this->google_email		= (string) $resultsObject->googleEmail;
							$this->created			= (string) $resultsObject->createdDate;
							$this->updated			= (string) $resultsObject->updateDate;
								return (object) array(
										'user_id'			=> $this->user_id,
										'name'				=> $this->name,
										'email'				=> $this->email,
										'linkedin_id'		=> $this->linkedin_id,
										'linkedin_email'	=> $this->linkedin_email,
										'facebook_id'		=> $this->facebook_id,
										'facebook_email'	=> $this->facebook_email,
										'google_id'			=> $this->google_id,
										'google_email'		=> $this->google_email,
										'created'			=> $this->created,
										'updated'			=> $this->updated,
										'session_hash'		=> $this->session_hash
								);
							} else {
								return false;
							}
						}
					}
					break;
				case 'SoapClient':
				default:
					try {
						$results = $this->oSoapClient->getCandidate($aSessionCriteria);
					} catch (Exception $e) {
						$this->addError($e);
						return false;
					}
			
					if ($returnXML) {
						return $results->return;
					} else {
						$resultsObject = simplexml_load_string($results->return, 'SimpleXMLElement');
			
						if ($resultsObject->status != 'false') {
							$this->user_id			= (string) $resultsObject->id;
							$this->name				= (string) $resultsObject->name;
							$this->surname			= (string) $resultsObject->surname;
							$this->email			= (string) $resultsObject->email;
							$this->facebook_id		= (string) $resultsObject->facebookId;
							$this->facebook_email	= (string) $resultsObject->facebookEmail;
							$this->linkedin_id		= (string) $resultsObject->linkedinId;
							$this->linkedin_email	= (string) $resultsObject->linkedinEmail;
							$this->google_id		= (string) $resultsObject->googleId;
							$this->google_email		= (string) $resultsObject->googleEmail;
							$this->created			= (string) $resultsObject->createdDate;
							$this->updated			= (string) $resultsObject->updateDate;
							return (object) array(
								'user_id'			=> $this->user_id,
								'name'				=> $this->name,
								'email'				=> $this->email,
								'linkedin_id'		=> $this->linkedin_id,
								'linkedin_email'	=> $this->linkedin_email,
								'facebook_id'		=> $this->facebook_id,
								'facebook_email'	=> $this->facebook_email,
					 			'google_id'			=> $this->google_id,
					 			'google_email'		=> $this->google_email,
								'created'			=> $this->created,
								'updated'			=> $this->updated,
								'session_hash'		=> $this->session_hash
							);
						} else {
							return false;
						}
					}
					break;
			}
		} else {
			return (object) array(
				'user_id'			=> $this->user_id,
				'name'				=> $this->name,
				'email'				=> $this->email,
				'linkedin_id'		=> $this->linkedin_id,
				'linkedin_email'	=> $this->linkedin_email,
				'facebook_id'		=> $this->facebook_id,
				'facebook_email'	=> $this->facebook_email,
	 			'google_id'			=> $this->google_id,
	 			'google_email'		=> $this->google_email,
				'created'			=> $this->created,
				'updated'			=> $this->updated,
				'session_hash'		=> $this->session_hash
			);
		}
	}

	public function update($returnXML = false) {

		$aUserCriteria = array();

		// Session Hash
		if (!empty($this->session_hash)) {
			$aUserCriteria['arg0'] = $this->session_hash;
		} else {
			return false;
		}

		// Full Name
		if (!empty($this->name)) {
			$aUserCriteria['arg1'] = $this->name;
		}
		// Surname (not used currently)
		if (!empty($this->surname)) {
			$aUserCriteria['arg2'] = $this->surname;
		}

		// Email Address
		if (!empty($this->email)) {
			$aUserCriteria['arg3'] = $this->email;
		}

		// Facebook Id
		if (!empty($this->facebook_id)) {
			$aUserCriteria['arg4'] = $this->facebook_id;
		}

		// Facebook Email
		if (!empty($this->facebook_email)) {
			$aUserCriteria['arg5'] = $this->facebook_email;
		}

		// LinkedIn Id
		if (!empty($this->linkedin_id)) {
			$aUserCriteria['arg6'] = $this->linkedin_id;
		}

		// LinkedIn Email
		if (!empty($this->linkedin_email)) {
			$aUserCriteria['arg7'] = $this->linkedin_email;
		}

		// Google Id
		if (!empty($this->google_id)) {
			$aUserCriteria['arg8'] = $this->google_id;
		}

		// Google Email
		if (!empty($this->google_email)) {
			$aUserCriteria['arg9'] = $this->google_email;
		}

		switch(get_class($this->oSoapClient)) {
			case 'nusoap_client':
				@$results = $this->oSoapClient->call('updateCandidate', array($aUserCriteria));
				if ($this->oSoapClient->fault) {
					$this->addError($this->oSoapClient->getError());
					return false;
				} else {
					if ($returnXML) {
						return $results['return'];
					} else {
						$resultsObject = simplexml_load_string($results['return'], 'SimpleXMLElement');
		
						if ($resultsObject->status == 'true') {
							// Return Results Object
							return (string) $resultsObject->session_hash;
						} else {
							return false;
						}
					}
				}
				break;
			case 'SoapClient':
			default:
				try {
					$results = $this->oSoapClient->updateCandidate($aUserCriteria);
				} catch (Exception $e) {
					$this->addError($e);
					return false;
				}
		
				if ($returnXML) {
					return $results->return;
				} else {
					$resultsObject = simplexml_load_string($results->return, 'SimpleXMLElement');
		
					if ($resultsObject->status == 'true') {
						// Return Results Object
						return (string) $resultsObject->session_hash;
					} else {
						return false;
					}
				}
				break;
		}
	}

	public function isLoggedIn($sessionHash, $returnXML = false) {
		$aSessionCriteria = array();
		
		if (!empty($sessionHash)) {
			$aSessionCriteria['arg0'] = $sessionHash;
		}

		switch(get_class($this->oSoapClient)) {
			case 'nusoap_client':
				@$results = $this->oSoapClient->call('isCandidateLoggedIn', array($aSessionCriteria));
				if ($this->oSoapClient->fault) {
					$this->addError($this->oSoapClient->getError());
					return false;
				} else {
					if ($returnXML) {
						return $results['return'];
					} else {
						$resultsObject = simplexml_load_string($results['return'], 'SimpleXMLElement');
		
						if ($resultsObject->status == 'true') {
							$this->session_hash = $sessionHash;
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
					$results = $this->oSoapClient->isCandidateLoggedIn($aSessionCriteria);
				} catch (Exception $e) {
					$this->addError($e);
					return false;
				}
		
				if ($returnXML) {
					return $results->return;
				} else {
					$resultsObject = simplexml_load_string($results->return, 'SimpleXMLElement');
		
					if ($resultsObject->status == 'true') {
						$this->session_hash = $sessionHash;
						return true;
					} else {
						return false;
					}
				}
				break;
		}
	}

	public function logout($sessionHash) {
		$aSessionCriteria = array();
		
		if (!empty($sessionHash)) {
			$aSessionCriteria['arg0'] = $sessionHash;
		}
		
		switch(get_class($this->oSoapClient)) {
			case 'nusoap_client':
				@$results = $this->oSoapClient->call('logoutCandidate', array($aSessionCriteria));
				if ($this->oSoapClient->fault) {
					$this->addError($this->oSoapClient->getError());
					return false;
				} else {
					if ($returnXML) {
						return $results['return'];
					} else {
						$resultsObject = simplexml_load_string($results['return'], 'SimpleXMLElement');
		
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
					$results = $this->oSoapClient->logoutCandidate($aSessionCriteria);
				} catch (Exception $e) {
					$this->addError($e);
					return false;
				}
		
				if ($returnXML) {
					return $results->return;
				} else {
					$resultsObject = simplexml_load_string($results->return, 'SimpleXMLElement');
		
					if ($resultsObject->status == 'true') {
						return true;
					} else {
						return false;
					}
				}
				break;
		}
	}

	public function login($aUserDetails, $returnXML = false) {

		$aUserCriteria = array();
	
		if (!empty($aUserDetails['facebook_id'])) {
			$aUserCriteria['arg0'] = $aUserDetails['facebook_id'];
		}
	
		if (!empty($aUserDetails['facebook_email'])) {
			$aUserCriteria['arg1'] = $aUserDetails['facebook_email'];
		}
	
		if (!empty($aUserDetails['linkedin_id'])) {
			$aUserCriteria['arg2'] = $aUserDetails['linkedin_id'];
		}
	
		if (!empty($aUserDetails['linkedin_email'])) {
			$aUserCriteria['arg3'] = $aUserDetails['linkedin_email'];
		}

		if (!empty($aUserDetails['google_id'])) {
			$aUserCriteria['arg4'] = $aUserDetails['google_id'];
		}
		
		if (!empty($aUserDetails['google_email'])) {
			$aUserCriteria['arg5'] = $aUserDetails['google_email'];
		}

		switch(get_class($this->oSoapClient)) {
			case 'nusoap_client':
				@$results = $this->oSoapClient->call('loginCandidate', array($aUserCriteria));
				if ($this->oSoapClient->fault) {
					$this->addError($this->oSoapClient->getError());
					return false;
				} else {
					if ($returnXML) {
						return $results['return'];
					} else {
						$resultsObject = simplexml_load_string($results['return'], 'SimpleXMLElement');
	
						if ($resultsObject->status == 'true') {
							// Return Results Object
							$this->session_hash = $resultsObject->session_hash;
							return (string) $resultsObject->session_hash;
						} else {
							return false;
						}
					}
				}
				break;
			case 'SoapClient':
			default:
				try {
					$results = $this->oSoapClient->loginCandidate($aUserCriteria);
				} catch (Exception $e) {
					$this->addError($e);
					return false;
				}
	
				if ($returnXML) {
					return $results->return;
				} else {
					$resultsObject = simplexml_load_string($results->return, 'SimpleXMLElement');

					if ($resultsObject->status == 'true') {
						// Return Results Object
						$this->session_hash = $resultsObject->session_hash;
						return (string) $resultsObject->session_hash;
					} else {
						return false;
					}
				}
				break;
		}
	}

	public function register($aUserDetails, $returnXML = false) {
		$aUserCriteria = array();

		if (!empty($aUserDetails['name'])) {
			$aUserCriteria['arg0'] = $aUserDetails['name'];
		}

		if (!empty($aUserDetails['surname'])) {
			$aUserCriteria['arg1'] = $aUserDetails['surname'];
		}

		if (!empty($aUserDetails['email'])) {
			$aUserCriteria['arg2'] = $aUserDetails['email'];
		}

		if (!empty($aUserDetails['facebook_id'])) {
			$aUserCriteria['arg3'] = $aUserDetails['facebook_id'];
		}

		if (!empty($aUserDetails['facebook_email'])) {
			$aUserCriteria['arg4'] = $aUserDetails['facebook_email'];
		}

		if (!empty($aUserDetails['linkedin_id'])) {
			$aUserCriteria['arg5'] = $aUserDetails['linkedin_id'];
		}

		if (!empty($aUserDetails['linkedin_email'])) {
			$aUserCriteria['arg6'] = $aUserDetails['linkedin_email'];
		}

		if (!empty($aUserDetails['google_id'])) {
			$aUserCriteria['arg7'] = $aUserDetails['google_id'];
		}
		
		if (!empty($aUserDetails['google_email'])) {
			$aUserCriteria['arg8'] = $aUserDetails['google_email'];
		}
		
		switch(get_class($this->oSoapClient)) {
			case 'nusoap_client':
				@$results = $this->oSoapClient->call('registerCandidate', array($aUserCriteria));
				if ($this->oSoapClient->fault) {
					$this->addError($this->oSoapClient->getError());
					return false;
				} else {
					if ($returnXML) {
						return $results['return'];
					} else {
						$resultsObject = simplexml_load_string($results['return'], 'SimpleXMLElement');

						if ($resultsObject->status == 'true') {
							// Return Results Object
							return (string) $resultsObject->session_hash;
						} else {
							return false;
						}
					}
				}
				break;
			case 'SoapClient':
			default:
				try {
					$results = $this->oSoapClient->registerCandidate($aUserCriteria);
				} catch (Exception $e) {
					$this->addError($e);
					return false;
				}

				if ($returnXML) {
					return $results->return;
				} else {
					$resultsObject = simplexml_load_string($results->return, 'SimpleXMLElement');

					if ($resultsObject->status == 'true') {
						// Return Results Object
						return (string) $resultsObject->session_hash;
					} else {
						return false;
					}
				}
				break;
		}
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