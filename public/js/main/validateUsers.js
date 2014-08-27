$().ready(function() {
	$("#FormData").validate({
        rules: {
            inputSucursal:      "required",
            inputUsuario:   "required",
            inputPassword:  "required",
            inputPasswordC: {
                required: true,
                equalTo: "#inputPassword",
            },  
            inputNombre :   "required",
            inputApps   :   "required",          
            inputEstatus:   "required",
            inputOperaciones:"required",
            inputEmail  : {
                required: true,
                email: true
            },              
            inputMovil    : {
                required: true,
                number: true,
                minlength: 10,
                maxlength: 10
            }       
        },
        messages: {
            inputSucursal   : "Campo Requerido",
            inputUsuario    : "Campo Requerido",
            inputPassword   : "Campo Requerido",
            inputNombre     : "Campo Requerido",
            inputApps       : "Campo Requerido",
            inputEstatus    : "Campo Requerido",
            inputOperaciones: "Campo Requerido",
            inputEmail      : {
                required: "Campo Requerido",
                email: "Debe de ingresar un mail válido"
            },
            inputPasswordC  : {
                required    : "Campo Requerido",
                equalTo     : "La contraseña no coincide."
            }, 
            inputMovil    : {
                required  : "Campo Requerido",
                number    : "Este campo acepta solo números",
                minlength : "El Teléfono debe de ser de 10 dígitos",
                maxlength : "El Teléfono debe de ser de 10 dígitos"
            },                           
        },
        submitHandler: function(form) {
            form.submit();
        }
    });	

    if($("#catId").val()>-1){
        $("#inputPassword").rules("remove", "required");
        $("#inputPasswordC").rules("remove", "required");          
    }
});

function addValidatePass(valueInput){
    if(valueInput!=""){
        $("#inputPassword").rules("add",  {required:true});
        $("#inputPasswordC").rules("add", {required: true,equalTo: "#inputPassword"});   
    }else{
        $("#inputPassword").rules("remove", "required");
        $("#inputPasswordC").rules("remove", "required");
    }
}

function deleteRowRel(){   
    var idItem = $("#catId").val();
    $.ajax({
        url: "/main/users/getinfo",
        type: "GET",
        dataType : 'json',
        data: { catId : idItem, 
                optReg: 'deleteRel'},
        success: function(data) {
            var result = data.answer; 

            if(result == 'deleted'){
                location.href = '/main/users/getinfo?catId='+idItem;
            }else if(result == 'problem'){
                alert("hubo problema");          
            }else{
                alert("no hay data");          
            }
        }
    });    
}

function openSearch(){
    $('#iFrameSearch').attr('src','/main/users/searchactivos');
    $("#MyModalSearch").modal("show");
}

function assignValue(nameValue,IdValue){
    $("#inputIdAssign").val(IdValue);
    $("#inputSearch").val(nameValue);
    $("#MyModalSearch").modal("hide");
}

function backToMain(){
  var mainPage = $("#hRefLinkMain").val();
  location.href= mainPage;
}


function optionAll(inputCheck){
    if(inputCheck){
        $('.chkOn').prop('checked', true);         
    }else{
        $('.chkOn').prop('checked', false);
    }
}
