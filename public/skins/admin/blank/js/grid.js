$(document).ready(function(){

	// Custom Action button inside the list of the records
	$("#bulkactions").on("click", function(){ 
			if($('#bulkactions').val()){
				$.post('/admin/' + $('#bulkactions').attr('rel') + '/bulk/', {params: $.param($('.table :checkbox:checked')) + '&do='+$('#actions').val()}, 
						function(data){
							if(data.reload !== undefined){
								window.location.href = data.reload;
							}
							
							if(data.url !== undefined){
								window.location.href = data.url;
							}
							
							if(data.mex !== undefined){
								$("#mex").html(data.mex).fadeIn(300, function(){
									setTimeout(function() {window.location.reload();} , 500); // delays 0,5 sec
								});
							}
				}, 'json');
			}
			return false;
		}
	); 
	
	$('table.dataTable').delegate('tbody tr', 'click', function(event) {
        if (event.target.type !== 'checkbox') {
            var checkbox = $(this).find(':checkbox');
            checkbox.trigger('click');
        }
    });
	
	$('table.dataTable').delegate('tbody tr', 'dblclick', function(event) {
		var link = $(this).find('a.editlink');
		window.location.href = link.attr("href");
	});
	
	// Select all the checkboxes
	$('.selectall').click(
		function(){
			$(this).parent().parent().parent().parent().find("input[type='checkbox']").attr('checked', $(this).is(':checked'));   
		}
	);
	
	// Custom Action button inside the list of the records
	$("#statusbtn").click( 
			function () {
				if($('#statusbtn').val()){
					$.post('/admin/' + $('#statusbtn').attr('rel') + '/searchprocess/', {status_id: $('#statuses').val()}, 
							function(data){
						if(data.reload !== undefined){
							window.location.href = data.reload;
						}
						
						if(data.mex !== undefined){
							$("#mex").html(data.mex).fadeIn(1000, function(){
								$(this).fadeOut(5000);  
								window.location.reload();
							});
						}
					}, 'json');
				}
				return false;
			}
	); 
	
	// Custom Action button 
	$(".bulkbtnactions").click( 
		function () {
			var domain = $(this).attr('rel');
			var action = $(this).attr('act');
			$.post('/admin/domains/bulk/', {params: 'item=' + domain + '&do='+action}, 
					function(data){
						var css = domain.replace(".", "_");
						if(data.mex !== undefined){
							$(".mex_"+css).html(data.mex).fadeIn(1000, function(){
							    $(this).fadeOut(2000);         
							});
						}
			}, 'json');
			return false;
		}
	); 	
});	