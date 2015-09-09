<?php
/**
 * @desc: 实现类
 * @author: 樊亚磊 mail:fanyalei@aliyun.com QQ:451802973
 */
class UserImpl extends Abst {

    public function login($name, $pwd, $remember) {
        if ($name && $pwd) {
            $res = Mysql::getInstance('slave')->fetchRow('select uid, token from users where name = :name and password = :pwd', array(
                ':name' => $name,
                ':pwd' => substr(md5($pwd . PWD), 6, 20),
            ));
            if ($res['uid']) {
                if ($remember) {
                    Mysql::getInstance()->execute('update users set token = :token where uid = :uid', array(
                        ':uid' => $res['uid'],
                        ':token' => sha1($name . substr(md5($pwd . PWD), 6, 20))
                    ));
                }
                return $res['uid'];
            } else {
                return 0;
            }
        } else {
            return 0;
        }
    }

    public function checkToken($token) {
        $res = Mysql::getInstance('slave')->fetchColumn('select uid from users where token = :token', array(':token' => $token));
        return $res ? $res : 0;
    }

    public function setLoginTime($uid) {
        $res = Mysql::getInstance()->execute('update users set lastLoginTime = :lastLoginTime where uid = :uid', array(
            ':uid' => $uid,
            ':lastLoginTime' => date('Y-m-d H:i:s'),
        ));
        return $res ? true : false;
    }

    public function regist($name, $pwd, $role = 0) {
        $mysql = Mysql::getInstance();
        if ($mysql->fetchColumn('select uid from users where name = :name', array(':name' => $name))) {
            return -1;
        }
        $time = date('Y-m-d H:i:s');
        $mysql->execute('insert into users (name, password, role, time, lastLoginTime)
                         values (:name, :pwd, :role, :time, :lastLoginTime)', array(
            ':name' => $name,
            ':pwd' => substr(md5($pwd . PWD), 6, 20),
            ':role' => $role,
            ':time' => $time,
            ':lastLoginTime' => $time,
        ));
        $id = $mysql->lastInsertid();
        return $id ? $id : 0;
    }

    public function quota($uid, $quota = 1000) {
        $mysql = Mysql::getInstance();
        $mysql->execute('update users set quota = :quota where uid = :uid', array(':uid' => $uid, ':quota' => $quota));
        return $mysql->getRowCount() ? 1 : 0;
    }

    public function getUserInfo($uid) {
        return Mysql::getInstance('slave')->fetchRow('select uid, name, avatar, capacity, role from users where uid = :uid', array(':uid' => $uid));
    }

    public function getUseSpace($uid) {
        return Mysql::getInstance('slave')->fetchColumn('select size from users where uid = :uid', array(':uid' => $uid));
    }
}
?>