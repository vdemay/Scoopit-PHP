<?php 

#################################################################################
## EXECUTOR
#################################################################################
// Execute request to Scoop. Requests are oauth authenticated
class ScoopExecutor {
	private $consumerToken;
	private $accessToken;
	private $signatureMethod;
	private $httpBackend;
	function __construct($consumerToken,$accessToken,$httpBackend){
		$this->consumerToken = $consumerToken;
		$this->accessToken = $accessToken;
		$this->signatureMethod =  new OAuthSignatureMethod_HMAC_SHA1();
		$this->httpBackend = $httpBackend;
	}

	// url must not contain any parameters
	function execute($url){
		$parsed = parse_url($url);
		$params = array();
		parse_str($parsed['query'], $params);
		$req = OAuthRequest::from_consumer_and_token($this->consumerToken, $this->accessToken, "GET", $url, $params);
		$req->sign_request($this->signatureMethod,$this->consumerToken, $this->accessToken);
		try {
			//die($req->to_url());
			$responseBody = $this->httpBackend->executeHttpGet($req->to_url());
			return json_decode($responseBody);
		} catch(ScoopHttpNot200Exception $e) {
			throw new ScoopAuthenticationException("Unable to execute opensocial query, server response : ".$e->toString());
		}
	}

	function executeDelete($url, $params=array()){
		$req = OAuthRequest::from_consumer_and_token($this->consumerToken, $this->accessToken, "DELETE", $url, $params);
		$req->sign_request($this->signatureMethod,$this->consumerToken, $this->accessToken);
		try {
			$responseBody = $this->httpBackend->executeHttpDelete($req->to_url());
			return json_decode($responseBody);
		} catch(ScoopHttpNot200Exception $e) {
			throw new ScoopAuthenticationException("Unable to execute opensocial query, server response : ".$e->toString());
		}
	}

	function executePost($url,$postData){
		if($postData==null || $postData==""){
			throw new Exception("Null data");
		}
		$req = OAuthRequest::from_consumer_and_token($this->consumerToken, $this->accessToken, "POST", $url, array());
		$req->sign_request($this->signatureMethod,$this->consumerToken, $this->accessToken);

		try {
			$responseBody = $this->httpBackend->executeHttpPost($req->to_url(), $postData);
			return json_decode($responseBody);
		} catch(ScoopHttpNot200Exception $e) {
			throw new ScoopAuthenticationException("Unable to execute opensocial query, server response : ".$e->toString());
		}
	}

	function executePut($url,$putData){
		if($putData==null || $putData==""){
			throw new Exception("Null data");
		}
		$req = OAuthRequest::from_consumer_and_token($this->consumerToken, $this->accessToken, "PUT", $url, array());
		$req->sign_request($this->signatureMethod,$this->consumerToken, $this->accessToken);

		try {
			$responseBody = $this->httpBackend->executeHttpPut($req->to_url(), $putData);
			return json_decode($responseBody);
		} catch(ScoopHttpNot200Exception $e) {
			throw new ScoopAuthenticationException("Unable to execute opensocial query, server response : ".$e->toString());
		}
	}
}
#################################################################################

?>