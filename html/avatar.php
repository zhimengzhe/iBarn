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
<!--                            <img width="40" height="40" alt="--><?php //echo htmlspecialchars($userinfo['name'], ENT_NOQUOTES); ?><!--" src="--><?php //echo $userinfo['avatar']; ?><!--">-->
                            <img width="40" height="40" alt="<?php echo htmlspecialchars($userinfo['name'], ENT_NOQUOTES); ?>" src="<?php echo $userinfo['avatar'] ? htmlspecialchars($userinfo['avatar'], ENT_NOQUOTES) : DEFAULT_AVATAR; ?>">
                            <?php if ($userinfo['email']) { ?><span class="username">Email：<?php echo htmlspecialchars($userinfo['email'], ENT_NOQUOTES); ?></span><?php } ?>
                            <b class="caret"></b>
                        </a>
                        <ul class="dropdown-menu extended logout">
                            <div class="log-arrow-up"></div>
                            <li><a href="index.php?m=user&a=set"><i class="icon-edit"></i><?php echo t('编辑'); ?></a></li>
                            <li><a href="index.php?m=user&a=person"><i class="icon-eye-open"></i><?php echo t('查看'); ?></a></li>
                            <li><a href="index.php?m=user&a=avatar"><i class="icon-cog"></i><?php echo t('设置头像'); ?></a></li>
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
                        <img id="showAvatar" src="<?php echo $userinfo['avatar'] ? htmlspecialchars($userinfo['avatar'], ENT_NOQUOTES) : DEFAULT_AVATAR; ?>" width="143px" height="143px"/>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4" style="top:100px; float: none;display: block; margin:auto;">
                <div class="box box-primary">
                    <div class="box-header">
                        <h3 class="box-title"><?php echo t('设置头像'); ?>：</h3>
                    </div><!-- /.box-header -->
                    <div class="box-body">
                        <div id="ucontainer" style="margin-top: 30px;">
                            <button class="btn btn-info" type="button" id="avatar">
                                <i class="icon-cloud-upload"></i>
                                <?php echo t('上传头像'); ?>
                            </button>
                            <a style="margin-left:20px;" href="index.php"><?php echo t('返回网盘'); ?></a>
                        </div>
                    </div>
                </div>
            </div><!-- /.col -->
        </div>
    </section>
</section><!-- /.content -->
<script src="lib/view/js/jquery.js"></script>
<script src="lib/view/js/bootstrap.min.js"></script>
<script type="text/javascript" src="lib/plupload/js/plupload.full.min.js"></script>
<script>
    var uploader = new plupload.Uploader({
        runtimes : 'html5,flash,silverlight,html4',
        browse_button : 'avatar',
        container: document.getElementById('ucontainer'),
        multi_selection : false,
        url : 'index.php?a=upload',
        chunk_size : '1024kb',
        flash_swf_url : 'lib/plupload/js/Moxie.swf',
        silverlight_xap_url : 'lib/plupload/js/Moxie.xap',
        filters : {
            max_file_size : '15mb',
            mime_types : []
        },
//            multipart_params : { uid : 1 },
        init: {
            FilesAdded: function(up, files) {
                uploader.start();
            },
            UploadComplete: function(up, files) {
                plupload.each(files, function(f) {
                    var data = {
                        name : f.name,
                        size : f.origSize,
                        mime : f.type
                    };
                    $.post("index.php?a=setAvatar", data, function(ret) {
                        if (ret.code == 1) {
//                            $('#showAvatar').attr('src', ret.data);
                            window.location.reload();
                        } else {
                            alert(ret.data);
                        }
                    }, 'json');
                });
            }
        }
    });
    uploader.init();
</script>
</body>
</html>