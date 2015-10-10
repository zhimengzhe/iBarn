<?php include_once 'head.php'; ?>
      <!--main content start-->
      <section>
          <section class="wrapper">
              <div class="row">
                  <div class="modal-dialog">
                      <div class="modal-content" style="margin-bottom: 200px;">
                          <div class="modal-body pull-left">
                              <div class="modalTitleSmall pull-left"><?php echo t('密码'); ?>：</div>
                              <div class="col-lg-8 pull-left">
                                  <input class="form-control" id="pwd" type="text" placeholder="<?php echo t('请输入密码'); ?>">
                                  <input type="hidden" id="mapId" name="mapId" value="<?php echo $mapId; ?>">
                              </div>
                              <div class="col-lg-2 pull-left">
                                  <button type="button" class="btn btn-success" onclick="file.pwd();"><?php echo t('确定'); ?></button>
                              </div>
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

    <script type="text/javascript" src="js/file.js"></script>
<?php include_once 'foot.php'; ?>