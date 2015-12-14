<?php include_once 'head.php'; ?>
      <!--main content start-->
      <section id="main-content">
          <section class="wrapper">
              <!--state overview start-->
              <div class="row state-overview">
              		<ul class="buttons pull-left">
                  		<li>
                            <div id="ucontainer">
                                <button class="btn btn-danger" type="button" data-target="#myModal1" data-toggle="modal">
                                    <i class="icon-remove-sign"></i>
                                    <?php echo t('彻底删除'); ?>
                                </button>
                            </div>
                      </li>
                  	  <li>
                          <button class="btn btn-success" type="button" onclick="file.recover();">
                              <i class="icon-smile"></i>
                              <?php echo t('还原资料'); ?>
                          </button>
                      </li>
                  </ul>
                  <form action="index.php?class=recycle" method="post" onsubmit="return file.check();">
                      <div class="searchRight pull-right">
                          <div class="input-group m-bot15">
                              <div class="input-group-btn">
                                  <button class="btn btn-white" type="button"><?php echo t('全部'); ?></button>
                              </div>
                              <input type="text" class="form-control" id="search" name="search" value="<?php echo $_REQUEST['search']; ?>">
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
                                        <div class="size"><?php echo t('大小'); ?></div>
                                        <div class="updateTime"><?php echo t('上传时间'); ?></div>
                                    </div>
                                </div>
                            </li>
                            <?php if ($list) {
                            foreach ((array)$list as $k => $v) {
                            ?>
                      		<li id="li_<?php echo $v['id']; ?>" onclick="">
                          		<div class="listTableIn pull-left">
                              		<div class="listTableInL pull-left">
                                      <div class="cBox"><input name="classLists" type="checkbox" value="<?php echo $v['id']; ?>"></div>
                                      <div class="name">
                                          <em class="<?php echo $v['icon']; ?>"></em>
                                          <span class="div_pro">
                                              <?php echo htmlspecialchars($v['name'], ENT_NOQUOTES); ?>
                                          </span>
                                      </div>
                                  </div>
                                  <div class="listTableInR pull-right">
                                      <div class="size"><?php echo $v['size']; ?></div>
                                      <div class="updateTime"><?php echo $v['time']; ?></div>
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
                          <li><a href="index.php?class=recycle&search=<?php echo htmlspecialchars($search, ENT_NOQUOTES); ?>&curPage=<?php echo max($curPage-1, 1); ?>"><?php echo t('上一页'); ?></a></li>
                      <?php }
                      if ($curPage < $page) { ?>
                          <li><a href="index.php?class=recycle&search=<?php echo htmlspecialchars($search, ENT_NOQUOTES); ?>&curPage=<?php echo min(max($curPage, 1)+1, $page); ?>"><?php echo t('下一页'); ?></a></li>
                      <?php } ?>
                  </ul>
              <?php } ?>
              <input type="hidden" id="fileId">
              <input type="hidden" id="path" value="<?php echo htmlspecialchars($_REQUEST['path'], ENT_NOQUOTES); ?>">
              <div aria-hidden="false" aria-labelledby="myModalLabel" role="dialog" tabindex="-1" id="myModal1" class="modal fade in" style="display: none;">
                  <div class="modal-dialog">
                      <div class="modal-content">
                          <div class="modal-header pull-left">
                              <button aria-hidden="true" data-dismiss="modal" class="close" type="button">×</button>
                              <h4 class="modal-title"><?php echo t('删除'); ?></h4>
                          </div>
                          <div class="modal-body pull-left">
                              <div class="delText"><?php echo t('确定要删除吗？'); ?></div>
                          </div>
                          <div class="modal-footer">
                              <button type="button" class="btn btn-success" onclick="file.realDel();"><?php echo t('确定'); ?></button>
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
    <script src="lib/view/js/owl.carousel.js" ></script>
    <script src="lib/view/js/jquery.customSelect.min.js" ></script>
    <script src="lib/view/js/respond.min.js" ></script>

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
</script>
</body>
</html>
