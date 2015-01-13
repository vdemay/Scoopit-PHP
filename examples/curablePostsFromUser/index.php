<?php
   include_once 'header_inc.php';
   
   // Include needed files
   include_once("../../ScoopIt.php");
   
   // Construct scoop var, which handle API communication
   $localUrl = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
   $scoop = new ScoopIt(new SessionTokenStore(), $localUrl, $consumerKey, $consumerSecret);
   
   // Login in, if not previously logged in, it will issue a redirection
   // to scoop.it servers to log the user in. 
   // You can omit the call below if you want to use the api in "anonymous"
   // mode.
   $scoop->login();
   

   include_once 'header_html.php';
?>
<?php
	// Get the current user
	$currentUser = $scoop->profile(null)->user;
	// Display the current user name
	echo "<h1>Hello ".$currentUser->name."</h1>";

	echo "<hr/>";
	
	echo "<a href='profile.php'>View Topics</a>";

?>
