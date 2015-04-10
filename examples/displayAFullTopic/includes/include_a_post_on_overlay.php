<?php 
// Display a post over the page
//
// Parameters
// ----------
// $post : the post to display
//
?>

<div id='topicBannerOverlay'>&nbsp;</div>
<script type="text/javascript">
	jQuery(document).ready(function(){
		jQuery("#topicBanner").click(function(){
			closePostAndViewTopic(<?php echo $_REQUEST["post"] ?>);
		});
		jQuery("#viewboxOverlay").click(function(){
			closePostAndViewTopic(<?php echo $_REQUEST["post"] ?>);
		});
	    jQuery("#thePostToPrint .htmlfragment").show();
	});
</script>

<div id="thePostToPrint">
	<?php
		include 'include_a_post.php';
	?>
	<div id="closeThePostToPrint" onclick="closePostAndViewTopic(<?php echo $post->id ?>)">
		<img src="resources/img/postinpopup/close.png" />
	</div>
</div>
