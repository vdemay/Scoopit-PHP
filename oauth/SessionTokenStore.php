<?php

include("TokenStore.php");

// Store authentication tokens in the session
class SessionTokenStore implements TokenStore{
	public function __construct(){
		session_start();
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
		return $_SESSION['scoop.requestToken'];
	}
	public function getAccessToken(){
		return $_SESSION['scoop.accessToken'];
	}
	public function getVerifier(){
		return $_SESSION['scoop.verifier'];
	}
	public function getSecret(){
		return $_SESSION['scoop.secret'];
	}
	
	// flush
	public function flushRequestToken(){
		session_unregister('scoop.requestToken');
	}
	public function flushAccessToken(){
		session_unregister('scoop.accessToken');
	}
	public function flushVerifier(){
		session_unregister('scoop.verifier');
	}
	public function flushSecret(){
		session_unregister('scoop.secret');
	}
}

?>