$(document).ready(function(){
	
	// Handle all the select2 objects
	$(".select2").each(function(){
		$(this).select2(select2Factory($(this)));
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
	
	/* Start Categories and Products */
	$("#productcategories").on("select2-selecting", function(e) {
		$('#products').empty();
		
		$('#products').select2({
			allowClear: true,
			width: '100%',
			ajax: {
	            url: '/admin/orders/getproducts/id/' + e.val,
	            dataType: 'json',
	            cache: true,
	            data: function (term, page) {
	                return {
	                    term: term
	                };
	            },
	            results: function (data) {
	                var results = [];
	                $.each(data, function (index, item) {
	                    var id = $('#products').attr("field-id");
	                    var field_data = $('#products').attr("fields-data");
	                    var i, mask, mask_length;
	                    
	                    mask = field_data.split(' ');
	                    mask_length = mask.length;

	                    output = '';
	                    for (i = 0; i < mask_length; i++) {
	                        if (i > 0) output += ' ';
	                        field = item[mask[i]];
	                        if (typeof field === 'undefined') {
	                            output += mask[i];
	                        } else {
	                            output += field;
	                        };
	                    }
	                    
	                    results.push({
	                        id: item[id],
	                        text: output
	                    });
	                });
	                return {
	                    results: results,
	                };
	            }
			}
		}).trigger("change");
		
	}); 
	
	// Handle the billing cycles in the orders management
	$("#billingid").change(function () {
		$.post('/admin/orders/getbillings/id/' + $("#products").val() + '/billid/' + $("#billingid").val(), {}, function(data){
			
			if(data[0] !== undefined) {
				$('#price').val(data[0].price);
			}else{
				// If there is not any billing cycle settings then get flat price
				$.post('/admin/orders/getproductinfo/id/' + $("#products").val(), {}, function(data){
					if(data.price_1) {
						$.bootstrapGrowl('Flat price has been set.', { type: 'info', offset: {from: 'top', amount: 60}, allow_dismiss: true, stackup_spacing: 10 });
						$('#price').val(data.price_1);
					}else{
						$.bootstrapGrowl('No billing cycle set and no flat price set in the product detail page.', { type: 'danger', offset: {from: 'top', amount: 60}, allow_dismiss: true, stackup_spacing: 10 });
						$('#price').val(0);
					}
				}, 'json');
				
			}
		}, 'json');
	});
	
 
	// Handle the products in the orders management
	$("#products").on("select2-selecting", function(e) {
		
		$.post('/admin/orders/getproductinfo/id/' + e.val, {}, function(data){
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
		
		$('#billingid').select2({
			width: '100%',
			ajax: {
	            url: '/admin/orders/getbillingcycles/id/' + e.val,
	            dataType: 'json',
	            cache: true,
	            data: function (term, page) {
	                return {
	                    term: term
	                };
	            },
	            results: function (data) {
	                var results = [];
	                $.each(data, function (index, item) {
	                    var id = $('#billingid').attr("field-id");
	                    var field_data = $('#billingid').attr("fields-data");
	                    var i, mask, mask_length;
	                    
	                    mask = field_data.split(' ');
	                    mask_length = mask.length;

	                    output = '';
	                    for (i = 0; i < mask_length; i++) {
	                        if (i > 0) output += ' ';
	                        field = item[mask[i]];
	                        if (typeof field === 'undefined') {
	                            output += mask[i];
	                        } else {
	                            output += field;
	                        };
	                    }
	                    
	                    results.push({
	                        id: item[id],
	                        text: output
	                    });
	                });
	                return {
	                    results: results,
	                };
	            }
			}
		}).trigger("change");
			
	}); 
}); 

// Select2 auto-creation
function select2Factory(select2) {
	var prefmultiple = select2.attr("multiple");
    return {
    	allowClear: true,
    	width: "100%",
    	multiple: prefmultiple,
        ajax: {
            url: select2.attr("url-search") + "/term/",
            dataType: 'json',
            cache: true,
            data: function (term, page) {
                return {
                    term: term
                };
            },
            results: function (data) {
                var results = [];
                $.each(data, function (index, item) {
                    var id = select2.attr("field-id");
                    var field_data = select2.attr("fields-data");
                    var i, mask, mask_length;
                    
                    mask = field_data.split(' ');
                    mask_length = mask.length;

                    output = '';
                    for (i = 0; i < mask_length; i++) {
                        if (i > 0) output += ' ';
                        field = item[mask[i]];
                        if (typeof field === 'undefined') {
                            output += mask[i];
                        } else {
                            output += field;
                        }
                    }
                    
                    results.push({
                        id: item[id],
                        text: output
                    });
                });
                return {
                    results: results
                };
            },
        },
        initSelection: function (element, callback) {
            var id = $(element).val();
            var fieldid = select2.attr("field-id");
            var field_data = select2.attr("fields-data");
            var i, mask, mask_length;
            
            mask = field_data.split(' ');
            mask_length = mask.length;

            $.ajax(select2.attr("url-search") + "/id/" + id, {dataType: "json"}).done(function(items) { 
            	if (typeof items[0] !== 'undefined') {	
	            	output = '';
	                for (i = 0; i < mask_length; i++) {
	                    if (i > 0) output += ' ';
	                    field = items[0][mask[i]];
	                    if (typeof field === 'undefined') {
	                        output += mask[i];
	                    } else {
	                        output += field;
	                    }
	                }
	
	                var data = {id: items[0][fieldid], text: output };
	                callback(data);
            	}
            });
        }
    };
}