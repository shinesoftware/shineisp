// function that writes the list order to a cookie
function saveOrder() {
    $(".column").each(function(index, value){
        var colid = value.id;
        
        var cookieName = "cookie-" + colid;
        // Get the order for this column.
        var order = $('#' + colid).sortable("toArray");
        
        // For each portlet in the column
        for ( var i = 0, n = order.length; i < n; i++ ) {
            // Determine if it is 'opened' or 'closed'
            var v = $('#' + order[i] ).find('.widget-content').is(':visible');
            // Modify the array we're saving to indicate what's open and
            //  what's not.
            order[i] = order[i] + ":" + v;
            
            
        }
        $.cookie(cookieName, order, { path: "/", expiry: new Date(2019, 1, 1)});
    });
}

// function that restores the list order from a cookie
	function restoreOrder() {
    $(".column").each(function(index, value) {
        var colid = value.id;
        var cookieName = "cookie-" + colid;
        var cookie = $.cookie(cookieName);
        if ( cookie == null ) { return; }
        var IDs = cookie.split(",");
        for (var i = 0, n = IDs.length; i < n; i++ ) {
            var toks = IDs[i].split(":");
            if ( toks.length != 2 ) {
                continue;
            }
            var portletID = toks[0];
            var visible = toks[1];
            
            var portlet = $(".column")
                .find('#' + portletID)
                .appendTo($('#' + colid));
            if (visible === 'false') {
                portlet.find(".ui-icon").toggleClass("ui-icon-minus");
                portlet.find(".ui-icon").toggleClass("ui-icon-plus");
                portlet.find(".widget-content").hide();
            }
        }
    });
} 


$(document).ready( function () {
    $(".column").sortable({
        connectWith: ['.column'],
        handle: '.widget-header',
        cursor: 'move',
        placeholder: "ui-state-highlight",
        stop: function() { saveOrder(); }
    }); 
    
    $( ".column" ).disableSelection();

    $(".portlet")
        .find(".widget-header")
        .prepend('<span class="ui-icon ui-icon-minus"></span>')
        .end()
        .find(".widget-content");

    restoreOrder();

    $(".widget-header .ui-icon").click(function() {
        $(this).toggleClass("ui-icon-minus");
        $(this).toggleClass("ui-icon-plus");
        $(this).parents(".portlet:first").find(".widget-content").toggle();
        saveOrder(); // This is important
    });
    $(".widget-header .ui-icon").hover(
        function() {$(this).addClass("ui-icon-hover"); },
        function() {$(this).removeClass('ui-icon-hover'); }
    );
});