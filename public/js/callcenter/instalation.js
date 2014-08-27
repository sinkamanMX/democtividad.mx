var geocoder;
var infoWindow;
var infoLocation;
var markers = [];
var bounds;
var arrayTravels=Array();
var mapGeo = null;

$( document ).ready(function() {
	initMapToDraw()


    $("#FormData").validate({
        rules: {
            inputLatitude : "required",
            inputLongitude: "required"       
        },
        // Se especifica el texto del mensaje a mostrar
        messages: {
            inputLatitude : "Campo Requerido",
            inputLongitude: "Campo Requerido"
        },
        
        submitHandler: function(form) {

        	if($("#inputDom").val()==0){
				if($("#inputLatitude").val()=="" || $("#inputLongitude").val()=="" ){
					alert("Debe de ingresar un Centro de Instalación");
        			return false;
        		}
        	}else{
				if($("#inputLatitude").val()=="" || $("#inputLongitude").val()=="" ){
					alert("Debe de ubicar el punto exacto para la Instalación");
        			return false;
        		}        		
        	}
            form.submit();
        }
    });  	
});  	


function initMapToDraw(){
	if(mapGeo==null){
		geocoder = new google.maps.Geocoder();
		var mapOptions = {
			zoom: 5,
			center: new google.maps.LatLng(19.435113686545755,-99.13316173010253)
		};
		mapGeo = new google.maps.Map(document.getElementById('myMapDraw'),mapOptions);	
		google.maps.event.addListener(mapGeo, 'click', function(event) {
			$("#inputLatitude").val(event.latLng.lat());
			$("#inputLongitude").val(event.latLng.lng());
			drawGeos()
		});	
	}else{
		google.maps.event.trigger(mapGeo, 'resize');
	}
	var inputDir = $("#inputDom").val();
	var inputDirO = $("#inputOtherDom").val();
	if(inputDir=='1' || inputDirO=='2'){
		var address = $("#inputDir").val();
	    geocoder.geocode({ 'address': address }, function (results, status) {
	        if (status == google.maps.GeocoderStatus.OK) {
	            var latitude  = results[0].geometry.location.lat();
	            var longitude = results[0].geometry.location.lng();

				$("#inputLatitude").val(latitude);
				$("#inputLongitude").val(longitude);
				drawGeos()
	        } else {
	            alert("Request failed.")
	        }
	    });		
	}else{
		drawGeos()
	}

	bounds = new google.maps.LatLngBounds();	
}


function drawGeos(){
	var latitude  = $("#inputLatitude").val();
	var longitude = $("#inputLongitude").val();

	if(latitude!="" && latitude!="0" && 
		longitude!="" && longitude!="0"){
		addMaker(latitude,longitude); 	
	}
}

function addMaker(latitud,longitud){
	removeMap();
	var latitude  = latitud;
	var longitude = longitud;

	var position = new google.maps.LatLng(latitude, longitude);

	marker1 = new google.maps.Marker({
	    map: mapGeo,
	    position: position,
	    draggable:true,
		animation: google.maps.Animation.DROP,
    });	
	markers.push(marker1);
    google.maps.event.addListener(marker1, 'click', toggleBounce);	

    google.maps.event.addListener(marker1, "dragend", function(event) {
		$("#inputLatitude").val(event.latLng.lat());
		$("#inputLongitude").val(event.latLng.lng());
		drawGeos()
    }); 
    mapGeo.setZoom(18);
    mapGeo.setCenter(position); 
    mapGeo.panTo(position);        
}


function toggleBounce() {
  if (marker.getAnimation() != null) {
    marker.setAnimation(null);
  } else {
    marker.setAnimation(google.maps.Animation.BOUNCE);
  }
}

function removeMap(){
	if(markers || markers.length>-1){
		for (var i = 0; i < markers.length; i++) {
	          markers[i].setMap(null);
		}	
		markers = [];
	}
}

function centerInstalacion(idCenter){
	$("#").html("");
	var centerSelected = $("#txt"+idCenter).val();
	var centerInfo 	= centerSelected.split('|');

	var position = new google.maps.LatLng(centerInfo[9], centerInfo[10]);
	$("#inputLatitude").val(centerInfo[9]);
	$("#inputLongitude").val(centerInfo[10]);	

	var Direccion = "<span>"+centerInfo[2]+", No ext. "+centerInfo[3]+", No Int. "+centerInfo[4]+" </span>"+
					"<span>Col."+centerInfo[5]+", Mun. "+centerInfo[6]+" </span>"+
					"<span>Edo."+centerInfo[7]+", C.P "+centerInfo[8]+" </span>";

	$("#dataCentro").html("<h3>"+centerInfo[1]+"</h3>"+Direccion);
	drawGeos()
}