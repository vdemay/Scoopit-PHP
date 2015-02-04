<?php 
include_once 'header.php';

// Include needed files
include_once("../../ScoopIt.php");
 
 
// Construct scoop var, which handle API communication
$scoop = new ScoopIt(new SessionTokenStore(), "", $consumerKey, $consumerSecret);


$interests = $scoop->getCustomRequest("interest-list")

?>

	Interrests available
	<ul>
		<?php
			foreach ($interests->interests as $interest) {
				echo "<li>$interest->name (id:<a href='interest.php?id=$interest->id'>$interest->id</a> shorname:<a href='interest.php?shortName=$interest->shortName'>$interest->shortName)</a></li>";
			}
		?>
	</ul>
	<hr/>
	<p>
		Raw Content of profile Query:<br/>
		<textarea style="width:600px; height: 200px">
		<?php print_r($interests);?>
		</textarea>
	</p>

<?php include_once 'footer.php';?>
