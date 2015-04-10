<?php
// Parameters
// ----------
// $post : a post to print
// $isPinnedPost : true to display the pinned post
// $inPopup : true to display the post in a popup
// $whiteLabel : true to work as white label
//
// FIXME  : check unicity with Scoop.it web site

if (!isset($isPinnedPost)) {
	$isPinnedPost = false;
}
if (!isset($inPopup)) {
	$inPopup = false;
}

if (!isset($whiteLabel)) {
	$whiteLabel = false;
}

$url = null;
$shareUrl = null;
if ($whiteLabel) {
	$url = $post->url;
	$shareUrl = $localUrl.'&post='.$post->id;
} else {
	$url = $post->scoopUrl;
	$shareUrl = $post->scoopUrl;
}

?>

<div class="onePost <?php if($isPinnedPost && !$inPopup) echo "sticky" ?>">
	 <div class="clear"></div>
	 <div id="post<?php if($inPopup) echo "InPopup" ?>_<?php echo $post->id ?>">
	 	<div class="postView post <?php if($isPinnedPost && !$inPopup) echo "favorite" ?> <?php if(isset($post->imageSize)) echo $post->imageSize; ?> <?php if(isset($post->imagePosition)) echo $post->imagePosition; ?>">
	 		
	 		<!-- POST META -->
	 		<div class="metas">
						  <div class="sourceicon">
						    <img width="16px" src="<?php echo $post->source->iconUrl;?>">
						  </div>
						  <?php if(isset($post->twitterAuthor)) { ?>
                <span><a href="<?php echo "http://www.twitter.com/".$post->twitterAuthor ?>"><?php echo "@".$post->twitterAuthor ?> </a></span> -
						  <?php } else { ?>
						    <span><a href="<?php if($post->source->url == 'http://www.scoop.it/u/itpartners') echo 'http://www.itpartners.fr/?IdNode=4674'; else echo $post->source->url; ?>"><?php echo $post->source->name ?> </a></span> -
              <?php } ?>
   						<span><?php echo date("M d Y H:i:s", $post->curationDate / 1000 ) ?></span>
            </div>
            
            
            <!-- POST TITLE -->
            <div class="title">
              	<h2 class="postTitleView">
                	<a href="<?php echo $url ?>" target="_blank" id="post_title"><?php echo $post->title ?></a>
              	</h2>
            </div>
            
            
            <!-- HTML FRAGMENT OR IMAGE URL -->
            <?php
              if(property_exists($post, "htmlFragment")) { ?>
                <div class="htmlFragment"><?php echo $post->htmlFragment ?></div><?php
              } else if(property_exists($post, "imageUrl")) { ?>
                <div class="image">
                  	<a href="<?php echo $url ?>" target="_blank" id="post_title">
                    	<img src="<?php echo $post->imageUrl ?>" onload="adjustImage(this, 'false')"/>
					</a>
                </div> <?php
              }
            ?>
            
            <div class="description">
						  <div id="post_description">
                <?php echo isset($post->htmlContent) ? $post->htmlContent : $post->content ?>
						  </div>
						  <div class="clear"></div>
            </div>
            
            <div style="clear:both"></div>
        
        </div>
            
            <!-- SHARE AREA -->
            <table cellspacing="0" cellpadding="0" class="actionsBar">
            <tr>
              <td>
              	<div class="clear">
              		<!-- SOURCE -->
		            <div class="postSource">
		  			  <?php if(isset($post->url)) { ?>
		                Source : 
		                <a target="_blank" href="<?php echo $post->url ?>">
		                	<?php echo getDomain($post->url); ?>
		            	</a>
		              <?php } ?>
		            </div>
              	</div>
              </td>
              <td align="right" class="rightSide">
                <div class="rightItem" id="sharers_<?php echo $post->id ?>" style="*z-index:201">
                  <img onclick="showLittleBox(jQuery('#sharers_<?php echo $post->id ?>_box'))" src="resources/img/post/icon_toolbar_post_share1.png" alt="Share" title="Share" class="shareAction"/>
            			<div id="sharers_<?php echo $post->id ?>_box" class="littleBoxContainer" style="top: 24px">
                    <div class="littleBoxContent">
                      <div>
                        <a style="display:block; width:90px" target="_blank" href="http://www.facebook.com/sharer.php?u=<?php echo urlencode($shareUrl) ?>">
                          <img title="Share on Facebook" alt="Share on Facebook" src="resources/img/socialnetwork/facebook.png">
                          Facebook
                        </a>
                      </div>
                      <div>
                        <a style="display:block; width:90px" target="_blank" href="http://twitter.com/share?url=<?php echo urlencode($shareUrl) ?>&text=<?php
                          echo urlencode($post->title." | @scoopit");
                          if(isset($post->twitterAuthor)) {
                            echo urlencode(" via @".$post->twitterAuthor);
                          }
                        ?>">
                          <img title="Share on Twitter" alt="Share on Twitter" src="resources/img/socialnetwork/twitter.png">
                          Twitter
                        </a>
                      </div>
                      <div>
                        <a style="display:block; width:90px" target="_blank" href="https://www.linkedin.com/cws/share?url=<?php echo urlencode($shareUrl) ?>">
                          <img title="Share on Linked In" alt="Share on Linked In" src="resources/img/socialnetwork/linkedin.png">
                          LinkedIn
                        </a>
                      </div>
                  		<div id="<?php echo $post->id ?>_copylink_container" class="copyLinkWrapper">
                  			<div id="<?php echo $post->id ?>_copylink" style="width:90px">
                  				<img src="resources/img/post/copylink.png" alt="C"/>
                  				<span class="copylink">Copy link</span>
                  				<span class="linkcopied">Link copied!</span>
                  			</div>
                  		</div>
                    </div>
                    <script type="text/javascript">
                  		jQuery(document).ready(function() {
                  			ZeroClipboard.setMoviePath('resources/swf/ZeroClipboard10.swf');
                  			clip = new ZeroClipboard.Client();
                  			clip.setCSSEffects(false);
                  			clip.glue('<?php echo $post->id ?>_copylink', '<?php echo $post->id ?>_copylink_container');
                  			clip.setText('<?php echo $localUrl.'&post='.$post->id ?>');
                  			clip.addEventListener('complete', function (client, text) {
                  				jQuery("#<?php echo $post->id ?>_copylink .copylink").hide();
                  				jQuery("#<?php echo $post->id ?>_copylink .linkcopied").fadeIn();
                  				setTimeout(function() {
                  					jQuery("#<?php echo $post->id ?>_copylink .linkcopied").hide();
                  					jQuery("#<?php echo $post->id ?>_copylink .copylink").fadeIn();
                  				}, 2000);
                  			});
                  		});
                    </script>
                  </div>
                  <div class="clear"></div>
                </div>
                <div class="rightItem">
                	<g:plusone size="big" annotation="none" href="<?php echo $shareUrl ?>"></g:plusone>
                </div>
              </td>
            </tr>
          </table>
	 </div>
</div>

