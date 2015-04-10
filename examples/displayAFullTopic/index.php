<?php 
include_once 'header.php';

// Include needed files
include_once("../../ScoopIt.php");
 
 
// Construct scoop var, which handle API communication
$scoop = new ScoopIt(new SessionTokenStore(), "", $consumerKey, $consumerSecret);

?>
<?php include_once './includes/include_topic.php';?>

<?php include_once 'footer.php';?>