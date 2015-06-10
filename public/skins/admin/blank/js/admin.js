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
	        beforeSend: function(xhr){
	        	$(".tt-hint").addClass("loading"); 
	          },
             filter: function(parsedResponse){
	        	  $(".tt-hint").removeClass("loading"); 
	              return parsedResponse;
             }
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
	    toolbar2: "code preview media | emoticons | link image",
	    image_advtab: true,
	    content_css : "/resources/wysiwyg/tinymce/skins/lightgray/custom_content.css",

	});
	 
});


function onChangeCountry( that ){
    var countryid   = $(that).val();
    
    $.ajax({
         'url'          : '/admin/regions/getall/country_id/'+countryid
        ,'dataType'     : 'json'
        ,'success'      : function( data ){
            if( data.success == true ) {
                var regions = $(that).closest('form').find('select[name="region_id"]');
                if( data.total == 0 ) {
                    regions.attr('disabled','disabled');
                    regions.html('');
                    
                    var area = $(that).closest('form').find('[name="area"]');
                    if( area.is('input:text') ) {
                        area.val("");
                    } else {
                        var parent  = area.parent();
                        area.remove();
                        parent.append('<input type="text" id="area" value="" name="area" class="text-input large-input" />');
                    }
                    
                } else {
                    regions.removeAttr('disabled');
                    $.each( data.rows, function( key, value ) {
                        regions.append('<option value="'+value.region_id+'">'+value.name+'</option>');
                    });
                    
                }
            }
        }
        
    });
}

