jQuery(document).ready(function(){
	var nocache = new Date().getMilliseconds();
	sliderPlugin = jQuery('#slider').bxSlider({
		"mode":"fade",
		"adaptiveHeight":true,
		"auto":true,
		"controls":true,
		"speed":800,
		"pause":5000,
		"randomStart":false,
		"pager":false,
		"preloadImages":"all",
		 "useCSS":false,
		"infiniteLoop": true,
		"hideControlOnEnd": true,
		"startSlide": 0		
	});
	
	jQuery(".bx-controls-direction").on( "click", ".bx-prev.disabled", function() {
		var slideQty = sliderPlugin.getSlideCount();
		sliderPlugin.goToSlide(slideQty-1);
	});

	jQuery(".bx-controls-direction").on( "click", ".bx-next.disabled", function() {
		sliderPlugin.goToSlide(0);
	});
});

jQuery(window).load(function(){
	jQuery(".bx-controls-direction").css({
		"visibility":"visible"
	})
});