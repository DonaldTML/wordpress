<?php 

class SMSGlobal {

	var $server_url = 'https://www.smsglobal.com.au/http-api.php';
	var $username;
	var $password;
	var $sender_number = 'JobAlert';
	var $max_sms_split = 2;

	/**
	 * Function sendSMS
	 * 
	 * This function allows sending SMS via SMS Global
	 * @param int $destination detaination phone number
	 * @param string $text text for sms
	 * @param int $schedule_time as unix timestamp
	 */
	function SendSMS($destination, $text, $schedule_time = NULL) {

		//try load configuration file and try again.

		$content =	'action=sendsms'.
					'&user='  .rawurlencode($this->username).
					'&password=' . rawurlencode($this->password).
					'&to=' . rawurlencode($destination).
					'&from=' . rawurlencode($this->sender_number).
					'&text=' . rawurlencode($text) .
					'&maxsplit=' . $this->max_sms_split;
		
		if ((!empty($schedule_time)) && (is_numeric($schedule_time))) {
			$content .= '&scheduledatetime=' . rawurlencode(date('Y-m-d H:i:s', $schedule_time));
		}

		//$smsglobal_response = file_get_contents('http://www.smsglobal.com.au/http-api.php?'.$content);
		// Initialise CURL
		$ch = curl_init($this->server_url);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $content);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		if (PHP_OS == 'WINNT') {
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false );
		}

		// Send Request
		$smsglobal_response = curl_exec ($ch);
		// Close CURL
		curl_close ($ch);
		//Sample Response
		//OK: 0; Sent queued message ID: 04b4a8d4a5a02176 SMSGlobalMsgID:6613115713715266 
		//$smsglobal_response = 'ERROR: 11 SMSGlobalMsgID:';

		/* Build Response Array */
		$responseArray = array();
		// Status
		if (strstr($smsglobal_response, 'OK: 0;')) {
			$responseArray['status'] = true;
		} else {
			$responseArray['status'] = false;
		}

		// Queue ID
		if (strstr($smsglobal_response, 'Sent queued message ID:')) {
			$queue_id = explode('Sent queued message ID:',$smsglobal_response);
			$queue_id = explode(' ', $queue_id[1]);
			$queue_id = $queue_id[1];

			$responseArray['queue_id'] = $queue_id;
		} else if (strstr($smsglobal_response, 'SMSGLOBAL DELAY MSGID:')) {
			$queue_id = explode('SMSGLOBAL DELAY MSGID:',$smsglobal_response);
			$queue_id = $queue_id[1];

			$responseArray['queue_id'] = $queue_id;
			$responseArray['message_id'] = $queue_id;
		} else {
			$responseArray['queue_id'] = false;
		}

		// SMSGlobal Message ID
		if ((strstr($smsglobal_response, 'SMSGlobalMsgID:')) && (count(explode('SMSGlobalMsgID:', $smsglobal_response)) == 2)) {
			$smsglobal_message_id = explode('SMSGlobalMsgID:', $smsglobal_response);
			$smsglobal_message_id = trim($smsglobal_message_id[1]);
			$responseArray['message_id'] = (!empty($smsglobal_message_id) ? $smsglobal_message_id : false);
		} else {
			$responseArray['message_id'] = false;
		}

		// Error Code
		if (strstr($smsglobal_response, 'ERROR:')) {
			$error_code = (explode(' ', $smsglobal_response, 3));
			$error_code = $error_code[1];
			$responseArray['error'] = $this->errorLookup($error_code);
		} else {
			$responseArray['error'] = false;
		}

		$responseArray['raw_response'] = $smsglobal_response;

		$explode_response = explode('SMSGlobalMsgID:', $smsglobal_response);

		if($responseArray['queue_id'] != false) { //Message Success

			//SMSGlobal Message ID
			#echo $smsglobal_message_id;
			return $responseArray;
			//return array('Status' => true, ;
			} else { //Message Failed
			#echo 'Message Failed'.'<br />';

			//SMSGlobal Response
			#echo $smsglobal_response;
			return false;
		}
	}
	
	function errorLookup($error_code) {
		$error_lookup = array(
						1 => array(
									'id' 			=> 1,
									'short_code'	=> 'ESME_RINVMSGLEN',
									'description'	=> 'Message Length is invalid',
									'type'			=> 'Error'
						),
						2 => array(
									'id'			=> 2,
									'short_code'	=> 'ESME_RINVCMDLEN',
									'description'	=> 'Command Length is invalid',
									'type'			=> 'Error'
						),
						3 => array(
									'id'			=> 3,
									'short_code'	=> 'ESME_RINVCMDID',
									'description'	=> 'Invalid Command ID',
									'type'			=> 'Error'
						),
						4 => array(
									'id'			=> 4,
									'short_code'	=> 'ESME_RINVBNDSTS',
									'description'	=> 'Incorrect Bind',
									'type'			=> 'Error'
						),
						5 => array(
									'id'			=> 5,
									'short_code'	=> 'ESME_RALYBND',
									'description'	=> 'ESME Already in Bound State',
									'type'			=> 'Error'
						),
						10 => array(
									'id'			=> 10,
									'short_code'	=> 'ESME_RINVSRCADR',
									'description'	=> 'Invalid Source Address',
									'type'			=> 'Error'
						),
						11 => array(
									'id'			=> 11,
									'short_code'	=> 'ESME_RINVDSTADR',
									'description'	=> 'Invalid Destination Address',
									'type'			=> 'Error'
						),
						12 => array(
									'id'			=> 12,
									'short_code'	=> 'ESME_RINVMSGID',
									'description'	=> 'Message ID is invalid',
									'type'			=> 'Error'
						),
						13 => array(
									'id'			=> 13,
									'short_code'	=> 'ESME_RBINDFAIL',
									'description'	=> 'Bind Failed',
									'type'			=> 'Error'
						),
						14 => array(
									'id'			=> 14,
									'short_code'	=> 'ESME_RINVPASWD',
									'description'	=> 'Invalid Password',
									'type'			=> 'Error'
						),
						69 => array(
									'id'			=> 69,
									'short_code'	=> 'ESME_RSUBMITFAIL',
									'description'	=> 'Submit SM failed',
									'type'			=> 'Error'
						),
						88 => array(
									'id'			=> 88,
									'short_code'	=> 'ESME_RTHROTTLED',
									'description'	=> 'Exceeded allowed message limits',
									'type'			=> 'Error'
						),
						102 => array(
									'id'			=> 102,
									'short_code'	=> '',
									'description'	=> 'Destination not covered or Unknown prefix',
									'type'			=> 'Error'
						),
						400 => array(
									'id'			=> 400,
									'short_code'	=> '',
									'description'	=> 'Send message timed-out',
									'type'			=> 'Timeout'
						),
						401 => array(
									'id'			=> 401,
									'short_code'	=> '',
									'description'	=> 'System temporarily disabled',
									'type'			=> 'System Error'
						),
						402 => array(
									'id'			=> 402,
									'short_code'	=> '',
									'description'	=> 'No Response from SMSGlobal SMSC',
									'type'			=> 'No Response'
						),
				);
		if (isset($error_lookup[$error_code])) {
			return $error_lookup[$error_code];
		} else {
			return array('id' => $error_code, 'short_code' => null, 'description' => 'Unknown error', 'type' => 'Unknown');
		}
	}

}
?>