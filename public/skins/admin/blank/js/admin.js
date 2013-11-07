$(document).ready(function(){
	
	// Common controller for the submition of the forms
	$('#submit').click(function() { $("form:first").submit(); });
	 
	/* Date picker */
	$(function() {
		$.datepicker.setDefaults($.datepicker.regional['']); 
		$( ".date" ).datepicker($.datepicker.regional['it']);
	});
	
	$('#searchbox').typeahead({
		name: 'searchbox',
		limit: 10, 
		remote: {
	        url: '/admin/search/do/q/%QUERY',
	    },
	    template: [
			'<p class="repo-name"><i class="glyphicon {{icon}}"></i> {{section}}: {{value}}</p>',
			].join(''),
		engine: Hogan 
	}).on("typeahead:selected", function($e, datum){ 
	    window.location = datum['url'];
	});
	
	$("#search").autocomplete('/admin/customers/searchajax', {
	   extraParams: {
	       q: function() { return $("#search").val(); }
	   }
	}); 
	 
	 $('.wysiwyg').wysihtml5({
		 "html": true,
		 "color": true,
		 parserRules:  wysihtml5ParserRules,
		// Whether urls, entered by the user should automatically become clickable-links
		    autoLink:             true,
		 // Whether the rich text editor should be rendered on touch devices (wysihtml5 >= 0.3.0 comes with basic support for iOS 5)
	    supportTouchDevices:  true,
	 });
	 
	 $('.multiselect').selectpicker();
	 
	 $('input.rating').rating();
	 
});
