<?php
class NewLocation
{

	//private $oSoapClient;
	//var $advertiserId;
	//var $recruiterId;
	//var $onlyFirstLevel = false;
	//var $withAdCount = false;

	//getAllGeoLocationIdsJson?advertiserId=10000&recruiterId=2521&Authorization=asdfsf

	var $apiBase;
	var $recId;
	var $advId;
	var $apiMethod = 'ads/getAllGeoLocationIdsJson';
	var $Authorization;

	public function __construct($apiBase, $advertiserId, $recruiterId = null, $Authorization)
	{
		// Set variables
		$this->apiBase = $apiBase;
		$this->advId = $advertiserId;
		$this->recId = $recruiterId;
		$this->Authorization = $Authorization;
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
		//'{"countries":[{"country":{"State":[{"name":"New South Wales","Suburbs":[{"id":1,"name":"Hornsby"},{"id":99,"name":"Zetland"},{"id":3,"name":"Sydney"},{"id":100,"name":"Erskine Park"},{"id":4,"name":"Turramurra"},{"id":101,"name":"Saint Clair"}],"id":1}],"name":"Australia","id":1}},{"country":{"State":[{"name":"Texas","Suburbs":[{"id":55,"name":"Houston"}],"id":27}],"name":"United States","id":3}}]}'
		try {
			$url = $this->apiBase . $this->apiMethod .  '?advertiserId=' . $this->advId . '&recruiterId=' . $this->recId;
			$header = array();
			$header[] = 'Authorization:' . base64_encode($this->Authorization);
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

		$resultsObject = json_decode($results, true);
		return $resultsObject;
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
