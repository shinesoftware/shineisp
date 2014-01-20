$(document).ready(function(){
	
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
	
	$("#legalform_id").change(function () {
		$.post('/admin/customers/companytype/id/' + $("#legalform_id").val(), {}, function(data){
			$('#type_id').empty();
			if(data){
				$.each(data, function(name,value){
					$('#type_id').append('<option value="' + name + '">' + value + '</option>');
				});
			}
		}, 'json');
	}); 
	
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
	
	
	tinymce.init({
	    selector: "textarea.wysiwyg-simple",
		width:      '100%',
	    plugins:    [ "paste anchor link" ],
		menubar:    false,
		paste_preprocess : function(pl, o) {o.content = strip_tags( o.content,'' );},
	    toolbar:    "bold italic | bullist outdent indent | alignleft aligncenter alignright alignjustify"
	});
    
}); 




//Strips HTML and PHP tags from a string for TinyMCE plugin
function strip_tags (str, allowed_tags) {
	var key = '', allowed = false;
	var matches = [];
	var allowed_array = [];
	var allowed_tag = '';
	var i = 0;
	var k = '';
	var html = '';
	var replacer = function (search, replace, str) {
		return str.split(search).join(replace);
	}
	;
	// Build allowes tags associative array
	if (allowed_tags) {
		allowed_array = allowed_tags.match(/([a-zA-Z0-9]+)/gi);
	}
	str += '';
	// Match tags
	matches = str.match(/(<\/?[\S][^>]*>)/gi);
	// Go through all HTML tags
	for (key in matches) {
		if (isNaN(key)) {
			// IE7 Hack
			continue;
		}
		// Save HTML tag
		html = matches[key].toString();
		// Is tag not in allowed list? Remove from str!
		allowed = false;
		// Go through all allowed tags
		for (k in allowed_array) {
			// Init
			allowed_tag = allowed_array[k];
			i = -1;
			if (i != 0) {
				i = html.toLowerCase().indexOf('<'+allowed_tag+'>');
			}
			if (i != 0) {
				i = html.toLowerCase().indexOf('<'+allowed_tag+' ');
			}
			if (i != 0) {
				i = html.toLowerCase().indexOf('</'+allowed_tag)   ;
			}
			// Determine
			if (i == 0) {
				allowed = true;
				break;
			}
		}
		if (!allowed) {
			str = replacer(html, "", str);
			// Custom replace. No regexing
		}
	}
	return str;
}