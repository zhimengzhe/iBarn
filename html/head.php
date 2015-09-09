<!DOCTYPE html>
<html lang="cn">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="iBarn - 会思考的网盘。考研资料应有尽有！">
    <meta name="keyword" content="iBarn,网盘,云盘">
    <link rel="shortcut icon" href="img/favicon.png">

    <title>iBarn - 会思考的网盘</title>

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
</head>
<body <?php if (strtolower($_REQUEST['m']) == 'user') { ?>class="loginBg"<?php } ?>>
<section id="container">
    <?php if (strtolower($_REQUEST['m']) == 'user') { ?>
    <div class="loginLogo"></div>
    <?php } else { ?>
    <!--header start-->
    <header class="header white-bg">
        <?php if (strtolower($_REQUEST['a']) != 'own' && strtolower($_REQUEST['m']) != 'user') { ?>
            <div class="sidebar-toggle-box">
                <div data-original-title="收起菜单" data-placement="right" class="icon-reorder tooltips"></div>
            </div>
        <?php } ?>
        <!--logo start-->
        <div class="pull-left logo" title="iBarn - 会思考的网盘"><a href="#">iBarn</a></div>
        <!--logo end-->
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
                        <li><a href="#"><i class="icon-suitcase"></i>简介</a></li>
                        <li><a href="#"><i class="icon-cog"></i>设置</a></li>
                        <li><a href="#"><i class="icon-bell-alt"></i>消息</a></li>
                        <li><a href="index.php?m=user&a=logout"><i class="icon-key"></i>退出</a></li>
                    </ul>
                </li>
                <!-- user login dropdown end -->
            </ul>
            <!--search & user info end-->
        </div>
        <?php } ?>
    </header>
    <!--header end-->
    <?php } ?>
    <!--sidebar start-->
    <?php if (strtolower($_REQUEST['a']) != 'own' && strtolower($_REQUEST['m']) != 'user' && strtolower($_REQUEST['m']) != 'pay') { ?>
    <aside>
        <div id="sidebar"  class="nav-collapse">
            <!-- sidebar menu start-->
            <ul class="sidebar-menu" id="nav-accordion">
                <li <?php if (!$_REQUEST['m'] && !$_REQUEST['class'] && $_REQUEST['a'] != 'offer') { ?>class="on"<?php } ?>>
                    <a href="index.php">
                        <i class="icon-folder-close-alt"></i>
                        <span>所有资料</span>
                    </a>
                </li>
                <li class="inMenu <?php if ($_REQUEST['type'] == 1) { ?>on<?php } ?>">
                    <a href="index.php?type=1">
                        &nbsp;<i class="icon-file-text"></i>
                        <span>&nbsp;我的文档</span>
                    </a>
                </li>
                <li class="inMenu <?php if ($_REQUEST['type'] == 2) { ?>on<?php } ?>">
                    <a href="index.php?type=2">
                        <i class="icon-picture"></i>
                        <span>我的图片</span>
                    </a>
                </li>
                <li class="inMenu <?php if ($_REQUEST['type'] == 3) { ?>on<?php } ?>">
                    <a href="index.php?type=3">
                        <i class="icon-music"></i>
                        <span>&nbsp;我的音乐</span>
                    </a>
                </li>
                <li class="inMenu <?php if ($_REQUEST['type'] == 4) { ?>on<?php } ?>">
                    <a href="index.php?type=4">
                        <i class="icon-film"></i>
                        <span>我的视频</span>
                    </a>
                </li>
                <li class="inMenu <?php if ($_REQUEST['type'] == 5) { ?>on<?php } ?>">
                    <a href="index.php?type=5">
                        <i class="icon-download"></i>
                        <span>&nbsp;BT种子</span>
                    </a>
                </li>
                <li class="inMenu <?php if ($_REQUEST['type'] == 6) { ?>on<?php } ?>">
                    <a href="index.php?type=6">
                        <i class="icon-folder-open-alt"></i>
                        <span>其他</span>
                    </a>
                </li>
                <li <?php if ($_REQUEST['m'] == 'collection') { ?>class="on"<?php } ?>>
                    <a href="index.php?m=collection&a=getCollect">
                        <i class="icon-star"></i>
                        <span>我的收藏</span>
                    </a>
                </li>
                <li <?php if ($_REQUEST['m'] == 'share' && $_REQUEST['a'] == 'getMyShare') { ?>class="on"<?php } ?>>
                    <a href="index.php?m=share&a=getMyShare">
                        <i class="icon-share"></i>
                        <span>我的分享</span>
                    </a>
                </li>
                <li <?php if ($_REQUEST['a'] == 'offer') { ?>class="on"<?php } ?>>
                    <a href="index.php?a=offer">
                        <i class="icon-gift"></i>
                        <span>官方推荐</span>
                    </a>
                </li>
                <li <?php if ($_REQUEST['m'] == 'share' && $_REQUEST['a'] == 'getPub') { ?>class="on"<?php } ?>>
                    <a href="index.php?m=share&a=getPub">
                        <i class="icon-rss"></i>
                        <span>公共资源</span>
                    </a>
                </li>
                <li <?php if ($_REQUEST['class'] == 'recycle') { ?>class="on"<?php } ?>>
                    <a href="index.php?class=recycle">
                        <i class="icon-trash"></i>
                        <span>回收站</span>
                    </a>
                </li>
            </ul>
            <!-- sidebar menu end-->
        </div>
        <?php if (SPACE) { $space = json_decode(SPACE, true); } ?>
        <div class="capacity">
            <div class="progress progress-xs">
                <div style="width: <?php echo $space['percent']; ?>%" aria-valuemax="100" aria-valuemin="0" aria-valuenow="<?php echo $space['percent']; ?>" role="progressbar" class="progress-bar">
                    <span class="sr-only"><?php echo $space['percent']; ?>% Complete (success)</span>
                </div>
            </div>
            <div class="pull-left">
                <span><?php echo $space['spaceFormat']; ?></span> / <?php echo $space['all']; ?>.00G
            </div>
        </div>
    </aside>
    <?php } ?>
    <!--sidebar end-->