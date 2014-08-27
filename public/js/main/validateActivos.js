$().ready(function() {
	$("#FormData").validate({
        rules: {
            inputMarca	: "required",
            inputModelo	: "required",
            inputColor  : "required",
            inputDesc   : "required",
            inputPlacas : "required",
            inputMotor  : "required"
        },
        messages: {
            inputMarca	: "Campo Requerido",
            inputModelo	: "Campo Requerido",
            inputColor	: "Campo Requerido",
            inputDesc   : "Campo Requerido",
            inputPlacas : "Campo Requerido",
            inputMotor  : "Campo Requerido"
        },
        
        submitHandler: function(form) {
            form.submit();
        }
    });	
});

function backToMainModule(){
	var mainPage = $("#hRefLinkMain").val();
	location.href= mainPage;
}