<?php
/**
 * @desc: 版权所有，翻版必究，未经同意不得用于商业项目
 * @author: 樊亚磊
 * @mail:fanyalei@aliyun.com
 * @QQ:451802973
 */
$stime = microtime(true);
include_once 'conf/config.php';
$m = isset($_REQUEST['m']) ? ucwords($_REQUEST['m']) : 'Core';
$a = isset($_REQUEST['a']) ? $_REQUEST['a'] : 'index';
if (!file_exists(CONFIG_PATH . 'install.lock')) {
    include VIEW_PATH . 'install.php';
    exit;
}
require_once ACTION_PATH . 'User.class.php';
if (!(strtolower($m) == 'core' && ($a == 'view' || $a == 'down' || $a == 'mdown' || $a == 'own') || strtolower($m) == 'collection' && $a == 'collect' || strtolower($m) == 'share' && $a == 'pwd')) {
    if (!$_SESSION['CLOUD_UID'] && $m != 'User') {
        $islogin = json_decode(User::isLogin(), true);
        if ($islogin['code'] != SUCC) {
            header('Location: index.php?m=user');
            exit;
        }
    }
}
$_REQUEST['uid'] = (int)$_SESSION['CLOUD_UID'];
if ($_REQUEST['uid']) {
    $uidLen = strlen($_REQUEST['uid']);
    for ($i = 0; $i < $uidLen; $i++) {
        if ($i % 2 == 0) {
            $uidDir .=  sprintf("%02d", substr($_REQUEST['uid'], $i, 2)) . DS;
        }
    }
    $space = User::getUserSpace();
    $userInfo = User::getUserInfo();
    $all = LIMIT + (int)$userInfo['capacity'];
    $space['percent'] = $space['space'] / ($all * 1024 * 1024) * 100;
    $space['all'] = round($all/1024, 2);
    define('SPACE', json_encode($space));
}
define('UID_DIR', $uidDir);
define('UP_DIR', ROOT_PATH . 'files' . DS . $uidDir);
define('DATA_DIR', ROOT_PATH . DIR . DS . $uidDir);
$actionFile = ACTION_PATH . $m . '.class.php';
if (!file_exists($actionFile)) {
    echo Response::json(FAIL, array('no file'));
    exit;
}
require_once $actionFile;
if (!class_exists($m)) {
    echo Response::json(FAIL, array('no class'));
    exit;
}
$act = new $m();
if (!method_exists($act, $a)) {
    echo Response::json(FAIL, array('no method'));
    exit;
}
FilterManager::execute($m, $a);
$act->$a();
Log::getInstance('apistat')->write(array(
    'api' => 'm=' . $m . '&a=' . $a,
    'runTime' => round(microtime(true) - $stime, 5)
), 'file');
session_write_close();
?>