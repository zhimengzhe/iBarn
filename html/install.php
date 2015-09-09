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
<body>
<section id="container">
    <header class="header white-bg">
        <div class="pull-left logo" title="iBarn - 会思考的网盘"><a href="#">iBarn</a></div>
    </header>
    <section class="wrapper">
        <div class="row">
            <div class="col-md-4" style="top:50px; float: none;display: block; margin:auto;">
                <div class="box box-primary">
                    <div class="box-header">
                        <h3 class="box-title">创建管理员账号</h3>
                    </div><!-- /.box-header -->

                    <div class="box-body">
                        <div class="form-group">
                            <input type="text" placeholder="用户名" class="form-control" id="name">
                        </div>
                        <div class="form-group">
                            <input type="text" placeholder="密码" class="form-control" id="pwd">
                        </div>
                    </div>

                    <div class="box-header">
                        <h3 class="box-title">存储文件目录</h3>
                    </div>
                    <div class="box-body">
                        <div class="form-group">
                            <input type="text" placeholder="存储文件目录" id="file" class="form-control" value="<?php echo dirname(__DIR__) . DS . 'files'; ?>">
                        </div>
                    </div>
                    <div class="box-header">
                        <h3 class="box-title">配置Mysql数据库</h3>
                    </div>
                    <div class="box-body">
                        <div class="form-group">
                            <input type="text" placeholder="数据库用户名" id="dbuname" class="form-control">
                            <input type="text" placeholder="数据库密码" id="dbpwd" class="form-control">
                            <input type="text" placeholder="数据库名" id="dbname" class="form-control">
                            <input type="text" placeholder="数据库HOST" id="host" class="form-control">
                            <input type="text" placeholder="数据库端口" id="port" value="3306" class="form-control">
                        </div>
                    </div>
                    <div class="box-footer">
                        <button class="btn btn-primary" type="button" onclick="install();">一键安装</button>
                    </div>
                </div>
            </div><!-- /.col -->
        </div>
    </section>
</section><!-- /.content -->
<script src="lib/view/js/jquery.js"></script>
<script src="lib/view/js/bootstrap.min.js"></script>
<script>
    function error(obj) {
        var oTimer = null;
        var i = 0;
        oTimer = setInterval(function() {
            i++;
            i == 5 ? clearInterval(oTimer) : (i % 2 == 0 ? obj.css("background-color", "#ffffff") : obj.css("background-color", "#ffd4d4"));
        }, 200);
    }
    function install() {
        var name = $('#name').val();
        var pwd = $('#pwd').val();
        var file = $('#file').val();
        var dbuname = $('#dbuname').val();
        var dbpwd = $('#dbpwd').val();
        var dbname = $('#dbname').val();
        var host = $('#host').val();
        if (!name) {
            error($('#name'));
            return;
        }
        if (!name) {
            error($('#name'));
            return;
        }
        if (!pwd) {
            error($('#pwd'));
            return;
        }
        if (!file) {
            error($('#file'));
            return;
        }
        if (!dbname) {
            error($('#dbname'));
            return;
        }
        if (!host) {
            error($('#host'));
            return;
        }
        $.ajax({
            url: 'install.php',
            type: 'POST',
            data:{ name : name, pwd : pwd, file : file, dbuname : dbuname, dbpwd : dbpwd, dbname : dbname, host : host },
            dataType: 'json',
            timeout: 8000,
            success: function(data){
                if (data.code == 1) {
                    window.location.href = 'index.php';
                } else {
                    alert(data.data);
                }
            }
        });
    }
</script>
</body>
</html>