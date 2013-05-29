$(document).ready(function() {
	var obj = $('#sublist_orders_statusHistoryGrid');
	if ( 'undefined' != typeof obj.html() ) {	
		obj.dataTable().fnSort([ [0,'desc'] ]);
	}
});