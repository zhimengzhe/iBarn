<!DOCTYPE html>
<html lang="cn">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="iBarn">
    <meta name="keyword" content="iBarn">
    <link rel="shortcut icon" href="img/favicon.png">
    <title>iBarn</title>
    <!-- Bootstrap core CSS -->
    <link href="lib/view/css/bootstrap.min.css" rel="stylesheet">
    <link href="lib/view/css/bootstrap-reset.css" rel="stylesheet">
    <!--external css-->
    <link href="lib/view/assets/font-awesome/css/font-awesome.css" rel="stylesheet" />
    <link href="lib/view/assets/jquery-easy-pie-chart/jquery.easy-pie-chart.css" rel="stylesheet" type="text/css" media="screen"/>
    <link rel="stylesheet" href="lib/view/css/owl.carousel.css" type="text/css">
    <!-- Custom styles for this template -->
    <link href="lib/view/css/style.css" rel="stylesheet">
    <link href="lib/view/css/style-responsive.css" rel="stylesheet" />
    <link href="lib/view/css/index.css" rel="stylesheet" />
    <link rel="stylesheet" type="text/css" href="lib/view/assets/jquery-multi-select/css/multi-select.css" />

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 tooltipss and media queries -->
    <!--[if lt IE 9]>
    <script src="lib/view/js/html5shiv.js"></script>
    <script src="lib/view/js/respond.min.js"></script>
    <![endif]-->
    <script type="text/javascript" src="js/cookie.js"></script>
</head>
<body>
<section id="container">
    <header class="header white-bg">
        <div class="pull-left logo" title="iBarn" style="float: left;"><a href="index.php">iBarn</a></div>
        <div style="color: #ffffff;float:left;margin-top: 43px;"><a href="javascript:;" onclick="Cookies.set('lang', 'zh');window.location.reload();" style="color: #ffffff;">中文</a> | <a href="javascript:;" onclick="Cookies.set('lang', 'en');window.location.reload();" style="color: #ffffff;">English</a></div>

        <?php if ($userinfo) { ?>
            <div class="top-nav">
                <!--search & user info start-->
                <ul class="nav pull-right top-menu">
                    <!-- user login dropdown start-->
                    <li class="dropdown">
                        <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                            <img width="40" height="40" alt="<?php echo htmlspecialchars($userinfo['name'], ENT_NOQUOTES); ?>" src="<?php echo $userinfo['avatar']; ?>">
                            <span class="username"><?php echo htmlspecialchars($userinfo['name'], ENT_NOQUOTES); ?></span>
                            <b class="caret"></b>
                        </a>
                        <ul class="dropdown-menu extended logout">
                            <div class="log-arrow-up"></div>
                            <li><a href="index.php?m=user&a=set"><i class="icon-cog"></i><?php echo t('设置'); ?></a></li>
                            <li><a href="index.php?m=user&a=person"><i class="icon-suitcase"></i><?php echo t('简介'); ?></a></li>
                            <li><a href="#"><i class="icon-bell-alt"></i><?php echo t('消息'); ?></a></li>
                            <li><a href="index.php?m=user&a=logout"><i class="icon-key"></i><?php echo t('退出'); ?></a></li>
                        </ul>
                    </li>
                    <!-- user login dropdown end -->
                </ul>
                <!--search & user info end-->
            </div>
        <?php } ?>
    </header>
    <section class="wrapper">
        <div class="row" style="background-image:url(img/bg0.jpg); border-bottom: 1px solid #bbb;height: 190px;margin-top: -20px;">
            <div class="container">
                <div class="col-md-2 col-sm-3 col-xs-6" style="padding-left:0;">
                    <div style="margin-top: 22px;">
                        <img src="<?php echo htmlspecialchars(($userinfo['avatar'] ? $userinfo['avatar'] : 'img/default.png'), ENT_NOQUOTES); ?>" width="143px" height="143px"/>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4" style="top:100px; float: none;display: block; margin:auto;">
                <div class="box box-primary">
                    <div class="box-header">
                        <h3 class="box-title"><?php echo t('用户名'); ?>：</h3>
                    </div><!-- /.box-header -->
                    <div class="box-body">
                        <div class="form-group">
                            <?php echo htmlspecialchars($userinfo['name'], ENT_NOQUOTES); ?>
                        </div>
                    </div>
                    <div class="box-header">
                        <h3 class="box-title"><?php echo t('邮箱地址'); ?>：</h3>
                    </div><!-- /.box-header -->
                    <div class="box-body">
                        <div class="form-group">
                            <?php echo htmlspecialchars($userinfo['email'], ENT_NOQUOTES); ?>
                        </div>
                    </div>
                </div>
                <div class="box-footer" style="margin-top: 30px;">
                    <a class="btn btn-primary" type="button" href="index.php"><?php echo t('返回网盘'); ?></a>
                </div>
            </div><!-- /.col -->
        </div>
    </section>
</section><!-- /.content -->
<script src="lib/view/js/jquery.js"></script>
<script src="lib/view/js/bootstrap.min.js"></script>
<script>
    function set() {
        var email = $('#email').val();
        var pwd = $('#pwd').val();
        var npwd = $('#npwd').val();
        var nrpwd = $('#nrpwd').val();
        if (!email && !pwd) {
            alert(file.lang('请完整填写修改项'));
            return;
        }
        if (npwd != nrpwd) {
            alert(file.lang('两次输入的新密码不一致'));
            return;
        }
        $.ajax({
            url: 'index.php?m=user&a=setUser',
            type: 'POST',
            data:{ email : email, pwd : pwd, npwd : npwd, nrpwd : nrpwd },
            dataType: 'json',
            timeout: 8000,
            success: function(data){
                alert(data.data);
            }
        });
    }
</script>
</body>
</html>