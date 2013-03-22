$(document).ready(function(){
	// Update the total of the product
	function UpdatePrice(){
		if($(".select-billing-cycle").length){
		    $.post('/products/getprice/id/' + $(".select-billing-cycle").val(), {}, function(data){   
		        var pricelbl = data.pricelbl;
		        var price = data.price;
		        var pricetax = data.pricepermonths;
		        
		        $('#priceval').empty();
		        $('#pricetaxval').empty();
		        $('#pricefreq').empty();
		        $('#priceval').append(pricelbl);
		        $('#pricetaxval').append(pricetax);
		        $('#pricefreq').append(data.name);
		        $('.sticker').fadeIn("slow");
		    }, 'json');
		}
	}		

	//Update the price in the product detail page
	UpdatePrice();
	
	//Updating the price 
	$(".select-billing-cycle").change(function () {
		UpdatePrice();
	}); 
});	