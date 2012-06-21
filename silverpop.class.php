<?php
/*
Silverpop API class, updated to version 8.6
Written by Or Weinberger (or.weinberger@888holdings.com)

Last updated: June 2012
*/
class Silverpop {
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
	
	public function GetJobStatus($job_id) {
		$postdata = "<GetJobStatus><JOB_ID>" . $job_id . "</JOB_ID></GetJobStatus>";
		$podurl = $this->podurl . ";jsessionid=" . $this->sessionId;
		$curlData = $this->curlPost($podurl,$postdata,$this->proxy);
		$array = $this->convertXML($curlData);
		return $array;
	}
	
	public function AddRecipient($list_id,$created_from,$send_autoreply=false) {
		$postdata = "<AddRecipient><LIST_ID>" . $list_id . "</LIST_ID><CREATED_FROM>" . $created_from . "<SEND_AUTO_REPLY>" . $send_autoreply . "</SEND_AUTO_REPLY></CREATED_FROM></AddRecipient>";
		$podurl = $this->podurl . ";jsessionid=" . $this->sessionId;
		$curlData = $this->curlPost($podurl,$postdata,$this->proxy);
		$array = $this->convertXML($curlData);
		return $array;
	}
	
	public function RemoveRecipient($list_id,$email) {
		$postdata = "<RemoveRecipient><LIST_ID>" . $list_id . "</LIST_ID><EMAIL>" . $email . "</EMAIL></RemoveRecipient>";
		$podurl = $this->podurl . ";jsessionid=" . $this->sessionId;
		$curlData = $this->curlPost($podurl,$postdata,$this->proxy);
		$array = $this->convertXML($curlData);
		return $array;
	}
	
	public function ListDCRulesetsForMailing($template_id) {
		$postdata = "<ListDCRulesetsForMailing><MAILING_ID>" . $template_id . "</MAILING_ID></ListDCRulesetsForMailing>";
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