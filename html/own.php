<?php include_once 'head.php'; ?>
<link href="lib/view/css/share.css" rel="stylesheet" type="text/css"/>
      <!--main content start-->
      <section>
          <section class="wrapper">
              <!--state overview start-->
              <div class="row state-overview">
                  <ul class="buttons pull-left">
                      <li id="down"  style="margin-left: 120px;">
                          <button class="btn btn-success" type="button" onclick="down();">
                              <i class="icon-download"></i>
                              下载
                          </button>
                      </li>
                      <?php if (!$_REQUEST['pid']) { ?>
                      <li>
                          <div id="ucontainer">
                              <button class="btn btn-info" type="button" onclick="file.collect();">
                                  <i class="icon-star"></i>
                                  收藏
                              </button>
                          </div>
                      </li>
                      <?php } ?>
                  </ul>
                  <form action="index.php?m=share&a=getPub" method="post" onsubmit="return file.check();">
                      <div class="searchRight pull-right">
                          <div class="input-group m-bot15">
                              <div class="input-group-btn">
                                  <button class="btn btn-white" type="button">全部</button>
                              </div>
                              <input type="text" class="form-control" id="search" name="search" value="<?php echo htmlspecialchars($_REQUEST['search'], ENT_NOQUOTES); ?>" placeholder="搜你想要">
                          </div>
                          <button class="btn btn-success searchButton" type="submit">
                              <i class="icon-search"></i>
                              搜索
                          </button>
                      </div>
                  </form>
              </div>
              <!--state overview end-->
              <?php if ($_REQUEST['pid']) { ?>
                  <div class="row">
                      <div class="col-lg-12">
                          <!--breadcrumbs start -->
                          <ul class="breadcrumb">
                              <li><a href="index.php?a=own&urlkey=<?php echo base_convert($mapInfo['id'], 10, 36); ?>"><i class="icon-mail-reply"></i> 返回</a></li>
                              <li class="active">当前目录</li>
                          </ul>
                          <!--breadcrumbs end -->
                      </div>
                  </div>
              <?php } ?>
              <div class="row">
                  <div class="col-lg-12">
                      <ul class="listType pull-left">
                          <?php if ($list) {
                              foreach ((array)$list as $k => $v) {
                          ?>
                          <li onmouseenter="$('#checkShow<?php echo $v['id']; ?>').show();$(this).attr('class', 'in')" onmouseleave="if($('#squaredFour<?php echo $v['id']; ?>').prop('checked') == false) {$('#checkShow<?php echo $v['id']; ?>').hide();$(this).removeClass('in');}">
                              <div class="squaredFour" id="checkShow<?php echo $v['id']; ?>">
                                  <input type="checkbox" id="squaredFour<?php echo $v['id']; ?>" name="squaredCheckbox" class="squaredCheckbox" value="<?php echo $v['id']; ?>"/>
                                  <label for="squaredFour<?php echo $v['id']; ?>"></label>
                              </div>
                              <a target="_self" <?php if ($v['type'] == 2) { ?>data-lightbox="roadtrip"<?php } ?> <?php if ($v['isdir']) { ?> href="index.php?a=own&urlkey=<?php echo base_convert($mapInfo['id'], 10, 36); ?>&pid=<?php echo $v['id']; ?>" <?php } elseif ($v['type'] == 2) { ?> href="index.php?a=view&urlkey=<?php echo base_convert($v['id'], 10, 36); ?>" <?php } else { ?> href="index.php?a=down&urlkey=<?php echo base_convert($v['id'], 10, 36); ?>" <?php } ?> id="a_<?php echo $v['id']; ?>">
                                  <div class="big <?php echo $v['bicon'] . 'Big'; ?>"></div>
                                  <p><?php echo htmlspecialchars(mb_substr($v['name'], 0, 10, 'utf8'), ENT_NOQUOTES); ?></p>
                              </a>
                          </li>
                          <?php }
                                } elseif ($mapInfo['type'] == 2) { ?>
                              <div class="row shareOnly pull-left">
                                  <div class="shareOnlyImg">
                                      <a target="_blank" id="a_<?php echo $mapInfo['id']; ?>" href="index.php?a=view&urlkey=<?php echo $_REQUEST['urlkey']; ?>"><img src="index.php?a=view&urlkey=<?php echo $_REQUEST['urlkey']; ?>" width="480" height="476"></a>
                                  </div>
                              </div>
                          <?php } elseif ($mapInfo && !$mapInfo['isdir']) { ?>
                              <div class="row shareOnly pull-left">
                                  <div class="shareOnlyFile">
                                      <a id="a_<?php echo $mapInfo['id']; ?>" href="index.php?a=down&urlkey=<?php echo $_REQUEST['urlkey']; ?>">
                                          <div class="big <?php echo $mapInfo['bicon'] . 'Big'; ?>"></div>
                                          <p><?php echo htmlspecialchars($mapInfo['name'], ENT_NOQUOTES); ?></p>
                                      </a>
                                  </div>
                              </div>
                          <?php } ?>
                      </ul>
                  </div>
              </div>
              <input type="hidden" id="sid" value="<?php echo $shareInfo['id']; ?>">
              <input type="hidden" id="fid" value="<?php echo $mapInfo['id']; ?>">
              <input type="hidden" id="pid" value="<?php echo $_REQUEST['pid']; ?>">
              <?php if ($page > 1) { ?>
                  <ul class="pagination pagination-sm">
                      <?php if ($curPage > 1) { ?>
                          <li><a href="javascript:;" onclick="page(-1);">上一页</a></li>
                      <?php }
                      if ($curPage < $page) { ?>
                          <li><a href="javascript:;" onclick="page(0);">下一页</a></li>
                      <?php } ?>
                  </ul>
              <?php } ?>
          </section>
      </section>
      <!--main content end-->
  </section>

    <!-- js placed at the end of the document so the pages load faster -->
    <script src="lib/view/js/jquery.js"></script>
    <script src="lib/view/js/bootstrap.min.js"></script>
    <script class="include" type="text/javascript" src="lib/view/js/jquery.dcjqaccordion.2.7.js"></script>
    <script src="lib/view/js/jquery.scrollTo.min.js"></script>
    <script src="lib/view/js/jquery.nicescroll.js" type="text/javascript"></script>
    <script src="lib/view/js/jquery.sparkline.js" type="text/javascript"></script>
    <script src="lib/view/assets/jquery-easy-pie-chart/jquery.easy-pie-chart.js"></script>
    <script src="lib/view/js/owl.carousel.js" ></script>
    <script src="lib/view/js/jquery.customSelect.min.js" ></script>
    <script src="lib/view/js/respond.min.js" ></script>

    <script class="include" type="text/javascript" src="lib/view/js/jquery.dcjqaccordion.2.7.js"></script>

    <!--common script for all pages-->
    <script type="text/javascript" src="lib/view/js/common-scripts.js"></script>

    <!--script for this page-->
    <script type="text/javascript" src="lib/view/js/sparkline-chart.js"></script>
    <script type="text/javascript" src="lib/view/js/easy-pie-chart.js"></script>
    <script type="text/javascript" src="lib/view/js/count.js"></script>

    <script type="text/javascript" src="lib/view/assets/bootstrap-wysihtml5/bootstrap-wysihtml5.js"></script>
    <script type="text/javascript" src="lib/view/assets/bootstrap-datetimepicker/js/bootstrap-datetimepicker.js"></script>
    <script type="text/javascript" src="lib/view/assets/bootstrap-colorpicker/js/bootstrap-colorpicker.js"></script>
    <script type="text/javascript" src="lib/view/assets/bootstrap-timepicker/js/bootstrap-timepicker.js"></script>
    <script type="text/javascript" src="lib/view/assets/jquery-multi-select/js/jquery.multi-select.js"></script>
    <script type="text/javascript" src="lib/view/assets/jquery-multi-select/js/jquery.quicksearch.js"></script>
    <script type="text/javascript" src="lib/view/js/advanced-form-components.js"></script>
    <script type="text/javascript" src="lib/view/js/checkbox.js"></script>
    <script type="text/javascript" src="js/file.js"></script>
    <script>
    $("#chkAll").click(function() {
        if (this.checked) {
            $('input:checkbox[name="classLists"]').prop("checked", true);
        } else {
            $('input:checkbox[name="classLists"]').prop("checked", false);
        }
    });
    $('input[name="classLists"]').click(function(){
        $('#chkAll').attr('checked', $('input[name="classLists"]:checked').length == $('input[name="classLists"]').length);
    });
    $('body').on('hidden', '.modal', function() {$(this).removeData('modal');});

    //custom select box
    $(function() {
        $('select.styled').customSelect();
        $(".listTable.pull-left").on('click', 'li', function() {
            if ($(this).attr('id') != 'fileList') {
                if ($(this).hasClass("selected")) {
                    $(this).removeClass("selected").find(":checkbox").prop("checked", false);
                } else {
                    $(this).addClass("selected").find(":checkbox").prop("checked", true);
                }
            }
        });
    });
    function page(type) {
        if (type == -1) {
            href = 'index.php?m=share&a=getPub&search=<?php echo $search; ?>&curPage=<?php echo max($curPage-1, 1); ?>';
        } else if (type == 0) {
            href = 'index.php?m=share&a=getPub&search=<?php echo $search; ?>&curPage=<?php echo min(max($curPage, 1)+1, $page); ?>';
        }
        window.location.href = href;
    }
    function down() {
        id = $('#fid').val();
        pid = $('#pid').val();
        if (pid) {
            var ids = new Array();
            $('input[name="squaredCheckbox"]:checked').each(function(){
                ids.push($(this).val());
            });
            var idstr = ids.join(',');
            if (!idstr) {
                alert('请选择要下载的文件');
                return false;
            }

            href = '';
            if(idstr.indexOf(",") <= 0){
                href = $('#a_' + idstr).attr('href');
                info = href.match(/pid=([^&]+)/);
            }
            if (href && !info) {
                window.location.href = 'index.php?a=down&id=' + idstr;
            } else {
                window.location.href = 'index.php?a=mdown&ids=' + idstr;
            }
        } else {
            if (!id) {
                alert('请选择要下载的文件');
                return false;
            }
            href = $('#a_' + id).attr('href');
            info = href.match(/urlkey=([^&]+)/);
            if (!info) {
                alert('请选择要下载的文件');
                return false;
            }
            pinfo = href.match(/pid=([^&]+)/);
            if (href && !pinfo) {
                window.location.href = 'index.php?a=down&urlkey=' + info[1];
            } else {
                window.location.href = 'index.php?a=mdown&ids=' + id;
            }
        }
    }
</script>
<?php include_once 'foot.php'; ?>