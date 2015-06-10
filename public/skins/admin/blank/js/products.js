$(document).ready(function(){
	
	/* ######################## PRODUCTS ####################### */
	 $('.sublist').dataTable({
	    "bRetrieve":true,
		"sPaginationType": "full_numbers",
	 });
	
	 $("#categories").dynatree({
			   	initAjax: {
			          url: "/admin/products/getcategories/"
			       },
			       data: {mode: "all"},
			       checkbox: true,
			       // Override class name for checkbox icon, so radio buttons are displayed:
			       //classNames: {checkbox: "dynatree-radio"},
			       // Select mode 3: multi-hier
			       selectMode: 2,
			       minExpandLevel: 2
			   });
	
   $("form").submit(function() {
       
       // then append Dynatree selected 'checkboxes':
       var tree = $("#categories").dynatree("getTree");
       var data = tree.serializeArray();
       var items = new Array();
       $('#categoriesitems').remove();
       
       $.each(data, function(i, fd) {
           items[i]= fd.value;
       });    
       
       if(isArray(items) && items.length > 0){
       	$(this).append("<input type='hidden' id='categoriesitems' name='categories' value='"+items.join("/")+"' />");
       }     
   });
	
	
    // Add and Remove management
	 $('#add').click(function() {
		  return !$('.tmpitems option:selected').remove().appendTo('.selecteditems');
	 });
	 
	 $('#remove').click(function() {
	  return !$('.selecteditems option:selected').remove().appendTo('.tmpitems');
	 });
	 
	$(".selecteditems").change(function () {
		$.post('/admin/domains/getdomaincreationdate/id/' + $(".selecteditems").val(), {}, function(data){
			if(data.creationdate) {
				$('#date_start').val(data.creationdate);
			}
		}, 'json');
	}); 
	
	$(".searchitems").change(function () {
		$.post('/admin/domains/getdomainsbyajax/search/' + $(".searchitems").val(), {}, function(data){
			$('.tmpitems').empty();
			if(data){
				$.each(data, function(name,value){
					$('.tmpitems').append('<option value="' + name + '">' + value + '</option>');
				});
			}
		}, 'json');
	}); 
 
}); 


function isArray(variable) {
	if (variable.constructor == Array)
		return true;
	else
	    return false;
}

	