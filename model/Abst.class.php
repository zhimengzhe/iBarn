<?php
/**
 * @desc: 抽象类
 * @author: 樊亚磊 mail:fanyalei@aliyun.com QQ:451802973
 */
abstract class Abst {

	private $module = '';

	public function __construct($module = '') {
		$this->module = $module;
	}

	protected function getModule() {
		return $this->module;
	}

	protected static function cleanPath($path) {
		$path = preg_replace('/[\/\\\]{1,}/', '/', $path);
		return trim($path, "/");
	}

    protected static function arraySort($arr, $field, $sort = 0) {
        $sortTmp = array();
        $arrTmp  = array();
        foreach($arr as $key => $value){
            $sortTmp[$key] = $value[$field];
        }
        asort($sortTmp);
        foreach($sortTmp as $k=>$v){
            $arrTmp[] = $arr[$k];
        }
        return $sort ? array_reverse($arrTmp) : $arrTmp;
    }

    protected function trimSpace($s) {
        return preg_replace('/(　| )+$/', '', preg_replace('/^(　| )+/', '', $s));
    }

    protected static function curlPost($url, $data = array(), $cookiepath = '', $timeout = 6) {
        $userAgent = 'Mozilla/4.0+(compatible;+MSIE+6.0;+Windows+NT+5.1;+SV1)';
        $referer = $url;
        if(!is_array($data) || !$url) return '';
        $post = $data ? http_build_query($data) : '';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);				//设置访问的url地址参数不全
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);	    //设置超时
        curl_setopt($ch, CURLOPT_USERAGENT, $userAgent);	//用户访问代理 User-Agent
        curl_setopt($ch, CURLOPT_REFERER, $referer);		//设置 referer
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);		//跟踪301
        curl_setopt($ch, CURLOPT_POST, 1);					//指定post数据
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);		//添加变量
        curl_setopt($ch, CURLOPT_COOKIEJAR, $cookiepath);	//COOKIE的存储路径,返回时保存COOKIE的路径
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);		//返回结果
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:'));//避免data数据过长问题
        $content = curl_exec($ch);
        curl_close($ch);
        return $content;
    }

    protected static function curlGet($url, $timeout = 6) {
        $ssl = substr($url, 0, 8) == "https://" ? true : false;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_DNS_USE_GLOBAL_CACHE, true);
        if ($ssl) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        }
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:'));//避免data数据过长问题
        curl_setopt($ch, CURLOPT_DNS_CACHE_TIMEOUT, 86400);
		$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($code != 200) {
            return '';
        }
        $content = curl_exec($ch);
        curl_close($ch);
        return $content;
    }

    protected static function getMimeType($file) {
        $mime = include CONFIG_PATH . 'mime.php';
        if (!function_exists('mime_content_type')) {
            if (class_exists('finfo')) {
                $finfo    = finfo_open(FILEINFO_MIME_TYPE);
                $mimetype = strtolower(finfo_file($finfo, $file));
                finfo_close($finfo);
                $ext = array_search($mimetype, (array)$mime);
            } elseif (function_exists('exec')) {
                $result = exec('file -ib ' . escapeshellarg(__FILE__));
                if (0 === strpos($result, 'text/x-php') OR 0 === strpos($result, 'text/x-c++')) {
                    $mimetype = exec('file -ib ' . escapeshellarg($file));
                    $mimetype = $mimetype[0];
                } else {
                    $result = exec('file -Ib '.escapeshellarg(__FILE__));
                    if (0 === strpos($result, 'text/x-php') OR 0 === strpos($result, 'text/x-c++')) {
                        $mimetype = exec('file -Ib ' . escapeshellarg($file));
                        $mimetype = $mimetype[0];
                    }
                }
                $ext = array_search($mimetype, (array)$mime);
            }
        } else {
            $mimetype = strtolower(mime_content_type($file));
            $ext = array_search($mimetype, (array)$mime);
        }
        unset($mime);
        return array('mime' => $mimetype, 'ext' => $ext);
    }

    protected static function getExtByPath($path) {
        $url = parse_url($path);
        return pathinfo($url['path'], PATHINFO_EXTENSION);
    }

    protected static function getExtByMime($mimetype) {
        $mime = include CONFIG_PATH . 'mime.php';
        return array_search($mimetype, (array)$mime);
    }

    protected static function reName($path) {
        $url = parse_url($path);
        $finfo = pathinfo($url['path']);
        $pathArray = explode('/', $url['path']);
        $finfo['filename'] = str_replace('.' . $finfo['extension'], '', end($pathArray));
        if (($len = strlen($finfo['filename'])) && $len > 3 && $finfo['filename'][$len - 3] == '(' && is_numeric($finfo['filename'][$len - 2]) && $finfo['filename'][$len - 1] == ')') {
            $finfo['filename'] = substr($finfo['filename'], 0, $len - 2) . (intval($finfo['filename'][$len - 2]) + 1) . ')';
        } else {
            $finfo['filename'] .= '(1)';
        }
        $path = $finfo['dirname'] != '.' ? $finfo['dirname'] . '/' : '';
        $path .= $finfo['filename'];
        isset($finfo['extension']) && !empty($finfo['extension']) && $path .= '.' . $finfo['extension'];
        return $path;
    }

    protected static function formatBytes($size) {
        $units = array(' B', ' KB', ' M', ' G', ' T');
        for ($i = 0; $size >= 1024 && $i < 4; $i++) {
            $size /= 1024;
        }
        return round($size, 2) . $units[$i];
    }

    protected static function tree($arr, $pid = 0) {
        $ret = array();
        foreach($arr as $k => $v) {
            $tmp = array();
            if($v['pid'] == $pid) {
                $tmp['mapId'] = $arr[$k]['id'];
                $tmp['path'] = $arr[$k]['path'];
                $tmp['text'] = $arr[$k]['name'];
                unset($arr[$k]);
                $nodes = self::tree($arr, $v['id']);
                if ($nodes) {
                    $tmp['nodes'] = $nodes;
                }
                $ret[] = $tmp;
            }
        }
        return $ret;
    }

    protected static function filterName($name) {
        return preg_replace('/:*?<>|\"\/\\\/', '', $name);
    }

    protected static function filterPath($path) {
        return preg_replace('/:*?<>|\"/', '', $path);
    }

    protected function getClientIp() {
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            if ($pos = strpos($_SERVER['HTTP_X_FORWARDED_FOR'], ',')) {
                $client_ip = substr($_SERVER['HTTP_X_FORWARDED_FOR'], 0, $pos);
                if (filter_var($client_ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
                    return $client_ip;
                }
            }
        }
        return $_SERVER['REMOTE_ADDR'];
    }
}
?>