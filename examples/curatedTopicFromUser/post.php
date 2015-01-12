<?php 
	include_once 'header.php';
?>

url of the original article: <?php echo $_GET["url"];?>
<hr/>
Original Article:
<iframe src="<?php echo $_GET["url"];?>" style="width: 100%; height: 600px"></iframe>


<?php 
	include_once 'footer.php';
?>