<?php 
   include_once 'header_inc.php';

   // Include needed files
   include_once("../../ScoopIt.php");
   
   
   // Construct scoop var, which handle API communication
   $scoop = new ScoopIt(new SessionTokenStore(), "", $consumerKey, $consumerSecret);
   
   //profile url and shortName
   $topicId = $_GET["id"];
    
   //Pagination
   $postPerPage = 30;
   $page = 0;
   if (isset($_GET["page"])) {
   	$page = intval($_GET["page"]);
   }
    
   $topic = $scoop->topic($topicId, 0, ($page+1)*$postPerPage, $page);
    
   //next and previous
   if ($page > 0) {
   	$prev = "?id=".$topicId."&page=".($page-1);
   }
   if ($topic->curablePostCount / $postPerPage > $page + 1) {
   	$next = "?id=".$topicId."&page=".($page+1);
   }
   
   include_once 'header_html.php';
?>

Your topic id is: <code><?php echo $topicId; ?></code>
	<br/>
	Your topic name is: <code><?php echo $topic->name; ?></code>
	<br/>
	<hr/>
	
	Last suggested content:<br/>
	<div style="text-align:center; width: 600px; border-bottom:1px solid #acacac; margin: auto; margin-bottom: 20px;">
		PAGINATION:<br/>
		<?php 
			if (isset($prev)) {
				echo "<a href='$prev'>Prev. Page</a>";
			}
			echo "&nbsp;&nbsp;&nbsp;-&nbsp;&nbsp;&nbsp;";
			if (isset($next)) {
				echo "<a href='$next'>Next. Page</a>";
			}
		?>
	</div>
	<?php
		foreach ($topic->curablePosts as $key=>$post) {
			if ($key > ($page)*$postPerPage) {
	?>
		<div style="text-align:center; width: 600px; border-bottom:1px solid #acacac; margin: auto; margin-bottom: 20px; ">
			<a href="<?php echo $post->url; ?>" style="font-size:20px"><?php echo $post->title; ?></a>
			<br/><img width="600px" src="<?php echo $post->imageUrls[0]; ?>"/>
			<p><?php echo $post->content?></p>
			<p><?php echo $post->insight?></p>
		</div>
	<?php 
			}
		}
	?>
	
	
	<hr/>
	<p>
		Raw Content of topic Query:<br/>
		<textarea style="width:600px; height: 200px">
		<?php print_r($topic);?>
		</textarea>
	</p>

<?php 
	include_once 'footer.php';
?>

