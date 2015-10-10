<?php include_once 'head.php'; ?>
<link href="lib/view/css/login.css" rel="stylesheet" />
    <div class="login">
        <div class="col-lg-10">
            <div class="input-group m-top20">
                <span class="input-group-addon"><i class="icon-user"></i></span>
                <input type="text" name="userName" id="userName" placeholder="<?php echo t('请输入用户名'); ?>" class="logininput form-control input-lg">
            </div>
            <div class="input-group m-top20">
                <span class="input-group-addon"><i class="icon-unlock-alt"></i></span>
                <input type="password" name="passWord" id="passWord" placeholder="<?php echo t('请输入密码'); ?>" class="logininput form-control input-lg">
            </div>
        </div>
        <div class="col-lg-10">
            <div class="checkbox">
                <label id="rem">
                        <input type="checkbox" id="remember" name="remember" checked="checked"> <?php echo t('自动登录'); ?>
                </label>
            </div>
        </div>
        <div class="col-lg-10">
            <button style="float: left;" class="btn btn-info" type="submit" id="loginbtn"><?php echo t('登录'); ?></button>
            <button style="display: none;float: left;" class="btn btn-info" type="submit" id="registbtn"><?php echo t('注册'); ?></button>
            <div style="float: left;margin-left: 20px;margin-top: 23px;" id="aregist">
                <label>
                    <a href="javascript:;" onclick="$('#rem, #loginbtn, #aregist').hide();$('#registbtn, #alogin').show();" style="color: #444;"><?php echo t('注册'); ?></a>
                </label>
            </div>
            <div style="float: left;margin-left: 20px;margin-top: 23px;display: none;" id="alogin">
                <label>
                    <a href="javascript:;" onclick="$('#rem, #loginbtn, #aregist').show();$('#registbtn, #alogin').hide();" style="color: #444;"><?php echo t('登录'); ?></a>
                </label>
            </div>
            <div style="color: #ffffff;float:left;margin-top: 23px;margin-left: 60px;"><a href="javascript:;" onclick="Cookies.set('lang', 'zh');window.location.reload();" style="color: #ffffff;">中文</a> | <a href="javascript:;" onclick="Cookies.set('lang', 'en');window.location.reload();" style="color: #ffffff;">English</a></div>
        </div>
    </div>
</section>
<script src="lib/view/js/jquery.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $(".logininput").keypress(function(event){
            event = event||window.event;
            if (event.keyCode == 13) {
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
        });
        $("#registbtn").click(function() {
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
            $.ajax({
                url: 'index.php?m=user&a=regist',
                type: 'POST',
                data:{ userName : $('#userName').val(), passWord : $('#passWord').val() },
                dataType: 'json',
                timeout: 8000,
                success: function(ret) {
                    if (ret.code == 1) {
                        window.location.href = 'index.php';
                    } else {
                        alert(ret.data);
                    }
                }
            });
        });
    });
</script>
</body>
</html>