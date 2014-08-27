$().ready(function() {
	$("#FormData").validate({
        rules: {
            inputDescripcion:      "required",    
        },
        messages: {
            inputDescripcion   : "Campo Requerido",                           
        },
        submitHandler: function(form) {
            form.submit();
        }
    });	
});

function optionAll(inputCheck){
    if(inputCheck){
        $('.chkOn').prop('checked', true);         
    }else{
        $('.chkOn').prop('checked', false);
    }
}

function backToMain(){
  var mainPage = $("#hRefLinkMain").val();
  location.href= mainPage;
}
