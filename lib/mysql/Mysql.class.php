<?php
/**
 * @desc: mysql基类
 * @author: 樊亚磊 mail:fanyalei@aliyun.com QQ:451802973
 */
class Mysql {
	private static $handle;
	private $conn;
	private $affectRowCount = 0;
    private $longtime = 0.3;

    public function __construct($db = 'master') {
		$config = require CONFIG_PATH . 'mysql.php';
        if ($db == 'slave') {
            shuffle($config[$db]);
            $serv = array_shift($config[$db]);
        } else {
            $serv = $config[$db];
        }
		try {
			$this->conn = new PDO('mysql:host=' . $serv['host'] . ';port=' . $serv['port'] . ';dbname=' . $serv['dbname'], $serv['user'], $serv['pwd']);
            $this->conn->exec('SET NAMES utf8');
		} catch ( PDOException $e ) {
            Log::getInstance('ConnectError')->write(json_encode(array('error' => $e->getMessage())));
            exit;
		}
	}

    public function __destruct() {
        $this->conn = null;
    }

	public static function getInstance($db = 'master') {
		if (!isset(self::$handle[$db]) || !is_object(self::$handle[$db])) {
			self::$handle[$db] = new self($db);
		}
		return self::$handle[$db];
	}

	public function execute($sql, $params = array()) {
        $startTime = microtime(true);
		$this->affectRowCount = 0;
		$stmt = $this->conn->prepare($sql);
        if ($stmt) {
            if($params) {
                foreach ($params as $k => &$param) {
                    $stmt->bindParam($k, $param, PDO::PARAM_STR, strlen($param));
                }
            }
        } else {
            return false;
        }
		$res = $stmt->execute();
        $endTime = microtime(true);
        $execTime = round($endTime - $startTime, 3);
        if ($this->longtime && $execTime > $this->longtime) {
            Log::getInstance('LongTime')->write(json_encode(array('sql' => $sql, 'params' => $params, 'runTime' => $execTime)));
        }
        if (!$res) {
            $error = $stmt->errorInfo();
            if (isset($error[2]) && $error[2]) {
                Log::getInstance('DBerror')->write(json_encode(array('sql' => $sql, 'params' => $params, 'error' => $error[2])));
            }
        }
		$this->affectRowCount = $res ? $stmt->rowCount() : 0;
		return $stmt;
	}

	public function lastInsertid() {
		return $this->conn->lastinsertid();
	}

	public function fetchColumn($sql, $params = array()) {
        $stmt = $this->execute($sql, $params);
		return $stmt->fetchColumn();
	}

    public function getRowCount() {
    	return $this->affectRowCount;
    }

    public function fetchAll($sql, $params = array()) {
        $stmt = $this->execute($sql, $params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function fetchRow($sql, $params = array()) {
        $stmt = $this->execute($sql, $params);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}