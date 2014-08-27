var map = null;
var geocoder;
var infoWindow;
var infoLocation;
var markers = [];
var bounds;
var arrayTravels="";
var mon_timer=60;
var startingOp=true;

$( document ).ready(function() {
	$('#tabs').tab();
	initMapToDraw();
	$('#slideTimeUp').slider({
		formater: function(value) {
			mon_timer = value;
			timerUpdate()
			return 'Actualizar cada ' + value + ' segs.';			
		},
		value: 60
	}).on('slide', function(ev){
		var valorSegs = ev.value;
		mon_timer = valorSegs;
		timerUpdate()
    	$("#labelMinutes").html(valorSegs);
  	});

	$("#countdown").countdown360({
	    radius: 30,
	    seconds: 20,
	    seconds: mon_timer,
	    label: ['seg', 'segs'],
	    fontColor: '#FFFFFF',
	    autostart: false,
	    onComplete: function () {
	      mapLoadData()
	    }		
	}).start()  

	mapLoadData();
	setDataTable();
});

function timerUpdate(){
	if(mon_timer>0){
		$("#countdown").countdown360({
		    radius: 30,
		    seconds: 20,
		    label: ['seg', 'segs'],
		    fontColor: '#FFFFFF',
		    autostart: false,
		    onComplete: function () {
		      mapLoadData()
		    }		
		}).addSeconds(mon_timer);
	}else{
		$("#countdown").countdown360({
		    radius: 30,
		    seconds: 20,
		    label: ['seg', 'segs'],
		    fontColor: '#FFFFFF',
		    autostart: false,
		    onComplete: function () {
		      mapLoadData()
		    }		
		}).stop();
	}
}

function initMapToDraw(){
	infoWindow = new google.maps.InfoWindow;
    var mapOptions = {
      zoom: 5,
      center: new google.maps.LatLng(24.52713, -104.41406),
      mapTypeId: google.maps.MapTypeId.ROADMAP
    };
	map = new google.maps.Map(document.getElementById('Map'),mapOptions);
	
	bounds = new google.maps.LatLngBounds();
}

function mapClearMap(){
	if(markers || markers.length>-1){
		for (var i = 0; i < markers.length; i++) {
	          markers[i].setMap(null);
		}	
		markers = [];
	}
	arrayTravels=null;
}

function mapLoadData(){
	if(startingOp){
		var idObject = $("#inputId").val();
		mapClearMap();
		$.ajax({
			type: "POST",
	        url: "/callcenter/rastreo/lposition",
			data: { strInput: idObject},
	        success: function(datos){
				var result = datos;
				if(result!= ""){
					arrayTravels = result;
					printTravelsMap();
				}
	        }
		});
	}
}

/*
				0   $dataActivo['ID_ACTIVO']."|".
				1   $dataActivo['EVENTO']."|".
				2	$dataActivo['LATITUD']."|".
				3	$dataActivo['LONGITUD']."|".
				4	$dataActivo['VELOCIDAD']."|".
				5	$dataActivo['UBICACION']."|".
				6	$dataActivo['ANGULO']."|".
				7	$dataActivo['BATERIA'];
				8	$dataActivo['FECHA_GPS'];
*/
function printTravelsMap(){
	var travelInfo = arrayTravels.split('|');
    var content     = '';
    var markerTable = null;

    if(travelInfo[2]!="null" && travelInfo[3]!="null"){
    	var latitude  = travelInfo[2]; 
    	var longitude = travelInfo[3]; 

    	content='<table width="350" class="table-striped" ><tr><td align="right"><b>Evento</b></td><td width="200" align="left">'+travelInfo[1]+'</td><tr>'+
    			'<tr><td align="right"><b>Velocidad</b></td><td align="left">'+travelInfo[4]+'</td><tr>'+
    			'<tr><td align="right"><b>Fecha</b></td><td align="left">'+travelInfo[8]+' %</td><tr>'+
    			'<tr><td align="right"><b>Bateria</b></td><td align="left">'+travelInfo[7]+' %</td><tr>'+
    			'<tr><td align="right"><b>Velocidad</b></td><td align="left">'+travelInfo[4]+' kms/h.</td><tr>'+
    			'<tr><td align="right"><b>Ubicación</b></td><td align="left">'+travelInfo[5]+'</td><tr>'+
    			'</table>';
		markerTable = new google.maps.Marker({
			map: map,
			position: new google.maps.LatLng(latitude,longitude),
			title: 	travelInfo[1],
			icon: 	'/images/carMarker.png'
		});
		markers.push(markerTable);
		infoMarkerTable(markerTable,content);		    
    }			
	
	fitBoundsToVisibleMarkers();
	timerUpdate();
}

function fitBoundsToVisibleMarkers() {
	if(markers.length>0){
	    for (var i=0; i<markers.length; i++) {
			bounds.extend( markers[i].getPosition() );
	    }
	    if(markers.length==1){
			map.setZoom(13);
		  	map.panTo(markers[0].getPosition());
	    }else{
			map.fitBounds(bounds);
	    }
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


function setDataTable(){
	$('#tableRecorridoToday').dataTable( {
		"sDom": "<'row'<'span6'l><'span6'f>r>t<'row'<'span6'i><'span6'p>>",
		"sPaginationType": "bootstrap",
		"bDestroy": true,
		"bLengthChange": false,
		"bPaginate": true,
		"bFilter": true,
		"bSort": true,
		"bJQueryUI": true,
		"iDisplayLength": 5,      
		"bProcessing": true,
		"bAutoWidth": false,
		"bSortClasses": false,
	      "oLanguage": {
	          "sInfo": "Mostrando _TOTAL_ registros (_START_ a _END_)",
	          "sEmptyTable": "Sin registros.",
	          "sInfoEmpty" : "Sin registros.",
	          "sInfoFiltered": " - Filtrado de un total de  _MAX_ registros",
	          "sLoadingRecords": "Leyendo información",
	          "sProcessing": "Procesando",
	          "sSearch": "Buscar:",
	          "sZeroRecords": "Sin registros",
	          "oPaginate": {
	            "sPrevious": "Anterior",
	            "sNext": "Siguiente"
	          }          
	      }
	} );	

	$('#tableRecorridoA').dataTable( {
		"sDom": "<'row'<'span6'l><'span6'f>r>t<'row'<'span6'i><'span6'p>>",
		"sPaginationType": "bootstrap",
		"bDestroy": true,
		"bLengthChange": false,
		"bPaginate": true,
		"bFilter": true,
		"bSort": true,
		"bJQueryUI": true,
		"iDisplayLength": 5,      
		"bProcessing": true,
		"bAutoWidth": false,
		"bSortClasses": false,
	      "oLanguage": {
	          "sInfo": "Mostrando _TOTAL_ registros (_START_ a _END_)",
	          "sEmptyTable": "Sin registros.",
	          "sInfoEmpty" : "Sin registros.",
	          "sInfoFiltered": " - Filtrado de un total de  _MAX_ registros",
	          "sLoadingRecords": "Leyendo información",
	          "sProcessing": "Procesando",
	          "sSearch": "Buscar:",
	          "sZeroRecords": "Sin registros",
	          "oPaginate": {
	            "sPrevious": "Anterior",
	            "sNext": "Siguiente"
	          }          
	      }
	} );		
}

function MostrarMapa(idDay){	
	$("#countdown").hide('slow');
	startingOp=false;
	if(idDay==0){
		$("#btnShow").hide('slow');
		$("#btnHide").show('slow');
		$(".aShow").show('slow');		
	}else if(idDay==1){
		$("#btnShowY").hide('slow');
		$("#btnHideY").show('slow');
		$(".aShowY").show('slow');
	}
	drawRecorrido(idDay);
}

function OcultarRecorrido(idDay){
	$("#countdown").show('slow');
	startingOp=true;
	if(idDay==0){
		$("#btnShow").show('slow');
		$("#btnHide").hide('slow');
		$(".aShow").hide('slow');
	}else if(idDay==1){
		$("#btnShowY").show('slow');
		$("#btnHideY").hide('slow');
		$(".aShowY").hide('slow');

	}	
	mapLoadData();
}

function drawRecorrido(idDay){
	mapClearMap();
	var result = '';
	if(idDay==0){
		result = $("#positionsToday").html();
	}else{
		result = $("#positionsYest").html();
	}
	
	var	arrayTravels=new Array();
      	arrayTravels=result.split('!');	
    for(var i=0;i<arrayTravels.length;i++){    
    	var travelInfo = arrayTravels[i].split('|');
      	var markerTable = null; 	

	    if(travelInfo[2]!="null" && travelInfo[3]!="null"){
	    	var latitude  = parseFloat(travelInfo[2]); 
	    	var longitude = parseFloat(travelInfo[3]); 

	    	var content='<table width="350" class="table-striped" ><tr><td align="right"><b>Evento</b></td><td width="200" align="left">'+travelInfo[1]+'</td><tr>'+
	    			'<tr><td align="right"><b>Velocidad</b></td><td align="left">'+travelInfo[4]+'</td><tr>'+
	    			'<tr><td align="right"><b>Fecha</b></td><td align="left">'+travelInfo[8]+' </td><tr>'+
	    			'<tr><td align="right"><b>Bateria</b></td><td align="left">'+travelInfo[7]+' %</td><tr>'+
	    			'<tr><td align="right"><b>Velocidad</b></td><td align="left">'+travelInfo[4]+' kms/h.</td><tr>'+
	    			'<tr><td align="right"><b>Ubicación</b></td><td align="left">'+travelInfo[5]+'</td><tr>'+
	    			'</table>';			
			markerTable = new google.maps.Marker({
				map: map,
				position: new google.maps.LatLng(latitude,longitude),
				title: 	travelInfo[1],
				icon: 	'/images/carMarker.png'
			});
			markers.push(markerTable);
            infoMarkerTable(markerTable,content);   
            bounds.extend( markerTable.getPosition() );		    
	    }	      	

	    if(arrayTravels.length>1){
		    var iconsetngs = {
		        path: google.maps.SymbolPath.FORWARD_OPEN_ARROW,
		        strokeColor: '#155B90',
		        fillColor: '#155B90',
		        fillOpacity: 1,
		        strokeWeight: 4        
		    };

		    var line = new google.maps.Polyline({
		      map: map,
		      path: markers,
		      strokeColor: "#098EF3",
		      strokeOpacity: 1.0,
		      strokeWeight: 2,
		        icons: [{
		            icon: iconsetngs,
		            repeat:'35px',         
		            offset: '100%'}]
		    }); 	    	
	      map.fitBounds(bounds);  
	    }else if(arrayTravels.length==1){
	      map.setZoom(13);
	      map.panTo(markerTable.getPosition());  
	    }
    }  	
}

function centerDataMap(nameInput,idDay){
	var valuesDet ='';
	if(idDay==0){
		valuesDet = $("#rec"+nameInput).val();
	}else{
		valuesDet = $("#recYes"+nameInput).val();
	}
	var travelInfo = valuesDet.split('|');
    var content     = '';
    var markerTable = null;

    if(travelInfo[2]!="null" && travelInfo[3]!="null"){
    	var latitude  = travelInfo[2]; 
    	var longitude = travelInfo[3]; 

    	content='<table width="350" class="table-striped" ><tr><td align="right"><b>Evento</b></td><td width="200" align="left">'+travelInfo[1]+'</td><tr>'+
    			'<tr><td align="right"><b>Velocidad</b></td><td align="left">'+travelInfo[4]+'</td><tr>'+
    			'<tr><td align="right"><b>Fecha</b></td><td align="left">'+travelInfo[8]+' </td><tr>'+
    			'<tr><td align="right"><b>Bateria</b></td><td align="left">'+travelInfo[7]+' %</td><tr>'+
    			'<tr><td align="right"><b>Velocidad</b></td><td align="left">'+travelInfo[4]+' kms/h.</td><tr>'+
    			'<tr><td align="right"><b>Ubicación</b></td><td align="left">'+travelInfo[5]+'</td><tr>'+
    			'</table>';
		markerTable = new google.maps.Marker({
			map: map,
			position: new google.maps.LatLng(latitude,longitude),
			title: 	travelInfo[1],
			icon: 	'/images/carMarker.png'
		});
		markers.push(markerTable);
		infoMarkerTable(markerTable,content);	

		if(infoWindow){infoWindow.close();infoWindow.setMap(null);}
	    infoWindow.setContent(content);
      	infoWindow.open(map, markerTable);	
		map.setZoom(20);
	  	map.panTo(markerTable.getPosition()); 
    }			
}

function sendCommand(idCommand){
	var idObject = $("#inputId").val();

    $.ajax({
		url: "/callcenter/rastreo/sendcommand",
        type: "GET",
        dataType : 'json',
        data: { strInput  : idObject, 
        		strCommand: idCommand },
        success: function(data) {
            var result = data.answer; 
            if(result == 'insert'){
                $("#divMessage").html("<p>Comandos enviado correctamente.</p>");
            }else{
            	$("#divMessage").html("<p>No fue posible enviar el comando, favor de intentar mas tarde.</p>");
            }

            $("#modalMessages").modal("show");
        }
    });
}