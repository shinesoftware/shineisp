$(document).ready(function(){
	
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
});
	