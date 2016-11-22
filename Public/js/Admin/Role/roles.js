function deleteRole(url,data){
    var options = {};
        options.url = url;
        options.data = data;
        options.type = "post";
        options.success = function (data){
            if(data.result == '0'){
                $('#queryForm').submit();
            }else{
                alert(data.message);
            }
        }
    $.ajax(options);
}

$('.deleteRole').on('click',function (e){
    if(!confirm('确定删除?')){
        return;
    }
    var url = deleteUrl;
    var data = {roleId:$(this).data('info')};
    deleteRole(url,data);
});

function editUserHtml(url,data){
    var options = {};
        options.url = url;
        options.data = data;
        options.dataType = "HTML";
        options.success = function (html){
          $('#myModal').html(html);
          $('#myModal').modal('show');
        }
    $.ajax(options);
}

$('.addRoleHtml').on('click',function (e){
    var url = addUrl;
    var data = {};
    editUserHtml(url,data);
});

$('#myModal').on('click','.editRoleAjax',function (e){
    var options = {};
        options.url = addUrl;
    var name = $('#myModal input[name="name"]').val();
    var remark = $('#myModal textarea[name="remark"]').val();
    options.type = 'post';
    options.data = {isSave:true,name:name,remark:remark};
    options.success = function (data){
            if(data.result=='0'){
                $('#queryForm').submit();
            }else{
                alert(data.message);
            }
    }
    $.ajax(options);
});

function editRoleNodeHtml(url,data){
    var options = {};
        options.url = url;
        options.data = data;
        options.dataType = "HTML";
        options.success = function (html){
          $('#myModal2').html(html);
          initRoleNodes();
          $('#myModal2').modal('show');
        }
    $.ajax(options);
}

$('.editRoleNodeHtml').on('click',function (e){
    var url = assignNodeUrl;
    var data = {roleId:$(this).data('info')};
    editRoleNodeHtml(url,data);
});

$('#myModal2').on('click','.editRoleNodeAjax',function (e){
    var options = {};
        options.url = assignNodeUrl;
    var roleId = $('#myModal2 input[name="roleId"]').val();
    var nodeId = new Array();
    $('#myModal2 input[type="checkbox"][name="nodeId"]:checked').each(function (e){
        nodeId.push($(this).val());
    });
    options.type = 'post';
    options.data = {isSave:true,roleId:roleId,nodeId:nodeId};
    options.success = function (data){
            if(data.result=='0'){
                //alert(data.message);
                $('#myModal2').modal('hide');
            }else{
                alert(data.message);
            }
    }
    $.ajax(options);
});


//无限极向下
function umlimitDown(id,checkedStatus){
    $('#myModal2 table tbody input[data-pid="'+id+'"]').each(function (){
        var id = $(this).val();
        $(this).prop('checked',checkedStatus);
        umlimitDown(id,checkedStatus);
    });
}

//无限极向上
function umlimitUp(pid){
    var length = $('#myModal2 table tbody input[data-pid="'+pid+'"]:not(:checked)').length;
    var checkedStatus = true;
    if(length>0){
        checkedStatus = false;
    }
    $('#myModal2 table tbody input[value="'+pid+'"]').each(function(){
        $(this).prop('checked',checkedStatus);
        var pid = $(this).data('pid');
        umlimitUp(pid);
    });

}

//初始化选中
function initRoleNodes(){
    var ids = $('#myModal2 table').data('info');
    $(ids).each(function (key,id){
        $('#myModal2 table tbody input[value="'+id+'"]').click();
    });
}

//权限分配checkbox交互
$('#myModal2').on('click','table tbody input',function (e){
    var id = $(this).val();
    var level = $(this).data('level');
    var pid = $(this).data('pid');
    var checkedStatus = $(this).prop('checked');
    umlimitDown(id,checkedStatus);
    umlimitUp(pid);
});
