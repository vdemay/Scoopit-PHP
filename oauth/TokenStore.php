<?php 
// TokenStore store authentication tokens for the current user
// you can implements your own TokenStore or
// use the provided SessionTokenStore if you are not afraid of using sessions
interface TokenStore {
	// Store methods
	public function storeRequestToken($value);
	public function storeAccessToken($value);
	public function storeSecret($value);
	public function storeVerifier($value);
	// get methods
	public function getRequestToken();
	public function getAccessToken();
	public function getSecret();
	public function getVerifier();
	// flush methods.
	public function flushRequestToken();
	public function flushAccessToken();
	public function flushSecret();
	public function flushVerifier();
}

?>