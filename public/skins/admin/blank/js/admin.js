$(document).ready(function(){
	
	// Common controller for the submition of the forms
	$('#submit').click(function() { $("form:first").submit(); });
	 
	 /* MultiSelect Management */
	$(".multiselect").multiselect({minWidth:350}).multiselectfilter();

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
	 
});


	