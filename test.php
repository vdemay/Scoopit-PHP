<html>
<body>
	<pre>

		<?php
	
		session_start();
		
		include_once("ScoopIt.php");
		include_once("config.php");
		
		$scoop = new ScoopIt(new SessionTokenStore(), $localUrl, $consumerKey, $consumerSecret);
		
		//$scoop->logout();
		//print_r($scoop->profile(0));
		
		$scoop->login();
		//print_r($scoop->profile());
		
		print_r($scoop->topic(24001));
	
		?>

	</pre>

</body>
</html>
