$(document).ready(function(){
	
	$(window).bind("load resize scroll",function(e){
		$('.loginbox').css({
			position:'absolute',
			display:'block',
			left: ($(window).width() - $('.loginframe').outerWidth())/2,
			top: ($(window).height() - $('.loginframe').outerHeight())/2
		});

	}); 

	$(window).resize();
}); 	