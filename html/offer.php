<?php include_once 'head.php'; ?>
      <!--main content start-->
      <section id="main-content">
          <section class="wrapper">
              <!--state overview start-->
              <div class="row state-overview">
                  <ul class="buttons pull-left">
                      <li>
                          <div id="ucontainer">
                              <button class="btn btn-info" type="button" data-target="#myModal1" data-toggle="modal" onclick="$('#sid').val('');">
                                  <i class="icon-star"></i>
                                  <?php echo t('收藏'); ?>
                              </button>
                          </div>
                      </li>
                  </ul>
                  <form action="index.php?a=offer" method="post" onsubmit="return file.check();">
                      <div class="searchRight pull-right">
                          <div class="input-group m-bot15">
                              <div class="input-group-btn">
                                  <button class="btn btn-white" type="button"><?php echo t('全部'); ?></button>
                              </div>
                              <input type="text" class="form-control" id="search" name="search" value="<?php echo $_REQUEST['search']; ?>" placeholder="<?php echo t('搜你想要'); ?>">
                          </div>
                          <button class="btn btn-success searchButton" type="submit">
                              <i class="icon-search"></i>
                              <?php echo t('搜索'); ?>
                          </button>
                      </div>
                  </form>
              </div>
              <!--state overview end-->
              <div class="row">
                  <div class="col-lg-12">
                  		<ul class="listTable pull-left">
                            <li id="fileList">
                                <div class="listTableTop pull-left">
                                    <div class="listTableTopL pull-left">
                                        <div class="cBox"><input id="chkAll" type="checkbox"></div>
                                        <div class="name"><?php echo t('名称'); ?></div>
                                    </div>
                                    <div class="listTableTopR pull-right">
                                        <div class="size" id="size"><?php echo t('大小'); ?></div>
                                        <div class="updateTime" id="ctime"><?php echo t('上传时间'); ?></div>
                                    </div>
                                </div>
                            </li>
                            <?php if ($list) {
                            foreach ((array)$list as $k => $v) {
                            ?>
                      		<li id="li_<?php echo $v['id']; ?>">
                          		<div class="listTableIn pull-left" onmouseenter="$('#box_<?php echo $v['id']; ?>').show();" onmouseleave="$('#box_<?php echo $v['id']; ?>').hide();">
                              		<div class="listTableInL pull-left">
                                        <div class="cBox"><input name="classLists" type="checkbox" value="<?php echo $v['sid']; ?>"></div>
                                      <div class="name">
                                          <a target="_blank" id="a_<?php echo $v['sid']; ?>" href="index.php?a=own&urlkey=<?php echo base_convert($v['id'], 10, 36); ?>"><?php if ($v['collect']) { ?><i class="icon-star starFd"></i><?php } ?><em class="<?php echo $v['icon']; ?>"></em></a>
                                          <span class="div_pro">
                                              <a target="_blank" href="index.php?a=own&urlkey=<?php echo base_convert($v['id'], 10, 36); ?>"><?php echo htmlspecialchars($v['name'], ENT_NOQUOTES); ?></a>
                                          </span>
                                      </div>
                                  </div>
                                  <div class="listTableInR pull-right">
                                      <div class="size"><?php echo $v['size']; ?></div>
                                      <div class="updateTime"><?php echo $v['time']; ?></div>
                                      <div style="display:none;position: absolute;margin-left: -40px;" class="float_box" id="box_<?php echo $v['id']; ?>">
                                          <ul class="control">
                                              <li><a href="#" alt="<?php echo t('收藏'); ?>" onclick="$('#sid').val(<?php echo $v['sid']; ?>);" data-target="#myModal1" data-toggle="modal"><i class="icon-star"></i></a></li>
                                          </ul>
                                      </div>
                                  </div>
                              </div>
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
              <?php } ?>
              <input type="hidden" id="fileId">
              <input type="hidden" id="sid">
              <input type="hidden" id="order">
              <input type="hidden" id="by">
              <div aria-hidden="false" aria-labelledby="myModalLabel" role="dialog" tabindex="-1" id="myModal1" class="modal fade in" style="display: none;">
                  <div class="modal-dialog">
                      <div class="modal-content">
                          <div class="modal-header pull-left">
                              <button aria-hidden="true" data-dismiss="modal" class="close" type="button">×</button>
                              <h4 class="modal-title"><?php echo t('收藏'); ?></h4>
                          </div>
                          <div class="modal-body pull-left">
                              <div class="delText"><?php echo t('确定要收藏吗？'); ?></div>
                          </div>
                          <div class="modal-footer">
                              <button type="button" class="btn btn-success" onclick="file.collect();"><?php echo t('确定'); ?></button>
                              <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo t('取消'); ?></button>
                          </div>
                      </div>
                  </div>
              </div>
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
    <script src="lib/view/js/owl.carousel.js"></script>
    <script src="lib/view/js/jquery.customSelect.min.js"></script>
    <script src="lib/view/js/respond.min.js"></script>
    <script class="include" type="text/javascript" src="lib/view/js/jquery.dcjqaccordion.2.7.js"></script>

    <!--common script for all pages-->
    <script src="lib/view/js/common-scripts.js"></script>

    <!--script for this page-->
    <script src="lib/view/js/sparkline-chart.js"></script>
    <script src="lib/view/js/easy-pie-chart.js"></script>
    <script src="lib/view/js/count.js"></script>

    <script type="text/javascript" src="lib/view/assets/bootstrap-wysihtml5/bootstrap-wysihtml5.js"></script>
    <script type="text/javascript" src="lib/view/assets/bootstrap-datetimepicker/js/bootstrap-datetimepicker.js"></script>
    <script type="text/javascript" src="lib/view/assets/bootstrap-colorpicker/js/bootstrap-colorpicker.js"></script>
    <script type="text/javascript" src="lib/view/assets/bootstrap-timepicker/js/bootstrap-timepicker.js"></script>
    <script type="text/javascript" src="lib/view/assets/jquery-multi-select/js/jquery.multi-select.js"></script>
    <script type="text/javascript" src="lib/view/assets/jquery-multi-select/js/jquery.quicksearch.js"></script>
    <script src="lib/view/js/advanced-form-components.js"></script>

    <script type="text/javascript" src="js/file.js"></script>
    <script>
    function modalPwd(mapId) {
        $('#mapId').val(mapId);
        $('#myModal').modal('show');
    }
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
    $('body').on('hidden', '.modal', function () {$(this).removeData('modal');});

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
    <?php if (!$urlkey) { ?>
    $("#view, #down, #shareTime, #saveNum").click(function(){
        var by;
        if ($(this).children().hasClass("downward")) {
            $(this).children().removeClass("downward");
            $(this).children().addClass("descending");
            by = 'desc';
        } else {
            if ($(this).children().hasClass("descending")){
                $(this).children().removeClass("descending");
                $(this).children().addClass("downward");
                by = 'asc';
            } else {
                $(this).children().addClass("downward");
                by = 'asc';
            }
        }
        $('#order').val($(this).attr('id'));
        $('#by').val(by);
        $(this).parent().siblings().children().removeClass("downward");
        $(this).parent().siblings().children().removeClass("descending");
        $.ajax({
            url : 'index.php?m=share&a=getPub',
            type : 'POST',
            data : {
                order : $(this).attr('id'),
                by : by,
                search : encodeURIComponent($('#search').val()),
                res : 1,
                curPage : <?php echo (int)$curPage; ?>
            },
            dataType: 'html',
            timeout: 8000,
            success : function(data) {
                if (data) {
                    $(".listTable.pull-left li").not(":first").remove();
                　　$('#fileList').after(data);
                }
            }
        });
    });
    <?php } ?>
    function page(type) {
        order = $('#order').val();
        by = $('#by').val();
        if (!order) {
            order = <?php echo "'" . $_REQUEST['order'] . "'"; ?>;
        }
        if (!by) {
            by = <?php echo "'" . $_REQUEST['by'] . "'"; ?>;
        }
        if (type == -1) {
            href = 'index.php?m=share&a=getPub&urlkey=<?php echo htmlspecialchars($urlkey, ENT_NOQUOTES); ?>&search=<?php echo htmlspecialchars($search, ENT_NOQUOTES); ?>&curPage=<?php echo max($curPage-1, 1); ?>&order=' + order + '&by=' + by;
        } else if (type == 0) {
            href = 'index.php?m=share&a=getPub&urlkey=<?php echo htmlspecialchars($urlkey, ENT_NOQUOTES); ?>&search=<?php echo htmlspecialchars($search, ENT_NOQUOTES); ?>&curPage=<?php echo min(max($curPage, 1)+1, $page); ?>&order=' + order + '&by=' + by;
        }
        window.location.href = href;
    }
</script>
<?php include_once 'foot.php'; ?>