$(document).ready(function(){
	$(function() {
		$("#chkdb").click(function() {
		    // getting the value that user typed
		    var hostname    = $("#hostname").val();
		    var username    = $("#username").val();
		    var password    = $("#password").val();
		    var database    = $("#database").val();
		    
		    // forming the queryString
		    var data = 'hostname='+ hostname + '&username='+ username + '&password='+ password + '&database='+ database;
		 
		    // if checkString is not empty
		    if(hostname) {
		        // ajax call
		        $.ajax({
		            type: "POST",
		            url: "/setup/database/chkdb",
		            data: data,
		            beforeSend: function(html) { // this happen before actual call
		                $("#chkdbresult").html('');
		            },
		            success: function(html){ // this happen after we get result
		                $("#chkdbresult").show();
		                $("#chkdbresult").append(html);
		            }
		        });
		}
		return false;
		});
		});
	
	$('.progress').ajaxStart(function () {
	    $(this).dialog({
	        title: "Loading data...",
	        modal: true,
	        width: 50,
	        height: 100,
	        closeOnEscape: false,
	        resizable: false,
	        open: function () {
	            $(".ui-dialog-titlebar-close", $(this).parent()).hide(); //hides the little 'x' button
	        }
	    });
	}).ajaxStop(function () {
	    $(this).dialog('close');
	});
});