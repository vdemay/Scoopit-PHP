<html>
	<title>Scoop.it API Test</title>
<body>
	<?php
		// Start the session
		session_start();
		
		// Include needed files
		include_once("ScoopIt.php");
		include_once("config.php");
		
		// Construct scoop var, which handle API communication
		$scoop = new ScoopIt(new SessionTokenStore(), $localUrl, $consumerKey, $consumerSecret);
		
		//$scoop->logout();
		//print_r($scoop->profile(0));
		
		// Login
		$scoop->login();
		// Get the current user
		$currentUser = $scoop->profile(null)->user;
		// Display the current user name
		echo "<h1>Hello ".$currentUser->name."</h1>";
		

		// Get information about topic with 24001 lid
		$topic = $scoop->topic(24001);
		echo "<h2>Information about topic: <img width='32px' src='".$topic->mediumImageUrl."' /><i> ".$topic->name."</i></h2>";
		echo "<p>Here is the print_r() output for the object \$topic:</p>";

		echo "<center>";
		echo "<textarea>";
		print_r($topic);
		echo "</textarea>";
		echo "</center>";
	?>
	<div id="footer">
		<a href=" http://www.scoop.it">
			<img src="http://www.scoop.it/resources/img/api/poweredbyscoopit_25_transp.png" />
		</a>
	</div>
	<style type="text/css">
		#footer {
			text-align: center;
			margin: auto;
			margin-top: 20px;
		}
		
		a {
			text-decoration: none;
		}
		
		textarea {
			width: 700px;
			height: 500px;
			margin: auto;
		}
	</style>
</body>
</html>
