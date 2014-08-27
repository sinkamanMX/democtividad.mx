$.validator.addMethod('IP4Checker', function(value) {
    var ip = "^(([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.){3}([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])$";
    return value.match(ip);
}, 'IP inválida');

$().ready(function() {
    $("#tabs").tab();
    /*$(".chosen-select").chosen();*/
    $("#btnSearch").click(function() { openSearch(); return false; });
    $("#btnDelRel").click(function() { deleteRowRel(); return false; });
	$("#FormData").validate({
        rules: {
            inputMarca	: "required",
            inputModelo	: "required",
            inputServidor: "required",
            inputDesc   : "required",
            inputImei   : {
                required: true,
                number: true,
                minlength: 12,
                maxlength: 20
            },
            inputIp:{
                required: true,
                IP4Checker: true
            },
            inputPuerto : {
              required: true,
              number: true
            }
        },
        
        // Se especifica el texto del mensaje a mostrar
        messages: {
            inputMarca	: "Campo Requerido",
            inputModelo	: "Campo Requerido",
            inputServidor	: "Campo Requerido",
            inputDesc	: "Debe de seleccionar una opción",
            inputImei    : {
                required  : "Campo Requerido",
                number    : "Este campo acepta solo números",
                minlength : "El IMEI debe mímimo de 12 dígitos",
                maxlength : "El IMEI debe máximo de 20 dígitos"
              }, 
            inputIp    :{
                required  : "Campo Requerido",
                IP4Checker: "IP inválida"
            },
            inputPuerto : {
                required  : "Campo Requerido",
                number    : "Este campo acepta solo números",
            }
        },
        
        submitHandler: function(form) {
            form.submit();
        }
    });	
});

function backToMain(){
	var mainPage = $("#hRefLinkMain").val();
	location.href= mainPage;
}

function deleteRow(){	
	var idItem = $("#inputDelete").val();
    $.ajax({
        url: "/main/equipment/getinfo",
        type: "GET",
        dataType : 'json',
        data: { catId : idItem, 
        		optReg: 'delete'},
        success: function(data) {
            var result = data.answer; 

            if(result == 'deleted'){
            	$("#modalConfirmDelete").modal('hide'); 
            }else if(result == 'problem'){
                alert("hubo problema");          
            }else{
                alert("no hay data");          
            }
        }
    });    
}

function deleteRowRel(){   
    var idItem = $("#catId").val();
    $.ajax({
        url: "/main/equipment/getinfo",
        type: "GET",
        dataType : 'json',
        data: { catId : idItem, 
                optReg: 'deleteRel'},
        success: function(data) {
            var result = data.answer; 

            if(result == 'deleted'){
                location.href = '/main/equipment/getinfo?catId='+idItem;
            }else if(result == 'problem'){
                alert("hubo problema");          
            }else{
                alert("no hay data");          
            }
        }
    });    
}

function openSearch(){
    $('#iFrameSearch').attr('src','/main/equipment/searchactivos');
    $("#MyModalSearch").modal("show");
}

function assignValue(nameValue,IdValue){
    $("#inputIdAssign").val(IdValue);
    $("#inputSearch").val(nameValue);
    $("#MyModalSearch").modal("hide");
}