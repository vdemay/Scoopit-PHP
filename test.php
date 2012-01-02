<html>
<body>
	<pre>

		<?php
	
		session_start();
		
		include("ScoopIt.php");
	
		$localUrl = "http://localhost/dev/goojet/dev/scoop-client/php/test.php";
		$consumerKey = "YOUR_KEY_HERE";
		$consumerSecret = "YOUR_SECRET_HERE";
	
		$scoop = new ScoopIt(new SessionTokenStore(), $localUrl, $consumerKey, $consumerSecret);
		
		$scoop->logout();
		print_r($scoop->profile(0));
		
		$scoop->login();
		print_r($scoop->profile());
	
		?>

	</pre>

</body>

</html>
