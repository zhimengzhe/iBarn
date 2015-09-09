<?php include_once 'head.php'; ?>
<link href="lib/view/css/login.css" rel="stylesheet" />
    <div class="login">
        <div class="col-lg-10">
            <div class="input-group m-top20">
                <span class="input-group-addon"><i class="icon-user"></i></span>
                <input type="text" name="userName" id="userName" placeholder="请输入用户名" class="logininput form-control input-lg">
            </div>
            <div class="input-group m-top20">
                <span class="input-group-addon"><i class="icon-unlock-alt"></i></span>
                <input type="password" name="passWord" id="passWord" placeholder="请输入密码" class="logininput form-control input-lg">
            </div>
        </div>
        <div class="col-lg-10">
            <div class="checkbox">
                <label>
                    <input type="checkbox" id="remember" name="remember" checked="checked"> 自动登录
                </label>
            </div>
        </div>
        <div class="col-lg-10">
            <button class="btn btn-info" type="submit" id="loginbtn" name="Submit">登 录</button>
        </div>
    </div>
</section>
<script src="lib/view/js/jquery.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $(".logininput").keypress(function(event){
            event = event||window.event;
            if(event.keyCode==13){
                $("#loginbtn").click();
            }
        });
        $("#loginbtn").click(function() {
            var k = 0;
            var ajaxhtml = "";
            $(".logininput").each(function(i, obj) {
                if ($(obj).val().trim() == "") {
                    k++;
                    $(this).css("border-color", "red");
                    $(this).focus();
                    return false;
                }
            });
            if (k != 0) return;
            remember = 0;
            if($("#remember").is(':checked')) {
                remember = 1;
            }
            $.ajax({
                url: 'index.php?m=user&a=login',
                type: 'POST',
                data:{ userName : $('#userName').val(), passWord : $('#passWord').val(), 'remember' : remember },
                dataType: 'json',
                timeout: 8000,
                success: function(data) {
                    if (data.code == 1) {
                        window.location.href = 'index.php';
                    } else {
                        alert(data.data);
                    }
                }
            });
        })
    });
</script>
</body>
</html>