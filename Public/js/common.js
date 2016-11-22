/*
    ajax请求
    默认GET方式
    json格式
 */
function ajax(option){
    /*var options = {};
    options.url = option.url;
    options.success = option.success?option.success:function (){};
    options.data = option.data?option.data:{};
    options.type = option.type?option.type:'GET';
    options.dataType = option.dataTyp?option.dataTyp:'json';
    $.ajax(options);*/
}

//删除记录后当记录数是一条 翻页-1 这里耦合太高复用不高
function goPage(){
}

//全局ajax请求判断
$.ajaxSetup({
    error:function (xhr,status){
        var httpStatus = xhr.status;
        var response = eval("("+xhr.responseText+")");
        if(httpStatus==300){
            alert(response.message);
            if(response.result == '201'){
                window.location.href = '/';
            }
        }
    }
});

//select多选ajax 并选中
//data.url
//data.data
//data.dom
//data.first 第一个key value {key:',name=""}
//data.valueName //key名称
//data.textName //text名称
//data.afterCall // 请求后的callback; //暂时用来ajax请求后选中选中option
function ajaxAppendSelect(data){
    var options = {};
    options.url = data.url;
    options.data = data.data;
    options.type = "get";
    options.dataType="json";
    options.success = function(msg){
        var options = msg.data;
        appendOptions({dom:data.dom,data:options,valueName:data.valueName,textName:data.textName,first:data.first});
        if(typeof(data.afterCall) == 'function'){
            data.afterCall();
        }
    };
    $.ajax(options);
}

//data.dom 追加的dom
//data.first 第一个key value
//data.data //数据二维数组
//data.valueName //key名称
//data.textName //text名称
function appendOptions(data){
    var options = data.data;
    var optionsStr = '';
    var first = data.first;
    if(first){
        optionsStr = '<option value="'+first.key+'">'+first.name+'</option>';
    }
    for( i in options){
        optionsStr = optionsStr+'<option value="'+options[i][data.valueName]+'">'+options[i][data.textName]+'</option>';
    }
    data.dom.html(optionsStr);
}

//没有联动单纯选项
//任意ajax添加select数据加到options参照ajaxZone方法
//默认回调取select data-info属性
/**
 * dom: select元素
 * url:
 * params:
 * key: 默认：''
 * name:默认：'全部'
 * valueName:默认：id
 * textName:默认：name
 */
function ajaxSelectAndRecord(options){
    var pkid = options.dom.data('info');
    var callback = {};
    if(pkid){
        callback = function (){
            options.dom.val(pkid);
        };
    }
    var data = {};
    data.url = options.url;
    data.data = options.params||{};
    data.dom = options.dom;
    var key = '';
    if(options.key){
        key = options.key;
    }
    var name = '';
    if(options.name){
        name = options.name;
    }
    data.first = {key:key,name:name};
    var id = 'id';
    if(options.valueName){
        id = options.valueName;
    }
    data.valueName = id;
    var name = 'name';
    if(options.textName){
        name = options.textName;
    }
    data.textName  = name;
    data.afterCall = callback;
    ajaxAppendSelect(data);
}


//省市区
function ajaxZone(dom,params,callback){
    var data = {};
    if(dom.data('url')){
        data.url = dom.data('url');
    }else{
        data.url = sysZoneAjaxUrl;
    }
    data.data = params;
    data.dom = dom;
    var first  = dom.data('first');
    if(!first){
        data.first = {key:'',name:'全部'};
    }else{
        first = eval('('+first+')');
        data.first = {key:first.key,name:first.name};
    }
    var second = dom.data('second');
    if(!second){
        data.valueName = 'id';
        data.textName  = 'name';
    }else{
        second = eval('('+second+')');
        data.valueName = second.key;
        data.textName = second.name;
    }
    data.afterCall = callback;
    ajaxAppendSelect(data);
}

//初始化选中
function zoneInitSelect(provinceDom,cityDom,areaDom){
    //var provinceDom = $('#queryForm select[name="provinceId"]');
    var selectedPId = provinceDom.data('info');
    var callback = {}
    if(selectedPId){
        callback = function (){
            provinceDom.val(selectedPId);
        };
    }
    ajaxZone(provinceDom,{},callback);

    var selectedCId = cityDom.data('info');
    if(selectedPId){
        //var cityDom = $('#queryForm select[name="cityId"]');
        var callback = {};
        if(selectedCId){
            callback = function (){
                cityDom.val(selectedCId);
            };
        }
        ajaxZone(cityDom,{parentId:selectedPId},callback);
    }

    if(areaDom){
        var selectedAId = areaDom.data('info');
        if(selectedCId){
            var callback = {};
            if(selectedAId){
                callback = function (){
                    areaDom.val(selectedAId);
                };
            }
            ajaxZone(areaDom,{parentId:selectedCId},callback);
        }
    }
}

//初始化事件change绑定
function zoneInitEvent(provinceDom,cityDom,areaDom){
    provinceDom.on('change',function (e){
        var seletedId = $(this).val();
        var subSelect = cityDom;
        if(seletedId){
            ajaxZone(subSelect,{parentId:seletedId},{});
        }else{
            ajaxZone(subSelect,{parentId:-99},{});
        }
        if(areaDom){
            ajaxZone(areaDom,{parentId:-99},{});
        }
    });
    if(areaDom){
        cityDom.on('change',function (e){
            var seletedId = $(this).val();
            var subSelect = areaDom;
            if(seletedId){
                ajaxZone(subSelect,{parentId:seletedId},{});
            }else{
                ajaxZone(subSelect,{parentId:-99},{});
            }
        });
    }
}

function zoneInit(provinceDom,cityDom,areaDom){
    zoneInitSelect(provinceDom,cityDom,areaDom);
    zoneInitEvent(provinceDom,cityDom,areaDom);
}

//清空表单rest
function formReset(curObj){
    var form = $(curObj).parents('form');
    form.find('input[type="text"],select').val('');
}

/**
 * 模态框 html 刷新
 * url      [链接地址]
 * params   [请求参数]
 * dom      [模态框dom]
 * callback [请求后的回调方法]预留
 */
function htmlViewModal(url,params,dom,callback){
    var options = {};
    options.url = url;
    options.data = params;
    options.type = "GET";
    options.dataType = "html";
    options.success = function (html){
        dom.html(html);
        if(typeof(callback) == 'function'){
            callback();
        }
        dom.modal('show');
    }
    $.ajax(options);
}

/**
 * 延迟绑定 只能get请求
 * 分页ajax局部刷新tp后台bootsrap一起使用
 * evetDomStr:事件绑定元素
 * domStr :局部刷新的dom元素
 * suDomStr:事件绑定子元素字符串
 */
function pageAjax(evtDomStr,domStr,subDomStr,params){
    var str = 'a';
    if(subDomStr){
        str = subDomStr+' '+str;
    }
    $(evtDomStr).on('click',str,function (e){
        e.preventDefault();
        var options = {};
        var url = $(this).attr('href');
        if(!url){
            return ;
        }
        options.url = url;
        options.type="get";
        options.dataType="html";
        options.data = params;
        options.success = function (html){
            $(domStr).html(html);
        }
        $.ajax(options);
    });

}

// 验证重复元素，有重复返回true；否则返回false
function isRepeat(arr) {
    var hash = {};
    for(var i in arr) {
        if(hash[arr[i]])
        {
            return true;
        }
        // 不存在该元素，则赋值为true，可以赋任意值，相应的修改if判断条件即可
        hash[arr[i]] = true;
    }
    return false;
}

//获取表单属性在值
function getFormParams(dom){
    var params = {};
    dom.find('input,select,textarea').each(function (){
            var name = $(this).attr('name');
            var val = $(this).val();
            if(name){
                params[name] = val;
            }
    });
    return params;
}

//表单条件导出
function excelForm(dom,url){
    var length = $('#hidden-excel-frame').length;
    if(length>1){
        alert('excel导出异常iframe');
    }
    if(length==0){
        var iframe = '<div style="display: none"><iframe name="hidden-excel-frame" id="hidden-excel-frame"></iframe></div>';
        length = $('#page-wrapper').length;
        if(length==0||length>1){
            alert('excel导出异常#page-wrapper');
        }
        $('#page-wrapper').append(iframe);
    }
    var old_action = dom.attr("action");
    var old_target = dom.attr("target");
    if(!old_target){
        old_target = '_self';
    }
    var action_url = url;
    var target = "hidden-excel-frame";
    dom.attr("action", action_url);
    dom.attr("target",target);
    dom.submit();
    dom.attr("action", old_action);
    dom.attr("target", old_target);
}


/**
 * edit add modal ajax
 */

function editModalAjax(modalDom,formDom,url,sucCallback){
    var options = {};
    options.url = url;
    options.data = getFormParams(formDom);
    options.type = "post";
    options.dataType = "json";
    options.success = function (data){
        if(data.result=='0'){
            //modalDom.modal('hide');
            window.location.href = $('#queryForm').attr('action');
        }else{
            alert(data.message);
        }
    }
    if(typeof(sucCallback) == 'function'){
        options.success = sucCallback;
    }
    $.ajax(options);
}

function queryForm(dom){
    dom.find('input[name="p"]').val(1);
    dom.submit();
}

/**
 * initdate
 */
function initDatePicker(domClass){
    //var date = new Date();
    $(domClass).each(function (){
        var date = new Date();
        var str = date.getFullYear()+'-'+(date.getMonth()+1)+'-'+date.getDate();
        $(this).datetimepicker({
            language:  'zh-CN',
            format: 'yyyy-mm-dd',
            autoclose: true,
            todayBtn: true,
            /*startDate: str,*/
            forceParse: true,
            todayHighlight: true,
            startView:2,
            minView: 2,
            showMeridian:true,
            minuteStep: 10
        });
    });
}

function initDateRangePicker(domClass,cb) {
    $(domClass).each(function (){
        $(this).daterangepicker({
            "showDropdowns": true,
            "ranges": {
                "今日": [moment().startOf('day'), moment()],
                "昨日": [moment().subtract('days', 1).startOf('day'), moment().subtract('days', 1).endOf('day')],
                "最近7日": [moment().subtract('days', 6), moment()],
                "最近30天": [moment().subtract('days', 30),moment()],
                "这个月": [moment().startOf('month'),moment().endOf('month')],
                "上个月": [moment().subtract('months',1).startOf('month'),moment().subtract('months',1).endOf('month')]
            },
            "alwaysShowCalendars": true,
            "locale":{
                "format":'YYYY-MM-DD',
                "separator":'  -  ',
                "applyLabel":'确定',
                "cancelLabel":'取消',
                "customRangeLabel":'自定义',
                "daysOfWeek":[ '日', '一', '二', '三', '四', '五', '六' ],
                "monthNames":['一月', '二月', '三月', '四月', '五月', '六月', '七月', '八月', '九月', '十月', '十一月', '十二月' ],
                "firstDay":1

            }
        }, function(start,end) {
            start = start.format('YYYY-MM-DD');
            end = end.format('YYYY-MM-DD');
            cb(start,end);
        });
    });
}

/*form html ajax*/
function formAjax(formDomStr,objDomStr){
    var formDom = $(formDomStr);
    var url = formDom.attr('action');
    var params = getFormParams(formDom);
    htmlAjax($(objDomStr),url,params,{});
}

/* htmlAjax*/
function htmlAjax(dom,url,params,callback){
    var options = {};
    options.url = url;
    options.type="get";
    options.dataType="html";
    options.data = params?params:{};
    options.success = function (html){
        dom.html(html);
        typeof(callback)=='function'?calllback():'';
    }
    $.ajax(options);
}
