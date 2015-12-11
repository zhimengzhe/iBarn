<?php
/**
 * @desc: 控制层
 * @author: 樊亚磊 mail:fanyalei@aliyun.com QQ:451802973
 */
class Share extends Abst {

    public function getMyShare() {
        $uid   = (int)$_REQUEST['uid'];
        $urlkey = $_REQUEST['urlkey'];
        $curPage = max($_REQUEST['curPage'], 1);
        $perPage = $_REQUEST['perPage'] ? (int)$_REQUEST['perPage'] : 100;
        $name = $_REQUEST['search'];
        $order = $_REQUEST['order'];
        $by = $_REQUEST['by'] == 'asc' ? 'asc' : 'desc';
        $fac = Factory::getInstance();
        $userinfo = Factory::getInstance('user')->getUserInfo($uid);
        if ($urlkey) {
            $mapId = base_convert($urlkey, 36, 10);
            $map = $fac->getFileMap($mapId);
            $list = $fac->getDirList($map['uid'], $mapId, $order, $by, 0, $curPage, $perPage);
        } else {
            $list = $fac->getMyShareList($uid, $curPage, $perPage, $name, $order, $by);
        }
        if ($list) {
            $fileIocn = json_decode(ICON, true);
            foreach ((array)$list as $k => $v) {
                if (trim($v['name']) == '') {
                    $mapInfo = $fac->getFileMap($v['mapId']);
                    $list[$k]['name'] = $mapInfo['name'] ? $mapInfo['name'] : tip('资料已被删除');
                    $list[$k]['type'] = $mapInfo['type'];
                }
                if ($urlkey) {
                    $list[$k]['mapId'] = $v['id'];
                    $list[$k]['id'] = 0;
                    $list[$k]['shareTime'] = $_REQUEST['shareTime'];
                }
                $icon = $fileIocn[pathinfo($list[$k]['name'], PATHINFO_EXTENSION)];
                if (!$list[$k]['isdir']) {
                    $list[$k]['icon'] = $icon ? $icon : $fileIocn['default'];
                } else {
                    $list[$k]['icon'] = $fileIocn['folder'];
                }
            }
        }
        if (!$_REQUEST['res']) {
            if ($urlkey) {
                $num = $fac->getDirNum($uid, $mapId, 0);
            } else {
                $num = $fac->getMyShareNum($uid, $name);
            }
            $page = ceil($num/$perPage);
            include VIEW_PATH . 'myshare.php';
        } else {
            $html = '';
            if ($list) {
                foreach ((array)$list as $l) {
                    $html .= $this->formatHtml($l);
                }
            }
            echo $html;
        }
    }

    public function getShareMe() {
        $uid   = (int)$_REQUEST['uid'];
        $urlkey = $_REQUEST['urlkey'];
        $curPage = max($_REQUEST['curPage'], 1);
        $perPage = $_REQUEST['perPage'] ? (int)$_REQUEST['perPage'] : 100;
        $name = $_REQUEST['search'];
        $order = $_REQUEST['order'];
        $by = $_REQUEST['by'] == 'asc' ? 'asc' : 'desc';
        $fac = Factory::getInstance();
        $userinfo = Factory::getInstance('user')->getUserInfo($uid);
        if ($urlkey) {
            $mapId = base_convert($urlkey, 36, 10);
            $map = $fac->getFileMap($mapId);
            $list = $fac->getDirList($map['uid'], $mapId, $order, $by, 0, $curPage, $perPage);
        } else {
            $list = $fac->getShareMeList($uid, $curPage, $perPage, $name, $order, $by);
        }
        if ($list) {
            $fileIocn = json_decode(ICON, true);
            foreach ((array)$list as $k => $v) {
                if (trim($v['name']) == '') {
                    $mapInfo = $fac->getFileMap($v['mapId']);
                    $list[$k]['name'] = $mapInfo['name'] ? $mapInfo['name'] : tip('资料已被删除');
                    $list[$k]['type'] = $mapInfo['type'];
                }
                if ($urlkey) {
                    $list[$k]['mapId'] = $v['id'];
                    $list[$k]['id'] = 0;
                    $list[$k]['shareTime'] = $_REQUEST['shareTime'];
                }
                $icon = $fileIocn[pathinfo($list[$k]['name'], PATHINFO_EXTENSION)];
                if (!$list[$k]['isdir']) {
                    $list[$k]['icon'] = $icon ? $icon : $fileIocn['default'];
                } else {
                    $list[$k]['icon'] = $fileIocn['folder'];
                }
            }
        }
        if (!$_REQUEST['res']) {
            if ($urlkey) {
                $num = $fac->getDirNum($uid, $mapId, 0);
            } else {
                $num = $fac->getShareMeNum($uid, $name);
            }
            $page = ceil($num/$perPage);
            include VIEW_PATH . 'shareme.php';
        } else {
            $html = '';
            if ($list) {
                foreach ((array)$list as $l) {
                    $html .= $this->formatHtml($l);
                }
            }
            echo $html;
        }
    }

    protected function formatHtml($info = array()) {
        if (!$info['icon']) {
            $fileIocn = json_decode(ICON, true);
            if (!$info['isdir']) {
                $icon = $fileIocn[pathinfo($info['name'], PATHINFO_EXTENSION)];
                $info['icon'] = $icon ? $icon : $fileIocn['default'];
            } else {
                $info['icon'] = $fileIocn['folder'];
            }
        }
        if ($info['collect']) {
            $collect = '<i class="icon-star starFd"></i>';
        }
        if ($_REQUEST['a'] == 'getMyShare') {
            if (!$info['isdir']) {
                $href = "index.php?a=down&id=" . $info['mapId'];
            } else {
                $href = "index.php?a=mdown&ids=" . $info['mapId'];
            }
            $func = '<li><a href="' . $href . '"><i class="icon-download-alt"></i></a></li>';
        } else {
            $func = '<li><a href="#" onclick="$(\'#sid\').val(' . $info['id'] . ');" data-target="#myModal1" data-toggle="modal"><i class="icon-star"></i></a></li>';
        }
        return  '<li id="li_' . $info['id'] . '">
                <div class="listTableIn pull-left" onmouseenter="$(\'#box_' . $info['mapId'] . '\').show();" onmouseleave="$(\'#box_' . $info['mapId'] . '\').hide();">
                    <div class="listTableInL pull-left">
                      <div class="cBox"><input name="classLists" type="checkbox" value="' . $info['id'] . '"></div>
                      <div class="name">
                          <a id="a_' . $info['id'] . '" target="_blank" href="index.php?a=own&urlkey=' . base_convert($info['mapId'], 10, 36) . '">' . $collect . '
                          <em class="' . $info['icon'] . '"></em></a>
                          <span class="div_pro"><a target="_blank" href="index.php?a=own&urlkey=' . base_convert($info['mapId'], 10, 36) . '">' . htmlspecialchars($info['name'], ENT_NOQUOTES) . '</a></span>
                </div>
                </div>
                <div class="listTableInR pull-right">
                    <div class="size">' . number_format($info['view']) . '</div>
                    <div class="size">' . number_format($info['down']) . '</div>
                    <div class="size">' . number_format($info['saveNum']) . '</div>
                    <div class="updateTime" id="shareTime">' . $info['shareTime'] . '</div>
                    <div style="display:none;position: absolute;margin-left: -40px;" class="float_box" id="box_' . $info['mapId'] . '">
                      <ul class="control">' . $func . '
                      </ul>
                    </div>
                </div>
                </div>
                </li>';
    }

    public function getPub() {
        $curPage = max($_REQUEST['curPage'], 1);
        $perPage = $_REQUEST['perPage'] ? (int)$_REQUEST['perPage'] : 100;
        $name = $_REQUEST['search'];
        $order = $_REQUEST['order'];
        $by = $_REQUEST['by'] == 'asc' ? 'asc' : 'desc';
        $fac = Factory::getInstance();
        $userinfo = Factory::getInstance('user')->getUserInfo($_REQUEST['uid']);
        $list = $fac->getPubList($curPage, $perPage, $name, $order, $by);
        if ($list) {
            $fileIocn = json_decode(ICON, true);
            foreach ((array)$list as $k => $v) {
                if (trim($v['name']) == '') {
                    $mapInfo = $fac->getFileMap($v['mapId']);
                    if (!$mapInfo) {
                        unset($list[$k]);
                        continue;
                    }
                    $list[$k]['urlkey'] = base_convert($v['mapId'], 10, 36);
                    $list[$k]['name'] = $mapInfo['name'] ? $mapInfo['name'] : '名字不存在';
                    $list[$k]['type'] = $mapInfo['type'];
                }
                $list[$k]['pwd'] = $v['pwd'] ? 1 : 0;
                $icon = $fileIocn[pathinfo($list[$k]['name'], PATHINFO_EXTENSION)];
                if (!$list[$k]['isdir']) {
                    $list[$k]['icon'] = $icon ? $icon : $fileIocn['default'];
                } else {
                    $list[$k]['icon'] = $fileIocn['folder'];
                }
                $collecInfo = $fac->isCollect($_REQUEST['uid'], $v['mapId']);
                if ($collecInfo) {
                    $list[$k]['collect'] = 1;
                } else {
                    $list[$k]['collect'] = 0;
                }
            }
        }
        if (!$_REQUEST['res']) {
            $num = $fac->getPubNum($name);
            $page = ceil($num/$perPage);
            include VIEW_PATH . 'pub.php';
        } else {
            $html = '';
            if ($list) {
                foreach ((array)$list as $l) {
                    $html .= $this->formatHtml($l);
                }
            }
            echo $html;
        }
    }

    public function shares() {
        $uid = (int)$_REQUEST['uid'];
        $type = $_REQUEST['type'] ? (int)$_REQUEST['type'] : 1;
        $mapId = (int)$_REQUEST['id'];
        $price = (int)$_REQUEST['price'];
        $pwd = self::trimSpace(rawurldecode($_REQUEST['pwd']));
        $suser = self::trimSpace(rawurldecode($_REQUEST['suser']));
        if (strlen($pwd) > 8) {
            echo Response::json(FAIL, array(tip('密码不能超过8位')));
            exit;
        }
        if ($type != 1) {
            if (!$suser) {
                echo Response::json(FAIL, array(tip('被分享人不能为空')));
                exit;
            } else {
                $userinfo = Factory::getInstance('user')->getUserByName($suser);
                $sid = $userinfo['uid'];
                if (!$sid) {
                    echo Response::json(FAIL, array(tip('被分享人不存在')));
                    exit;
                }
                if ($uid == $sid) {
                    echo Response::json(FAIL, array(tip('不能分享给自己')));
                    exit;
                }
            }
        }
        $overTime = $_REQUEST['overTime'];
        if (!$uid || !$mapId) {
            echo Response::json(LACK, array(tip('参数不全')));
            exit;
        }
        $res = Factory::getInstance()->share($uid, $type, $mapId, $pwd, $overTime, $price, $sid);
        if ($res == -1) {
            echo Response::json(FORB, array(tip('文件已分享，不能重复分享')));
        } elseif ($res) {
            echo Response::json(SUCC, array(tip('操作成功')));
        } else {
            echo Response::json(FAIL, array(tip('操作失败')));
        }
    }

    public function unShare() {
        $uid = (int)$_REQUEST['uid'];
        $mapId = $_REQUEST['ids'];
        if (!$uid || !$mapId) {
            echo Response::json(LACK, array(tip('参数不全')));
            exit;
        }
        $res = Factory::getInstance()->unShare($uid, $mapId);
        if ($res) {
            echo Response::json(SUCC, array(tip('操作成功')));
        } else {
            echo Response::json(FAIL, array(tip('操作失败')));
        }
    }

    public function unShareAll() {
        $uid = (int)$_REQUEST['uid'];
        if (!$uid) {
            echo Response::json(LACK, array(tip('参数不全')));
            exit;
        }
        $res = Factory::getInstance()->unShareAll($uid);
        if ($res) {
            echo Response::json(SUCC, array(tip('操作成功')));
        } else {
            echo Response::json(FAIL, array(tip('操作失败')));
        }
    }

    public function pwd() {
        $mapId = (int)$_REQUEST['mapId'];
        $pwd = $_REQUEST['pwd'];
        if (!$mapId || !$pwd) {
            echo Response::json(LACK, array(tip('参数不全')));
            exit;
        }
        if (strlen($pwd) > 8) {
            echo Response::json(FAIL, array(tip('密码不能超过8位')));
            exit;
        }
        $res = Factory::getInstance()->pwd($mapId, $pwd);
        if ($res) {
            $_SESSION['share'][self::getClientIp() . ':' . $mapId] = 1;
            echo Response::json(SUCC, array('urlkey' => base_convert($mapId, 10, 36)));
        } else {
            echo Response::json(FAIL, array(tip('验证失败')));
        }
    }
}
?>