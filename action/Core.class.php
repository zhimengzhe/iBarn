<?php
/**
 * @版权所有 翻版必究
 * @desc: 控制层
 * @author: 樊亚磊
 * @mail:fanyalei@aliyun.com
 * @QQ:451802973
 */
class Core extends Abst {

    public function index() {
        $uid  = (int)$_REQUEST['uid'];
        $path = trim(self::trimSpace(rawurldecode($_REQUEST['path'])), '/');
        $search = self::trimSpace(rawurldecode($_REQUEST['search']));
        $fac = Factory::getInstance();
        $order = $_REQUEST['order'];
        $by = $_REQUEST['by'] == 'asc' ? 'asc' : 'desc';
        $type = $_REQUEST['type'];
        $class = $_REQUEST['class'];
        $curPage = max($_REQUEST['curPage'], 1);
        $perPage = $_REQUEST['perPage'] ? (int)$_REQUEST['perPage'] : 60;
        if ($order == 'ctime') {
            $order = 'time';
        }
        if (trim($search) != '') {
            $list = $fac->search($uid, $search, $order, $by, $type, $class, $curPage, $perPage);
            $num = $fac->searchNum($uid, $search, $type, $class);
        } else {
            if ($path) {
                $pathinfo = explode('/', trim($path, '/'));
                $num = count($pathinfo);
                $name = $pathinfo[$num - 1];
                if (!empty($pathinfo)) {
                    $pid = 0;
                    foreach ($pathinfo as $k => $v) {
                        if ($k == $num - 1) {
                            break;
                        }
                        $mapinfo = $fac->getFileMapByName($uid, $pid, $v);
                        $pid = $mapinfo['id'];
                    }
                }
                $mapinfo = $fac->getFileMapByName($uid, $pid, $name);
                $id = (int)$mapinfo['id'];
            } else {
                $id = 0;
            }
            if (!$class) {
                $list = $fac->getDirList($uid, $id, $order, $by, $type, $curPage, $perPage);
                $num = $fac->getDirNum($uid, $id, $type);
            } elseif ($class == 'recycle') {
                $list = $fac->getRecyList($uid, $order, $by, $curPage, $perPage);
                $num = $fac->getRecyNum($uid, $id);
            }
        }
        if ($list) {
            $fileIocn = json_decode(ICON, true);
            foreach ($list as $x => $y) {
                $icon = $fileIocn[pathinfo($y['name'], PATHINFO_EXTENSION)];
                if (!$y['isdir']) {
                    $list[$x]['size'] = self::formatBytes($y['size']);
                    $list[$x]['icon'] = $icon ? $icon : $fileIocn['default'];
                    $list[$x]['bicon'] = trim(strrchr($list[$x]['icon'], ' '));
                } else {
                    $list[$x]['size'] = 0;
                    $list[$x]['icon'] = $fileIocn['folder'];
                    $list[$x]['bicon'] = 'folder';
                }
                if (!$class) {
                    $shareInfo = $fac->isShare($uid, $y['id']);
                    if ($shareInfo) {
                        $list[$x]['share'] = 1;
                    } else {
                        $list[$x]['share'] = 0;
                    }
                }
            }
        }
        if (!$_REQUEST['res']) {
            $page = ceil($num/$perPage);
            $userinfo = Factory::getInstance('user')->getUserInfo($uid);
            $total = count($list);
            if (!$class) {
                if ($path) {
                    $paths = explode('/', trim($path, '/'));
                    array_pop($paths);
                    $prePath = implode('/', $paths);
                }
                include VIEW_PATH . 'index.php';
            } elseif ($class == 'recycle') {
                include VIEW_PATH . 'recycle.php';
            }
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
        if ($info['isdir']) {
            $href = 'index.php?path=' . (trim($_REQUEST['path'], '/') ? trim($_REQUEST['path'], '/') . '/' : '') . htmlspecialchars($info['name'], ENT_NOQUOTES);
            $down = 'index.php?a=mdown&ids=' . $info['id'];
        } else {
            if ($info['type'] == 2) {
                $href = 'index.php?a=view&urlkey=' . base_convert($info['id'], 10, 36);
                $data = 'data-lightbox="roadtrip"';
            } else {
                $href = 'index.php?a=down&urlkey=' . base_convert($info['id'], 10, 36);
            }
            $down = 'index.php?a=down&id=' . $info['id'];
        }
        if (!$info['icon']) {
            $fileIocn = json_decode(ICON, true);
            if (!$info['isdir']) {
                $icon = $fileIocn[pathinfo($info['name'], PATHINFO_EXTENSION)];
                $info['icon'] = $icon ? $icon : $fileIocn['default'];
            } else {
                $info['icon'] = $fileIocn['folder'];
            }
        }
        if ($info['share']) {
            $share = '<div class="shareFd"></div>';
        }
        return  '<li id="li_' . $info['id'] . '">
                <div class="listTableIn pull-left" onmouseenter="$(\'#box_' . $info['id'] . '\').show();" onmouseleave="$(\'#box_' . $info['id'] . '\').hide();">
                    <div class="listTableInL pull-left">
                      <div class="cBox"><input name="classLists" type="checkbox" value="' . $info['id'] . '"></div>
                      <div class="name">' . $share . '
                            <em class="' . $info['icon'] . '"></em>
                            <span class="div_pro"><a target="_self" ' . $data . ' href="'. $href . '" id="a_' . $info['id']. '">' . htmlspecialchars($info['name'], ENT_NOQUOTES) . '</a></span>
                </div>
                </div>
                <div class="listTableInR pull-right">
                    <div class="size">' . $info['size'] . '</div>
                    <div class="updateTime">' . $info['time'] . '</div>
                    <div style="display:none;" class="float_box" id="box_' . $info['id'] . '">
                        <ul class="control">
                            <li><a href="' . $down . '"><i class="icon-download-alt"></i></a></li>
                            <li><a href="#" onclick="modalShare(' . $info['id'] . ', \'' . base_convert($info['id'], 10, 36) . '\');" data-toggle="modal"><i class="icon-share"></i></a></li>
                            <li><a href="#" onclick="modalName(' . $info['id'] . ', \'' . htmlspecialchars($info['name'], ENT_NOQUOTES) . '\')" data-toggle="modal"><i class="icon-edit"></i></a></li>
                            <li><a href="#" onclick="modalTrans();" data-toggle="modal"><i class="icon-random"></i></a></li>
                            <li><a href="#" onclick="modalDel(\'' . htmlspecialchars($info['name'], ENT_NOQUOTES) . '\')" data-toggle="modal"><i class="icon-remove"></i></a></li>
                        </ul>
                </div>
                </div>
                </div>
                </li>';
    }

    public function formatBigHtml($info = array()) {
        if ($info['isdir']) {
            $href = 'index.php?path=' . (trim($_REQUEST['path'], '/') ? trim($_REQUEST['path'], '/') . '/' : '') . htmlspecialchars($info['name'], ENT_NOQUOTES);
        } else {
            if ($info['type'] == 2) {
                $href = 'index.php?a=view&urlkey=' . base_convert($info['id'], 10, 36);
                $data = 'data-lightbox="roadtrip"';
            } else {
                $href = 'index.php?a=down&urlkey=' . base_convert($info['id'], 10, 36);
            }
        }
        if (!$info['bicon']) {
            $fileIcon = json_decode(ICON, true);
            $icon = $fileIcon[pathinfo($info['name'], PATHINFO_EXTENSION)];
            if (!$info['isdir']) {
                $icon = $icon ? $icon : $fileIcon['default'];
                $info['bicon'] = trim(strrchr($icon, ' '));
            } else {
                $info['bicon'] = 'folder';
            }
        }
        if ($info['share']) {
            $share = '<div class="shareFdBig"></div>';
        }
        return '<li onmouseenter="$(\'#checkShow' . $info['id'] . '\').show();$(this).attr(\'class\', \'in\')" onmouseleave="if($(\'#squaredFour' . $info['id'] . '\').prop(\'checked\') == false) {$(\'#checkShow' . $info['id'] . '\').hide();$(this).removeClass(\'in\');}" id="bli_' . $info['id'] . '">
                   <div class="squaredFour" id="checkShow' . $info['id'] . '">
                       <input type="checkbox" id="squaredFour' . $info['id'] . '" name="squaredCheckbox" class="squaredCheckbox" value="' . $info['id'] . '"/>
                       <label for="squaredFour' . $info['id'] . '"></label>
                   </div>
                   <a target="_self" ' . $data . ' href="'. $href . '" id="ba_' . $info['id'] . '">
                       <div class="big ' . $info['bicon'] . 'Big">' . $share . '</div>
                       <p>' . htmlspecialchars(mb_substr($info['name'], 0, 12, 'utf8'), ENT_NOQUOTES) . '</p>
                       <input type="hidden" id="aname_' . $info['id'] . '" value="' . htmlspecialchars($info['name'], ENT_NOQUOTES) . '">
                   </a>
                  </li>';
    }

    public function own() {
        if ($_REQUEST['urlkey']) {
            $mapId = base_convert($_REQUEST['urlkey'], 36, 10);
        } else {
            $mapId = (int)$_REQUEST['id'];
        }
        if (!$mapId) {
            include VIEW_PATH . 'error.php';
            exit;
        }
        $mapInfo = Factory::getInstance()->getFileMap($mapId);
        $shareInfo = Factory::getInstance()->getShareByMap($mapId);
        $shareInfo = $shareInfo[0];
        if ($_REQUEST['uid']) {
            $userinfo = Factory::getInstance('user')->getUserInfo((int)$_REQUEST['uid']);
        }
        if (!$shareInfo) {
            include VIEW_PATH . 'error.php';
            exit;
        }
        if (strtotime($shareInfo['overTime']) < time() && $shareInfo['overTime'] != '0000-00-00 00:00:00') {
            include VIEW_PATH . 'error.php';
            exit;
        }
        if ($shareInfo['pwd'] && !$_SESSION['share'][self::getClientIp() . ':' . $mapId] && $_REQUEST['uid'] != $mapInfo['uid']) {
            include VIEW_PATH . 'pwd.php';
        } else {
            $curPage = max($_REQUEST['curPage'], 1);
            $perPage = $_REQUEST['perPage'] ? (int)$_REQUEST['perPage'] : 100;
            if ($_REQUEST['pid']) {
                $pid = (int)$_REQUEST['pid'];
                if ($pid != $mapId) {
                    $info = Factory::getInstance()->getFileMap($pid);
                    if (strpos($info['path'], $mapId) === false) {
                        include VIEW_PATH . 'error.php';
                        exit;
                    }
                }
                $list = Factory::getInstance()->getDirList($mapInfo['uid'], $pid, '', '', 0, $curPage, $perPage);
            } elseif ($mapInfo && $mapInfo['isdir']) {
                $list[0] = $mapInfo;
            }
            $fileIcon = json_decode(ICON, true);
            if ($list) {
                foreach ($list as $x => $y) {
                    $icon = $fileIcon[pathinfo($y['name'], PATHINFO_EXTENSION)];
                    if (!$y['isdir']) {
                        $list[$x]['icon'] = $icon ? $icon : $fileIcon['default'];
                        $list[$x]['bicon'] = trim(strrchr($list[$x]['icon'], ' '));
                    } else {
                        $list[$x]['bicon'] = 'folder';
                    }
                }
            } else {
                $icon = $fileIcon[pathinfo($mapInfo['name'], PATHINFO_EXTENSION)];
                $icon = $icon ? $icon : $fileIcon['default'];
                $mapInfo['bicon'] = trim(strrchr($icon, ' '));
            }
            include VIEW_PATH . 'own.php';
        }
    }

    public function getFileByName() {
        $uid  = (int)$_REQUEST['uid'];
        $pid  = (int)$_REQUEST['pid'];
        $name = self::trimSpace($_REQUEST['name']);
        $res  = Factory::getInstance()->getFileMapByName($uid, $pid, $name);
        echo Response::json(SUCC, $res);
    }

    public function upload() {
        if (!$_REQUEST['uid']) {
            echo Response::json(LACK, array(tip('用户ID不能为空')));
            exit;
        }
        $_REQUEST['name'] = self::filterName(rawurldecode(self::trimSpace($_REQUEST['name'])));
        if (!$_REQUEST['name']) {
            echo Response::json(LACK, array(tip('文件名不能为空')));
            exit;
        }
        if (!$_REQUEST['type']) {
            if (!file_exists(DATA_DIR)) {
                $res = mkdir(DATA_DIR, 0777, true);
                if (!$res) {
                    echo Response::json(FAIL, array(tip('存储目录创建失败')));
                    exit;
                }
            }
        }
        if (!file_exists(UP_DIR)) {
            $res = mkdir(UP_DIR, 0777, true);
            if (!$res) {
                echo Response::json(FAIL, array(tip('存储目录创建失败')));
                exit;
            }
        }
        include LIB_PATH . 'plupload' . DS . 'PluploadHandler.php';
        PluploadHandler::no_cache_headers();
        PluploadHandler::cors_headers();
        if (!PluploadHandler::handle(array(
            'target_dir' => UP_DIR,
            //'allow_extensions' => 'jpg,jpeg,png'
        ))) {
            echo Response::json(FAIL, array(tip('上传失败')));
            exit;
        } else {
            echo Response::json(SUCC, array(tip('上传成功')));
        }
    }

    public function putFile() {
		ini_set('memory_limit','-1');
        if (!$_REQUEST['uid']) {
            echo Response::json(LACK, array(tip('用户ID不能为空')));
            exit;
        }
        $name = isset($_REQUEST['name']) ? self::filterName(self::trimSpace(rawurldecode($_REQUEST['name']))) : self::filterName(self::trimSpace($_FILES['file']['name']));
        if (!$name) {
            echo Response::json(LACK, array(tip('文件名不能为空')));
            exit;
        }
        $hash = $_REQUEST['hash'];
        if (!$hash) {
            echo Response::json(LACK, array(tip('hash为必填参数')));
            exit;
        }
        $size = (int)$_REQUEST['size'];
        if (!$size) {
            $size = $_FILES['file']['size'];
        }
        //$mime = $_REQUEST['mime'];
		$mime = '';
        if ($_FILES["file"]["type"] && !$mime) {
            $mime = $_FILES["file"]["type"];
        }
        $exist = Factory::getInstance()->uploadCheck($hash, $size);
        if ($exist) {
			$filePath = $exist['location'];
            $size = $exist['size'];
            $md5 = $exist['md5'];
            if (!$mime) {
                $mime = $exist['mime'];
            }
        } else {
            $filePath = UP_DIR . $name;
            if (strtoupper(substr(PHP_OS, 0, 3)) == 'WIN') {
                $filePath = iconv('utf-8', 'gbk//IGNORE', $filePath);
            }
			if (!file_exists($filePath)) {
				echo Response::json(LACK, array(tip('文件路径不存在')));
				exit;
			}
			if (!$size) {
				$size = sprintf('%u', filesize($filePath));
			}
			$md5 = md5_file($filePath);
        }
		$check = Factory::getInstance('user')->isExceedLimits($_REQUEST['uid']);
        if ($check) {
            if (!$exist) {
                unlink($filePath);
            }
            echo Response::json(FAIL, array(tip('存储空间已满')));
            exit;
        }
        $origin = isset($_REQUEST['origin']) ? $_REQUEST['origin'] : 'os';
        if (!$mime) {
            $mimeinfo = self::getMimeType($filePath);
            $mime = $mimeinfo['mime'];
        }
        $mime = $mime ? $mime : 'application/octet-stream';
        $cover = $_REQUEST['cover'] ? 1 : 0;
        if (!$exist) {
            $newPath = $this->moveFile($filePath);
            if ($newPath) {
                $filePath = $newPath;
            } else {
                echo Response::json(FAIL, array(tip('文件入库失败，请重试')));
                exit;
            }
        } else {
            unlink(UP_DIR . $name);
        }
        $res = Factory::getInstance()->addFile($_REQUEST['uid'], trim(self::filterPath(rawurldecode($_REQUEST['path'])), '/') . '/' . $name, $filePath, $size, 0, $origin, $mime, $hash, $cover, $md5);
        if ($res == 2) {
            echo Response::json(FORB, array(tip('目录层级过多，超过限制')));
        } elseif ($res && is_array($res)) {
            $res['size'] = $this->formatBytes($res['size']);
            if ($_REQUEST['type']) {
                $res = $this->formatBigHtml($res);
            } else {
                $res = $this->formatHtml($res);
            }
            echo Response::json(SUCC, array($res));
        } else {
            echo Response::json(FAIL, array(tip('文件入库失败，请重试')));
        }
    }

    protected function moveFile($path, $status = 0) {
        if (!extension_loaded('fastdfs_client')) {
            $type = 0;
        } else {
            $type = 1;
        }
        if ($status == 0) {
            if (!$type) {
                $ext = self::getExtByPath(self::trimSpace($path));
                $fileName = md5(uniqid()) . ($ext ? '.' . $ext : '');
                $newDir =  DATA_DIR . date('Ymd');
                $newPath = $newDir . DS . $fileName;
                if (!file_exists($newDir)) {
                    mkdir($newDir, 0777, true);
                }
                if (rename($path, $newPath)) {
                    return $newPath;
                } else {
                    return false;
                }
            } else {
                include FDFS . 'Exception.php';
                include FDFS . 'Base.php';
                include FDFS . 'Tracker.php';
                include FDFS . 'Storage.php';
                $confinfo = include FDFS . 'config.php';
                $group = include FDFS . 'group.php';
                if ($confinfo['tracker']) {
                    shuffle($confinfo['tracker']);
                }
                $gs = array_keys((array)$group);
                if ($gs) {
                    shuffle($gs);
                }
                $tracker = new FastDFS\Tracker($confinfo['tracker'][0], $confinfo['trackerPort']);
                $storageInfo = $tracker->applyStorage($gs[0]);
                $storage = new FastDFS\Storage($storageInfo['storage_addr'], $storageInfo['storage_port']);
                $ret = $storage->uploadFile($storageInfo['storage_index'], $path);
				unlink($path);
                return $ret['group'] . DS . $ret['path'];
            }
        } else {
            return $path;
        }
    }

    public function uploadCheck() {
        $fileName = self::trimSpace(rawurldecode($_REQUEST['fileName']));
        $fileSize = (int)$_REQUEST['fileSize'];
        $size = (int)$_REQUEST['size'];
        $fileCount = (int)$_REQUEST['maxFileCount'];
        $hash = $_REQUEST['hash'];
        $hcouns = 0;
        if ($hash) {
            if (Factory::getInstance()->uploadCheck($hash, $fileSize)) {
                echo Response::json(SUCC, array($fileSize));
                exit;
            }
        }
        if ($fileCount > 0) {
            for ($i = 0; $i < $fileCount; $i++) {
                if (!file_exists(UP_DIR . $fileName . ".dir.part" . DS . $i)) {
                    $hcouns = $i * $size;
                    break;
                }
            }
        }
        echo Response::json(SUCC, array($hcouns));
    }

    public function addFolder() {
        $uid = (int)$_REQUEST['uid'];
        $name = self::filterName(self::trimSpace(rawurldecode($_REQUEST['name'])));
        $path = self::filterPath(self::trimSpace(rawurldecode($_REQUEST['path'])));
        $origin = isset($_REQUEST['origin']) ? $_REQUEST['origin'] : 'os';
        $cover = $_REQUEST['cover'] ? 1 : 0;
        if (!$uid) {
            echo Response::json(LACK, array(tip('用户ID不能为空')));
            exit;
        }
        if (!$name) {
            echo Response::json(LACK, array(tip('文件名不能为空')));
            exit;
        }
        if (strlen($name) > 200) {
            echo Response::json(FORB, array(tip('文件夹名不能超过200个字符')));
            exit;
        }
        $res = Factory::getInstance()->addFolder($uid, $path, $name, $origin, $cover);
        if (!$cover && $res == 3) {
            echo Response::json(FORB, array(tip('同名文件夹已存在')));
            exit;
        }
        if ($res == 2) {
            echo Response::json(FORB, array(tip('目录层级过多，超过限制')));
        } elseif ($res && is_array($res)) {
            if ($_REQUEST['type']) {
                $res = $this->formatBigHtml($res);
            } else {
                $res = $this->formatHtml($res);
            }
            echo Response::json(SUCC, array($res));
        } else {
            echo Response::json(FAIL, array(tip('创建失败')));
        }
    }

    public function setName() {
        $mapId = (int)$_REQUEST['id'];
        $name = self::filterName(self::trimSpace(rawurldecode($_REQUEST['name'])));
        $aname = self::filterName(self::trimSpace(rawurldecode($_REQUEST['aname'])));
        $ext = pathinfo($aname, PATHINFO_EXTENSION);
        if (!$mapId || trim($name) == '') {
            echo Response::json(LACK, array(tip('参数不全')));
            exit;
        }
        $res = Factory::getInstance()->setName($mapId, $name . ($ext ? '.' . $ext : ''));
        if ($res == 2) {
            echo Response::json(FORB, array(tip('同名文件已存在')));
        } elseif ($res == 1) {
            echo Response::json(SUCC, array($name . ($ext ? '.' . $ext : '')));
        } else {
            echo Response::json(FAIL, array(tip('修改失败')));
        }
    }

    public function delFileMap() {
        $uid = (int)$_REQUEST['uid'];
        $mapId = $_REQUEST['ids'];
        if (!$mapId || !$uid) {
            echo Response::json(LACK, array(tip('参数不全')));
            exit;
        }
        $mapIds = explode(',', $mapId);
        if ($mapIds) {
            $fac = Factory::getInstance();
            foreach ($mapIds as $m) {
                $mapinfo = $fac->getFileMap($m);
                $res = $fac->delFileMap($uid, $m);
                if ($res == 1) {
                    $fac->recycle($mapinfo);
                    if ($mapinfo['isdir']) {
                        $info = $fac->getFileMapByPid($uid, $m);
                        if ($info) {
                            $ids=  '';
                            foreach ((array)$info as $v) {
                                $v['status'] = 1;
                                $fac->recycle($v);
                                $ids .= $v['id'] . ',';
                            }
                            $res = $fac->delFileMap($uid, trim($ids, ','));
                            if ($res) {
                                $fac->unShareByMapId(trim($ids, ','));
                                $fac->unCollectByMapId(trim($ids, ','));
                            }
                        }
                    }
                }
            }
        }
        if ($res) {
            echo Response::json(SUCC, array(tip('删除成功')));
        } else {
            echo Response::json(FAIL, array(tip('删除失败')));
        }
    }

    public function delRecycle() {
        $ids = $_REQUEST['ids'];
        $uid = (int)$_REQUEST['uid'];
        $res = Factory::getInstance()->delRecycle($uid, $ids);
        if ($res) {
            echo Response::json(SUCC, array(tip('删除成功')));
        } else {
            echo Response::json(FAIL, array(tip('删除失败')));
        }
    }

    public function recover() {
        $ids = explode(',', $_REQUEST['ids']);
        $uid = (int)$_REQUEST['uid'];
        if ($ids) {
            $fac = Factory::getInstance();
            foreach ($ids as $v) {
                $res = $fac->recover($uid, (int)$v);
                if ($res) {
                    $fac->delRecycle($uid, (int)$v);
                }
            }
        }
        if ($res) {
            echo Response::json(SUCC, array(tip('还原成功')));
        } else {
            echo Response::json(FAIL, array(tip('还原失败')));
        }
    }

    public function move() {
        $uid    = (int)$_REQUEST['uid'];
        $smapId = $_REQUEST['smapId'];
        $dmapId = (int)$_REQUEST['dmapId'];
        $dpath  = self::trimSpace($_REQUEST['dpath']);
        $cover  = (int)$_REQUEST['cover'];
        if (!$uid || !$smapId) {
            echo Response::json(LACK, array(tip('参数不全')));
            exit;
        }
        $sid = explode(',', $smapId);
        if ($sid) {
            foreach ($sid as $s) {
                $res = Factory::getInstance()->move($uid, $s, $dmapId, $dpath, $cover);
            }
        }
        if ($res == 1) {
            echo Response::json(SUCC, array(tip('移动成功')));
        } elseif ($res == 2) {
            echo Response::json(FORB, array(tip('没有移动')));
        } else {
            echo Response::json(FAIL, array(tip('移动失败')));
        }
    }

    public function duplicate() {
        $uid    = (int)$_REQUEST['uid'];
        $smapId = (int)$_REQUEST['smapId'];
        $dmapId = (int)$_REQUEST['dmapId'];
        $dpath  = self::trimSpace($_REQUEST['dpath']);
        $cover  = (int)$_REQUEST['cover'];
        if (!$uid || !$smapId || !$dmapId) {
            echo Response::json(LACK, array(tip('参数不全')));
            exit;
        }
        $res = Factory::getInstance()->duplicate($uid, $smapId, $dmapId, $dpath, $cover);
        if ($res == 1) {
            echo Response::json(SUCC, array('复制成功'));
        } else {
            echo Response::json(FAIL, array('复制失败'));
        }
    }

    public function view() {
        set_time_limit(60);
        $urlkey = $_REQUEST['urlkey'];
        if (!$urlkey) {
            $mapId = (int)$_REQUEST['id'];
            if (!$mapId) {
                echo Response::json(LACK, array(tip('参数不全')));
                exit;
            }
        } else {
            $mapId = base_convert($urlkey, 36, 10);
        }
        $fac = Factory::getInstance();
        $info = $fac->getFileMap($mapId);
        if (!$info['location'] && !$info['isdir']) {
            include VIEW_PATH . 'error.php';
            exit;
        }
        //
        if ($_SESSION['CLOUD_UID'] != $info['uid']) {
            $pids = str_replace('/', ',', trim($info['path'], '/'));
            $ids = ($pids ? $pids . ',' : '') . $mapId;
            $shares = $fac->getShareByMap($ids);
            if ($shares) {
                $keys = array();
                foreach ($shares as $v) {
                    $keys[$v['mapId']] = $v['mapId'];
                    $shares[$v['mapId']] = $v;
                }
                $shareInfo = $shares[max($keys)];
            } else {
                include VIEW_PATH . 'error.php';
                exit;
            }
            if ($shareInfo) {
                if (strtotime($shareInfo['overTime']) < time() && $shareInfo['overTime'] != '0000-00-00 00:00:00') {
                    include VIEW_PATH . 'error.php';
                    exit;
                }
                $fac->incrShareView($shareInfo['id']);
                if ($info['isdir'] || ($shareInfo['pwd'] && !$_SESSION['share'][self::getClientIp() . ':' . $shareInfo['mapId']])) {
                    include VIEW_PATH . 'error.php';
                    exit;
                }
            } else {
                include VIEW_PATH . 'error.php';
                exit;
            }
        } elseif ($info['isdir']) {
            if ($urlkey) {
                header('Location: index.php?a=own&urlkey=' . $urlkey);
            } else {
                header('Location: index.php?a=own&id=' . $mapId);
            }
            exit;
        }
        //
        header('Content-type: ' . ($info['mime'] ? $info['mime'] : 'application/octet-stream'));
        if (!extension_loaded('fastdfs_client')) {
            readfile($info['location']);
        } else {
            $group = include FDFS . 'group.php';
            $g = strtok($info['location'], '/');
            $ips = $group[$g];
            if ($ips) {
                shuffle($ips);
                echo file_get_contents('http://' . $ips[0] . '/' . $info['location']);
            } else {
                include VIEW_PATH . 'error.php';
            }
        }
    }

    public function down() {
        set_time_limit(2 * 3600);
        $urlkey = $_REQUEST['urlkey'];
        if (!$urlkey) {
            $mapId = (int)$_REQUEST['id'];
            if (!$mapId) {
                echo Response::json(LACK, array(tip('参数不全')));
                exit;
            }
        } else {
            $mapId = base_convert($urlkey, 36, 10);
        }
        $fac = Factory::getInstance();
        $info = $fac->getFileMap($mapId);
        if (!$info['location']) {
            include VIEW_PATH . 'error.php';
            exit;
        }
        //
        if ($_SESSION['CLOUD_UID'] != $info['uid']) {
            $pids = str_replace('/', ',', trim($info['path'], '/'));
            $ids = ($pids ? $pids . ',' : '') . $mapId;
            $shares = $fac->getShareByMap($ids);
            if ($shares) {
                $keys = array();
                foreach ($shares as $v) {
                    $keys[$v['mapId']] = $v['mapId'];
                    $shares[$v['mapId']] = $v;
                }
                $shareInfo = $shares[max($keys)];
            } else {
                include VIEW_PATH . 'error.php';
                exit;
            }
            if ($shareInfo) {
                if (strtotime($shareInfo['overTime']) < time() && $shareInfo['overTime'] != '0000-00-00 00:00:00') {
                    include VIEW_PATH . 'error.php';
                    exit;
                }
                $fac->incrShareDown($shareInfo['id']);
                if ($info['isdir'] || ($shareInfo['pwd'] && !$_SESSION['share'][self::getClientIp() . ':' . $shareInfo['mapId']])) {
                    include VIEW_PATH . 'error.php';
                    exit;
                }
            } else {
                include VIEW_PATH . 'error.php';
                exit;
            }
        }
        //
        $url = parse_url(trim($info['name'], '/'));
        $pathArray = explode('/', $url['path']);
        $fileName = end($pathArray);
        if (!$fileName) {
            $fileName = 'sorry.' . pathinfo($url['path'], PATHINFO_EXTENSION);
        }
        $fsize = (int)$info['size'];
        if ($info['location']) {
            header('Content-type: ' . ($info['mime'] ? $info['mime'] : 'application/octet-stream'));
            if (preg_match("/MSIE/", $_SERVER["HTTP_USER_AGENT"])) {
                header('Content-Disposition: attachment; filename="' . rawurlencode($fileName) . '"');
            } else if (preg_match("/Firefox/", $_SERVER["HTTP_USER_AGENT"])) {
                header('Content-Disposition: attachment; filename*="utf8\'\'' . $fileName . '"');
            } else {
                header('Content-Disposition: attachment; filename="' . $fileName . '"');
            }
            session_write_close();
            if (!extension_loaded('fastdfs_client')) {
                header('cache-control:public');
                include LIB_PATH . 'down' . DS . 'FileDownload.class.php';
                $obj = new FileDownload();
                $obj->download($info['location'], $fileName, $fsize, true);
            } else {
                $group = include FDFS . 'group.php';
                $g = strtok($info['location'], '/');
                $ips = $group[$g];
                if ($ips) {
                    shuffle($ips);
                    echo file_get_contents('http://' . $ips[0] . '/' . $info['location']);
                } else {
                    include VIEW_PATH . 'error.php';
                }
            }
        } else {
            header("HTTP/1.1 404 Not Found");
            header('Content-Type: text/html; charset=utf-8');
        }
    }

    public function mdown() {
        if (!extension_loaded('zip')) {
            include VIEW_PATH . 'error.php';
            exit;
        }
        if (extension_loaded('fastdfs_client')) {
            include VIEW_PATH . 'error.php';
            exit;
        }
        set_time_limit(2 * 3600);
        $fac = Factory::getInstance();
        $ids = $_REQUEST['ids'];
        $info = $fac->getFileMap($ids);
        if (is_numeric($ids)) {
            $tmp = $info;
            unset($info);
            $info[0] = $tmp;
        }
        if ($info) {
            foreach ($info as $v) {
                //
                if ($_SESSION['CLOUD_UID'] != $v['uid']) {
                    $pids = str_replace('/', ',', trim($v['path'], '/'));
                    $ids = ($pids ? $pids . ',' : '') . $v['id'];
                    $shares = $fac->getShareByMap($ids);
                    if ($shares) {
                        $keys = array();
                        foreach ($shares as $y) {
                            $keys[$y['mapId']] = $y['mapId'];
                            $shares[$y['mapId']] = $y;
                        }
                        $shareInfo = $shares[max($keys)];
                    } else {
                        continue;
                    }
                    if ($shareInfo) {
                        if (strtotime($shareInfo['overTime']) < time() && $shareInfo['overTime'] != '0000-00-00 00:00:00') {
                            continue;
                        }
                        $fac->incrShareDown($shareInfo['id']);
                        if ($shareInfo['pwd'] && !$_SESSION['share'][self::getClientIp() . ':' . $shareInfo['mapId']]) {
                            continue;
                        }
                    } else {
                        continue;
                    }
                }
                //
                if ($v['isdir']) {
                    $list = $fac->getFileMapByPid($v['uid'], $v['id'], 1);
                    if ($list) {
                        foreach ($list as $y) {
                            $dirs[] = $y['location'];
                        }
                    }
                } else {
                    $dirs[] = $v['location'];
                }
            }
        } else {
            include VIEW_PATH . 'error.php';
            exit;
        }
        $zip = new zipArchive();
        $zipName = md5(uniqid() . time()) . '.zip';
        $zip->open($zipName, ZIPARCHIVE::OVERWRITE);
        foreach ($dirs as $v) {
            $zip->addFile($v, basename($v));
        }
        $zip->close();
        header('Content-Type:Application/zip');
        if (preg_match("/MSIE/", $_SERVER["HTTP_USER_AGENT"])) {
            header('Content-Disposition: attachment; filename="' . rawurlencode($zipName) . '"');
        } else if (preg_match("/Firefox/", $_SERVER["HTTP_USER_AGENT"])) {
            header('Content-Disposition: attachment; filename*="utf8\'\'' . $zipName . '"');
        } else {
            header('Content-Disposition: attachment; filename="' . $zipName . '"');
        }
        header('Content-Length:' . filesize($zipName));
        session_write_close();
        readfile($zipName);
		ob_flush();
		flush();
        unlink($zipName);
    }

    public function setUpload() {
        $size = (int)$_REQUEST['size'];
        $str = "<?php
        ini_set('post_max_size', '" . $size . "M');
        ini_set('upload_max_filesize','" . ($size + 3) . "M');
        ini_set('memory_limit','" . ($size + 3) . "M');
        ?>";
        file_put_contents(CONFIG_PATH . 'upload.php', $str);
        echo Response::json(SUCC, array(tip('设置成功')));
    }

    public function getTree() {
        $uid = (int)$_REQUEST['uid'];
        $list = Factory::getInstance()->getTree($uid);
        $res = '[{"text": "' . t('所有资料') . '", "mapId": "0"}]';
        if ($list) {
            $list = $this->tree($list);
            $ret = array(0 => array('text' => t('所有资料'), 'mapId' => 0));
            $ret[0]['nodes'] = $list;
            unset($list);
            $res = json_encode($ret);
        }
        echo $res;
    }

    public function offer() {
        $curPage = max($_REQUEST['curPage'], 1);
        $perPage = $_REQUEST['perPage'] ? (int)$_REQUEST['perPage'] : 100;
        $name = $_REQUEST['search'];
        $order = $_REQUEST['order'];
        $by = $_REQUEST['by'] == 'asc' ? 'asc' : 'desc';
        $fac = Factory::getInstance();
        $userinfo = Factory::getInstance('user')->getUserInfo($_REQUEST['uid']);
        $list = $fac->getOfferList($curPage, $perPage, $name, $order, $by);
        if ($list) {
            $fileIocn = json_decode(ICON, true);
            foreach ((array)$list as $k => $v) {
                $list[$k]['pwd'] = $v['pwd'] ? 1 : 0;
                $icon = $fileIocn[pathinfo($list[$k]['name'], PATHINFO_EXTENSION)];
                if (!$list[$k]['isdir']) {
                    $list[$k]['size'] = self::formatBytes($v['size']);
                    $list[$k]['icon'] = $icon ? $icon : $fileIocn['default'];
                } else {
                    $list[$k]['size'] = 0;
                    $list[$k]['icon'] = $fileIocn['folder'];
                }
                $collecInfo = $fac->isCollect($_REQUEST['uid'], $v['id']);
                if ($collecInfo) {
                    $list[$k]['collect'] = 1;
                } else {
                    $list[$k]['collect'] = 0;
                }
            }
        }
        $num = $fac->getOfferNum($name);
        $page = ceil($num/$perPage);
        include VIEW_PATH . 'offer.php';
    }
}
?>