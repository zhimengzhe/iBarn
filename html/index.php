<?php include_once 'head.php'; ?>
<link rel="stylesheet" type="text/css" href="lib/view/assets/bootstrap-datetimepicker/css/datetimepicker.css" />
<link href="lib/view/css/share.css" rel="stylesheet" type="text/css"/>
      <!--main content start-->
      <section id="main-content">
          <section class="wrapper">
              <!--state overview start-->
              <div class="row state-overview">
              		<ul class="buttons pull-left">
                  		<li>
                            <div id="ucontainer">
                                <button class="btn btn-info" type="button" id="pickfiles">
                                    <i class="icon-cloud-upload"></i>
                                    <?php echo t('上传资料'); ?>
                                </button>
                            </div>
                        </li>
                        <li>
                            <button class="btn btn-success" type="button" data-target="#myModal5" data-toggle="modal">
                                <i class="icon-folder-close"></i>
                                <?php echo t('新建文件夹'); ?>
                            </button>
                        </li>
                        <li>
                            <button class="btn btn-danger" type="button" data-target="#myModal4" onclick="$('#delid').val('');" data-toggle="modal">
                                <i class="icon-remove"></i>
                                <?php echo t('删除'); ?>
                            </button>
                        </li>
                        <li id="down">
                            <button class="btn btn-success" type="button" onclick="down();">
                                <i class="icon-download"></i>
                                <?php echo t('下载'); ?>
                            </button>
                        </li>
                        <li>
                            <button class="btn btn-warning" type="button" onclick="modalTrans();$('#sid').val('');" data-toggle="modal">
                                <i class="icon-random"></i>
                                <?php echo t('移动'); ?>
                            </button>
                        </li>
                        <li id="share">
                            <button class="btn btn-info" type="button" onclick="modalShare(0, 0);">
                                <i class="icon-share"></i>
                                <?php echo t('分享'); ?>
                            </button>
                        </li>
                        <li id="rename" <?php if ($_COOKIE['show'] != 'block') { ?>style="display: none;" <?php } ?>>
                            <button class="btn btn-danger" type="button" onclick="modalName(0, '');" data-toggle="modal">
                                <i class="icon-edit"></i>
                                <?php echo t('重命名'); ?>
                            </button>
                        </li>
                  </ul>
<!--                  <h4 style="margin-left: 15px;margin-bottom: 20px;">如果觉得好，在<a target="_blank" href="https://github.com/zhimengzhe/iBarn">https://github.com/zhimengzhe/iBarn</a>上给颗星；欢迎参观主站：<a href="http://www.godeye.org">http://www.godeye.org</a></h4>-->
                  <form action="index.php" onsubmit="return file.check();">
                      <div class="searchRight pull-right">
                            <div class="input-group m-bot15">
                              <div class="input-group-btn">
                                  <button class="btn btn-white" type="button"><?php echo t('全部'); ?></button>
                              </div>
                              <input type="text" class="form-control" id="search" name="search" value="<?php echo htmlspecialchars($_REQUEST['search'], ENT_NOQUOTES); ?>" placeholder="<?php echo t('搜你想要'); ?>">
                              <input type="hidden" id="type" name="type" value="<?php echo (int)$_REQUEST['type']; ?>">
                          </div>
                          <button class="btn btn-success searchButton" type="submit">
                              <i class="icon-search"></i>
                              <?php echo t('搜索'); ?>
                          </button>
                      </div>
                  </form>
                  <div class="toggle pull-right">
                      <button class="listBtn <?php if (!$_COOKIE['show'] || $_COOKIE['show'] == 'list') { ?>active<?php } ?>"><i class="icon-list-ul"></i></button>
                      <button class="blockBtn <?php if ($_COOKIE['show'] == 'block') { ?>active<?php } ?>"><i class="icon-th-large"></i></button>
                  </div>
                  <input type="hidden" id="path" name="path" value="<?php echo htmlspecialchars($_REQUEST['path'], ENT_NOQUOTES); ?>">
                  <input type="hidden" id="dpath" name="dpath">
                  <input type="hidden" id="sid" name="sid">
                  <input type="hidden" id="delid" name="delid">
                  <div class="mainMenu">
                      <a href="index.php?type=1">
                      <section class="panel">
                          <div class="symbol terques">
                              <i class="icon-file-text"></i>
                          </div>
                          <div class="value">
                              <h4><?php echo t('我的文档'); ?></h4>
                          </div>
                      </section>
                      </a>
                  </div>
                  <div class="mainMenu">
                      <a href="index.php?type=2">
                      <section class="panel">
                          <div class="symbol red">
                              <i class="icon-picture"></i>
                          </div>
                          <div class="value">
                              <h4><?php echo t('我的图片'); ?></h4>
                          </div>
                      </section>
                      </a>
                  </div>
                  <div class="mainMenu">
                      <a href="index.php?type=3">
                      <section class="panel">
                          <div class="symbol yellow">
                              <i class="icon-music"></i>
                          </div>
                          <div class="value">
                              <h4><?php echo t('我的音乐'); ?></h4>
                          </div>
                      </section>
                      </a>
                  </div>
                  <div class="mainMenu">
                      <a href="index.php?type=4">
                      <section class="panel">
                          <div class="symbol blue">
                              <i class="icon-film"></i>
                          </div>
                          <div class="value">
                              <h4><?php echo t('我的视频'); ?></h4>
                          </div>
                      </section>
                      </a>
                  </div>
                  <div class="mainMenu">
                      <a href="index.php?type=5">
                      <section class="panel">
                          <div class="symbol terques">
                              <i class="icon-cloud-download"></i>
                          </div>
                          <div class="value">
                              <h4><?php echo t('BT种子'); ?></h4>
                          </div>
                      </section>
                      </a>
                  </div>
                  <div class="mainMenu">
                      <a href="index.php?type=6">
                      <section class="panel">
                          <div class="symbol yellow">
                              <i class="icon-folder-open-alt"></i>
                          </div>
                          <div class="value">
                              <h4><?php echo t('其他'); ?></h4>
                          </div>
                      </section>
                      </a>
                  </div>
              </div>
              <!--state overview end-->
              <?php if ($_REQUEST['path']) { ?>
              <div class="row">
                  <div class="col-lg-12">
                      <!--breadcrumbs start -->
                      <ul class="breadcrumb">
                          <li><a href="index.php?path=<?php echo $prePath; ?>"><i class="icon-mail-reply"></i> <?php echo t('返回上一级'); ?></a></li>
                          <li><a href="index.php"><?php echo t('所有资料'); ?></a></li>
                          <li class="active"><?php echo t('当前目录'); ?></li>
                      </ul>
                      <!--breadcrumbs end -->
                  </div>
              </div>
              <?php } ?>
              <div class="row">
                  <div class="col-lg-12" id="block" <?php if ($_COOKIE['show'] != 'block') { ?>style="display: none;"<?php } ?>>
                      <ul class="listType pull-left">
                        <?php if ($list) {
                            foreach ((array)$list as $k => $v) {
                        ?>
                          <li onmouseenter="$('#checkShow<?php echo $v['id']; ?>').show();$(this).attr('class', 'in')" onmouseleave="if($('#squaredFour<?php echo $v['id']; ?>').prop('checked') == false) {$('#checkShow<?php echo $v['id']; ?>').hide();$(this).removeClass('in');}" id="bli_<?php echo $v['id']; ?>">
                              <div class="squaredFour" id="checkShow<?php echo $v['id']; ?>">
                                  <input type="checkbox" id="squaredFour<?php echo $v['id']; ?>" name="squaredCheckbox" class="squaredCheckbox" value="<?php echo $v['id']; ?>"/>
                                  <label for="squaredFour<?php echo $v['id']; ?>"></label>
                              </div>
                              <a target="_self" <?php if ($v['type'] == 2) { ?>data-lightbox="img_<?php echo $v['id']; ?>"<?php } ?> <?php if ($v['isdir']) { ?> href="index.php?path=<?php echo (trim(rawurlencode($_REQUEST['path']), '/') ? trim(rawurlencode($_REQUEST['path']), '/') . '/' : '') . htmlspecialchars($v['name'], ENT_NOQUOTES); ?>" <?php } elseif ($v['type'] == 2) { ?> href="index.php?a=view&urlkey=<?php echo base_convert($v['id'], 10, 36); ?>" <?php } else { ?> href="index.php?a=down&urlkey=<?php echo base_convert($v['id'], 10, 36); ?>" <?php } ?> id="ba_<?php echo $v['id']; ?>">
                                  <div id="d_<?php echo $v['id']; ?>" class="big <?php echo $v['bicon'] . 'Big'; ?>"><?php if ($v['share']) { ?><div class="shareFdBig"></div><?php } ?></div>
                                  <p><?php if (function_exists('mb_substr')) { echo htmlspecialchars(mb_substr($v['name'], 0, 12, 'utf8'), ENT_NOQUOTES);
                                      } else { echo htmlspecialchars(substr($v['name'], 0, 12), ENT_NOQUOTES); } ?></p>
                                  <input type="hidden" id="aname_<?php echo $v['id']; ?>" value="<?php echo htmlspecialchars($v['name'], ENT_NOQUOTES); ?>">
                              </a>
                          </li>
                        <?php }
                        } ?>
                      </ul>
                  </div>
                  <div class="col-lg-12" id="list" <?php if ($_COOKIE['show'] == 'block') { ?>style="display: none;"<?php } ?>>
                  		<ul class="listTable pull-left">
                            <li id="fileList">
                                <div class="listTableTop pull-left">
                                    <div class="listTableTopL pull-left">
                                        <div class="cBox"><input id="chkAll" name="chkAll" type="checkbox"></div>
                                        <div class="name" id="name"><?php echo t('名称'); ?><div class="seq"></div></div>
                                    </div>
                                    <div class="listTableTopR pull-right">
                                        <div class="size" id="size"><?php echo t('大小'); ?><div class="seq"></div></div>
                                        <div class="updateTime" id="ctime"><?php echo t('上传时间'); ?><div class="seq"></div></div>
                                    </div>
                                </div>
                            </li>
                            <?php if ($list) {
                            foreach ((array)$list as $k => $v) {
                                $ext = pathinfo($v['name'], PATHINFO_EXTENSION);
                            ?>
                      		<li id="li_<?php echo $v['id']; ?>">
                          		<div class="listTableIn pull-left" onmouseenter="$('#box_<?php echo $v['id']; ?>').show();" onmouseleave="$('#box_<?php echo $v['id']; ?>').hide();">
                              		<div class="listTableInL pull-left">
                                      <div class="cBox"><input name="classLists" id="classLists<?php echo $v['id']; ?>" type="checkbox" value="<?php echo $v['id']; ?>" class="classLists"></div>
                                      <div class="name">
                                          <a target="_self" id="a_<?php echo $v['id']; ?>" <?php if ($v['type'] == 2) { ?>data-lightbox="img_<?php echo $v['id']; ?>"<?php } ?> <?php if ($v['isdir']) { ?> href="index.php?path=<?php echo (trim(rawurlencode($_REQUEST['path']), '/') ? trim(rawurlencode($_REQUEST['path']), '/') . '/' : '') . htmlspecialchars($v['name'], ENT_NOQUOTES); ?>" <?php } elseif ($v['type'] == 2) { ?> href="index.php?a=view&urlkey=<?php echo base_convert($v['id'], 10, 36); ?>" <?php } else { ?> href="index.php?a=down&urlkey=<?php echo base_convert($v['id'], 10, 36); ?>" <?php } ?> id="p_<?php echo $v['id']; ?>"><?php if ($v['share']) { ?><div class="shareFd"></div><?php } ?><em class="<?php echo $v['icon']; ?>"></em></a>
                                      		<span class="div_pro">
                                                <a id="sa_<?php echo $v['id']; ?>" target="_self" <?php if ($v['type'] == 2) { ?>data-lightbox="img_<?php echo $v['id']; ?>"<?php } ?> <?php if ($v['isdir']) { ?> href="index.php?path=<?php echo (trim(rawurlencode($_REQUEST['path']), '/') ? trim(rawurlencode($_REQUEST['path']), '/') . '/' : '') . htmlspecialchars($v['name'], ENT_NOQUOTES); ?>" <?php } elseif ($v['type'] == 2) { ?> href="index.php?a=view&urlkey=<?php echo base_convert($v['id'], 10, 36); ?>" <?php } else { ?> href="index.php?a=down&urlkey=<?php echo base_convert($v['id'], 10, 36); ?>" <?php } ?>><?php echo htmlspecialchars($v['name'], ENT_NOQUOTES); ?></a>
                                  		    </span>
                                  		</div>
                                  </div>
                                  <div class="listTableInR pull-right">
                                      <div class="size"><?php echo $v['size']; ?></div>
                                      <div class="updateTime"><?php echo $v['time']; ?></div>
                                      <div style="display:none;" class="float_box" id="box_<?php echo $v['id']; ?>">
                                          <ul class="control">
                                              <li><a title="<?php echo t('下载'); ?>" <?php if (!$v['isdir']) { ?>href="index.php?a=down&id=<?php echo $v['id']; ?>"<?php } else {?>href="index.php?a=mdown&ids=<?php echo $v['id']; ?>"<?php } ?>><i class="icon-download-alt"></i></a></li>
                                              <li><a title="<?php echo t('分享'); ?>" href="#" onclick="modalShare(<?php echo $v['id']; ?>, '<?php echo base_convert($v['id'], 10, 36); ?>');" data-toggle="modal"><i class="icon-share"></i></a></li>
                                              <li><a title="<?php echo t('编辑'); ?>" href="#" onclick="modalName(<?php echo $v['id']; ?>, '<?php echo htmlspecialchars($v['name'], ENT_NOQUOTES); ?>');" data-toggle="modal"><i class="icon-edit"></i></a></li>
                                              <li><a title="<?php echo t('移动'); ?>" href="#" onclick="$('#sid').val(<?php echo $v['id']; ?>);modalTrans();" data-toggle="modal"><i class="icon-random"></i></a></li>
                                              <li><a title="<?php echo t('删除'); ?>" href="#" onclick="modalDel('<?php echo htmlspecialchars($v['name'], ENT_NOQUOTES); ?>');$('#delid').val(<?php echo $v['id']; ?>);" data-toggle="modal"><i class="icon-remove"></i></a></li>
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
                  <ul class="pagination pagination-sm pull-right">
                      <?php if ($curPage > 1) { ?>
                          <li><a href="javascript:;" onclick="page(-1);"><?php echo t('上一页'); ?></a></li>
                      <?php }
                      if ($curPage < $page) { ?>
                          <li><a href="javascript:;" onclick="page(0);"><?php echo t('下一页'); ?></a></li>
                      <?php } ?>
                  </ul>
              <?php } ?>
              <input type="hidden" id="fileId">
              <input type="hidden" id="dirId">
              <input type="hidden" id="order">
              <input type="hidden" id="by">
              <div aria-hidden="true" aria-labelledby="myModalLabel" role="dialog" tabindex="-1" id="myModal" class="modal fade" style="display: none;">
                  <div class="modal-dialog">
                      <div class="modal-content">
                          <div class="modal-header pull-left">
                              <button aria-hidden="true" data-dismiss="modal" class="close" type="button">×</button>
                              <h4 class="modal-title"><?php echo t('共享文件'); ?></h4>
                          </div>
                          <div class="modal-body pull-left">
                              <div class="w100 pull-left">
                                  <div class="modalTitle pull-left"><?php echo t('共享对象'); ?>：</div>
                                  <div class="modalTitleR pull-left">
                                      <select id="shareType" onchange="show();">
                                          <option value="1"><?php echo t('公开'); ?></option>
                                          <option value="2"><?php echo t('个人'); ?></option>
                                      </select>
                                  </div>
                              </div>
                              <div class="w100 pull-left">
                                  <div class="modalTitle pull-left"><?php echo t('共享链接'); ?>：</div>
                                  <div class="modalTitleR pull-left" id="href"></div>
                              </div>
                              <div id="showSid" class="w100 pull-left" style="display: none;">
                                  <div class="modalTitle pull-left"><?php echo t('被分享人'); ?>：</div>
                                  <div class="modalTitleR pull-left">
                                      <input type="text" id="suser" data-provide="typeahead" autocomplete="off" class="form-control" placeholder="<?php echo t('输入被分享人用户名'); ?>">
                                  </div>
                              </div>
                              <div class="w100 pull-left" id="showPub">
                                  <div class="modalTitle pull-left"></div>
                                  <div class="modalTitleR pull-left">
                                      <div class="form-group pull-left">
                                          <label class="col-lg-3 col-sm-2 control-label pwd" for="editPwd"><input type="checkbox" id="editPwd" name="edit"> <?php echo t('设置密码'); ?></label>
                                          <div class="col-lg-4" id="editPwdIpt" style="display: none;">
                                              <input type="password" placeholder="<?php echo t('至多8位'); ?>" id="inputPassword" class="form-control">
                                          </div>
                                      </div>
                                      <div class="form-group pull-left">
                                          <label class="col-lg-3 col-sm-2 control-label pwd" for="editDate">
                                              <input type="checkbox" id="editDate" name="edit"> <?php echo t('设置过期时间'); ?></label>
                                          <div class="col-lg-4" id="editDateIpt" style="display: none;">
                                              <input type="text" id="inputDate" class="form_datetime-adv form-control">
                                          </div>
                                      </div>
                                  </div>
                              </div>
                          </div>
                          <div class="modal-footer">
                              <button type="button" class="btn btn-success" onclick="file.share();"><?php echo t('确定'); ?></button>
                              <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo t('取消'); ?></button>
                          </div>
                      </div>
                  </div>
              </div>
              <div aria-hidden="false" aria-labelledby="myModalLabel" role="dialog" tabindex="-1" id="myModal2" class="modal fade in" style="display: none;">
                  <div class="modal-dialog">
                      <div class="modal-content">
                          <div class="modal-header pull-left">
                              <button aria-hidden="true" data-dismiss="modal" class="close" type="button">×</button>
                              <h4 class="modal-title"><?php echo t('编辑'); ?></h4>
                          </div>
                          <div class="modal-body pull-left">
                          		<div>
                                    <div class="modalTitleSmall pull-left"><?php echo t('名称'); ?>：</div>
                                    <div class="col-lg-10 marginB10 pull-left">
                                  		<input class="form-control" id="newName" type="text" placeholder="<?php echo t('请输入名称'); ?>">
                                        <input type="hidden" id="aname" name="aname">
                                    </div>
                                </div>
                          </div>
                          <div class="modal-footer">
                              <button type="button" class="btn btn-success" onclick="file.setName();"><?php echo t('确定'); ?></button>
                              <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo t('取消'); ?></button>
                          </div>
                      </div>
                  </div>
              </div>
              <div aria-hidden="false" aria-labelledby="myModalLabel" role="dialog" tabindex="-1" id="myModal3" class="modal fade in" style="display: none;">
                  <div class="modal-dialog">
                      <div class="modal-content">
                          <div class="modal-header pull-left">
                              <button aria-hidden="true" data-dismiss="modal" class="close" type="button">×</button>
                              <h4 class="modal-title"><?php echo t('移动到'); ?></h4>
                          </div>
                          <div class="modal-body pull-left" style="height: 412px;overflow-y: scroll;">
                              <div id="tree"></div>
                          </div>
                          <div class="modal-footer">
                              <button type="button" class="btn btn-success" onclick="file.trans();"><?php echo t('确定'); ?></button>
                              <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo t('取消'); ?></button>
                          </div>
                      </div>
                  </div>
              </div>
              <div aria-hidden="false" aria-labelledby="myModalLabel" role="dialog" tabindex="-1" id="myModal4" class="modal fade in" style="display: none;">
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
                              <button type="button" class="btn btn-success" onclick="file.del();"><?php echo t('确定'); ?></button>
                              <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo t('取消'); ?></button>
                          </div>
                      </div>
                  </div>
              </div>
              <div aria-hidden="false" aria-labelledby="myModalLabel" role="dialog" tabindex="-1" id="myModal5" class="modal fade in" style="display: none;">
                  <div class="modal-dialog">
                      <div class="modal-content">
                          <div class="modal-header pull-left">
                              <button aria-hidden="true" data-dismiss="modal" class="close" type="button">×</button>
                              <h4 class="modal-title"><?php echo t('新建文件夹'); ?></h4>
                          </div>
                          <div class="modal-body pull-left">
                              <div>
                                  <div class="modalTitleSmall pull-left"><?php echo t('名称');?>：</div>
                                  <div class="col-lg-10 marginB10 pull-left">
                                      <input class="form-control" id="folderName" type="text" placeholder="<?php echo t('请输入名称'); ?>">
                                  </div>
                              </div>
                          </div>
                          <div class="modal-footer">
                              <button type="button" class="btn btn-success" onclick="file.addFolder();"><?php echo t('确定'); ?></button>
                              <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo t('取消'); ?></button>
                          </div>
                      </div>
                  </div>
              </div>
          </section>
      </section>
      <!--main content end-->
  </section>
  <div id="msg_win">
    <div class="icos"><a id="msg_min" title="<?php echo t('最小化'); ?>" href="javascript:void 0">_</a><a id="msg_close" title="<?php echo t('关闭'); ?>" href="javascript:void 0">×</a></div>
    <div id="msg_title"><?php echo t('上传文件'); ?></div>
    <div id="msg_content">
    	<ul class="pull-left" id="progress"></ul>
    </div>
  </div>

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
    <script src="lib/view/js/respond.min.js"></script>

    <script class="include" type="text/javascript" src="lib/view/js/jquery.dcjqaccordion.2.7.js"></script>

    <!--common script for all pages-->
    <script src="lib/view/js/common-scripts.js"></script>

    <!--script for this page-->
    <script src="lib/view/js/sparkline-chart.js"></script>
    <script src="lib/view/js/easy-pie-chart.js"></script>
    <script src="lib/view/js/count.js"></script>

    <script type="text/javascript" src="lib/view/assets/bootstrap-typeahead/bootstrap3-typeahead.js"></script>
    <script type="text/javascript" src="lib/view/assets/bootstrap-wysihtml5/bootstrap-wysihtml5.js"></script>
    <script type="text/javascript" src="lib/view/assets/bootstrap-datetimepicker/js/bootstrap-datetimepicker.js"></script>
    <script type="text/javascript" src="lib/view/assets/bootstrap-colorpicker/js/bootstrap-colorpicker.js"></script>

    <script type="text/javascript" src="lib/view/assets/jquery-multi-select/js/jquery.multi-select.js"></script>
    <script type="text/javascript" src="lib/view/assets/jquery-multi-select/js/jquery.quicksearch.js"></script>
    <script type="text/javascript" src="lib/view/js/advanced-form-components.js"></script>
    <script src="lib/view/js/checkbox.js"></script>

    <script type="text/javascript" src="lib/plupload/js/plupload.full.min.js"></script>
    <script type="text/javascript" src="lib/hash/hash.js"></script>
    <script type="text/javascript" src="lib/hash/sha.js"></script>
    <script type="text/javascript" src="lib/treeview/src/js/bootstrap-treeview.js"></script>
    <script type="text/javascript" src="js/file.js"></script>
    <script type="text/javascript" src="js/upload.js"></script>
    <script type="text/javascript">
    $('body').on('hidden', '.modal', function () {$(this).removeData('modal');});
    function modalShare(id, urlkey) {
        if (!id) {
            if (Cookies.get('show') == 'block') {
                name="squaredCheckbox";
            } else {
                name="classLists";
            }
            if ($('input[name="'+ name +'"]:checked').length > 1) {
                alert(file.lang('每个文件共享链接唯一，一次只能分享一个文件'));
                return false;
            }
            $('input[name="'+ name +'"]:checked').each(function() {
                id = $(this).val();
            });
            if (!id) {
                alert(file.lang('请选择要分享资源'));
                return false;
            }
            href = 'index.php?a=view&id=' + id;
        } else {
            href = 'index.php?a=view&urlkey=' + urlkey;
        }
        $('#fileId').val(id);
        $('#href').html('<a target="_blank" href="' + href + '">'+window.location.href.substring(0,window.location.href.lastIndexOf('/')) + '/' + href + '</a>');
        $('#myModal').modal('show');
    }
    function modalTrans() {
        $('#myModal3').modal('show');
        $.ajax({
            url: 'index.php?a=getTree',
            type: 'POST',
            dataType: 'json',
            timeout: 8000,
            success: function(data) {
                $('#tree').treeview({data: data});
                $('#tree').on('nodeSelected', function(event, data) {
                    $('#dirId').val(data.mapId);
                    $('#dpath').val(data.path);
                });
                $('#tree').on('nodeUnselected', function(event, data) {
                    $('#dirId').val('');
                });
            }
        });
    }
    function modalName(id, name) {
        if (!id) {
            if ($('input[name="squaredCheckbox"]:checked').length > 1) {
                alert(file.lang('一次只能重命名一个资料'));
                return false;
            }
            $('input[name="squaredCheckbox"]:checked').each(function(){
                id = $(this).val();
            });
            if (!id) {
                alert(file.lang('请选择要重命名的资料'));
                return false;
            }
            name = $('#aname_' + id).val();
        }
        $('#newName').val(name.replace(/\.\w+$/, ''));
        $('#aname').val(name);
        $('#fileId').val(id);
        $('#myModal2').modal('show');
    }
    function modalDel(name) {
        $('.delText label').text(name);
        $('#myModal4').modal('show');
    }
    function down() {
        if (Cookies.get('show') == 'block') {
            name = 'squaredCheckbox';
        } else {
            name = 'classLists';
        }
        var ids = new Array();
        $('input[name="' + name + '"]:checked').each(function() {
            ids.push($(this).val());
        });
        var idstr = ids.join(',');
        if (!idstr) {
            alert(file.lang('请选择要下载的文件'));
            return false;
        }
        href = '';
        if(idstr.indexOf(",") <= 0){
            if (name == 'squaredCheckbox') {
                href = $('#ba_' + idstr).attr('href');
            } else {
                href = $('#a_' + idstr).attr('href');
            }
            info = href.match(/urlkey=([^&]+)/);
        }
        if (href && info) {
            window.location.href = 'index.php?a=down&id=' + idstr;
        } else {
            window.location.href = 'index.php?a=mdown&ids=' + idstr;
        }
    }
    var uploader = new plupload.Uploader({
      runtimes : 'html5,flash,silverlight,html4',
      browse_button : 'pickfiles',
      container: document.getElementById('ucontainer'),
      url : 'index.php?a=upload',
      chunk_size : '1024kb',
      flash_swf_url : 'lib/plupload/js/Moxie.swf',
      silverlight_xap_url : 'lib/plupload/js/Moxie.xap',
      filters : {
//          max_file_size : '4096mb',
          mime_types : []
      },
      multipart_params : { path : $('#path').val() },
      init: {
          FilesAdded: function(up, files) {
              plupload.each(files, function(file) {
                  document.getElementById('progress').innerHTML +=
                  '<li><div class="uploadTitle pull-left">' + file.name + '</div>'+
                  '<div class="uploadSize pull-left">'+ plupload.formatSize(file.size) +'</div>'+
                  '<div id="' + file.id + '" class="uploadProportion pull-right">'+
                  '</div></li>';
                  var hash = new hashMe(file.getNative(), function OutputHash(data) {
                      file.hash = data;
                      var data = {
                          fileName : file.name,
                          fileSize : file.origSize,
                          hash : data,
                          size : up.settings.chunk_size,
                          maxFileCount : Math.ceil(file.origSize/up.settings.chunk_size)
                      };
                      $.post("index.php?a=uploadCheck", data, function(dy) {
                          file.loaded = dy.data;
                          setTimeout(function() {
                              Message.init();
                              uploader.start();
                          }, 10);
                      }, 'json');
                  });
              });
          },
          UploadComplete: function(up, files) {
              if (Cookies.get('show') == 'block') {
                  type = 1;
              } else {
                  type = 0;
              }
              plupload.each(files, function(f) {
                  var data = {
                      name : f.name,
                      hash : f.hash,
                      path : $('#path').val(),
                      size : f.origSize,
                      mime : f.type,
                      type : type
                  };
                  $.post("index.php?a=putFile", data, function(ret) {
                      if (ret.code == 1) {
                          document.getElementById(f.id).innerHTML = file.lang('上传成功');
                          if (!type) {
                              $('#fileList').after($("<div></div>").html(ret.data).text());
                              var ps = $(".listTableIn").position();
                              if (ps) {
                                  $(".float_box").css("position", "absolute");
                                  $(".float_box").css("right", ps.left); //距离左边距
                                  $(".float_box").css("top", + 7); //距离上边距
                              }
                          } else {
                              $('.listType').prepend($("<div></div>").html(ret.data).text());
                          }
                          setTimeout('Message.close()', 4000);
                      } else {
                          document.getElementById(f.id).innerHTML = file.lang(ret.data);
                      }
                  }, 'json');
              });
              uploader.splice();
              uploader.refresh();
          },
          UploadProgress: function(up, file) {
              document.getElementById(file.id).innerHTML = '<div class="progress progress-xs"><div style="width: ' + file.percent + '%" aria-valuemax="100" aria-valuemin="0" aria-valuenow="' + file.percent + '" role="progressbar" class="progress-bar progress-bar-info"></div></div></div>';
          }
      }
    });
    uploader.init();

    $(document).ready(function() {
        var ps = $(".listTableIn").position();
        if (ps) {
            $(".float_box").css("position", "absolute");
            $(".float_box").css("right", ps.left); //距离左边距
            $(".float_box").css("top", + 7); //距离上边距
        }
        $(".listTable.pull-left").on('click', 'li', function() {
            if ($(this).attr('id') != 'fileList') {
                if ($(this).hasClass("selected")) {
                    $(this).removeClass("selected").find(":checkbox").prop("checked", false);
                } else {
                    $(this).addClass("selected").find(":checkbox").prop("checked", true);
                }
            }
        });
        $(".toggle button").click(function() {
            if($(this).hasClass("active")){
                return false;
            } else {
                $(this).addClass("active");
                $(this).siblings().removeClass("active");
                if ($(this).hasClass('listBtn')) {
                    $('#list').show();
                    $('#block').hide();
                    Cookies.set('show', 'list');
                    $('#rename').hide();
                    window.location.reload();
                } else {
                    $('#list').hide();
                    $('#block').show();
                    Cookies.set('show', 'block');
                    $('#rename').show();
                    window.location.reload();
                }
            }
        });
        $('#suser').typeahead({
            source : function (query, process) {
                $.ajax({
                    url: 'index.php?m=user&a=getUsersByName',
                    type: 'post',
                    data: { name: query },
                    dataType: 'json',
                    success: function (ret) {
                        return process(ret);
                    }
                });
            }
        });
    })
    $("#chkAll").click(function() {
        if (this.checked) {
            $('input:checkbox[name="classLists"]').prop("checked", true);
        } else {
            $('input:checkbox[name="classLists"]').prop("checked", false);
        }
    });
    window.onload = function() {
        $('input:checkbox[name="classLists"]').prop("checked", false);
    }
    $('input[name="classLists"]').click(function(){
        $('#chkAll').attr('checked', $('input[name="classLists"]:checked').length == $('input[name="classLists"]').length);
    });

    var i = 0;
    var j = 0;
    $('#editPwd').click(function() {
        i++%2==0?$('#editPwdIpt').show():$('#editPwdIpt').hide();
    });
    $('#editDate').click(function() {
        j++%2==0?$('#editDateIpt').show():$('#editDateIpt').hide();
    });

    $("#name, #size, #ctime").click(function() {
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
        $(this).parent().siblings().children().removeClass("downward");
        $(this).parent().siblings().children().removeClass("descending");
        $('#order').val($(this).attr('id'));
        $('#by').val(by);
        $.ajax({
            url : 'index.php',
            type : 'POST',
            data : {
                path : encodeURIComponent($('#path').val()),
                order : $(this).attr('id'),
                by : by,
                search : encodeURIComponent($('#search').val()),
                type : $('#type').val(),
                res : 1,
                curPage : <?php echo (int)$curPage; ?>
            },
            dataType: 'html',
            timeout: 8000,
            success : function(data) {
                if (data) {
                    $(".listTable.pull-left li").not(":first").remove();
                　　$('#fileList').after(data);
                    var ps = $(".listTableIn").position();
                    if (ps) {
                        $(".float_box").css("position", "absolute");
                        $(".float_box").css("right", ps.left); //距离左边距
                        $(".float_box").css("top", + 7); //距离上边距
                    }
                }
            }
        });
    });
    function show() {
        if ($('#shareType').val() == 1) {
            $('#showPub').show();
            $('#showSid').hide();
        } else {
            $('#showPub').hide();
            $('#showSid').show();
        }
    }
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
            href = 'index.php?path=<?php echo htmlspecialchars($path, ENT_NOQUOTES); ?>&search=<?php echo htmlspecialchars($search, ENT_NOQUOTES); ?>&curPage=<?php echo max($curPage-1, 1); ?>&type=<?php echo (int)$type; ?>&order=' + order + '&by=' + by;
        } else if (type == 0) {
            href = 'index.php?path=<?php echo htmlspecialchars($path, ENT_NOQUOTES); ?>&search=<?php echo htmlspecialchars($search, ENT_NOQUOTES); ?>&curPage=<?php echo min(max($curPage, 1)+1, $page); ?>&type=<?php echo (int)$type; ?>&order=' + order + '&by=' + by;
        }
        window.location.href = href;
    }
</script>
<?php include_once 'foot.php'; ?>