(function($){  
    // Attach this new method to jQuery
    $.fn.extend({
        // This is where you write your plugin's name
        popup: function(options) {
			var defaults = {
				text: "Waiting...",				// The default text
				dom: null,						// If dom is present, take this element
				hideOnClick: false,				// By defaut this popup is blocked until we call closePopup()
				showAnimation: false			// By default show the small clock
			};
        	var options =  $.extend(defaults, options);  
            // Iterate over the current set of matched elements
            return this.each(function() {
            	if(!$('#overlay').length>0) {
            		// Create a global overlay
                	$("body").prepend('<div id="overlay"></div>');
        			$('#overlay').css('opacity','0.5');
        			$('#overlay').css('background-color','black');
    				$('#overlay').css('position','absolute');
        			$('#overlay').css('height', jQuery(document).height());
        			$('#overlay').css('width','100%');
        			$('#overlay').css('z-index','20000');
        			
        			// Create the popup sub-overlay
        			$("body").prepend("<div id='overlaypopup'></div>");
        			$('#overlaypopup').css('z-index','20001');
    				$('#overlaypopup').css('position','absolute');
        			$('#overlaypopup').css('-moz-border-radius','10px');
        			$('#overlaypopup').css('-webkit-border-radius','10px');
        			$('#overlaypopup').css('border-radius','10px');
        			
        			$("#overlaypopup").append("<div id='popup'></div>");
        			$("#popup").css("background", "#222222");
        			$('#popup').css('color','white');
        			$('#popup').css('font-family','RockwellRegular');
        			$('#popup').css('font-size','18px');
        			$('#popup').css('text-align','center');
        			$('#popup').css('padding','10px');
        			$('#popup').css('position','absolute');
        			$('#popup').css('left','20px');
    				$('#popup').css('top','20px');
    				$('#popup').css('min-width','360px');
    				$('#popup').css('height','auto');
    				$('#popup').css('-moz-border-radius','10px');
        			$('#popup').css('-webkit-border-radius','10px');
        			$('#popup').css('border-radius','10px');
        			$('#popup').css('border','1px solid white');
        			$('#popup').append('<div id="time" style="margin-bottom: 10px;"></div>');
            	} else {
            		$('#overlay').show();
            		$("#overlaypopup").show();
            		$('#popup').show();
            	}
            	if (options.showAnimation) {
    				$('#time').append("<img src='/resources/img/popup/loader.gif' />");
    			}
    			if (options.dom) {
    				options.dom.style.display = "block";
    				$("#popup").append(options.dom);
    			} else {
    				$('#popup').html(options.text);
    			}
    			if (options.hideOnClick) {
    				setTimeout("handleBodyClick()", 200);
    			}
    			// Position of the overlaypopup
    			$('#overlaypopup').css("top", $(window).scrollTop() + (jQuery(window).height() / 2) - (jQuery('#popup').height()/2)+"px");
    			$('#overlaypopup').css('left',($(window).width() - $('#popup').width())/2  + 'px');
            });  
        }
    });  
})(jQuery);

(function($){  
	$.fn.closePopup = function() {
		return this.each(function() {
			$('#overlay').hide();
			$('#overlaypopup').hide();
			$('body').unbind("click", handler);
	});
};  
})(jQuery); 

function handleBodyClick() {
	$('body').click(handler);
}

var handler = function() {
	jQuery("#allPage").closePopup();
};

		