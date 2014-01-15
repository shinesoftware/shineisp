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
