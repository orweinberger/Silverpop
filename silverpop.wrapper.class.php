<?php
/*
Silverpop API Wrapper class, updated to version 8.6
Written by Or Weinberger (or.weinberger@888holdings.com)

You can either use the simple class, or the wrapper.
The wrapper is used to channel any API call and is used as a way to wrap your requests for the Silverpop API
The simple class can be used for simpler requests is is more 'user-friendly' but is lacking some API calls and some of the optional vars/flags in some of the API calls.

Usage notes:

$call_name - Is the __exact__ call name (case sensitive), for example: GetJobStatus

$var_key_val - Needs to be an array containing all mandatory (and possibly optional) fields in a key->val format. For example:
[LIST_ID] -> 1234567
[EMAIL] -> or.weinberger@888holdings.com

$flags - Possibly optional on some calls. Array with any flags needed for the request. So if you want to include this flag <MOVE_TO_FTP/>, the array should contain 'MOVE_TO_FTP' (or move_to_ftp, I will strtoupper).

Last updated: June 2012
*/
class SilverpopWrapper {
	private $sessionId;
	private $proxy;
	private $podurl;
	function __construct($username,$password,$podurl,$proxy=false) {
		$postdata = "<Login><USERNAME>" . $username . "</USERNAME><PASSWORD>" . $password . "</PASSWORD></Login>";
		$curlData = $this->curlPost($podurl,$postdata,$proxy);
		$array = $this->convertXML($curlData);
		$this->proxy = $proxy;
		$this->podurl = $podurl;
		$this->sessionId = $array['Body']['RESULT']['SESSIONID'];
	}
	
	public function APICall($call_name,$var_key_val,$flags=false) {
		$postdata = "<" . $call_name . ">";
		foreach ($var_key_val as $key=>$val) {
			$postdata .= "<" . strtoupper($key) . ">" . $val . "</" . strtoupper($key) . ">";	
		}
		if ($flags != false) {
			foreach ($flags as $flag) {
				$postdata .= "<" . strtoupper($flag) . "/>";	
			}
		}
		$postdata .= "</" . $call_name . ">";
		$podurl = $this->podurl . ";jsessionid=" . $this->sessionId;
		$curlData = $this->curlPost($podurl,$postdata,$this->proxy);
		$array = $this->convertXML($curlData);
		return $array;
	}
	
	private function curlPost($url,$postdata,$proxy) {
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_POST, 1);
		if ($proxy != false) {
			curl_setopt($curl, CURLOPT_PROXY, $proxy);
		}
		curl_setopt($curl, CURLOPT_POSTFIELDS, "xml=<?xml version=\"1.0\"?><Envelope><Body>" . $postdata . "</Body></Envelope>");
		curl_setopt($curl, CURLOPT_ENCODING, "");
		$curlData = curl_exec($curl);
		curl_close($curl);
		return $curlData;
	}
	
	private function convertXML($spxml) {
		$xml = simplexml_load_string($spxml);
		$json = json_encode($xml);
		$array = json_decode($json,TRUE);
		return $array;
	}
}

?>