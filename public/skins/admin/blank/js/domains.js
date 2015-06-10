$(document).ready(function(){ 
	
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