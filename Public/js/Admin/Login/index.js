$(document).ready(function (e){
    $('button[type="submit"]').on('click',function (e){
        e.preventDefault();
        var options = {};
        options.url = loginUrl;
        var name = $('input[name="nameLogin"]').val();
        var password = $('input[name="passwordLogin"]').val();
        var vcode = $('input[name="vcode"]').val();
        options.data = {nameLogin:name,password:password,vcode:vcode};
        options.type = "post";
        options.dataType = "json";
        options.success = function (data){
            if(data.result=='0'){
                window.location.href = welcomeUrl;
            }else{
                alert(data.message);
                $('.vcodeSrc').click();
            }
        }
        $.ajax(options);
        return false;
    });
    $('.vcodeSrc').on('click',function (e){
        var ranUrl = vcodeUrl+'?random='+Math.random();
        $(this).attr('src',ranUrl);
    });
});