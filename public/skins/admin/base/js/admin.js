$(document).ready(function(){
	
	
    // Wysiwyg TinyMCE Switch 
	$(function() {
	    var id = 'body'; // ID of your textarea (no # symbol) 
	        $("a.toggle").toggle(function(){
	           tinyMCE.execCommand('mceRemoveControl', false, id);
	        }, function () {
	            tinyMCE.execCommand('mceAddControl', false, id);
	    });
	});
	
	$('.notification').delay(1500).fadeTo("slow", 0);
	
	tinyMCE.baseURL='/resources/js/wysiwyg/tiny_mce'; // your path to tinyMCE
    tinyMCE.init({
        // General options
    	mode : "specific_textareas",
        editor_selector : "wysiwyg",
        theme : "advanced",
        plugins : "table,save,advhr,advimage,advlink,inlinepopups,insertdatetime,preview,media,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template",
        width : "900",
        height : "400",
        convert_urls : false,
        relative_urls : false,
        remove_script_host : true,
        theme_advanced_resizing : true
    });
    
    tinyMCE.init({
    	// General options
    	mode : "specific_textareas",
    	editor_selector : "wysiwygsimple",
    	theme : "simple",
    	plugins : "table,save,advhr,advimage,advlink,inlinepopups,insertdatetime,preview,media,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template",
    	width : "800",
    	height : "200",
    	convert_urls : false,
    	relative_urls : false,
    	remove_script_host : true,
    	theme_advanced_resizing : true
    });
    
    // Loader Management
	var $loading = $('#loading');
	$loading.ajaxStart(function() {
		 $.blockUI({ 
			 	message: $('#loading'),
	            fadeIn: 100, 
	            fadeOut: 2000, 
	            showOverlay: true, 
	            centerY: true, 
	            css: { 
	                border: 'none', 
	                padding: '15px', 
	                backgroundColor: '#000', 
	                '-webkit-border-radius': '10px', 
	                '-moz-border-radius': '10px', 
	                opacity: .8, 
	                color: '#fff'
	            } 
	        }); 
	});
	$loading.ajaxStop($.unblockUI);
	
	// bind change event to select
	$('#languageswitcher').bind('change', function () {
		var url = $(this).val(); // get selected value
		if (url) { // require a URL
			window.location = url; // redirect
		}
		return false;
	});
	
	/* Tab Management*/
	 $( "#tabs" ).tabs();
	
	 /* MultiSelect Management */
	 $(".multiselect").multiselect({minWidth:350}).multiselectfilter();

	/* Date picker */
	$(function() {
		$.datepicker.setDefaults($.datepicker.regional['']); 
		$( ".date" ).datepicker($.datepicker.regional['it']);
	});
	
	// MENU
	$("ul.subnav a").click(function() { //When trigger is clicked...
		$(this).parent().find("ul.subnav").toggle('fast').show(); //Drop down the subnav on click
	});	
	
	// Delete button 
	$("a.delete").click(function () {
		if (confirm("Sei sicuro di voler cancellare il record?")) {
		    document.location = $(this).attr('href');
		}
		return false;
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

    /* ######################## ORDERS ####################### */
    
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
 
	// Handle the billing cycles in the orders management
	$("#billingid").change(function () {
		$.post('/admin/orders/getbillings/id/' + $("#products").val() + '/billid/' + $("#billingid").val(), {}, function(data){
			
			if(data[0] !== undefined) {
				if(data[0].BillingCycle.months !== undefined) {
					$('#price').val(data[0].price * data[0].BillingCycle.months);
				}
			}else{
				// If there is not any billing cycle settings then get flat price
				$.post('/admin/orders/getproductinfo/id/' + $("#products").val(), {}, function(data){
					if(data.price_1) {
						alert('No billing cycle set in the product detail page. Flat price has been set.'); 
						$('#price').val(data.price_1);
					}else{
						alert('No billing cycle set and no flat price set in the product detail page.');
						$('#price').val(0);
					}
				}, 'json');
				
			}
		}, 'json');
	});
	
	/* Start Categories and Products */
	$("#productcategories").change(function () {
		$.post('/admin/orders/getproducts/id/' + $("#productcategories").val(), {}, function(data){
			$('#products').empty();
			console.log(data);
			if(data[0]){
				$('.productslist').show();
			}else{
				$('.productslist').hide();
				$('.domain').hide();
			}
			
			$('.domain').show();
			$('.billingcycle').show();
			
			$('#products').append('<option value=""></option>');
			$.each(data, function(index, record){
				$('#products').append('<option value="' + record.product_id + '">' + record.name + '</option>');
			});
		}, 'json');
	}); 
	
	//Orders form: invoice destination management
	$("#customer_id").change(function () {
		$.post('/admin/customers/getcustomerinfo/id/' + $("#customer_id").val(), {}, function(data){
			if(data.parent_id) {
				$('#customer_parent_id > option').each(function(){
           if($(this).val()==data.parent_id)
     				$(this).attr("selected", true);
           else
     				$(this).attr("selected", false);
			     });
        }
			else {
				$('#customer_parent_id > option').each(function(){
           if($(this).val()==data.customer_id)
     				$(this).attr("selected", true);
           else
     				$(this).attr("selected", false);
           });
     }
		}, 'json');
	});
 
	// Handle the products in the orders management
	$(".getproducts").change(function () {
		$.post('/admin/orders/getproductinfo/id/' + $("#products").val(), {}, function(data){
			$('#price').empty();
			$('#description').empty();
			
			if(data.ProductsData !== undefined && data.ProductsData[0].name !== undefined) {
				$('#description').val(data.ProductsData[0].name);
			}
			
			if(data.ProductsAttributesGroups.isrecurring){
				 $('.billingcycle_id').show();
			}else{
				 $('.billingcycle_id').hide();
			}
			
			if(data.price_1) {
				$('#price').val(data.price_1);
			}else{
				$('#price').val(0);
			}
			
			if(data.type == "domain") {
				$('.domain').show();
				$('.generic').hide();
			}else{
				$('.domain').hide();
				$('.generic').show();
				$('#domain').val('');
			}
			
		}, 'json');
	}); 
	
    /* ######################## PRODUCTS ####################### */
	$('.sublist').dataTable({
	    "bJQueryUI": true,
	    "bRetrieve":true,
		"sPaginationType": "full_numbers",
	 });
	
	 $("#categories").dynatree({
    	initAjax: {
           url: "/admin/products/getcategories/"
        },
        data: {mode: "all"},
        checkbox: true,
        // Override class name for checkbox icon, so rasio buttons are displayed:
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
	
	
	/* ######################## ORDERS ITEMS/SERVICES ####################### */

	// Items management
	 $('#add_items').click(function() {
		  return !$('.tmpitems option:selected').remove().appendTo('.items');
	 });
	 
	 $('#remove_items').click(function() {
	  return !$('.items option:selected').remove().appendTo('.tmpitems');
	 });
	 
	// Select all the multiple list in the form
	$(".multiple_select_form").submit(function() { 
		$(".items option").attr('selected', 'selected');
	});
	
	
	/* ######################## INVOICES ####################### */
	
	//Invoices form: invoice destination management
	$("#order_id").change(function () {
		$.post('/admin/customers/getcustomerinfo/id/' + $("#order_id").val(), {}, function(data){
			if(data.parent_id) {
				$('#customer_parent_id > option').each(function(){
            if($(this).val()==data.parent_id)
      				$(this).attr("selected", true);
            else
      				$(this).attr("selected", false);
			     });
         }else {
				$('#customer_parent_id > option').each(function(){
            if($(this).val()==data.customer_id)
      				$(this).attr("selected",true);
            else
      				$(this).attr("selected",false);
            });
      }
		}, 'json');
	});
	
	
	/* ######################## CUSTOMERS ####################### */
    $(".customerupdate").click(function () {
    	if($("#nic_id").text()){
	    	var txtnichandle = $("#nic_id").text().trim().split(" - ");;
	    	if(txtnichandle[0]){
		    	$.post('/admin/customers/getcustomersremoteinfo/id/' + txtnichandle[0], {}, function(data){}, 'json');
	    	}	
    	}
	});
    
    
    /* ######################## DOMAINS ####################### */
    
    $(".regactionsbtn").click(function () {
    	if($("#regActions").val() && $("#domain_id").val()){
    		if($("#regActions").val()){
    			$.post('/admin/domains/addtask/id/' + $("#domain_id").val() + '/method/' + $("#regActions").val(), {}, function(data){
    				if(data){
    					if(data.result == 1){
    						alert('Task Added');
    					}
    				}
    			}, 'json');
    		}	
    	}
    }); 
	
	 $('#chkdomain').click(function(){
		 $('#chkdomain').attr('href', '/admin/domains/checkdomain/domain/' + $("#domain").val() + '.' + $("#tld").val());
	 });
	 
	// set the window's location property to the value of the option the user has selected
	 $('.goto').change(function() {
	  window.location = $(this).val();
	});
    
});

function isArray(variable) {
	if (variable.constructor == Array)
		return true;
	else
	    return false;
}
	