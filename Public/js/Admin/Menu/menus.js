function editMenuHtml(url,data){
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

$('.addMenuHtml').on('click',function (e){
    var url = addUrl;
    var data = {};
    editMenuHtml(url,data);
});
$('.editMenuHtml').on('click',function (e){
    var url = editUrl;
    var data = {menuId:$(this).data('info')};
    editMenuHtml(url,data);
});

$('#myModal').on('click','.editMenuAjax',function (e){
    var options = {};
    var menuId = $('#myModal input[name="menuId"]').val()
    if(menuId){
        options.url = editUrl;
    }else{
        options.url = addUrl;
    }

    var title = $('#myModal input[name="title"]').val();
    var parentMenuId = $('#myModal select[name="parentMenuId"]').val();
    var path = $('#myModal input[name="path"]').val();
    var sort = $('#myModal input[name="sort"]').val();
    var isShow = $('#myModal input[name="isShow"]:checked').val();

    options.type = 'post';
    options.data = {isSave:true,menuId:menuId,parentMenuId:parentMenuId,title:title,path:path,sort:sort,isShow:isShow};
    options.success = function (data){
            if(data.result=='0'){
                $('#queryForm').submit();
            }else{
                alert(data.message);
            }
    }
    $.ajax(options);
});

function deleteMenu(url,data){
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


$('.deleteMenu').on('click',function (e){
    if(!confirm('确定删除?')){
        return;
    }
    var url = deleteUrl;
    var data = {menuId:$(this).data('info')};
    deleteMenu(url,data);
});