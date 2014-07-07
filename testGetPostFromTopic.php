<?php
   // Start the session
   session_start();
   
   // Include needed files
   include_once("ScoopIt.php");
   
   //TODO -------------------------------------------------------------------
   //TODO -------------------------------------------------------------------
   //TODO -------------------------------------------------------------------
   //            generate tokens here https://www.scoop.it/dev/apps
   //TODO -------------------------------------------------------------------
   //TODO -------------------------------------------------------------------
   //TODO -------------------------------------------------------------------
   $consumerKey = "homautomat|Ygj1wV16HP-8zvYfAyNOuNwcinW-4KZTHeGS7hTo_7M";
   $consumerSecret = "q4gEbcFvEADmyRo6P4HcguWwypTAL142hlFeIZlBiZYBQUUhyh";
   
   // Construct scoop var, which handle API communication
   $scoop = new ScoopIt(new SessionTokenStore(), "", $consumerKey, $consumerSecret);

?>
<html>
	<title>Scoop.it API Test</title>
<body>
	<h1>Request output</h1>
	<?php
		// Get information about topic http://www.scoop.it/t/iphone-and-ipad-development
		// shortName is the last part of the Url of a topic
		$topic = $scoop->resolveTopicFromItsShortName("iphone-and-ipad-development");
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
	
	<h1>List of posts title</h1>
	<ul>
	<?php
		$posts = $topic->curatedPosts;
		foreach ($posts as $post) {
			echo "<li>".$post->title."</li>";
		}
	?>
	</ul>
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
