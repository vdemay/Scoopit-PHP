<?php 

include("ScoopHttpBackend.php");

// Default curl implementation this is some crap.
class ScoopCurlHttpBackend implements ScoopHttpBackend {
	// The folowing code uses curl as http backend.
	// Note that this is really crappy, pecl_http really has a better interface.
	public function executeHttpGet($url){
		//die($url);
		$curlHandler = curl_init();
		curl_setopt($curlHandler, CURLOPT_URL, $url);
		curl_setopt($curlHandler, CURLOPT_RETURNTRANSFER,1);
		curl_setopt($curlHandler, CURLOPT_ENCODING , "gzip");
		curl_setopt($curlHandler, CURLOPT_CAINFO , realpath(dirname(__FILE__))."/startssl.pem");

		try {
			$body = curl_exec($curlHandler);
			if($body==false){
			  throw new Exception("Unable to curl ".$url." reason: ".curl_error($curlHandler));
                        }
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
		curl_setopt($curlHandler, CURLOPT_ENCODING , "gzip");
		curl_setopt($curlHandler, CURLOPT_URL, $url);
		curl_setopt($curlHandler, CURLOPT_RETURNTRANSFER,true);
		curl_setopt($curlHandler, CURLOPT_CAINFO , realpath(dirname(__FILE__))."/startssl.pem");
		// THE CRAPIEST THING I'VE EVER SEEN :
		$putData = tmpfile();
		fwrite($putData, $putString);
		fseek($putData, 0);
		curl_setopt($curlHandler, CURLOPT_POSTFIELDS, $postData);
		try {
			$body = curl_exec($curlHandler);
			if($body==false){
			  throw new Exception("Unable to curl ".$url." reason: ".curl_error($curlHandler));
                        }
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
?>