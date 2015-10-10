<?php
/** 
 * 过滤管理器
 */
class FilterManager {

	private static $prefilters = array("Filter");
    const MSG = "有不被允许的特殊字符";

	public static function execute($m, $a) {
		$arr = &self::$prefilters;
		foreach ((array)$arr as $val) {
			require_once __DIR__ . "/" . $val . '.class.php';
			$fun = "run";
			if (!$val::getInstance()->$fun($m, $a)) {
				die(Response::json(FAIL, array(t(FilterManager::MSG))));
			}
		}
	}

    public static function filterPath($path) {
        if (!preg_match('/[:*?<>|\"]/', $path)) {
            die(Response::json(FAIL, array(t(FilterManager::MSG))));
        }
    }

    public static function filterName($name) {
        if (!preg_match('/[:*?<>|\"\/\\\]/', $name)) {
            die(Response::json(FAIL, array(t(FilterManager::MSG))));
        }
    }
}
?>