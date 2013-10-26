$(document).ready(function(){
	
    // Wysiwyg TinyMCE Switch 
	$(function() {
	    var id = 'body'; // ID of your textarea (no # symbol) 
	        $("a.toggle").toggle(function(){
	           tinyMCE.execCommand('mceRemoveControl', false, id);
	        }, function () {
	            tinyMCE.execCommand('mceAddControl', false, id);
	    });
	});
	
	tinyMCE.baseURL='/resources/js/wysiwyg/tiny_mce'; 
    tinyMCE.init({
        // General options
    	mode : "specific_textareas",
        editor_selector : "wysiwyg",
        theme : "advanced",
        plugins : "table,save,advhr,advimage,advlink,inlinepopups,insertdatetime,preview,media,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template",
        width : "900",
        height : "400",
        convert_urls : false,
        relative_urls : false,
        remove_script_host : true,
        theme_advanced_resizing : true
    });

    tinyMCE.init({
        // General options
    	mode : "specific_textareas",
        editor_selector : "wysiwyg_fullpage",
        theme : "advanced",
        plugins : "table,save,advhr,advimage,advlink,inlinepopups,insertdatetime,preview,media,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,fullpage",
        width : "900",
        height : "400",
        convert_urls : false,
        relative_urls : false,
        remove_script_host : true,
        theme_advanced_resizing : true
    });
    
    tinyMCE.init({
    	// General options
    	mode : "specific_textareas",
    	editor_selector : "wysiwygsimple",
    	theme : "simple",
    	plugins : "table,save,advhr,advimage,advlink,inlinepopups,insertdatetime,preview,media,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template",
    	width : "800",
    	height : "200",
    	convert_urls : false,
    	relative_urls : false,
    	remove_script_host : true,
    	theme_advanced_resizing : true
    });
});