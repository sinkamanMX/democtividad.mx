$( document ).ready(function() {
    var nowTemp = new Date();
    var now = new Date(nowTemp.getFullYear(), nowTemp.getMonth(), nowTemp.getDate(), 0, 0, 0, 0);
    var dateInter  = parseInt(nowTemp.getMonth())+1;  
    var todayMonth = (dateInter<10) ? "0"+dateInter : dateInter;
    var todayDay   = (nowTemp.getDate()<10) ? "0"+nowTemp.getDate(): nowTemp.getDate();        

    $("#inputFechaIn").val(nowTemp.getFullYear()+"-"+todayMonth+"-"+todayDay+ ' 00:00');    
    $("#inputFechaFin").val(nowTemp.getFullYear()+"-"+todayMonth+"-"+todayDay+ ' 23:59');    

    var checkin = $('#inputFechaIn').datetimepicker({
        format: "yyyy-mm-dd HH:ii",
        showMeridian: false,
        autoclose: true,
        todayBtn: true
    }).on('changeDate', function(ev) {

      if(ev.date.valueOf() > $('#inputFechaFin').datetimepicker('getDate').valueOf()){
        $('#inputFechaFin').datetimepicker('setDate', ev.date);   
      }

      $('#inputFechaFin').datetimepicker('setStartDate', ev.date);      
      $('#inputFechaFin').prop('disabled', false);
      $('#inputFechaFin')[0].focus();      
    });

    var checkout = $('#inputFechaFin').datetimepicker({
        format: "yyyy-mm-dd HH:ii",
        showMeridian: false,
        autoclose: true,
        todayBtn: true
    }).on('changeDate', function(ev) {
      if(ev.date.valueOf() > $('#inputFechaIn').datetimepicker('getDate').valueOf()){
        //$('#inputFechaIn').datetimepicker('setDate', ev.date);  
      }
      $('#inputFechaIn').datetimepicker('setEndDate', ev.date);
    }); 

      $("#FormData").validate({

      });       

      $('#dataTable').dataTable( {
        "sDom": "<'row'<'span6'l><'span6'f>r>t<'row'<'span6'i><'span6'p>>",
        "sPaginationType": "bootstrap",
        "bDestroy": true,
        "bLengthChange": false,
        "bPaginate": true,
        "bFilter": false,
        "bSort": true,
        "bJQueryUI": true,
        "iDisplayLength": 5,      
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