<?php
/**
 * @desc: 版权所有，翻版必究，未经同意不得用于商业项目
 * @author: 樊亚磊
 * @mail:fanyalei@aliyun.com
 * @QQ:451802973
 */
class User extends Abst {

    public function index() {
        include VIEW_PATH . 'login.php';
    }

    public function login() {
        $name = self::trimSpace($_REQUEST['userName']);
        $pwd  = self::trimSpace($_REQUEST['passWord']);
        $remember = (int)$_REQUEST['remember'];
        $res = Factory::getInstance('User')->login($name, $pwd, $remember);
        if ($res) {
            $_SESSION['CLOUD_UID'] = $res;
            setcookie('CLOUD_UID', $res, time() + 3600 * 24);
            if ($remember && $name && $pwd) {
                setcookie('token', sha1($name . substr(md5($pwd . PWD), 6, 20)), time() + 3600 * 24 * 7);
            }
            Factory::getInstance('User')->setLoginTime($res);
            echo Response::json(SUCC, array(tip('登录成功')));
        } else {
            echo Response::json(FAIL, array(tip('登录失败')));
        }
    }

    public function regist() {
        $name = self::trimSpace($_REQUEST['userName']);
        $pwd  = self::trimSpace($_REQUEST['passWord']);
        $role = $_REQUEST['role'] ? (int)$_REQUEST['role'] : 0;
        if (!$name || !$pwd) {
            echo Response::json(LACK, array(tip('用户名密码都不能为空')));
            exit;
        }
        $res = Factory::getInstance('User')->regist($name, $pwd, $role);
        if ($res) {
            $_SESSION['CLOUD_UID'] = $res;
            setcookie('CLOUD_UID', $res, time() + 3600 * 24);
            echo Response::json(SUCC, array(tip('注册成功')));
        } else {
            echo Response::json(FAIL, array(tip('注册失败')));
        }
    }

    public function isLogin() {
        $uid = $_SESSION['CLOUD_UID'];
        if ($uid) {
            if ($_COOKIE['CLOUD_UID'] == $uid) {
                return Response::json(SUCC, array(tip('登录状态')));
            } else {
				unset($_SESSION['CLOUD_UID']);
                setcookie('CLOUD_UID', NULL, time() - 3600);
                return Response::json(FAIL, array(tip('非登录状态')));
            }
        } elseif ($_COOKIE['token']) {
            $res = Factory::getInstance('User')->checkToken($_COOKIE['token']);
            if ($res) {
                $_SESSION['CLOUD_UID'] = $res;
                setcookie('CLOUD_UID', $res, time() + 3600 * 24);
                return Response::json(SUCC, array(tip('登录状态')));
            } else {
				setcookie('token', NULL, time() - 3600);
                return Response::json(FAIL, array(tip('非登录状态')));
            }
        }
    }

    public function logout() {
        unset($_SESSION['CLOUD_UID']);
        setcookie('CLOUD_UID', NULL, time() - 3600);
        setcookie('token', NULL, time() - 3600);
        header('Location: index.php?m=user');
        exit;
    }

    public function quota() {
        $uid = (int)$_REQUEST['uid'];
        $quota = (int)$_REQUEST['quota'];
        if (!$uid) {
            echo Response::json(LACK, array(tip('用户ID不能为空')));
            exit;
        }
        $res = Factory::getInstance('user')->quota($uid, $quota);
        if ($res) {
            echo Response::json(SUCC, array(tip('分配成功')));
        } else {
            echo Response::json(FAIL, array(tip('分配失败')));
        }
    }

    public function getUserSpace() {
        $uid = (int)$_REQUEST['uid'];
        if ($uid) {
            $res = Factory::getInstance('user')->getUserSpace($uid);
        }
        return array('space' => (int)$res, 'spaceFormat' => self::formatBytes((int)$res));
    }

    public function getUserInfo() {
        $uid = (int)$_REQUEST['uid'];
        return Factory::getInstance('user')->getUserInfo($uid);
    }

    public function getUsersByName() {
        $name = self::trimSpace(rawurldecode($_REQUEST['name']));
        $num = $_REQUEST['num'] ? (int)$_REQUEST['num'] : 6;
        if (!$name) {
            return array();
        }
        $info =  Factory::getInstance('user')->getUsersByName($name, $num);
        $ret = array();
        if ($info) {
            foreach ($info as $v) {
                $ret[] = $v['name'];
            }
        }
        echo json_encode($ret);
    }

    public function set() {
        $userinfo = Factory::getInstance('user')->getUserInfo($_SESSION['CLOUD_UID']);
        include VIEW_PATH . 'set.php';
    }

    public function setUser() {
        $uid = (int)$_REQUEST['uid'];
        if (!$uid) {
            echo Response::json(LACK, array(tip('用户ID不能为空')));
            exit;
        }
        $email = self::trimSpace(rawurldecode($_REQUEST['email']));
        $pwd = self::trimSpace(rawurldecode($_REQUEST['pwd']));
        $npwd = self::trimSpace(rawurldecode($_REQUEST['npwd']));
        $nrpwd = self::trimSpace(rawurldecode($_REQUEST['nrpwd']));
        if ($email && !$npwd) {
            $ret = Factory::getInstance('user')->setUserEmail($uid, $email);
        }
        if ($npwd) {
            $check = Factory::getInstance('user')->checkPwd($uid, $pwd);
            if (!$check) {
                echo Response::json(LACK, array(tip('原密码错误')));
                exit;
            }
            if ($npwd != $nrpwd) {
                echo Response::json(LACK, array(tip('两次输入的新密码不一致')));
                exit;
            }
            if ($pwd == $npwd) {
                echo Response::json(LACK, array(tip('密码未修改')));
                exit;
            }
            $ret = Factory::getInstance('user')->setUser($uid, $npwd, $email);
        }
        if ($ret) {
            echo Response::json(SUCC, array(tip('操作成功')));
        } else {
            echo Response::json(FAIL, array(tip('操作失败')));
        }
    }

    public function person() {
        $userinfo = Factory::getInstance('user')->getUserInfo($_SESSION['CLOUD_UID']);
        $uid  = (int)$_GET['uid'];
        if (!$uid) {
            $uid = $_SESSION['CLOUD_UID'];
        }
        $info = Factory::getInstance('user')->getUserInfo($uid);
        $curPage = max($_REQUEST['curPage'], 1);
        $perPage = $_REQUEST['perPage'] ? (int)$_REQUEST['perPage'] : 100;
        $fac = Factory::getInstance();
        $list = $fac->getMyShareList($uid, $curPage, $perPage);
        if ($list) {
            $fileIocn = json_decode(ICON, true);
            foreach ((array)$list as $k => $v) {
                if (trim($v['name']) == '') {
                    $mapInfo = $fac->getFileMap($v['mapId']);
                    $list[$k]['name'] = $mapInfo['name'] ? $mapInfo['name'] : tip('资料已被删除');
                    $list[$k]['type'] = $mapInfo['type'];
                }
                $icon = $fileIocn[pathinfo($list[$k]['name'], PATHINFO_EXTENSION)];
                if (!$list[$k]['isdir']) {
                    $list[$k]['icon'] = $icon ? $icon : $fileIocn['default'];
                    $list[$k]['bicon'] = trim(strrchr($list[$k]['icon'], ' '));
                } else {
                    $list[$k]['icon'] = $fileIocn['folder'];
                    $list[$k]['bicon'] = 'folder';
                }
            }
        }
        $num = $fac->getMyShareNum($uid);
        $page = ceil($num/$perPage);
        include VIEW_PATH . 'person.php';
    }

    public function avatar() {
        $uid = (int)$_REQUEST['uid'];
        $fac = Factory::getInstance('user');
        $userinfo = $fac->getUserInfo($uid);
        if (!$userinfo['avatar']) {
            $userinfo['avatar'] = DEFAULT_AVATAR;
        }
        include VIEW_PATH . 'avatar.php';
    }
}
?>