function deleteLog(url,data){
    var options = {};
        options.url = url;
        options.data = data;
        options.dataType = "json";
        options.success = function (data){
          if(data.result=='0'){
                //$('#queryForm').submit();
            }else{
                alert(data.message);
            }
        }
    $.ajax(options);
}

$('.deleteLog').on('click',function (e){
    if(!confirm('确定删除?')){
        return;
    }
    var url = deleteUrl;
    var data = {operationId:$(this).data('info')};
    deleteLog(url,data);
});

$(document).ready(function (){
    $('#createTime').datetimepicker({
        language:  'zh-CN',
        format: 'yyyy-mm-dd',
        autoclose: true,
        todayBtn: true,
        startDate: "2013-02-14",
        forceParse: true,
        todayHighlight: true,
        startView:2,
        minView: 2,
        showMeridian:true,
        minuteStep: 10
    });
});