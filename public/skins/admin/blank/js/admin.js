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
	    statusbar:  false,
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
	
	tinymce.init({
	    selector: "textarea.wysiwyg-simple",
		width:      '100%',
	    plugins:    [ "paste anchor link" ],
	    statusbar:  false,
		menubar:    false,
		paste_preprocess : function(pl, o) {o.content = strip_tags( o.content,'' );},
	    toolbar:    "bold italic | bullist | alignleft aligncenter alignright alignjustify"
	});
	 
});


// Strips HTML and PHP tags from a string for TinyMCE plugin
function strip_tags (str, allowed_tags) {
	var key = '', allowed = false;
	var matches = [];
	var allowed_array = [];
	var allowed_tag = '';
	var i = 0;
	var k = '';
	var html = '';
	var replacer = function (search, replace, str) {
		return str.split(search).join(replace);
	}
	;
	// Build allowes tags associative array
	if (allowed_tags) {
		allowed_array = allowed_tags.match(/([a-zA-Z0-9]+)/gi);
	}
	str += '';
	// Match tags
	matches = str.match(/(<\/?[\S][^>]*>)/gi);
	// Go through all HTML tags
	for (key in matches) {
		if (isNaN(key)) {
			// IE7 Hack
			continue;
		}
		// Save HTML tag
		html = matches[key].toString();
		// Is tag not in allowed list? Remove from str!
		allowed = false;
		// Go through all allowed tags
		for (k in allowed_array) {
			// Init
			allowed_tag = allowed_array[k];
			i = -1;
			if (i != 0) {
				i = html.toLowerCase().indexOf('<'+allowed_tag+'>');
			}
			if (i != 0) {
				i = html.toLowerCase().indexOf('<'+allowed_tag+' ');
			}
			if (i != 0) {
				i = html.toLowerCase().indexOf('</'+allowed_tag)   ;
			}
			// Determine
			if (i == 0) {
				allowed = true;
				break;
			}
		}
		if (!allowed) {
			str = replacer(html, "", str);
			// Custom replace. No regexing
		}
	}
	return str;
}
