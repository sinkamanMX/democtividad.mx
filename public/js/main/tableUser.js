function backToMain(){
  var mainPage = $("#hRefLinkMain").val();
  location.href= mainPage;
}

function deleteRow(){ 
  var idItem = $("#inputDelete").val();
    $.ajax({
        url: "/main/users/getinfo",
        type: "GET",
        dataType : 'json',
        data: { catId : idItem, 
            optReg: 'delete'},
        success: function(data){
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