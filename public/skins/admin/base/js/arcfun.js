function onEditTranche( idtranche ) {
	$.ajax({
		 url:'/admin/products/gettranche/id/'+idtranche
		,dataType :'json'
		,error:function(){
			alert("errore");
			return false;
		}
		,success:function( params ){
			
			$(':text[name="tranche_qty"]').val(params.quantity);
			$('SELECT[name="tranche_billing_cycle_id"]').val(params.billing_cycle_id);
			$(':text[name="tranche_setupfee"]').val(params.setupfee);
			$(':text[name="tranche_price"]').val(params.price);
			$(':text[name="tranche_measure"]').val(params.measurement);
			
			$('#trancheOperationTitle').html(params.title);
		}
	});
    
    return false;
}
function onCleanTranche(){
	$(':text[name="tranche_qty"]').val("");
	$('SELECT[name="tranche_billing_cycle_id"]').val("");
	$(':text[name="tranche_setupfee"]').val("");
	$(':text[name="tranche_price"]').val("");
	$(':text[name="tranche_measure"]').val("");
	
	$('#trancheOperationTitle').html("");
	return false;
}
