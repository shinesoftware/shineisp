$(document).ready(function(){
	
	$('.multiselect').selectpicker();
 
	$('input.rating').rating();
	
    // Set the active TAB in all the pages
	$('a[data-toggle="tab"]').on('click', function (e) {
		localStorage.setItem('lastTab', window.location.href + $(e.target).attr('href'));
	});

	//go to the latest tab, if it exists:
	var lastTab = localStorage.getItem('lastTab');

	if (lastTab) {
		var data = lastTab.split('#');
		if(window.location.href == data[0]){
			$('a[href="#'+data[1]+'"]').click();
		}else{
			// Set the first tab if cookie do not exist
	        $('a[data-toggle="tab"]:first').tab('show');
		}
	}else{
        // Set the first tab if cookie do not exist
        $('a[data-toggle="tab"]:first').tab('show');
    }
	  
	// Common controller for the submition of the forms
	$('#submit').click(function() { $("form:first").submit(); });
	 
	/* Date picker */
	$('.date').each(function() {
	    //standard options
	    var options = { dateFormat: $(this).attr('dateformat')};  
	    
	    //additional options
	    var additionalOptions = $(this).data("datepicker");
	     
	    //merge the additional options into the standard options and override defaults
	    jQuery.extend(options, additionalOptions);
	    
	     $(this).datepicker(options);
	});
	
	// Auto Search 
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
	 
	/* wysihtml5 integration
	 * =====================
	 $('.wysiwyg').wysihtml5({
		 "html": true,
		 "color": true,
		 parserRules:  wysihtml5ParserRules,
		// Whether urls, entered by the user should automatically become clickable-links
		    autoLink:             true,
		 // Whether the rich text editor should be rendered on touch devices (wysihtml5 >= 0.3.0 comes with basic support for iOS 5)
	    supportTouchDevices:  true,
	 });
	 */
	
	/* Summernote integration
	 $('.wysiwyg').summernote({
	 });
	 
	 $('.wysiwyg-simple').summernote({
		 toolbar: [
		           ['style', ['bold', 'italic', 'underline', 'clear']],
		           ['para', ['ul', 'ol']],
		         ]
	 });
	 */
	
	/* JQuery-te integration
	 * =====================
		$('.wysiwyg').jqte();
	*/
	
	/* TinyMCE integration */
	tinymce.init({
	    selector: "textarea.wysiwyg",
	    theme: "modern",
	    menubar : false,
	    width:      '100%',
	    plugins: [
	        "advlist autolink lists link image charmap print preview hr anchor pagebreak",
	        "searchreplace wordcount visualblocks visualchars code fullscreen",
	        "insertdatetime media nonbreaking save table contextmenu directionality",
	        "emoticons template paste textcolor"
	    ],
	    toolbar1: "styleselect | forecolor backcolor | undo redo | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent",
	    toolbar2: "preview media | emoticons | link image",
	    image_advtab: true,
	    content_css : "/resources/wysiwyg/tinymce/skins/lightgray/custom_content.css",

	});
	 
});

