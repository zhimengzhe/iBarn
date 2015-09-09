<?php
/**
 * @desc: 工厂类
 * @author: 樊亚磊 mail:fanyalei@aliyun.com QQ:451802973
 */
class Factory {
	
	private static $instances = array();

	public static function getInstance($module = 'Core') {
		if(!isset(self::$instances[$module])){
            $m = ucwords($module) . 'Impl';
            include_once  __DIR__ . '/' . $m . '.class.php';
            if (!class_exists($m)) {
                Response::json(FAIL, array('class not exists'));
                exit;
            }
            self::$instances[$module] = new $m($module);
		}
		return self::$instances[$module];
	}
}
?>