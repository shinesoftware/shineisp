
// Sorting of the email grid list 
$('#sublist_emailstemplatessends').dataTable({
    "bRetrieve":true,
	"sPaginationType": "full_numbers",
	"aaSorting": [[ 0, "desc" ]]
});