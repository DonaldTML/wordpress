<?php

class Adlogic_Job_Board_Utility
{

	/*
     *  Check to see if the utf8_encode method is callable AND not disabled 
     */
	public static function isUTF8EncodingAvailable()
	{
		$isCallable = false;
		$disabledFunctions = explode(',', ini_get('disable_functions'));
		if (is_callable("utf8_encode", false)) {
			if (!in_array("utf8_encode", $disabledFunctions)) {
				$isCallable = true;
			}
		}

		if (!$isCallable) {
			error_log("UTF8_Encode is enabled but utf8_encode() method is not available!");
		}

		return $isCallable;
	}

	public static function extractGeoLocationJSON($JSONString)
	{
		if (!isset($JSONString) || empty($JSONString)) {
			return;
		}
		$arr = json_decode($JSONString[0]);
		$result = array(
			"Suburb" => array(),
			"State" => array(),
			"Country" => array(),
			"PostCode" => array()
		);
		foreach ($arr as $location) {
			if ($location->type == "locality") {
				$result["Suburb"] = $location;
			} else if ($location->type == "administrative_area_level_1") {
				$result["State"] = $location;
			} else if ($location->type == "country") {
				$result["Country"] = $location;
			} else if ($location->type == "postal_code") {
				$result["PostCode"] = $location;
			}
		}
		return $result;
	}
}
