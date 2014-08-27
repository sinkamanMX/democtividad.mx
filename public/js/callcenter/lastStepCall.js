$( document ).ready(function() {
    var nowTemp = new Date();
    var now = new Date(nowTemp.getFullYear(), nowTemp.getMonth(), nowTemp.getDate(), 0, 0, 0, 0);

    $('#inputDate').datepicker({
        format: 'yyyy-mm-dd',
        autoclose: true,
          onRender: function(date) {
            return date.valueOf() < now.valueOf() ? 'disabled' : '';
          }
    }).on('changeDate', function(ev) {
	
	 });

    $("#FormData").validate({
        rules: {
            inputDate    : {
              required: true,
              date: true
            },   
            inputhorario    : "required",
            inputTelContacto    : {
              required: true,
              number: true,
              minlength: 10,
              maxlength: 10
            },
            inputContacto: "required",
        },
        messages: {
            inputDate    : {
                 required: "Campo Requerido",
                 date: "Ingresar una fecha válida"
            },   
            inputhorario    : "Campo Requerido",
            inputContacto   : "required",
            inputTelContacto: {
              required  : "Campo Requerido",
              number    : "Este campo acepta solo números",
              minlength : "El Teléfono debe de ser de 10 dígitos",
              maxlength : "El Teléfono debe de ser de 10 dígitos"
            }           
        },
        
        submitHandler: function(form) {
            form.submit();
        }
    });     	
}); 

function getHorariosCbo(inValue){    
    $("#divhorario").html("Cargando Información");
    var dateCita = $('#inputDate').val();
    $.ajax({
        url: "/callcenter/callservice/gethorarios",
        type: "GET",
        data: { dateID : dateCita,
                idUser : inValue },
        success: function(data) { 
            $("#divhorario").html("");

            var dataCbo = '<select class=" m-wrap id="inputhorario" name="inputhorario">';
            if(data!="no-info"){
                dataCbo += '<option value="">Seleccionar una opción</option>'+data+'</select>';
            }else{
                dataCbo += '<option value="">Sin Información</option>';
            }
            dataCbo += '</select>';
                                    
            $("#divhorario").html(dataCbo);
        }
    });
}   