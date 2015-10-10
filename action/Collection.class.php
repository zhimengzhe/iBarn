<?php
/**
 * @desc: 控制层
 * @author: 樊亚磊 mail:fanyalei@aliyun.com QQ:451802973
 */
class Collection extends Abst {

    public function getCollect() {
        $uid = (int)$_REQUEST['uid'];
        $curPage = max($_REQUEST['curPage'], 1);
        $perPage = $_REQUEST['perPage'] ? (int)$_REQUEST['perPage'] : 100;
        $name = $_REQUEST['search'];
        $order = $_REQUEST['order'];
        $by = $_REQUEST['by'] == 'asc' ? 'asc' : 'desc';
        $fac = Factory::getInstance();
        $userinfo = Factory::getInstance('user')->getUserInfo($uid);
        $list = $fac->getCollectList($uid, $curPage, $perPage, $name, $order, $by);
        if ($list) {
            $fileIcon = json_decode(ICON, true);
            foreach ((array)$list as $k => $v) {
                if (trim($v['name']) == '') {
                    $mapInfo = $fac->getFileMap($v['mapId']);
                    $list[$k]['name'] = trim($mapInfo['name']) != '' ? $mapInfo['name'] : '资料已被删除';
                    $list[$k]['size'] = self::formatBytes($v['size']);
                    $list[$k]['type'] = $mapInfo['type'];
                }
                $list[$k]['pwd'] = $v['pwd'] ? 1 : 0;
                $icon = $fileIcon[pathinfo($list[$k]['name'], PATHINFO_EXTENSION)];
                if (!$list[$k]['isdir']) {
                    $list[$k]['icon'] = $icon ? $icon : $fileIcon['default'];
                } else {
                    $list[$k]['icon'] = $fileIcon['folder'];
                }
            }
        }
        if (!$_REQUEST['res']) {
            $num = $fac->getCollectNum($uid, $name);
            $page = ceil($num/$perPage);
            include VIEW_PATH . 'collection.php';
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
            $fileIcon = json_decode(ICON, true);
            if (!$info['isdir']) {
                $icon = $fileIcon[pathinfo($info['name'], PATHINFO_EXTENSION)];
                $info['icon'] = $icon ? $icon : $fileIcon['default'];
            } else {
                $info['icon'] = $fileIcon['folder'];
            }
        }
        return  '<li id="li_' . $info['id'] . '">
                <div class="listTableIn pull-left">
                    <div class="listTableInL pull-left">
                      <div class="cBox"><input name="classLists" type="checkbox" value="' . $info['id'] . '"></div>
                      <div class="name">
                          <em class="' . $info['icon'] . '"></em>
                          <span class="div_pro">
                              <a target="_blank" href="index.php?a=own&urlkey=' . base_convert($info['mapId'], 10, 36) . '">' . htmlspecialchars($info['name'], ENT_NOQUOTES) . '</a>
                          </span>
                </div>
                </div>
                <div class="listTableInR pull-right">
                    <div class="size">' . $info['size'] . '</div>
                    <div class="updateTime">' . $info['collectTime'] . '</div>
                </div>
                </div>
                </li>';
    }

    public function collect() {
        $uid = (int)$_REQUEST['uid'];
        $ids = $_REQUEST['ids'];
        if (!$uid) {
            echo Response::json(LACK, array(tip('请先登录')));
            exit;
        }
        if (!$ids) {
            echo Response::json(LACK, array(tip('参数不全')));
            exit;
        }
        $idArray = explode(',', $ids);
        if ($idArray) {
            $fac = Factory::getInstance();
            foreach ($idArray as $v) {
                $shareInfo = $fac->getShareInfo($v);
                $fac->collect($uid, $shareInfo['uid'], $shareInfo['mapId']);
            }
            $fac->incrCollect($ids);
        }
        echo Response::json(SUCC, array(tip('操作成功')));
    }

    public function unCollect() {
        $uid = (int)$_REQUEST['uid'];
        $ids = $_REQUEST['ids'];
        if (!$uid || !$ids) {
            echo Response::json(LACK, array(tip('参数不全')));
            exit;
        }
        Factory::getInstance()->decrCollect($ids);
        $res = Factory::getInstance()->unCollect($uid, $ids);
        if ($res) {
            echo Response::json(SUCC, array(tip('操作成功')));
        } else {
            echo Response::json(FAIL, array(tip('操作失败')));
        }
    }

    public function unCollectAll() {
        $uid = (int)$_REQUEST['uid'];
        if (!$uid) {
            echo Response::json(LACK, array(tip('参数不全')));
            exit;
        }
        $res = Factory::getInstance()->unCollectAll($uid);
        if ($res) {
            echo Response::json(SUCC, array(tip('操作成功')));
        } else {
            echo Response::json(FAIL, array(tip('操作失败')));
        }
    }
}
?>