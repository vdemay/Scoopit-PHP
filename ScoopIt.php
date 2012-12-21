<?php

include("oauth/OAuth.php");
include("oauth/tokenStore/SessionTokenStore.php");
include("oauth/backend/ScoopCurlHttpBackend.php");
include("oauth/executor/ScoopExecutor.php");

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
	// $scoopitServerUrl : the scoopit server url. By default use the field $scitServer of this class
	// This method construct the Scoop object and authenticate the current user
	// This can do external redirection so, be sure to fill myUrl apprioriately
	public function __construct($tokenStore, $myUrl, $consumerKey, $consumerSecret, $httpBackend = null, $scoopitServerUrl = null) {
		if ($scoopitServerUrl != null) {
			$this->scitServer = $scoopitServerUrl;
		}
		
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
		
		$this->executor = new ScoopExecutor($this->consumer, $this->tokenStore, $this->httpBackend);
	}
	
	public function get($url){
		return $this->executor->execute($url);
	}
	 
	public function post($url,$postData){
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
	}
	
	public function logout(){
		$this->tokenStore->flushRequestToken();
		$this->tokenStore->flushAccessToken();
		$this->tokenStore->flushSecret();
	}
	
	public function resolve($type, $shortName) {
		return $this->get($this->scitServer."api/1/resolver?type=".$type."&shortName=".$shortName);
	}
	
	public function resolveTopicFromItsShortName($short_name) {
		$response = $this->resolve("Topic", $short_name);
		if ($response->id != null) {
			return $this->topic($response->id);
		}
		// Could not find any topic with this short_name
		return null;
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
	
	public function topic($id, $curated=30, $curable=0, $page=0, $since = -1) {
		return $this->get($this->scitServer."api/1/topic?id=".$id."&curated=".$curated."&curable=".$curable."&page=".$page."&since=".$since)->topic;
	}
	
	public function compilation($sort="rss", $since=0, $count=30, $ncomments=0, $page=0) {
		if($this->isLoggedIn()) {
			return $this->get($this->scitServer."api/1/compilation?&sort=".$sort."&since=".$since."&count=".$count."&ncomments".$ncomments."&page=".$page)->posts;
		} else {
			throw new Exception("You need to be connected to get your compilation of followed topics");
		}
	}
	
	public function rescoop($post_id, $topic_id) {
		$postData = "action=rescoop&id=".$post_id."&destTopicId=".$topic_id;
		return $this->post($this->scitServer."api/1/post", $postData);
	}
	
	public function share($post_id, $sharer, $text) {
		if (is_empty($text)) {
			$postData = "action=share&id=".$post_id."&shareOn=[{\"sharerId\": \"".$sharer->sharerId."\", \"cnxId\": ".$sharer->cnxId."}]";	
			return $this->post($this->scitServer."api/1/post", $postData);
		} else {
			$postData = "action=share&id=".$post_id."&shareOn=[{\"sharerId\": \"".$sharer->sharerId."\", \"cnxId\": ".$sharer->cnxId.", \"text\": ".$text."}]";	
			return $this->post($this->scitServer."api/1/post", $postData);
		}
	}
	
	public function notifications($since) {
		if($this->isLoggedIn()) {
			return $this->get($this->scitServer."api/1/notifications?since=".$since)->notifications;
		} else {
			throw new Exception("You have to be connected to get your notifications");
		}
	}
	
	public function createAPost($title, $url, $content, $imageUrl, $topicId) {
		$data = "action=create&title=".urlencode($title)."&url=".urlencode($url)."&content=".urlencode($content)."&imageUrl=".urlencode($imageUrl)."&topicId=".$topicId;
		if($this->isLoggedIn()) {
			return $this->post($this->scitServer."api/1/post", $data);
		} else {
			throw new Exception("You have to be connected to create a post");
		}
	}
	
	public function thankAPost($postId) {
		$data = "action=thank&id=".urlencode($postId);
		if($this->isLoggedIn()) {
			return $this->post($this->scitServer."api/1/post", $data);
		} else {
			throw new Exception("You have to be connected to thank a post");
		}
	}

	public function commentAPost($postId, $commentText) {
	  $data = "action=comment&id=".urlencode($postId)."&commentText=".urlencode($commentText);
	  if($this->isLoggedIn()) {
	    return $this->post($this->scitServer."api/1/post", $data);
	  } else {
	    throw new Exception("You have to be connected to thank a post");
	  }
	}
	
	public function search($query, $type="post", $count=20, $page=0, $lang="en", $topicId=null) {
		$data = "query=".urlencode($query)."&type=".urlencode($type)."&count=".urlencode($count)."&page=".urlencode($page)."&lang=".urlencode($lang)."&topicId=".urlencode($topicId);
		if($this->isLoggedIn()) {
			return $this->get($this->scitServer."api/1/search?".$data);
		} else {
			throw new Exception("You have to be connected to perform a search.");
		}
	}
	
	/**
	 * Send a request builded by the user
	 * @param String $url
	 */
	public function getCustomRequest($url) {
		return $this->get($this->scitServer."api/1/".$url);
	}
}

?>
