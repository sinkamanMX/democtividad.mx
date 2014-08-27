$().ready(function() { 
    $("#btnSearch").click(function() { openSearch(); return false; });
    $("#btnDelRel").click(function() { deleteRowRel(); return false; });

	$("#FormData").validate({
        rules: {
            inputMarca	: "required",
            inputModelo	: "required",
            inputDesc   : "required",
            inputImei   : {
                required: true,
                minlength: 12,
                maxlength: 20
            },
            inputTel    : {
                required: true,
                number: true,
                minlength: 10,
                maxlength: 10
            }
        },
        messages: {
            inputMarca	: "Campo Requerido",
            inputModelo	: "Campo Requerido",
            inputDesc	: "Debe de seleccionar una opción",
            inputImei    : {
                required  : "Campo Requerido",
                minlength : "El IMEI debe mímimo de 12 dígitos",
                maxlength : "El IMEI debe máximo de 20 dígitos"
              }, 
            inputTel    : {
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

    $(".chzn-select").chosen();

    $('#dataTable').dataTable( {
        "sDom": "<'row'<'span6'l><'span6'f>r>t<'row'<'span6'i><'span6'p>>",
        "sPaginationType": "bootstrap",
        "bDestroy": true,
        "bLengthChange": false,
        "bPaginate": true,
        "bFilter": true,
        "bSort": true,
        "bJQueryUI": true,
        "iDisplayLength": 10,      
        "bProcessing": true,
        "bAutoWidth": true,
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
});

function backToMain(){
	var mainPage = $("#hRefLinkMain").val();
	location.href= mainPage;
}

function deleteRow(){	
	var idItem = $("#inputDelete").val();
    $.ajax({
        url: "/main/phones/getinfo",
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
        url: "/main/phones/getinfo",
        type: "GET",
        dataType : 'json',
        data: { catId : idItem, 
                optReg: 'deleteRel'},
        success: function(data) {
            var result = data.answer; 

            if(result == 'deleted'){
                location.href = '/main/phones/getinfo?catId='+idItem;
            }else if(result == 'problem'){
                alert("hubo problema");          
            }else{
                alert("no hay data");          
            }
        }
    });    
}

function openSearch(){
    $('#iFrameSearch').attr('src','/main/phones/searchactivos');
    $("#MyModalSearch").modal("show");
}

function assignValue(nameValue,IdValue){
    $("#inputIdAssign").val(IdValue);
    $("#inputSearch").val(nameValue);
    $("#MyModalSearch").modal("hide");
}

function backToMainModule(){
    var url = $("#hRefLinkMain").val();
    location.href=url;    
}