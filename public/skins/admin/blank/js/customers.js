$(document).ready(function(){
	
	/* ######################## CUSTOMERS ####################### */
    $(".customerupdate").click(function () {
    	if($("#nic_id").text()){
	    	var txtnichandle = $("#nic_id").text().trim().split(" - ");;
	    	if(txtnichandle[0]){
		    	$.post('/admin/customers/getcustomersremoteinfo/id/' + txtnichandle[0], {}, function(data){}, 'json');
	    	}	
    	}
	});
});

function onChangeCountry( that ){
    var countryid   = $(that).val();
    
    $.ajax({
         'url'          : '/admin/regions/getall/country_id/'+countryid
        ,'dataType'     : 'json'
        ,'success'      : function( data ){
            if( data.success == true ) {
                var regions = $(that).closest('form').find('select[name="region_id"]');
                if( data.total == 0 ) {
                    regions.attr('disabled','disabled');
                    regions.html('');
                    
                    var area = $(that).closest('form').find('[name="area"]');
                    if( area.is('input:text') ) {
                        area.val("");
                    } else {
                        var parent  = area.parent();
                        area.remove();
                        parent.append('<input type="text" id="area" value="" name="area" class="text-input large-input" />');
                    }
                    
                } else {
                    regions.removeAttr('disabled');
                    $.each( data.rows, function( key, value ) {
                        regions.append('<option value="'+value.region_id+'">'+value.name+'</option>');
                    });
                    
                }
            }
        }
        
    });
}

function onChangeRegions( that ) {
    var regionid   = $(that).val();
    
    $.ajax({
         'url'          : '/admin/provinces/getall/region_id/'+regionid
        ,'dataType'     : 'json'
        ,'success'      : function( data ){
            if( data.success == true ) {
                var area = $(that).closest('form').find('[name="area"]');
                if( data.total == 0 ) {
                    if( area.is('input:text') ) {
                        area.val("");
                    } else {
                        var parent  = area.parent();
                        area.remove();
                        parent.append('<input type="text" id="area" value="" name="area" class="text-input large-input" />');
                    }
                    
                } else {
                    if( area.is('input:text') ) {
                        var parent  = area.parent();
                        area.remove();
                        parent.append('<select name="area" class="text-input large-input" id="area"></select>');
                        area    = $('SELECT[name="area"]');
                    } else {
                        area.html("");
                    }
                    
                    $.each( data.rows, function( key, value ) {
                        area.append('<option value="'+value.province_id+'">'+value.name+'</option>');
                    });                        
                }
                console.log(area);
                /*
                if( data.total == 0 ) {
                    regions.attr('disabled','disabled');
                    regions.html('');
                } else {
                    regions.removeAttr('disabled');
                    $.each( data.rows, function( key, value ) {
                        regions.append('<option value="'+value.region_id+'">'+value.name+'</option>');
                    });
                    
                }*/
            }
        }
        
    });    
}