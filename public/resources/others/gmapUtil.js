(function() {

	var gmap = {
		geocoder:null,
		currentMap : [],
		currentInfoWindow : [],
		currentMarker :[],
		gmapEventType : {
			CLICK : 'click',
			DomReady:'domready'
		},
		geocodeRequestOption:{
			address:"",
			latLng:"",
			bounds:"",
			region:""
			
		},
		geocoderRequestType:{
			address:0,
			latLng:1
		},
		geocoderResults:[],
		latitude : 0,
		longitude : 0,
		gmapOptions : {

			zoom : 4,
			center : "",
			mapTypeId : google.maps.MapTypeId.ROADMAP
		},
		setLatitude : function(_latitude) {
			this.latitude = _latitude;
			return this;
		},
		setLongitude : function(_longitute) {
			this.longitute = _longitute;
			return this;
		},
		mapInit : function(options) {
			var tempMapElement = document.getElementById("map_canvas");
			if(tempMapElement != null) {
				var Latlng = new google.maps.LatLng(gmap.latitude, gmap.longitude);
				gmap.gmapOptions.center = Latlng;
				var map = new google.maps.Map(tempMapElement, gmap.gmapOptions);
				gmap.currentMap.push(map)
			}
			return this;
		},
		addEventToMap : function(eventType, callback) {
			if(gmap.currentMap[0] != null) {
				google.maps.event.addListener(gmap.currentMap[0], eventType, callback);
			}
			return this;
		},
		addMarkerToMap : function(location) {
			if( location == null) {
				var location = new google.maps.Latlng(gmap.latitude, gmap.longitude);
			}
			var marker = new google.maps.Marker({
				map : gmap.currentMap[0],
				position : location
			});
			if(marker != null){
				gmap.currentMarker.push(marker);
			}
			gmap.currentMap[0].setCenter(location);
			return this;
		},
		addEventToMarker : function(marker,eventType,callback){
			if(gmap.currentMap[0]){
				google.maps.event.addListener(marker,eventType,callback);
			}
		},
	    createInfoWindow: function(content){
	    	if(content != null){
	    		var newInfoWindow = google.maps.InfoWindow({
	    			content:content,maxWidth:270
	    		});
	    	 gmap.currentInfoWindow.push(newInfoWindow);	
	    	}
	       return this;
	    },
	    // bu metodda info window bir defa yaratılır daha sonra bu info windowu kullanarak
	    //sadece content ve position değiştirilir. ve map de bir tane info winow olur.
	    openInfoWindowOnMap: function(_content,location,callback,_callbackParameters){
	   	var newInfoWindow = null;
	    if(content != null){
	    	if(gmap.currentInfoWindow.length < 1){
	    		 newInfoWindow = new google.maps.InfoWindow({
	    			content:_content,maxWidth:300
	    		});
	    		gmap.currentInfoWindow.push(newInfoWindow);
	    	}
	    	newInfoWindow = gmap.currentInfoWindow[0];
	    	newInfoWindow.setContent(_content);	
	    	newInfoWindow.setPosition(location);
	   	    // burda info window um içindeki dom elementleri document in domu na kayıt olduktan sonra
	   	    // çalıştırılacak foksiyon buraya verilir
	   	    if(callback != null){
	   	    	google.maps.event.addListener(newInfoWindow,gmap.gmapEventType.DomReady,function(){
	   	    		callback(_callbackParameters);
	   	    	});
	   	      }
	   	      
	   	      newInfoWindow.open(gmap.currentMap[0]);
	       }
	   },
	   
	    
	    //geocoding and reverse geocoding map methods
	    setGeocederResults: function(results){
	    	gmap.geocoderResults.push(results);
	    },
	    // burda gelen sonucları hangi foksiyon kullanacaksa, onu buraya parametre olarak atıyoruz.(callback) 
	    getPossibleLocationWithLtdLng:function(_geocodeRequestType,location,callback){
	    	if(gmap.geocoder == null){
	    		gmap.geocoder = new google.maps.Geocoder();
	    	}
	   
	        if(_geocodeRequestType == gmap.geocoderRequestType.latLng){
	    	     gmap.geocoder.geocode({'latLng':location},function(results,status){
	    		 if(status == google.maps.GeocoderStatus.OK){
	    	        callback(results,location);
	    		  }
	    	    });
	    	}else if(_geocodeRequestType == gmap.geocoderRequestType.address){
	    		gmap.geocoder.geocode({'address':location},function(results,status){
	    		 if(status == google.maps.GeocoderStatus.OK){
	    	        callback(results,location);
	    		  }
	    	    });
	    	}
	    },
	    
	 }
	if(!window.$gmap) {
		window.$gmap = gmap
	}
})()