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
		getHorariosCbo(this.value);
	});

    $("#FormData").validate({
        rules: {
            inputDate    : {
              required: true,
              date: true
            },   
            inputhorario    : "required",
            /*inputTelContacto    : {
              required: true,
              number: true,
              minlength: 10,
              maxlength: 10
            },*/
            inputContacto: "required",
        },
        messages: {
            inputDate    : {
                 required: "Campo Requerido",
                 date: "Ingresar una fecha válida"
            },   
            inputhorario    : "Campo Requerido",
            inputContacto   : "required",
            /*inputTelContacto: {
              required  : "Campo Requerido",
              number    : "Este campo acepta solo números",
              minlength : "El Teléfono debe de ser de 10 dígitos",
              maxlength : "El Teléfono debe de ser de 10 dígitos"
            }    */       
        },
        
        submitHandler: function(form) {
            form.submit();
        }
    });  


    $('.upper').keyup(function()
    {
        $(this).val($(this).val().toUpperCase());
    });           	
}); 


function getHorariosCbo(inDate){    
    $("#divhorario").html("Cargando Información");
    $.ajax({
        url: "/callcenter/newservice/gethorarios",
        type: "GET",
        data: { dateID : inDate },
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