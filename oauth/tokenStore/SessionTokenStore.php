<?php

include("TokenStore.php");

// Store authentication tokens in the session
class SessionTokenStore implements TokenStore{
	public function __construct() {
		if (session_id() == "") {
			session_start();
		}
	}
	
	// store
	public function storeRequestToken($value){
		$_SESSION['scoop.requestToken']=$value;
	}
	public function storeAccessToken($value){
		$_SESSION['scoop.accessToken']=$value;
	}
	public function storeVerifier($value){
		$_SESSION['scoop.verifier']=$value;
	}
	public function storeSecret($value){
		$_SESSION['scoop.secret']=$value;
	}
	
	// get
	public function getRequestToken(){
		return isset($_SESSION['scoop.requestToken']) ? $_SESSION['scoop.requestToken'] : null;
	}
	public function getAccessToken(){
		return isset($_SESSION['scoop.accessToken']) ? $_SESSION['scoop.accessToken'] : null;
	}
	public function getVerifier(){
		return isset($_SESSION['scoop.verifier']) ? $_SESSION['scoop.verifier'] : null;
	}
	public function getSecret(){
		return isset($_SESSION['scoop.secret']) ? $_SESSION['scoop.secret'] : null;
	}
	
	// flush
	public function flushRequestToken(){
		unset($_SESSION['scoop.requestToken']);
	}
	public function flushAccessToken(){
		unset($_SESSION['scoop.accessToken']);
	}
	public function flushVerifier(){
		unset($_SESSION['scoop.verifier']);
	}
	public function flushSecret(){
		unset($_SESSION['scoop.secret']);
	}
}

?>