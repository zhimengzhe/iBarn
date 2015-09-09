<?php
/**
 * @desc: 版权所有，翻版必究，未经同意不得用于商业项目
 * @author: 樊亚磊
 * @mail:fanyalei@aliyun.com
 * @QQ:451802973
 */
ini_set('display_errors', 'Off');
include_once 'conf/config.php';
if (!file_exists(CONFIG_PATH . 'install.lock')) {
    include_once ACTION_PATH . 'Install.class.php';
    $install = new Install();
    echo $install->install();
}
?>