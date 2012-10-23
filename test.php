<?php
   // Start the session
   session_start();
   
   // Include needed files
   include_once("ScoopIt.php");
   include_once("config.php");
   
   // Construct scoop var, which handle API communication
   $scoop = new ScoopIt(new SessionTokenStore(), $localUrl, $consumerKey, $consumerSecret);
   
   // Login in, if not previously logged in, it will issue a redirection
   // to scoop.it servers to log the user in. 
   // You can omit the call below if you want to use the api in "anonymous"
   // mode.
   $scoop->login();
?>
<html>
	<title>Scoop.it API Test</title>
<body>
	<?php
		// Get the current user
		$currentUser = $scoop->profile(null)->user;
		// Display the current user name
		echo "<h1>Hello ".$currentUser->name."</h1>";
		

		// Get information about topic with 24001 lid (this can be also
                // called in anonymous mode).
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
