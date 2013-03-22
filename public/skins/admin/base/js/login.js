$(document).ready(function(){
	
	$(window).resize(function(){
		$('.loginframe').css({
			position:'absolute',
			display:'block',
			left: ($(window).width() - $('.loginframe').outerWidth())/2,
			top: ($(window).height() - $('.loginframe').outerHeight())/2
		});

	}); 

	$(window).resize();
}); 	