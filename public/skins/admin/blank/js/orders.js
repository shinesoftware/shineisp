$(document).ready(function(){
	
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
}); 