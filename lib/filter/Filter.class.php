<?php
/** 
 * 过滤器
 */
class Filter {
	 
	private static $paths = array();
	private static $names = array();
    private static $handle;
	
	function __construct(){
		$conf = require CONFIG_PATH . 'filter.php';
		self::$paths = &$conf['paths'];
		self::$names = &$conf['names'];
	}

    public static function getInstance() {
        if (!isset(self::$handle) || !is_object(self::$handle)) {
            self::$handle = new self();
        }
        return self::$handle;
    }

	public function run($m,$a){
		$m = strtolower($m);
		$a = strtolower($a);
		if(!empty(self::$paths[$m][$a])){
			if(!$this->filtePath($m, $a)){
				return false;
			}
		}
		if(!empty(self::$names[$m][$a])){
			 if(!$this->filteName($m, $a)){
			 	return false;
			 }
		}
	 	return true;
	}

	private function filtePath($m,$a){
		$list = &self::$paths[$m][$a];
		foreach( $list as $v){
			if(empty($_REQUEST[$v])){
				continue;
			}
            if (is_array($_REQUEST[$v])) {
                foreach ($_REQUEST[$v] as $k => $v) {
                    if(preg_match('/[:*?<>|\"]/', rawurldecode($v))){
                        unset($_REQUEST[$v][$k]);
                    }
                }
            } else {
                $_REQUEST[$v] = rawurldecode($_REQUEST[$v]);
                if(preg_match('/[:*?<>|\"]/', $_REQUEST[$v])){
                    return false;
                }
            }
		}
		return true;
	}
	
	private function filteName($m,$a){
		$list = &self::$names[$m][$a];
		foreach( $list as $v){
			if(empty($_REQUEST[$v])){
				continue;
			}
			$_REQUEST[$v] = rawurldecode($_REQUEST[$v]);
			!empty($_POST[$v]) && $_POST[$v] = rawurldecode($_POST[$v]);
			!empty($_GET[$v]) && $_GET[$v] = rawurldecode($_GET[$v]);
			if(preg_match('/[:*?<>|\"\/\\\]/', $_REQUEST[$v])){
				return false;
			}
		}
		return true;
	}
}
?>