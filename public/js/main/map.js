$( document ).ready(function() {
	initMapToDraw();
});

function initMapToDraw(){
	var size = $( window ).height();
	$("#Map").height(size-15);
	infoWindow = new google.maps.InfoWindow;
    var mapOptions = {
      zoom: 5,
      center: new google.maps.LatLng(24.52713, -104.41406),
      mapTypeId: google.maps.MapTypeId.ROADMAP
    };
	map = new google.maps.Map(document.getElementById('Map'),mapOptions);
	
	bounds = new google.maps.LatLngBounds();

	if($("#inputStatus").val()=='ok'){
		var latitude  = $("#inputLatitude").val();
		var longitude = $("#inputLongitude").val();
		var fecha  	  = $("#inputFecha").val();
		var bateria	  = $("#inputBateria").val();
		var ubicacion = $("#inputUbicacion").val();
		var velocidad = $("#inputVelocidad").val();
		var evento    = $("#inputEvento").val();

		var content='<table width="350" class="table-striped" ><tr><td align="right"><b>Evento</b></td><td width="200" align="left">'+evento+'</td><tr>'+
				'<tr><td align="right"><b>Fecha</b></td><td align="left">'+fecha+' </td><tr>'+	    			
				'<tr><td align="right"><b>Velocidad</b></td><td align="left">'+velocidad+' kms/h.</td><tr>'+
				'<tr><td align="right"><b>Bateria</b></td><td align="left">'+bateria+' %</td><tr>'+
				'<tr><td align="right"><b>Ubicación</b></td><td align="left">'+ubicacion+'</td><tr>'+
				'</table>';	    	
		markerTable = new google.maps.Marker({
			map: map,
			position: new google.maps.LatLng(latitude,longitude),
			title: 	evento,
			icon: 	'/images/carMarker.png'
		});

		var marker = markerTable;
		var latLng = marker.getPosition();
		infoWindow.setContent(content);
		infoWindow.open(map, marker);
		map.setZoom(15);
		 
		map.panTo(latLng);   
	}else if($("#inputStatus").val()=='no-pos'){
		$("#divMessage").html("<p>El Activo no cuenta con posición válida.</p>");
		$("#modalMessages").modal("show");
	}else{
		$("#divMessage").html("<p>No hay información del Activo solicitado.</p>");
		$("#modalMessages").modal("show");		
	}
}

function infoMarkerTable(marker,content){	
    google.maps.event.addListener(marker, 'click',function() {
      if(infoWindow){infoWindow.close();infoWindow.setMap(null);}
      var marker = this;
      var latLng = marker.getPosition();
      infoWindow.setContent(content);
      infoWindow.open(map, marker);
      map.setZoom(13);
	  map.setCenter(latLng); 
	  map.panTo(latLng);     
	});
}