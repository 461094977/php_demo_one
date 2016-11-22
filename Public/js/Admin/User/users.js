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

$('.addUserHtml').on('click',function (e){
    var url = addUrl;
    var data = {};
    editUserHtml(url,data);
});
$('.editUserHtml').on('click',function (e){
    e.preventDefault();
    var url = editUrl;
    var data = {userId:$(this).data('info')};
    editUserHtml(url,data);
});

function deleteUser(url,data){
    var options = {};
        options.url = url;
        options.data = data;
        options.dataType = "json";
        options.success = function (data){
          if(data.result=='0'){
                $('#queryForm').submit();
            }else{
                alert(data.message);
            }
        }
    $.ajax(options);
}

$('.deleteUser').on('click',function (e){
    if(!confirm('确定删除?')){
        return;
    }
    var url = deleteUrl;
    var data = {userId:$(this).data('info')};
    deleteUser(url,data);
})

$('#myModal').on('click','.editUserAjax',function (e){
    var options = {};
    var userId = $('#myModal input[name="userId"]').val();
    if(userId){
        options.url = editUrl;
    }else{
        options.url = addUrl;
    }
    var fullname = $('#myModal input[name="fullname"]').val();
    var name = $('#myModal input[name="name"]').val();
    var password = $('#myModal input[name="password"]').val();
    var roleId = $('#myModal select[name="roleId"]').val();
    var userImg = $('#myModal input[name="userImg"]').val();
    var mobile = $('#myModal input[name="mobile"]').val();
    var status = $('#myModal select[name="status"]').val();
    options.type = 'post';
    options.data = {isSave:true,userId:userId,fullname:fullname,name:name,password:password,roleId:roleId,userImg:userImg,mobile:mobile,status:status};
    options.success = function (data){
            if(data.result=='0'){
                //alert(data.message);
                $('#queryForm').submit();
            }else{
                alert(data.message);
            }
    }
    $.ajax(options);
});

$('#myModal').on('change','.upload',function (e){
  $("#preview_form").submit();
});

function msg_callback(msg)
{
  var data = eval('(' + msg + ')');
  if(data.result=='0'){
    $('#userImg').val(data.data.url);
  }else{
    alert(data.message);
  }
}

$(document).ready(function (){
    var options = {};
    options.url = roleUrl;
    options.dom = $('#queryForm select[name="roleId"]');
    ajaxSelectAndRecord(options);
});