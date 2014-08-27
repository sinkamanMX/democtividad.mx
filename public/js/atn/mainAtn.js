var source = new Array();
    source[0] = '/atn/main/getcitaspendientes';


$( document ).ready(function() {
    $('#calendar').fullCalendar({
        eventSources: [
        source[0]
        ],
        header: {
            left: 'prev,next',
            center: 'title',
            right: 'month,basicWeek,basicDay'
        },
        eventRender: function (event, element) {

        },
        eventClick: function (event) {
            console.log("aqui se io click");
            showDetail(event.id);
            return false;
        }
    });  
});

function searchDialog(){    
    $('#iFrameModaCita').attr('src','/atn/main/searchcitas');
    $("#MyModalSearch").modal("show");
}

function showDetail(idDate){
    $("#MyModalSearch").modal("hide");
    $("#myModalinfoVis").modal("show");        
    $('#iFrameDetCita').attr('src','/atn/main/citadetalle?strInput='+idDate);    
}

function closeDetail(){
    console.log("close windows");
    $("#MyModalSearch").modal("hide");
    $("#myModalinfoVis").modal("hide");    
    loadCalendar()
}

function loadCalendar(){
    $('#calendar').fullCalendar('refetchEvents');
}