<?php 

	/* *********************************** OAUTH CONFIGURATION ********************************* */
	// go to http://www.scoop.it/dev > Application Management > to generate your tokens
	$consumerKey = "Key";
	$consumerSecret = "Secret";
	
	/* ************************ URL WHERE THIS PAGE IS DISPLAYED ******************************* */
	$localUrl = "http://localhost/";
	
	
	/* *********************************** TYPE CONFIGURATION ********************************** */
	
	// Topic(s) to render : 
	// a comma separated list to get a compilation of all topics -> include include_aggregation
	// a single id to display one topic -> include include_topic
	// ask scoop.it to get you topic id
	//
	// Id for topic http://www.scoop.it/t/clients-affluents
	$topicId="3004";
	
	// Does post will be linked to original or not
	// if unset it wil lead to scoop.it
	$whiteLabel=false;
	
	
	
	/* ********************************** DISPLAY CONFIGURATION ******************************** */
	
	// nb post to display per page
	$nbPostsPerPage = 25;

	
	
	/* *********************************** CACHE CONFIGURATION ********************************* */
	//location on the server for cache : do not set it for no cache
	$cache_folder = "/tmp/cache";
	//cache expiration time in minutes
	$cache_time = 1;
	
	

?>