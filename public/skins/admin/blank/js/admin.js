$(document).ready(function(){
	
	// Common controller for the submition of the forms
	$('#submit').click(function() { $("form:first").submit(); });
	 
	/* Date picker */
	$(function() {
		$.datepicker.setDefaults($.datepicker.regional['']); 
		$( ".date" ).datepicker($.datepicker.regional['it']);
	});
	
	$("#search").autocomplete('/admin/customers/searchajax', {
	   extraParams: {
	       q: function() { return $("#search").val(); }
	   }
	}); 
		
	/* Autocomplete */
	 function formatItem(row) {
		 return "<strong>" + row[3] + "</strong> - " + row[1];
	 }
	 
	 $("#searchbox").autocomplete("/admin/search/do/",{
		 delay: 500,
		 minChars:2,
		 matchSubset:1,
		 matchContains:1,
		 cacheLength:10,
		 max: 10000,
		 width: 500,
		 formatItem:formatItem,
		 formatResult: function(row) {
				return row[1];
			},
		 autoFill:true
		 }
	 );
	 
	 $('#searchbox').result(function(event, data, formatted) {
	        var Id = data[0];
	        var Module = data[2];
	        location.href = "/admin/search/goto/mod/" + Module + "/id/" + Id;
	 });
	 
	 $('.wysiwyg').wysihtml5();
	 
	 $('.multiselect').selectpicker();
	 
	 $('input.rating').rating();
	 
	 $(":file").filestyle();
	 
});

// Form tab history 
$(function() { 
      $('a[data-toggle="tab"]').on('shown', function () {
        //save the latest tab; use cookies if you like 'em better:
        localStorage.setItem('lastTab', $(this).attr('href'));
       });

      //go to the latest tab, if it exists:
      var lastTab = localStorage.getItem('lastTab');
      if (lastTab) {
         $('a[href=' + lastTab + ']').tab('show');
      }else{
        // Set the first tab if cookie do not exist
        $('a[data-toggle="tab"]:first').tab('show');
      }
});

	