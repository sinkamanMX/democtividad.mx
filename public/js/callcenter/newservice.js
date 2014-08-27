$( document ).ready(function() {
    var nowTemp = new Date();
    var now = new Date(nowTemp.getFullYear(), nowTemp.getMonth(), nowTemp.getDate(), 0, 0, 0, 0);

    $('#inputNac').datepicker({
        format: 'yyyy-mm-dd',
        autoclose: true,
          onRender: function(date) {
            return date.valueOf() > now.valueOf() ? 'disabled' : '';
          }
    });
      $("#FormData").validate({
          rules: {
              inputTipo   : "required",
              inputTipService:"required",
              inputNombre : "required",
              inputApps   : "required",
              inputStreet : "required",
              inputEstado : "required",
              inputMunicipio: "required",
              inputcolonia: "required",
              inputCP     : "required",
              inputDom    : "required",
              inputNoExt  : "required", 
              inputGenero : "required",
              /*inputTel    : {
                required: true,
                number: true,
                minlength: 10,
                maxlength: 10
              },*/
              inputEmail  : {
                required: true,
                email: true
              },
              inputEmailConf  : {
                equalTo: "#inputEmail",
                email: true
              },                             
          },
          // Se especifica el texto del mensaje a mostrar
          messages: {
              inputTipo   : "Campo Requerido",
              inputTipService: "Campo Requerido",
              inputNombre : "Campo Requerido",
              inputApps   : "Campo Requerido",
              inputStreet : "Campo Requerido",
              inputEstado : "Campo Requerido",
              inputMunicipio: "Campo Requerido",
              inputcolonia: "Campo Requerido",
              inputCP     : "Campo Requerido",
              inputDom    : "Campo Requerido",
              inputNoExt  : "Campo Requerido", 
              inputGenero : "required",
              /*inputTel    : {
                required  : "Campo Requerido",
                number    : "Este campo acepta solo números",
                minlength : "El Teléfono debe de ser de 10 dígitos",
                maxlength : "El Teléfono debe de ser de 10 dígitos"
              },*/
              inputEmail  : {
                required: "Campo Requerido",
                email: "Debe de ingresar un mail válido"
              },
              inputEmailConf  : {
                required  : "Campo Requerido",
                equalTo   : "El email no coincide.",
                email: "Debe de ingresar un mail válido"
              },     
              inputRFC     : "Campo Requerido",
              inputRazon   : "Campo Requerido",       
              inputStreetO : "Campo Requerido",
              inputEstadoO : "Campo Requerido",
              inputMunicipioO: "Campo Requerido",
              inputcoloniaO: "Campo Requerido",
              inputCPO     : "Campo Requerido",
              inputNoExtO  : "Campo Requerido"   
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

function changeTypePerson(value){
  $("#FormData").validate().resetForm();  
  if(value=='M'){
    $("#divMoral").show('slow');
    $("#inputRFC").rules("add", {required:true});
    $("#inputRazon").rules("add", {required:true});     
  }else{
    $("#divMoral").hide('slow');
    $("#inputRFC").rules("remove", "required");
    $("#inputRazon").rules("remove", "required");     
  }  
}

function differentDom(value){
  $("#FormData").validate().resetForm();  
  if(value==0){
    $("#cboOptsDom").show('slow');    
  }else{
    $("#cboOptsDom").hide('slow');    
  }
  showFormDirection(0);
}

function showFormDirection(value){  
  if(value==2){
    $("#divDifDom").show('slow');
    setValidateForm(1);
  }else{
    $("#divDifDom").hide('slow');
    setValidateForm(0);
  }
}

function setValidateForm(optionValue){
  if(optionValue==0){
    $("#inputStreetO").rules("remove", "required");
    $("#inputEstadoO").rules("remove", "required");
    $("#inputMunicipioO").rules("remove", "required");
    $("#inputcoloniaO").rules("remove", "required");
    $("#inputCPO").rules("remove", "required");
    $("#inputNoExtO").rules("remove", "required");
  }else if(optionValue==1){
    $("#inputStreetO").rules("add", {required:true});
    $("#inputEstadoO").rules("add", {required:true});
    $("#inputMunicipioO").rules("add", {required:true});
    $("#inputcoloniaO").rules("add", {required:true});
    $("#inputCPO").rules("add", {required:true});
    $("#inputNoExtO").rules("add", {required:true});
  }   
}

function searchCp(typeSearch){
  var cp = 0;

  if(typeSearch==0){
    cp = $("#inputCP").val();
  }else{
    cp = $("#inputCPO").val();
  }

  $("#inputTipo").rules("remove", "required");
  $("#inputTipService").rules("remove", "required");
  $("#inputNombre").rules("remove", "required");
  $("#inputApps").rules("remove", "required");
  $("#inputStreet").rules("remove", "required");
  $("#inputEstado").rules("remove", "required");
  $("#inputMunicipio").rules("remove", "required");
  $("#inputcolonia").rules("remove", "required");
  $("#inputCP").rules("remove", "required");
  $("#inputDom").rules("remove", "required");
  $("#inputNoExt").rules("remove", "required");
  $("#inputGenero").rules("remove", "required");
  $("#inputEmail").rules("remove", "required");
  $("#inputEmailConf").rules("remove", "required"); 
  $("#inputStreetO").rules("remove", "required");
  $("#inputEstadoO").rules("remove", "required");
  $("#inputMunicipioO").rules("remove", "required");
  $("#inputcoloniaO").rules("remove", "required");
  $("#inputCPO").rules("remove", "required");
  $("#inputNoExtO").rules("remove", "required");  

  $("#inputOpr").val("searchCP");
  $("#inputSearch").val(cp);
  $("#typeSearch").val(typeSearch);
  $("#FormData").submit();
}