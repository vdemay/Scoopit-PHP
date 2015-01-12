<?php 
	include_once 'header.php';

   // Include needed files
   include_once("../../ScoopIt.php");
   
   
   // Construct scoop var, which handle API communication
   $scoop = new ScoopIt(new SessionTokenStore(), "", $consumerKey, $consumerSecret);
   
   //profile url and shortName
   $profileUrl = $_GET["profileUrl"];
   $shortName = str_replace("http://www.scoop.it/u/","",$_GET["profileUrl"]);
   
   //resolve profile
   $profile = $scoop->resolveUserFromItsShortName($shortName);
?>


	Your profile url is: <code><?php echo $profileUrl; ?></code>
	<br/>
	Your short name is: <code><?php echo $shortName; ?></code>
	<br/>
	<hr/>
	
	Topics you are curating are:
	<ul>
		<?php
			foreach ($profile->user->curatedTopics as $topic) {
				echo "<li><a href='topic.php?id=$topic->id'>$topic->name</a></li>";
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