$(document).ready(function(){
	
	// Common controller for the submition of the forms
	$('#submit').click(function() { $("form:first").submit(); });
	
	// TABS
	// Content box tabs:
	$('.content-box-content div.tab-content').hide(); 
	$('.content-box-content div.default-tab').show(); 
	
	$('ul.content-box-tabs li a').click( 
		function() { 
			$(this).parent().siblings().find("a").removeClass('current'); 
			$(this).addClass('current'); 
			var currentTab = $(this).attr('href'); 
			$(currentTab).siblings().hide(); 
			$(currentTab).show(); 
			$('#bitbreadcrump').show();
			$('#bitbreadcrump').html($(this).html());
			$('#buttons').show();
			return false; 
		}
	);
	
	// Scrolling header
	var $scrollingDiv = $("#scrollme");
	$('#main_content').scroll(function(){
		$scrollingDiv
			.stop()
			.animate({"marginTop": ($('#main_content').scrollTop()) + "px"}, "slow" );			
	});
	
});