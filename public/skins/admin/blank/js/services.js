$(document).ready(function(){
	
	/* ######################## ORDERS ITEMS/SERVICES ####################### */

	// Items management
	 $('#add_items').click(function() {
		  return !$('.tmpitems option:selected').remove().appendTo('.items');
	 });
	 
	 $('#remove_items').click(function() {
	  return !$('.items option:selected').remove().appendTo('.tmpitems');
	 });
	 
	// Select all the multiple list in the form
	$(".multiple_select_form").submit(function() { 
		$(".items option").attr('selected', 'selected');
	});
});