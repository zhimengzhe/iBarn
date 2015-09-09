<?php
/**
 * @desc: 日志类
 * @author: 樊亚磊 mail:fanyalei@aliyun.com
 * @date: 2014-03-09
 */
class Log {
	const STORE_TYPE_DB = "db";
	const TIME_TYPE_YEAR = "year";
	const TIME_TYPE_MONTH = "month";
	const TIME_TYPE_DAY = "day";
	const TIME_TYPE_NULL = "null";
    const HOST = "http://www.godeye.org";

	private $logPath;
	private $timeType;
	private $logName;
	private $storeName;
    private static $handle;

	public static function getInstance($logName = "",  $timeType = "") {
        if (!isset(self::$handle[$logName][$timeType]) || ! is_object(self::$handle[$logName][$timeType])) {
            self::$handle[$logName][$timeType] = new self($logName,  $timeType);
        }
        return self::$handle[$logName][$timeType];
	}

	private function __construct($logName = "",  $timeType = "") {
		$this->logName = $logName === "" ? "log" : $logName ;
		$this->timeType = $timeType;
		$this->logPath = defined("LOG_DIR")? LOG_DIR : "log/";
        if (!file_exists($this->logPath)) {
            mkdir($this->logPath);
        } else {
            chmod($this->logPath, 0777);
        }
	}

	public function write($content, $handle = 'file') {
        $info = array(
            'time'  =>  date('Y-m-d H:i:s'),
            'server'=>  addslashes($_SERVER['SERVER_ADDR']),
            'host'  =>  addslashes($_SERVER['SERVER_NAME']),
            'url'   =>  addslashes($_SERVER['REQUEST_URI']),
            'info'  =>  $content,
        );
        if ($handle == 'file') {
            file_put_contents($this->getStoreName(), $info['time'] . "|content:" . $info['info'] . "|url:" . $info['url'] . "|server:" . $info['server'] . "\n", FILE_APPEND);
        } elseif ($handle == 'redis') {
            //RedisConn::getInstance()->lPush($this->logName, json_encode($info));
        } elseif ($handle == 'url') {
            $userAgent = 'Mozilla/4.0+(compatible;+MSIE+6.0;+Windows+NT+5.1;+SV1)';
            $referer = self::HOST . '/api.php?a=stat';
            if (!is_array($info) || !$referer) return '';
            $post = $info ? http_build_query($info) : '';
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $referer);
            curl_setopt($ch, CURLOPT_TIMEOUT, 3);
            curl_setopt($ch, CURLOPT_USERAGENT, $userAgent);
            curl_setopt($ch, CURLOPT_REFERER, $referer);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
            curl_setopt($ch, CURLOPT_COOKIEJAR, '');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_exec($ch);
            curl_close($ch);
        }
	}

	private function getStoreName() {
		if(!empty($this->storeName)){
			return $this->storeName;
		}
		$name = $this->logName;
		switch ($this->timeType) {
			case self::TIME_TYPE_YEAR :
				$name .= date("Y");
				break;
			case self::TIME_TYPE_MONTH :
				$name .= date("Y_m");
				break;
			case self::TIME_TYPE_DAY :
				$name .= date("Y_m_d");
				break;
			case self::TIME_TYPE_NULL : 
				break;
		}
		$name = $this->logPath . $name . ".log";
		$this->storeName = $name;
		return $this->storeName;
	}
}
?>