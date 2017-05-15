<?php
$dataDir = 'data';
define('LIMIT', '1000');
ini_set("display_errors","Off");
ini_set('file_uploads','ON');
ini_set('max_input_time','180');
ini_set('max_execution_time', '7200');
//date_default_timezone_set("Etc/GMT");
header("Content-type: text/html; charset=utf-8");
header("Cache-Control: no-cache, must-revalidate");

define('DS', DIRECTORY_SEPARATOR);
define('ROOT_PATH', dirname(__DIR__) . DS);
define('MODEL_PATH', ROOT_PATH . 'model' . DS);
define('VIEW_PATH', ROOT_PATH . 'html' . DS);
define('LIB_PATH', ROOT_PATH . 'lib' . DS);
define('SQL_PATH', ROOT_PATH . 'sql' . DS);
define('CONFIG_PATH', ROOT_PATH . 'conf' . DS);
define('LOG_PATH', ROOT_PATH . 'log' . DS);
define('ACTION_PATH', ROOT_PATH . 'action' . DS);
define('DEFAULT_AVATAR', 'img/default.png');
define('PWD', 'openstorer');
define('DIR', $dataDir);
define('FDFS', LIB_PATH . 'fdfs' . DS);

$icon = array(
    'default' => 'db_class_png txt',
    'folder' => 'db_class_png folder',
    'txt' => 'db_class_png txt',
    'pdf' => 'db_class_png pdf',
    'ppt' => 'db_class_png',
    'pptx' => 'db_class_png',
    'doc' => 'db_class_png docx',
    'docx' => 'db_class_png docx',
    'xls' => 'db_class_png xlsx',
    'xlsx' => 'db_class_png xlsx',
    'gif' => 'db_class_png imag',
    'bmp' => 'db_class_png imag',
    'png' => 'db_class_png imag',
    'jpg' => 'db_class_png imag',
    'jpeg' => 'db_class_png imag',
    'tif' => 'db_class_png imag',
    'tiff' => 'db_class_png imag',
    'wbmp' => 'db_class_png imag',
    'ico' => 'db_class_png imag',
    'jng' => 'db_class_png imag',
    'svg' => 'db_class_png imag',
    'svgz' => 'db_class_png imag',
    'webp' => 'db_class_png imag',
    'mp3' => 'db_class_png audio',
    'mp4' => 'db_class_png video',
    'mid' => 'db_class_png audio',
    'midi' => 'db_class_png audio',
    'kar' => 'db_class_png audio',
    'ogg' => 'db_class_png audio',
    'spx' => 'db_class_png audio',
    '3gpp' => 'db_class_png video',
    'asf' => 'db_class_png video',
    'asx' => 'db_class_png video',
    'wmv' => 'db_class_png video',
    'avi' => 'db_class_png video',
    'mng' => 'db_class_png video',
    'm4v' => 'db_class_png video',
    'flv' => 'db_class_png video',
    'webm' => 'db_class_png imag',
    'mov' => 'db_class_png video',
    'mpg' => 'db_class_png video',
    'mpeg' => 'db_class_png video',
    '3gp' => 'db_class_png video'
);
define('ICON', json_encode($icon));
include_once CONFIG_PATH . 'upload.php';
include_once CONFIG_PATH . 'lang' . DS . 'lang.php';
include_once CONFIG_PATH . 'lang' . DS . 'tipLang.php';
include_once LIB_PATH . 'response/Response.class.php';
include_once LIB_PATH . 'log/Log.class.php';
include_once LIB_PATH . 'mysql/Mysql.class.php';
include_once LIB_PATH . 'filter/FilterManager.class.php';
include_once MODEL_PATH . 'Factory.class.php';
include_once MODEL_PATH  . 'Abst.class.php';

if (function_exists('session_start')) {
    session_name('OS_SSID');
    session_start();
}
?>