<?php 
	include_once 'header_inc.php';

   // Include needed files
   include_once("../../ScoopIt.php");
   
   
   // Construct scoop var, which handle API communication
   $scoop = new ScoopIt(new SessionTokenStore(), "", $consumerKey, $consumerSecret);
   
   //resolve profile
   $profile = $scoop->profile(null);
   
   include_once 'header_html.php';
    
?>

	Your short name is: <code><?php echo $profile->user->shortName; ?></code>
	<br/>
	<hr/>
	
	Topics you are curating are:
	<ul>
		<?php
			foreach ($profile->user->curatedTopics as $topic) {
				echo "<li>$topic->name (<a href='topicPostsToCurate.php?id=$topic->id'>posts to curate</a>)</li>";
			}
		?>
	</ul>
	
	<hr/>
	<p>
		Raw Content of profile Query:<br/>
		<textarea style="width:600px; height: 200px">
		<?php print_r($profile->user);?>
		</textarea>
	</p>
	
	
	
<?php include_once 'footer.php';?>