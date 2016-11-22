//点击菜单收缩bug
$(document).ready(function (e){

    $('div[class="collapse navbar-collapse navbar-ex1-collapse"]').find('ul li ul li a').on('click',function (e){
        e.preventDefault();
        //window.location.href = $(this).attr('href');
        var domA = $('<div style="display:none11;"><a href="'+$(this).data('info')+'" target="_self"><span id="tempAspan">11</span></a></div>');
        $('body').append(domA);
        $('#tempAspan').trigger('click');
        return;
    });

    $('div[class="collapse navbar-collapse navbar-ex1-collapse"]').find('ul li').on('click',function (e){
        e.preventDefault();
        e.stopPropagation();
        var isNone = $(this).find('ul').css('display');
        if(isNone=='none'){
            $(this).find('ul').css('display','inline-block');
        }else{
            $(this).find('ul').css('display','none');
        }
        $(this).toggleClass('nopen');
        $(this).toggleClass('open');
    });

    //退出
    $('.sysLoginout').on('click',function (e){
        e.preventDefault();
        var options = {};
        options.url = sysLoginoutUrl;
        options.type="post";
        options.dataType="json";
        options.success = function (data){
            if(data.result==0){
                window.location.href = sysLoginIndexUrl;
            }
        }
        $.ajax(options);
    });

});