$( document ).ready(function() {
    $("#FormData").validate({
        rules: {
            inputSerie  : "required",
            inputColor  : "required",
            inputAno    : "required",
            inputPlacas : "required",
            inputMarca  : "required",
            inputModelo : "required"
        },
        messages: {
            inputSerie  : "Campo Requerido",
            inputColor  : "Campo Requerido",
            inputAno    : "Campo Requerido",
            inputPlacas : "Campo Requerido",
            inputMarca  : "Campo Requerido",
            inputModelo : "Campo Requerido"
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