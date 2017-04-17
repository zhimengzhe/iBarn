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
                            <img width="40" height="40" alt="<?php echo htmlspecialchars($userinfo['name'], ENT_NOQUOTES); ?>" src="<?php echo $userinfo['avatar'] ? htmlspecialchars($userinfo['avatar'], ENT_NOQUOTES) : DEFAULT_AVATAR; ?>">
                            <span class="username"><?php echo htmlspecialchars($userinfo['name'], ENT_NOQUOTES); ?></span>
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
                <div class="col-md-2 col-sm-3 col-xs-6" style="padding-left:0;float:left;">
                    <div style="margin-top: 22px;">
                        <img src="<?php echo $info['avatar'] ? htmlspecialchars($info['avatar'], ENT_NOQUOTES) : DEFAULT_AVATAR; ?>" width="143px" height="143px"/>
                    </div>
                </div>
                <div style="margin-top: 22px;float:left;">
                    <div class="form-group">
                        <h3><?php echo htmlspecialchars($info['name'], ENT_NOQUOTES); ?></h3>
                    </div>
                    <div class="form-group">
                        Email : <?php echo htmlspecialchars($info['email'], ENT_NOQUOTES); ?>
                    </div>
                    <a class="btn btn-primary" type="button" href="index.php"><?php echo t('返回网盘'); ?></a>
                    <?php if ($userinfo['uid'] == $info['uid'] && $info['uid']) { ?>
                        <a class="btn btn-info" type="button" href="index.php?m=user&a=set"><?php echo t('编辑个人资料'); ?></a>
                    <?php } ?>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12" id="block">
                <div style="margin: 20px;"><h3><?php echo t('共享文件'); ?>：</h3></div>
                <ul class="listType pull-left">
                    <?php if ($list) {
                        foreach ((array)$list as $k => $v) {
                            ?>
                            <li id="bli_<?php echo $v['mapId']; ?>">
                                <a target="_self" <?php if ($v['type'] == 2) { ?>data-lightbox="img_<?php echo $v['mapId']; ?>"<?php } ?> <?php if ($v['isdir']) { ?> href="index.php?path=<?php echo (trim(rawurlencode($_REQUEST['path']), '/') ? trim(rawurlencode($_REQUEST['path']), '/') . '/' : '') . htmlspecialchars($v['name'], ENT_NOQUOTES); ?>" <?php } elseif ($v['type'] == 2) { ?> href="index.php?a=view&urlkey=<?php echo base_convert($v['mapId'], 10, 36); ?>" <?php } else { ?> href="index.php?a=down&urlkey=<?php echo base_convert($v['mapId'], 10, 36); ?>" <?php } ?> id="ba_<?php echo $v['mapId']; ?>">
                                    <div id="d_<?php echo $v['mapId']; ?>" class="big <?php echo $v['bicon'] . 'Big'; ?>"></div>
                                    <p><?php if (function_exists('mb_substr')) { echo htmlspecialchars(mb_substr($v['name'], 0, 12, 'utf8'), ENT_NOQUOTES);
                                        } else { echo htmlspecialchars(substr($v['name'], 0, 12), ENT_NOQUOTES); } ?></p>
                                    <input type="hidden" id="aname_<?php echo $v['mapId']; ?>" value="<?php echo htmlspecialchars($v['name'], ENT_NOQUOTES); ?>">
                                </a>
                            </li>
                        <?php }
                    } ?>
                </ul>
            </div>
        </div>
        <?php if ($page > 1) { ?>
            <ul class="pagination pagination-sm">
                <?php if ($curPage > 1) { ?>
                    <li><a href="javascript:;" onclick="page(-1);"><?php echo t('上一页'); ?></a></li>
                <?php }
                if ($curPage < $page) { ?>
                    <li><a href="javascript:;" onclick="page(0);"><?php echo t('下一页'); ?></a></li>
                <?php } ?>
            </ul>
            <ul class="pagination pagination-sm pull-right">
                <?php if ($curPage > 1) { ?>
                    <li><a href="javascript:;" onclick="page(-1);"><?php echo t('上一页'); ?></a></li>
                <?php }
                if ($curPage < $page) { ?>
                    <li><a href="javascript:;" onclick="page(0);"><?php echo t('下一页'); ?></a></li>
                <?php } ?>
            </ul>
        <?php } ?>
    </section>
</section><!-- /.content -->
<script src="lib/view/js/jquery.js"></script>
<script src="lib/view/js/bootstrap.min.js"></script>
</body>
</html>