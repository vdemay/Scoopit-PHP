<?php 

// Used to provide custom http code if you hate default curl code :P
interface ScoopHttpBackend {
	public function executeHttpGet($url);
	public function executeHttpPost($url,$putString);
}

?>