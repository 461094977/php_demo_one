//节点联动
function ajaxNodes(dom,params,callback){
    var data = {};
    data.url = nodesAjaxUrl;
    data.data = params;
    data.dom = dom;
    data.first = {key:'',name:''};
    data.valueName = 'id';
    data.textName  = 'title';
    data.afterCall = callback;
    ajaxAppendSelect(data);
}

//初始化选中
function nodeInitSelect(level1Dom,level2Dom){
    var selectedPId = level1Dom.data('info');
    var callback = {}
    if(selectedPId){
        callback = function (){
            level1Dom.val(selectedPId);
        };
    }
    ajaxNodes(level1Dom,{parentId:'0'},callback);

    var selectedCId = level2Dom.data('info');
    if(selectedPId){
        var callback = {};
        if(selectedCId){
            callback = function (){
                level2Dom.val(selectedCId);
            };
        }
        ajaxNodes(level2Dom,{parentId:selectedPId},callback);
    }
}

//初始化事件change绑定
function nodeInitEvent(level1Dom,level2Dom){
    level1Dom.on('change',function (e){
        var seletedId = $(this).val();
        var subSelect = level2Dom;
        if(seletedId){
            ajaxNodes(subSelect,{parentId:seletedId},{});
        }else{
            ajaxNodes(subSelect,{parentId:-99},{});
        }
    });
}

function nodeInit(level1Dom,level2Dom){
    nodeInitSelect(level1Dom,level2Dom);
    nodeInitEvent(level1Dom,level2Dom);
}
/*---*/


function editNodeHtml(url,data){
    var options = {};
        options.url = url;
        options.data = data;
        options.dataType = "HTML";
        options.success = function (html){
            $('#myModal').html(html);
            //节点联动
            var level1Dom = $('#myModal select[name="parentNode1"]');
            var level2Dom = $('#myModal select[name="parentNode2"]');
            nodeInit(level1Dom,level2Dom);
            $('#myModal').modal('show');
        }
    $.ajax(options);
}

$('.addNodeHtml').on('click',function (e){
    var url = addUrl;
    var data = {};
    editNodeHtml(url,data);
});
$('.editNodeHtml').on('click',function (e){
    var url = editUrl;
    var data = {nodeId:$(this).data('info')};
    editNodeHtml(url,data);
});

$('#myModal').on('click','.editNodeAjax',function (e){
    var options = {};
    var nodeId = $('#myModal input[name="nodeId"]').val()
    if(nodeId){
        options.url = editUrl;
    }else{
        options.url = addUrl;
    }
    var name = $('#myModal input[name="name"]').val();
    var title = $('#myModal input[name="title"]').val();
    var parentNode1 = $('#myModal select[name="parentNode1"]').val();
    var parentNode2= $('#myModal select[name="parentNode2"]').val();
    options.type = 'post';
    options.data = {isSave:true,nodeId:nodeId,parentNode1:parentNode1,parentNode2:parentNode2,name:name,title:title};
    options.success = function (data){
            if(data.result=='0'){
                $('#queryForm').submit();
            }else{
                alert(data.message);
            }
    }
    $.ajax(options);
});

function deleteNode(url,data){
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


$('.deleteNode').on('click',function (e){
    if(!confirm('确定删除?')){
        return;
    }
    var url = deleteUrl;
    var data = {nodeId:$(this).data('info')};
    deleteNode(url,data);
});
