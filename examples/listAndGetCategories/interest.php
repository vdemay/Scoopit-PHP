<?php 
	include_once 'header.php';

   // Include needed files
   include_once("../../ScoopIt.php");
   
   
   // Construct scoop var, which handle API communication
   $scoop = new ScoopIt(new SessionTokenStore(), "", $consumerKey, $consumerSecret);
   
   //profile url and shortName
   $interestId = $_GET["id"];
   $interestShortName = $_GET["shortName"];
   
   //Pagination
   $postPerPage = 30;
   $page = 0;
   if (isset($_GET["page"])) {
   	$page = intval($_GET["page"]);
   }
   
   $request = "interest";
   if (isset($interestId)) {
   		$request = $request."?id=".$interestId;
   } else if (isset($interestShortName)) {
   		$request = $request."?shortName=".$interestShortName;
   }
   $request = $request."&getPosts=true&page=".$page."&count=". $postPerPage;
   
   $interest = $scoop->getCustomRequest($request)->interest;
   
   //next and previous
   $paginationUrl = "";
   if (isset($interestId)) {
   	 $paginationUrl = $paginationUrl."?id=".$interestId;
   } else if (isset($interestShortName)) {
   	 $paginationUrl = $paginationUrl."?shortName=".$interestShortName;
   }
   if ($page > 0) {
   	$prev =  $paginationUrl."&page=".($page-1);
   }
   if ($topic->curatedPostCount / $postPerPage > $page + 1) {
   	$next =  $paginationUrl."&page=".($page+1);
   }
?>

	Interest id is: <code><?php echo $interest->id; ?></code>
	<br/>
	Interest name is: <code><?php echo $interest->name; ?></code>
	<br/>
	Interest shortName is: <code><?php echo $interest->shortName; ?></code>
	<br/>
	<?php 
	if (isset($interestId)) {
   		echo "Interest has been retreive by id";
   	} else if (isset($interestShortName)) {
   		echo "Interest has been retreive by shortName";
   	}
    ?>
    <br/>
	<hr/>
	
	Last posts in this interest:<br/>
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
		foreach ($interest->posts as $post) {
	?>
		<div style="text-align:center; width: 600px; border-bottom:1px solid #acacac; margin: auto; margin-bottom: 20px; ">
			<a href="post?url=<?php echo $post->url; ?>" style="font-size:20px"><?php echo $post->title; ?></a>
			<br/><img src="<?php echo $post->imageUrl; ?>"/>
			<p><?php echo $post->content?></p>
			<p><?php echo $post->insight?></p>
		</div>
	<?php 
		}
	?>
	
	
	<hr/>
	<p>
		Raw Content of interest Query (<?php echo $request; ?>):<br/>
		<textarea style="width:600px; height: 200px">
		<?php print_r($interest);?>
		</textarea>
	</p>
	
<?php ?>
   