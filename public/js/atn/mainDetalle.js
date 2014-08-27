$( document ).ready(function() { 
    var nowTemp = new Date();
    var now = new Date(nowTemp.getFullYear(), nowTemp.getMonth(), nowTemp.getDate(), 0, 0, 0, 0);

    $('#inputFecha').datetimepicker({
        format: "yyyy-mm-dd HH:ii",
        autoclose: true,
        startDate: now,
          onRender: function(date) {
            return date.valueOf() < now.valueOf() ? 'disabled' : '';
          }
    }).on('changeDate', function(ev) {
		$("#inputChangeDate").val("1"); 
    });

    $("#FormData").validate({
        rules: {
            inputFecha      : "required",   
            inputEstatus    : "required"
        },
        messages: {
            inputFecha      : "Campo Requerido",   
            inputEstatus    : "Campo Requerido"
        },
        
        submitHandler: function(form) {
            form.submit();
        }
    });   

});

function setEditDate(){
  $( ".divInput" ).show('slow');
  $( ".divLabel" ).hide('hide');
}