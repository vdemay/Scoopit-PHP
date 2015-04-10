function adjustImage(pic){
	var w = pic.offsetWidth;
	if (w > 319) {
		//force to big
		jQuery(pic).parents(".post").addClass("center").addClass("big");
	}
}

function showLittleBox(jQueryElementToShow) {
	if (jQueryElementToShow.is(":visible")) {
		hideLittleBox();
	} else {
		hideLittleBox();
		var parent = jQueryElementToShow.parent();
		parent.css("position", "relative");
		jQueryElementToShow.css("top", parent.outerHeight());
		jQueryElementToShow.toggle();
		jQuery(document).keyup(function(e) {
			  if (e.keyCode == 27) {
				  hideLittleBox();
			  }
		});
		if (jQueryElementToShow.is(":visible")) {
			window.setTimeout(function() {
				jQueryElementToShow.click(function(e) {
					e.stopPropagation();
				});
				jQuery('body').click(hideLittleBox);
			}, 200);
		}
	}
}

function hideLittleBox(e) {
	jQuery(".littleBoxContainer").hide();
	jQuery(document).unbind("keyup");
	jQuery('body').unbind('click', hideLittleBox);
}

function isiOS(){
    return (
        (navigator.platform.indexOf("iPhone") != -1) ||
        (navigator.platform.indexOf("iPod") != -1) ||
        (navigator.platform.indexOf("iPad") != -1)
    );
}

function closePostAndViewTopic(postLid) {
	jQuery("#closeThePostToPrint").animate({opacity: 0}, function(){$(this).remove();});
	jQuery("#topicBannerOverlay").remove();
	jQuery("#viewboxOverlay").animate({opacity: 0}, function(){$(this).remove();});
	jQuery("#showOriginalLink").animate({opacity: 0}, function(){$(this).remove();});

	jQuery("#showOriginalLink").hide();

	var thePost = jQuery(".viewbox table #post_" + postLid);
	// Add a random id into the parent div to make a correct replace.
	var idToReplace = Math.floor(Math.random()*1000);
	thePost.parent().attr("id", idToReplace);
	var thePostToAnimateAndToDelete = jQuery("#thePostToPrint");
	var viewbox = jQuery(".viewbox");
	if (thePost.length > 0 && !isiOS()) {
		// Animation to the post place
		thePostToAnimateAndToDelete.animate({
			top: thePost.offset().top - viewbox.offset().top + 46 + parseInt(thePostToAnimateAndToDelete.css("top"), 10),
			left: thePost.offset().left - viewbox.offset().left - 9
		}, {
			duration: 1000,
			step: function(now, fx) {
				if(fx.prop == 'top'){
					var percentage = 1 - ((fx.end-fx.now)/(fx.end-fx.start));
					$('html, body').scrollTop(thePost.offset().top * percentage);
					jQuery("#closeThePostToPrint").fadeTo(1 - percentage);
				}
			},
			complete: function() {
				thePostToAnimateAndToDelete.remove();
				var params = {
						view:				"topic",
						doNotBlinkSns: 		true,
						postLid:			postLid,
						action:				"replace",
						id:					idToReplace,
						absolutePosition:	thePost.parent().attr('x-absolute-position')
				}
				// Show the flash fragment
				jQuery(".postView .htmlfragment").show();
			}
		});
	} else {
		// A simple fadeout
		thePostToAnimateAndToDelete.animate({opacity: 0}, function(){
			thePostToAnimateAndToDelete.remove();
		});
		// Show the flash fragment
		jQuery(".postView .htmlfragment").show();
	}
}

function getCookie(c_name) {
	if (document.cookie.length>0) {
		c_start=document.cookie.indexOf(c_name + "=");
		if (c_start!=-1) {
		    c_start=c_start + c_name.length+1;
		    c_end=document.cookie.indexOf(";",c_start);
		    if (c_end==-1) c_end=document.cookie.length;
		    var val = unescape(document.cookie.substring(c_start,c_end));
			return val.replace(/['"]/g , '');
		}
	}
	return "";
}

function follow() {
  jQuery("#allPage").popup({
		dom: jQuery("#mailinput")[0],
		hideOnClick: false,
		showAnimation: false
	});
}
