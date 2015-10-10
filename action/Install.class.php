<?php
/**
 * @desc: 安装类
 * @author: 樊亚磊 mail:fanyalei@aliyun.com
 */
class Install extends Abst {

    public function check() {
        if (phpversion() < '5.0.0') {
            return Response::json(FAIL, array(t('您的php版本过低，不能安装本软件，请升级到5.0.0或更高版本再安装，谢谢！')));
        }
        if (!extension_loaded('PDO')) {
            return Response::json(FAIL, array(t('请加载PHP的PDO模块，谢谢！')));
        }
        if (!function_exists('session_start')) {
            return Response::json(FAIL, array(t('请开启session，谢谢！')));
        }
        if (!is_writable(ROOT_PATH)) {
            return Response::json(FAIL, array(t('请保证代码目录有写权限，谢谢！')));
        }
        $config = require CONFIG_PATH . 'mysql.php';
        try {
            $mysql = new PDO('mysql:host=' . $config['master']['host'] . ';port=' . $config['master']['port'], $config['master']['user'], $config['master']['pwd']);
        } catch (Exception $e) {
            return Response::json(FAIL, array(t('请正确输入信息连接mysql；开启php的PDO扩展,mysql扩展；保证启动mysql，谢谢！')));
        }
        $mysql->exec('CREATE DATABASE ' . $config['master']['dbname']);
        $mysql = null;
        unset($config);
        return Response::json(SUCC, array(t('检测通过')));
    }

    public function install() {
        //ini_set('display_errors', 'On');
        $name = self::trimSpace($_POST['name']);
        $pwd = self::trimSpace($_POST['pwd']);
        $file = self::trimSpace($_POST['file']);
        $dbuname = self::trimSpace($_POST['dbuname']);
        $dbpwd = self::trimSpace($_POST['dbpwd']);
        $dbname = self::trimSpace($_POST['dbname']);
        $host = self::trimSpace($_POST['host']);
        $port = $_POST['port'] ? (int)$_POST['port'] : 3306;
        if (!$name || !$pwd || !$file || !$dbuname || !$dbname || !$host) {
            return Response::json(FAIL, array('数据不能为空'));
        }
        $res = mkdir($file, 0777, true);
        if (!$res && !file_exists($file)) {
            return Response::json(FAIL, array(t('文件存储目录创建失败，请检查对应目录是否有写权限后重试')));
        }
        $mysqlConf = array();
        $mysqlConf['slave'][0]['user'] = $mysqlConf['master']['user'] = $dbuname;
        $mysqlConf['slave'][0]['pwd'] = $mysqlConf['master']['pwd'] = $dbpwd;
        $mysqlConf['slave'][0]['host'] = $mysqlConf['master']['host'] = $host;
        $mysqlConf['slave'][0]['port'] = $mysqlConf['master']['port'] = $port;
        $mysqlConf['slave'][0]['dbname'] = $mysqlConf['master']['dbname'] = $dbname;
        if (!file_exists(CONFIG_PATH . 'mysql.php')) {
            $int = file_put_contents(CONFIG_PATH . 'mysql.php', '<?php return ' . var_export($mysqlConf, true) . '; ?>');
            if (!$int) {
                return Response::json(FAIL, array(t('Conf目录文件写入失败，请检查是否有写权限')));
            }
        }
        $cres = $this->check();
        $check = json_decode($cres, true);
        if ($check['code'] != 1) {
            return $cres;
        }
        $this->executeSql(SQL_PATH . 'opendisk.sql');
        $ures = Factory::getInstance('user')->regist($name, $pwd, 1);
        if ($ures <= 0) {
            return Response::json(FAIL, array(t('管理员账号创建失败，请重新安装')));
        }
        $handle = fopen(CONFIG_PATH . 'install.lock', 'w');
        fclose($handle);
        $_SESSION['CLOUD_UID'] = $ures;
        setcookie('CLOUD_UID', $ures, time() + 3600 * 24);
        return Response::json(SUCC, array(t('安装成功')));
    }

    public function executeSql($file) {
        $fileInfo = file_get_contents($file);
        $sql = $this->parseSql($fileInfo);
        unset($fileInfo);
        $mysql = Mysql::getInstance();
        foreach ($sql as $s) {
            $mysql->execute($s);
        }
    }

    public function parseSql($sql = '') {
        $sql = str_replace("\r", "\n", $sql);
        $ret = array();
        $aSql = explode(";\n", trim($sql));
        unset($sql);
        $num = 0;
        foreach ($aSql as $sql) {
            $ret[$num] = '';
            $queries = explode("\n", $sql);
            $queries = array_filter($queries);
            foreach ($queries as $query) {
                $str1 = substr($query, 0, 1);
                if ($str1 !== '#' && $str1 !== '-') {
                    $ret[$num] .= $query;
                }
            }
            $num++;
        }
        return $ret;
    }
}
?>