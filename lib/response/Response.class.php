<?php
/**
 * @desc: 返回值格式
 * @author: 樊亚磊 mail:fanyalei@aliyun.com
 * @date: 2014-03-09
 */
if (!defined('CONFIG_PATH')) {
    define('CONFIG_PATH', dirname(dirname(__DIR__)) . '/conf/');
}
include_once CONFIG_PATH . 'code.php';
class Response {
    const JSON = 'application/json';
    const HTML = 'text/html';
    const JAVASCRIPT = 'text/javascript';
    const JS   = 'text/javascript';
    const TEXT = 'text/plain';
    const XML  = 'text/xml';

    static public $responseType = null;

    static public function json($code, $data = array()) {
        self::$responseType = Response::JSON;
        if (is_object($data)) {
            $data = get_object_vars($data);
        } elseif (!is_array($data)) {
            $data = array();
        }
        $ret['code'] = $code;
        $ret['data'] = $data;
        if (isset($_REQUEST['callback'])) {
            return $_REQUEST['callback'] . '(' . htmlspecialchars(json_encode($ret), ENT_NOQUOTES) . ')';
        } else {
            return htmlspecialchars(json_encode($ret), ENT_NOQUOTES);
        }
    }

    static public function html($code, $data = array()) {
        self::$responseType = Response::HTML;
        if (is_object($data)) {
            $data = get_object_vars($data);
        } elseif (!is_array($data)) {
            $data = array();
        }
        $ret['code'] = $code;
        $ret['data'] = $data;
        if (isset($_REQUEST['callback'])) {
            return $_REQUEST['callback'] . '(' . json_encode($ret) . ')';
        } else {
            return json_encode($data);
        }
    }

    static public function format($data, $format = 'json', $formatSetting){
        $charset = isset($formatSetting['charset']) ? $formatSetting['charset'] : 'utf8';
        if (strtolower($format) == 'json') {
            header("Content-type: " . Response::JSON.';charset=utf-8');
            return json_encode($data);
        } elseif (strtolower($format) == 'xml') {
            header("Content-type: " . Response::XML.';charset='.$charset);
            $xmlSetting = array(
                'root'     => 'response',
                'charset' => $charset,
                'num_key' => 'item',
                'version' => '1.0'
            );
            $formatSetting = is_array($formatSetting) ? $formatSetting : array();
            $xmlSetting    = array_merge($xmlSetting, $formatSetting);
            return toXml($data, $xmlSetting);
        } else {
            header("Content-type: " . Response::JAVASCRIPT.';charset=utf-8');
            return $format.'('.json_encode($data).');';
        }
    }
}

?>