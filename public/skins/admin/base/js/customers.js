$(document).ready(function() {
	var obj = $('#sublist_emailstemplatessends');
	if ( 'undefined' != typeof obj.html() ) {	
		obj.dataTable().fnSort([ [0,'desc'] ]);
	}
});