$(document).ready(function(){
	
	$("#searchbox").focus(function() {
		$(this).parent().addClass("curfocus");
		$(this).addClass("largebox");
	});

	$("#searchbox").blur(function() {
		$(this).parent().removeClass("curfocus");
		$(this).removeClass("largebox");
	});
	
	//Custom Action button inside the list of the records
	$(".chkdomain").click( 
		function () {
			if($('.domainame').val()){
				var domain =$('.domainame').val();
		        if(!/^(http(s)?\/\/:)?(www\.)?[a-zA-Z\-]{3,}(\.(com|net|org))?$/.test(domain))
	            {
		        	$('.domainame').empty().css("border", "2px solid #FF0000");
	                alert('invalid domain name');
	                return false;
	            }
		        $('.domainame').empty().css("border", "1px solid #CCCCCC");
				$.post('/common/checkdomain/', {name: $('.domainame').val(), tld: $('.tld').val()}, 
					function(data){
						if(data.available){
							$('#result').removeClass('big-notavailable');
							$('#result').addClass('big-available');
							$('a.ordernow').attr('href', '/common/buy/tld/' + data.tld + '/name/' + data.name + '/do/register');
						}else{
							$('#result').removeClass('big-available');
							$('#result').addClass('big-notavailable');
							$('a.ordernow').attr('href', '/common/buy/tld/' + data.tld + '/name/' + data.name + '/do/transfer');
						}
						$('#result').show();
						$('.price').html(data.price);
						$('.domain').html(data.domain);
						$('.message').html(data.mex);
						
					}, 'json');
			}
			return false;
		}
	);
	
	$(".updatecart").click( 
		function () {
			var data = $("input[name='field[]']").map( function(){ return {"id": this.id, "value": this.value};}).get();

			$.post('/cart/update/', {cart: data}, 
					function(result){
						location.reload();
			}, 'json');
			return false;
		}
	);
	
    
}); 