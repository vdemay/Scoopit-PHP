<?php

include("oauth/OAuth.php");
include("oauth/SessionTokenStore.php");



#################################################################################
## SCCOP.IT BACKEND : REQUESTER
#################################################################################

// Used to provide custom http code if you hate default curl code :P
interface ScoopHttpBackend{
	public function executeHttpGet($url);
	public function executeHttpPost($url,$putString);
}
// Default curl implementation this is some crap.
class ScoopCurlHttpBackend implements ScoopHttpBackend {
	// The folowing code uses curl as http backend.
	// Note that this is really crappy, pecl_http really has a better interface.
	public function executeHttpGet($url){
		//die($url);
		$curlHandler = curl_init();
		curl_setopt($curlHandler, CURLOPT_URL, $url);
		curl_setopt($curlHandler,CURLOPT_RETURNTRANSFER,1);
		try {
			$body = curl_exec($curlHandler);
			$status = curl_getinfo($curlHandler,CURLINFO_HTTP_CODE);
			if($status!=200){
				throw new ScoopHttpNot200Exception($url,$body,$status);
			}
			curl_close($curlHandler);
			return $body;
		}catch(Exception $e){
			curl_close($curlHandler);
			throw $e;
		}
	}

	public function executeHttpPost($url,$postData){
		$curlHandler = curl_init();
		curl_setopt($curlHandler, CURLOPT_URL, $url);
		curl_setopt($curlHandler,CURLOPT_RETURNTRANSFER,true);
		// THE CRAPIEST THING I'VE EVER SEEN :
		$putData = tmpfile();
		fwrite($putData, $putString);
		fseek($putData, 0);
		curl_setopt($curlHandler, CURLOPT_POSTFIELDS, $postData);
		try {
			$body = curl_exec($curlHandler);
			$status = curl_getinfo($curlHandler,CURLINFO_HTTP_CODE);
			if($status!=200){
				throw new ScoopHttpNot200Exception($url,$body,$status);
			}
			curl_close($curlHandler);
			return $body;
		}catch(Exception $e){
			curl_close($curlHandler);
			throw $e;
		}
	}
}

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

// You may want to catch this to present a decent =  error message for you're
// user ;)
// Every call to every method (including the constructor) of the Scoop object
// may throw this exception.
class ScoopAuthenticationException extends Exception {
	public function __construct($message){
		parent::__construct($message);
	}
}

class ScoopHttpNot200Exception extends Exception {
	public $body;
	public $status;
	public $url;
	public function __construct($url,$body, $status){
		$this->url=$url;
		$this->body = $body;
		$this->status = $status;
	}
	public function toString(){
		return "Url: $this->url\nStatus: $this->status\nBody: $this->body";
	}
}


#################################################################################
#################################################################################
#################################################################################

class ScoopIt {
	
	private $scitServer="http://www.scoop.it/";
	private $scitRequestTokenUrl;
	private $scitAccessTokenUrl;
	private $scitAuthorizeUrl ;
	private $signatureMethod ;
	
	private $myUrl;
	public  $tokenStore;
	private $consumer;
	
	private $executor;
	private $httpBackend;
	
	// $tokenStore : the TokenStore to be used to store authentication tokens
	// $myUrl : the url that calls this script (used to make external
	//          redirection of the OAuth Core 1.0 protocol)
	// $consumerKey the oauth consumer key (provided by Scoop)
	// $consumerSecret the oauth consumer secret (provided by Scoop)
	// $httpBackend : optional object used to do http request to scoop.com
	//                by default it will be an instance of ScoopCurlHttpBackend, and if you do
	//                not have cUrl, you can use pecl_http, just provide an instance of
	//                ScoopPeclHttpBackend...
	//
	// This method construct the Scoop object and authenticate the current user
	// This can do external redirection so, be sure to fill myUrl apprioriately
	public function __construct($tokenStore, $myUrl, $consumerKey, $consumerSecret, $httpBackend = null){
		$this->scitRequestTokenUrl=$this->scitServer."oauth/request";
		$this->scitAccessTokenUrl=$this->scitServer."oauth/access";
		$this->scitAuthorizeUrl=$this->scitServer."oauth/authorize";
		$this->signatureMethod = new OAuthSignatureMethod_HMAC_SHA1();
		$this->tokenStore = $tokenStore;
		$this->myUrl = $myUrl;
		$this->consumer=new OAuthConsumer($consumerKey,$consumerSecret,NULL);
		
		if($httpBackend == null){
			$this->httpBackend = new ScoopCurlHttpBackend();
		} else {
			$this->httpBackend = $httpBackend;
		}
		
		//anonymous
		$accessToken=$this->tokenStore->getAccessToken();
		if($accessToken==null){
			$this->executor = new ScoopExecutor($this->consumer,null,$this->httpBackend);
		} else if ($this->isLoggedIn()) {
			$secret = $this->tokenStore->getSecret();
			$token = new OauthConsumer($accessToken,$secret);
			$this->executor = new ScoopExecutor($this->consumer, $token, $this->httpBackend);
		} 
	}
	
	private function get($url){
		return $this->executor->execute($url);
	}
	 
	private function post($url,$postData){
		return $this->executor->executePost($url,$postData);
	}
	
	
	
	////////////////////// PUBLIC ////////////////////////////
	
	// Main authentication job is done here !
	public function login(){
		$accessToken = $this->tokenStore->getAccessToken();
		// Note that in this code the secret can be the one of the request token
		// or the access token
		$secret = $this->tokenStore->getSecret();
		 
		if($accessToken == null) {
			// we store the previously requested request token in the session...
			$requestToken = $this->tokenStore->getRequestToken();
			// The request token is only present in one case : the callback has been
			// called by scoop. So try to authenticate it and grab an access token !
			if($requestToken != null) {
				// try to grab the access token
				$token = new OAuthConsumer($requestToken,$secret);
				$parsed = parse_url($this->scitAccessTokenUrl);
				$params = array();
				// add verifier
				if (isset($parsed['query'])) {
					parse_str($parsed['query'], $params);
				}
				$params['oauth_verifier'] = $_GET['oauth_verifier'];
	
				$acc_req = OAuthRequest::from_consumer_and_token($this->consumer, $token, "GET", $this->scitAccessTokenUrl, $params);
				$acc_req->sign_request($this->signatureMethod, $this->consumer, $token);
				//                die($acc_req->to_url());
				// Execute the HTTP request & parse result as a tokens TODO FACTORIZE
				$resultParams = array();
				try {
					$responseBody = $this->httpBackend->executeHttpGet($acc_req->to_url());
					parse_str($responseBody, $resultParams);
				} catch(ScoopHttpNot200Exception $e) {
					// Do nothing : this will flush the request token and request a new request token
				}
	
				$accessToken=$resultParams['oauth_token'];
				$secret= $resultParams['oauth_token_secret'];
				// store token and secret in the session for future use
				$this->tokenStore->storeSecret($secret);
				$this->tokenStore->storeAccessToken($accessToken);
				$this->tokenStore->flushRequestToken();
			}
			if($accessToken==null){
				 
				// no access nor request token, requets a request token and
				// authorize this application to access to gj data.
				$parsed = parse_url($this->scitRequestTokenUrl);
				$params = array();
				if (isset($parsed['query'])) {
					parse_str($parsed['query'], $params);
				}
				 
				$acc_req = OAuthRequest::from_consumer_and_token($this->consumer, NULL, "GET", $this->scitRequestTokenUrl, $params);
				$acc_req->sign_request($this->signatureMethod, $this->consumer, NULL);
	
				// Execute the HTTP request & parse result as a tokens TODO FACTORIZE
				$resultParams = array();
				try {
					$responseBody = $this->httpBackend->executeHttpGet($acc_req->to_url());
					parse_str($responseBody, $resultParams);
				} catch(ScoopHttpNot200Exception $e) {
					throw new ScoopAuthenticationException("Unable to get a request token from Scoop, server response : ".$e->toString());
				}
				// parse response body
				$requestToken=$resultParams['oauth_token'];
				// store token and secret in the session for future use
				$this->tokenStore->storeSecret($resultParams['oauth_token_secret']);
				$this->tokenStore->storeRequestToken($requestToken);
				// redirect to the authroization url of scoop :
				// the user will then be asked to log in (in scoop) if needed
				// and it will have to accept that our application will
				// access to it's personal data.
				Header("Location: ".$this->scitAuthorizeUrl."?oauth_token=$requestToken&oauth_callback=".urlencode($this->myUrl));
				exit;
			}
		}
	
		// We are authenticated, construct the executor
		$token = new OauthConsumer($accessToken,$secret);
		$this->executor = new ScoopExecutor($this->consumer, $token, $this->httpBackend);
	}
	
	public function logout(){
		$this->tokenStore->flushRequestToken();
		$this->tokenStore->flushAccessToken();
		$this->tokenStore->flushSecret();
	}
	
	public function resolve($type, $shortName) {
		return $this->get($this->scitServer."api/1/resolver?type=".$type."&shortName=".$shortName);
	}
	
	public function test() {
		return $this->get($this->scitServer."api/1/test");
	}
	
	public function isLoggedIn() {
		$accessToken = $this->tokenStore->getAccessToken();
		return $accessToken != null;
	}
	
	public function profile($id, $getCuratedTopics = "true", $getFollowedTopics = "false", $curated = 0, $curable=0) {
		if (is_null($id) && $this->isLoggedIn()) {
			return $this->get($this->scitServer."api/1/profile?getCuratedTopics=".$getCuratedTopics."&getFollowedTopics=".$getFollowedTopics."&curable=".$curable."&curated=".$curated);
		} else if (!is_null($id)) {
			return $this->get($this->scitServer."api/1/profile?id=".$id."&getCuratedTopics=".$getCuratedTopics."&getFollowedTopics=".$getFollowedTopics."&curable=".$curable."&curated=".$curated);
		} else {
			throw new Exception("Profile without is not permitted in anonymous mode");
		}
	}
	
	public function aPost($id, $ncomments=0) {
		$thePost = $this->get($this->scitServer."api/1/post?id=".$id."&ncomments=".$ncomments);
		return $thePost;
	}
	
	public function topic($id, $curated=30, $curable=0, $page=0, $since = 0) {
		return $this->get($this->scitServer."api/1/topic?id=".$id."&curated=".$curated."&curable=".$curable."&page=".$page."&since=".$since)->topic;
	}
	
	public function compilation($sort="rss", $since=0, $count=30) {
		if($this->isLoggedIn()))
			return $this->get($this->scitServer."api/1/compilation?&sort=".$sort."&since=".$since."&count=".$count)->posts;
		else
			throw new Exception("You need to be connected to get your compilation of followed topics");
	}
	
	public function rescoop($post_id, $topic_id) {
		$postData = "action=rescoop&id=".$post_id."&destTopicId=".$topic_id;
		return $this->post($this->scitServer."api/1/post", $postData);
	}
	
	public function share($post_id, $sharer) {
		$postData = "action=share&id=".$post_id."&shareOn=[{\"sharerId\": \"".$sharer->sharerId."\", \"cnxId\": ".$sharer->cnxId."}]";
		return $this->post($this->scitServer."api/1/post", $postData);
	}
	
	public function notifications($since) {
		if($this->isLoggedIn()))
			return $this->get($this->scitServer."api/1/notifications?since=".$since)->notifications;
		else
			throw new Exception("You have to be connected to get your notifications");
	}
}

?>