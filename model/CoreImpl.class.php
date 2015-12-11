<?php
/**
 * @desc: 实现类
 * @author: 樊亚磊 mail:fanyalei@aliyun.com QQ:451802973
 */
class CoreImpl extends Abst {

    private $tier = 100;

    public function getDirList($uid, $id = 0, $order = 'time', $by = 'desc', $type = 0, $curPage = 1, $perPage = 100) {
        $bind = array(':uid' => $uid);
        if (in_array($order, array('name', 'size', 'time'))) {
            $mix = ' order by ' . $order . ' ' . $by;
        } else {
            $mix = ' order by isdir desc, time desc ';
        }
        if ($type) {
            $bind[':type'] = (int)$type;
            $where = ' and type = :type ';
        } else {
            $bind[':pid'] = $id;
            $where = ' and pid = :pid ';
        }
        $sql = 'select id, uid, name, pid, path, location, isdir, size, time, type
                  from filemap
              where uid = :uid ' . $where . $mix . ' limit ' . $perPage . ' offset ' . ($curPage - 1) * $perPage;
        return Mysql::getInstance('slave')->fetchAll($sql, $bind);
    }

    public function getDirNum($uid, $id = 0, $type = 0) {
        $bind = array(':uid' => $uid, ':pid' => $id);
        if ($type) {
            $bind[':type'] = (int)$type;
            $where = ' and type = :type ';
        }
        $sql = 'select count(*)
                  from filemap
              where uid = :uid and pid = :pid' . $where;
        return Mysql::getInstance('slave')->fetchColumn($sql, $bind);
    }

    public function getRecyList($uid, $order = 'time', $by = 'desc', $curPage = 1, $perPage = 100) {
        $bind = array(':uid' => $uid);
        if (in_array($order, array('name', 'size', 'time'))) {
            $mix = ' order by ' . $order . ' ' . $by;
        } else {
            $mix = ' order by isdir desc, time desc ';
        }
        $sql = 'select id, mapId, uid, name, pid, path, location, isdir, size, time
                  from recycle
              where uid = :uid and status = 0 ' . $mix . ' limit ' . $perPage . ' offset ' . ($curPage - 1) * $perPage;
        return Mysql::getInstance('slave')->fetchAll($sql, $bind);
    }

    public function getRecyNum($uid, $id = 0) {
        $bind = array(':uid' => $uid, ':pid' => $id);
        $sql = 'select count(*)
                    from recycle
                where uid = :uid and pid = :pid';
        return Mysql::getInstance('slave')->fetchColumn($sql, $bind);
    }

    public function search($uid, $search, $order = 'time', $by = 'desc', $type = 0, $class = '', $curPage = 1, $perPage = 100) {
        $table = $class != 'recycle' ? 'filemap' : 'recycle';
        if (in_array($order, array('name', 'size', 'time'))) {
            $mix = ' order by ' . $order . ' ' . $by;
        } else {
            $mix = '';
        }
        if ($type) {
            $where = ' and type = ' . (int)$type;
        }
        $sql = 'select id, uid, name, pid, path, location, isdir, size, time
                  from ' . $table . '
              where uid = :uid and name like :search' . $where . $mix . ' limit ' . $perPage . ' offset ' . ($curPage - 1) * $perPage;
        return Mysql::getInstance('slave')->fetchAll($sql, array(':uid' => $uid, ':search' => '%' . $search . '%'));
    }

    public function searchNum($uid, $search, $type = 0, $class = '') {
        $table = $class != 'recycle' ? 'filemap' : 'recycle';
        if ($type) {
            $where = ' and type = ' . (int)$type;
        }
        $sql = 'select count(*)
                  from ' . $table . '
              where uid = :uid and name like :search' . $where;
        return Mysql::getInstance('slave')->fetchColumn($sql, array(':uid' => $uid, ':search' => '%' . $search . '%'));
    }

    public function getFileMapByName($uid, $pid, $name) {
        $sql = 'select id, pid, path from filemap where uid = :uid and pid = :pid and name = :name';
        return Mysql::getInstance('slave')->fetchRow($sql, array(':uid' => $uid, ':pid' => $pid, ':name' => $name));
    }

    public function getFileMap($id) {
        if (strpos($id, ',') !== false) {
            $sql = 'select * from filemap where find_in_set(cast(id as char), :id)';
            return Mysql::getInstance('slave')->fetchAll($sql, array(':id' => $id));
        } else {
            $sql = 'select * from filemap where id = :id';
            return Mysql::getInstance('slave')->fetchRow($sql, array(':id' => $id));
        }
    }

    public function getShareInfo($id) {
        $sql = 'select * from share where id = :id';
        return Mysql::getInstance('slave')->fetchRow($sql, array(':id' => $id));
    }

    public function getShareByMap($mapIds) {
        if (strpos($mapIds, ',') !== false) {
            $sql = 'select id, mapId, pwd, overTime, price from share where find_in_set(cast(mapId as char), :ids)';
        } else {
            $sql = 'select id, mapId, pwd, overTime, price from share where mapId = :ids';
        }
        return Mysql::getInstance('slave')->fetchAll($sql, array(':ids' => $mapIds));
    }

    public function isShare($uid, $mapIds) {
        if (strpos($mapIds, ',') !== false) {
            $sql = 'select id, mapId, pwd, overTime from share where uid = :uid and find_in_set(cast(mapId as char), :ids)';
        } else {
            $sql = 'select id, mapId, pwd, overTime from share where uid = :uid and mapId = :ids';
        }
        return Mysql::getInstance('slave')->fetchAll($sql, array(':uid' => $uid, ':ids' => $mapIds));
    }

    public function addFile($uid, $path, $location, $size = 0, $isdir = 0, $origin = 'os', $mime = '', $hash = '', $isCover = 0, $md5 = '') {
        $mysql = Mysql::getInstance();
        $loc = $mysql->fetchColumn('select location from fileinfo where md5 = :md5 and size = :size', array(
            ':md5' => $md5,
            ':size' => $size
        ));
        if ($loc) {
            $location = $loc;
        }
        $type = 0;
        if ($mime) {
            $type = $this->getTypeByMime($mime);
        }
        $res = $this->addFileMap($uid, $path, $location, $size, $mime, $isdir, $origin, $isCover, $type);
        if ($res) {
            if (!$loc) {
                $mysql->execute('insert into fileinfo (hash, mime, location, md5, size, time)
                                 values (:hash, :mime, :location, :md5, :size, :time)', array(
                    ':hash' => $hash,
                    ':mime' => strtolower($mime),
                    ':location' => $location,
					':md5' => $md5,
                    ':size' => $size,
                    ':time' => date('Y-m-d H:i:s')
                ));
            }
            if ($isdir == 0) {
                $uinfo = $mysql->fetchRow('select sum(size) size, count(*) num from filemap where uid = :uid and isdir = 0', array(':uid' => $uid));
                $mysql->execute('update users set fileNum = :fileNum, size = :size where uid = :uid', array(
                    ':uid' => $uid,
                    ':fileNum' => $uinfo['num'],
                    ':size' => $uinfo['size']
                ));
            }
        }
        return $res;
    }

    public function getTypeByMime($mime) {
        $mimeinfo = explode('/', $mime);
        $type = 6;
        $text = array(
            'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'vnd.ms-powerpoint', 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'vnd.ms-excel', 'application/x-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'application/pdf'
        );
        if (in_array($mime, $text) || $mime == 'text/plain') {
            $type = 1;
        }
        if ($mimeinfo[0] == 'image') {
            $type = 2;
        }
        if ($mimeinfo[0] == 'audio') {
            $type = 3;
        }
        if ($mimeinfo[0] == 'video' || $mime == 'application/x-shockwave-flash') {
            $type = 4;
        }
        if ($mime == 'application/x-bittorrent') {
            $type = 5;
        }
        return $type;
    }

    public function uploadCheck($hash, $size) {
        $ret = Mysql::getInstance()->fetchRow('select location, size, md5, mime from fileinfo where hash = :hash and size = :size', array(
            ':hash' => $hash,
            ':size' => $size
        ));
        return $ret['location'] ? array('location' => $ret['location'], 'size' => $ret['size'], 'md5' => $ret['md5'], 'mime' => $ret['mime']) : array();
    }

    public function addFileMap($uid, $path, $location = '', $size = 0, $mime = '', $isDir = 1, $origin = 'os', $isCover = 0, $type = 0) {
        $mysql = Mysql::getInstance();
        $pathInfo = array_filter(explode('/', trim($path, '/')));
        $num = count($pathInfo);
        if ($num > $this->tier) {
            return 2;
        }
        if (!empty($pathInfo)) {
            $time = date('Y-m-d H:i:s');
            $ipid = $pid = $dirSize = 0;
            $isdir = 1;
            $loc = '';
            $numPath = '/';
            foreach ($pathInfo as $k => $v) {
                if ($k == $num - 1) {
                    $loc = $location;
                    $dirSize = $size;
                    $isdir = $isDir;
                }
                $pathId = $mysql->fetchColumn('select id from filemap where uid = :uid and name = :name and pid = :pid', array(
                    ':uid' => $uid,
                    ':name' => $v,
                    ':pid' => $pid
                ));
                if ($pathId) {
                    if (!$isCover && $k == $num - 1) {
                        if ($isDir == 1) {
                            return 3;
                        }
                        $v = $this->reName($v);
                        while ($mysql->fetchColumn('select id from filemap where uid = :uid and name = :name and pid = :pid', array(
                            ':uid' => $uid,
                            ':name' => $v,
                            ':pid' => $pid
                        ))) {
                            $v = $this->reName($v);
                        }
                    }
                    $pid = $pathId;
                    if ($isCover && $loc) {
                        $mysql->execute('delete from filemap where id = :id', array(
                            ':id' => $pathId
                        ));
                    }
                }
                if (!$pathId || $loc) {
                    $mysql->execute('insert into filemap (uid, name, path, pid, location, isdir, size, mime, type, origin, time)
                                     values (:uid, :name, :path, :pid, :location, :isdir, :size, :mime, :type, :origin, :time)', array(
                        ':uid' => $uid,
                        ':name' => $v,
                        ':path' => $numPath,
                        ':pid' => (int)$ipid,
                        ':location' => $loc,
                        ':size' => $dirSize,
                        ':mime' => $mime,
                        ':type' => ($isdir ? 0 : $type),
                        ':isdir' => $isdir,
                        ':origin' => $origin,
                        ':time' => $time
                    ));
                    $pid = $mysql->lastInsertid();
                    if (!$pid) {
                        return 0;
                    }
                }
                if ($k < $num - 1) {
                    $numPath = $numPath . $pid . '/';
                }
                $ipid = $pid;
            }
            return array('id' => $pid, 'name' => $v, 'location' => $loc, 'isdir' => $isdir, 'type' => ($isdir ? 0 : $type), 'size' => $dirSize, 'time' => $time);
        } else {
            return 0;
        }
    }

    public function addFolder($uid, $path, $name, $origin = 'os', $isCover = 0) {
        return $this->addFileMap($uid, trim($path, '/') . '/' . $name, '', 0, '', 1, $origin, $isCover);
    }

    public function getMyShareList($uid, $curPage = 1, $perPage = 20, $name = '', $order = 'shareTime', $by = 'desc') {
        $bind = array(':uid' => $uid);
        if (in_array($order, array('view', 'down', 'saveNum', 'shareTime'))) {
            $mix = ' order by ' . $order . ' ' . $by;
        } else {
            $mix = ' order by isdir desc, shareTime desc ';
        }
        $sql = 'select id, mapId, pwd, isdir, size, view, down, saveNum, shareTime
                  from share
              where uid = :uid ' . $mix . ' limit ' . $perPage . ' offset ' . ($curPage - 1) * $perPage;
        if (trim($name) != '') {
            $like = ' and m.name like :name';
            $bind[':name'] = '%' . $name . '%';
            $sql = 'select s.id, s.mapId, s.isdir, s.size, s.view, s.down, s.saveNum, s.shareTime, s.pwd, m.name, m.type from share s
                      left join fileMap m on s.mapId = m.id
                  where s.uid = :uid ' . $like . $mix . ' limit ' . $perPage . ' offset ' . ($curPage - 1) * $perPage;
        }
        return Mysql::getInstance('slave')->fetchAll($sql, $bind);
    }

    public function getMyShareNum($uid, $name = '') {
        $bind = array(':uid' => $uid);
        $sql = 'select count(*) from share where uid = :uid';
        if (trim($name) != '') {
            $like = ' and m.name like :name';
            $bind[':name'] = '%' . $name . '%';
            $sql = 'select count(s.*) from share s left join fileMap m on s.mapId = m.id
                  where s.uid = :uid' . $like;
        }
        return Mysql::getInstance('slave')->fetchColumn($sql, $bind);
    }

    public function getShareMeList($uid, $curPage = 1, $perPage = 20, $name = '', $order = 'shareTime', $by = 'desc') {
        $bind = array(':sid' => $uid);
        if (in_array($order, array('view', 'down', 'saveNum', 'shareTime'))) {
            $mix = ' order by ' . $order . ' ' . $by;
        } else {
            $mix = ' order by isdir desc, shareTime desc ';
        }
        $sql = 'select id, mapId, pwd, isdir, size, view, down, saveNum, shareTime
                  from share
              where sid = :sid and type = 2 ' . $mix . ' limit ' . $perPage . ' offset ' . ($curPage - 1) * $perPage;
        if (trim($name) != '') {
            $like = ' and m.name like :name';
            $bind[':name'] = '%' . $name . '%';
            $sql = 'select s.id, s.mapId, s.isdir, s.size, s.view, s.down, s.saveNum, s.shareTime, s.pwd, m.name, m.type from share s
                      left join fileMap m on s.mapId = m.id
                  where s.sid = :sid and s.type = 2 ' . $like . $mix . ' limit ' . $perPage . ' offset ' . ($curPage - 1) * $perPage;
        }
        return Mysql::getInstance('slave')->fetchAll($sql, $bind);
    }

    public function getShareMeNum($uid, $name = '') {
        $bind = array(':sid' => $uid);
        $sql = 'select count(*) from share where sid = :sid and type = 2';
        if (trim($name) != '') {
            $like = ' and m.name like :name';
            $bind[':name'] = '%' . $name . '%';
            $sql = 'select count(s.*) from share s left join fileMap m on s.mapId = m.id
                  where s.sid = :sid and s.type = 2' . $like;
        }
        return Mysql::getInstance('slave')->fetchColumn($sql, $bind);
    }

    public function getPubList($curPage, $perPage, $name = '', $order = 'shareTime', $by = 'desc') {
        $this->delPub();
        $bind = array();
        if (in_array($order, array('view', 'down', 'saveNum', 'shareTime'))) {
            $mix = ' order by ' . $order . ' ' . $by;
        } else {
            $mix = ' order by isdir desc, shareTime desc ';
        }
        $sql = 'select id, uid, mapId, isdir, size, view, down, saveNum, shareTime, pwd
                  from share
              where type = 1 and (overTime >= now() or overTime = "0000-00-00 00:00:00") ' . $mix
            . ' limit ' . $perPage . ' offset ' . ($curPage - 1) * $perPage;
        if (trim($name) != '') {
            $like = ' and m.name like :name';
            $bind[':name'] = '%' . $name . '%';
            $sql = 'select s.id, s.uid, s.mapId, s.isdir, s.size, s.view, s.down, s.saveNum, s.shareTime, s.pwd, m.id, m.name, m.type from share s
                      left join fileMap m on s.mapId = m.id
                  where s.type = 1 and (s.overTime >= now() or s.overTime = "0000-00-00 00:00:00") '
                . $like . $mix . ' limit ' . $perPage . ' offset ' . ($curPage - 1) * $perPage;
        }
        return Mysql::getInstance('slave')->fetchAll($sql, $bind);
    }

    public function getPubNum($name = '') {
        $bind = array();
        $sql = 'select count(*) from share where type = 1 and (overTime >= now() or overTime = "0000-00-00 00:00:00")';
        if (trim($name) != '') {
            $like = ' and m.name like :name';
            $bind[':name'] = '%' . $name . '%';
            $sql = 'select count(s.*) from share s left join fileMap m on s.mapId = m.id
                  where s.type = 1 and (s.overTime >= now() or s.overTime = "0000-00-00 00:00:00")' . $like;
        }
        return Mysql::getInstance('slave')->fetchColumn($sql, $bind);
    }

    private function delPub() {
        $mysql = Mysql::getInstance();
        $mysql->execute('delete from share where overTime < now() and overTime != "0000-00-00 00:00:00"');
        return $mysql->getRowCount() ? 1 : 0;
    }

    public function share($uid, $type, $mapId, $pwd, $overTime, $price = 0, $sid = 0) {
        $mysql = Mysql::getInstance();
        $fileMap = $mysql->fetchRow('select location, isdir from filemap where id = :mapId and uid = :uid', array(':mapId' => $mapId, ':uid' => $uid));
        if (!$fileMap['location'] && $fileMap['isdir'] == 0) {
            return 0;
        }
        if ($type == 1) {
            $sql = 'select id from share where uid = :uid and mapId = :mapId and (overTime > now() or overTime = "0000-00-00 00:00:00")';
            if ($mysql->fetchColumn($sql, array(
                ':uid' => $uid,
                ':mapId' => $mapId
            ))) {
                return -1;
            }
            $sql = 'insert into share (uid, mapId, isdir, type, pwd, shareTime, overTime, price)
                values (:uid, :mapId, :isdir, :type, :pwd, :shareTime, :overTime, :price)';
            $mysql->execute($sql, array(
                ':uid' => $uid,
                ':mapId' => $mapId,
                ':isdir' => $fileMap['isdir'],
                ':type' => $type,
                ':pwd' => $pwd,
                ':shareTime' => date('Y-m-d H:i:s'),
                ':overTime' => $overTime,
                ':price' => $price
            ));
            $id = $mysql->lastInsertid();
            return $id ? $id : 0;
        } else {
            $sql = 'select id from share where uid = :uid and sid = :sid and mapId = :mapId and type = :type';
            if ($mysql->fetchColumn($sql, array(
                ':uid' => $uid,
                ':sid' => $sid,
                ':mapId' => $mapId,
                ':type' => $type
            ))) {
                return -1;
            }
            $sql = 'insert into share (uid, sid, mapId, isdir, type, shareTime, price)
                    values (:uid, :sid, :mapId, :isdir, :type, :shareTime, :price)';
            $mysql->execute($sql, array(
                ':uid' => $uid,
                ':sid' => $sid,
                ':mapId' => $mapId,
                ':isdir' => $fileMap['isdir'],
                ':type' => $type,
                ':shareTime' => date('Y-m-d H:i:s'),
                ':price' => $price
            ));
            $id = $mysql->lastInsertid();
            return $id ? $id : 0;
        }
    }

    public function unShare($uid, $ids) {
        $mysql = Mysql::getInstance();
        $sql = 'delete from share where uid = :uid and find_in_set(cast(id as char), :ids)';
        $mysql->execute($sql, array(':uid' => $uid, ':ids' => $ids));
        return $mysql->getRowCount() ? 1 : 0;
    }

    public function unShareByMapId($ids) {
        $mysql = Mysql::getInstance();
        $sql = 'delete from share where find_in_set(cast(mapId as char), :ids)';
        $mysql->execute($sql, array(':ids' => $ids));
        return $mysql->getRowCount() ? 1 : 0;
    }

    public function unShareAll($uid) {
        $mysql = Mysql::getInstance();
        $sql = 'delete from share where uid = :uid';
        $mysql->execute($sql, array(':uid' => $uid));
        return $mysql->getRowCount() ? 1 : 0;
    }

    public function getCollectList($uid, $curPage = 1, $perPage = 20, $name = '', $order = 'collectTime', $by = 'desc') {
        $bind = array(':uid' => $uid);
        if (in_array($order, array('size', 'collectTime'))) {
            $mix = ' order by c.' . $order . ' ' . $by;
        } else {
            $mix = ' order by c.isdir desc, c.collectTime desc ';
        }
        $sql = 'select c.id, c.sid, c.isdir, c.size, c.collectTime, c.mapId, s.pwd from collection c, share s
              where c.mapId = s.mapId and c.uid = :uid ' . $mix . ' limit ' . $perPage . ' offset ' . ($curPage - 1) * $perPage;
        if (trim($name) != '') {
            $like = ' and m.name like :name';
            $bind[':name'] = '%' . $name . '%';
            $sql = 'select c.id, c.isdir, c.size, c.collectTime, c.mapId, s.pwd, m.name, m.type
                      from collection c inner join share s on c.mapId = s.mapId
                          left join fileMap m on c.mapId = m.id
                  where c.uid = :uid ' . $like . $mix . ' limit ' . $perPage . ' offset ' . ($curPage - 1) * $perPage;
        }
        return Mysql::getInstance('slave')->fetchAll($sql, $bind);
    }

    public function getCollectNum($uid, $name = '') {
        $bind = array(':uid' => $uid);
        $sql = 'select count(*) from collection where uid = :uid ';
        if (trim($name) != '') {
            $like = ' and m.name like :name';
            $bind[':name'] = '%' . $name . '%';
            $sql = 'select count(c.*) from collection c left join fileMap m on c.mapId = m.id
                  where c.uid = :uid ' . $like;
        }
        return Mysql::getInstance('slave')->fetchColumn($sql, $bind);
    }

    public function getCollectByMap($mapIds) {
        if (strpos($mapIds, ',') !== false) {
            $sql = 'select id, mapId, uid, sid from collection where find_in_set(cast(mapId as char), :ids)';
        } else {
            $sql = 'select id, mapId, uid, sid from collection where mapId = :ids';
        }
        return Mysql::getInstance('slave')->fetchAll($sql, array(':ids' => $mapIds));
    }

    public function isCollect($uid, $mapIds) {
        if (strpos($mapIds, ',') !== false) {
            $sql = 'select id, mapId, uid, sid from collection where uid = :uid and find_in_set(cast(mapId as char), :ids)';
        } else {
            $sql = 'select id, mapId, uid, sid from collection where uid = :uid and mapId = :ids';
        }
        return Mysql::getInstance('slave')->fetchAll($sql, array(':uid' => (int)$uid, ':ids' => $mapIds));
    }

    public function collect($uid, $sid, $mapId) {
        $mysql = Mysql::getInstance();
        $fileMap = $mysql->fetchRow('select location, isdir, size from filemap where id = :mapId and uid = :sid', array(':mapId' => $mapId, ':sid' => $sid));
        if (!$fileMap['location'] && $fileMap['isdir'] == 0) {
            return 0;
        }
        $sql = 'select id from collection where uid = :uid and mapId = :mapId and sid = :sid';
        if ($mysql->fetchColumn($sql, array(
            ':uid' => $uid,
            ':sid' => $sid,
            ':mapId' => $mapId
        ))) {
            return 0;
        }
        $sql = 'insert into collection (uid, sid, mapId, location, isdir, size, collectTime)
                values (:uid, :sid, :mapId, :location, :isdir, :size, :collectTime)';
        $mysql->execute($sql, array(
            ':uid' => $uid,
            ':sid' => $sid,
            ':mapId' => $mapId,
            ':location' => $fileMap['location'],
            ':isdir' => $fileMap['isdir'],
            ':size' => $fileMap['size'],
            ':collectTime' => date('Y-m-d H:i:s')
        ));
        $id = $mysql->lastInsertid();
        return $id ? $id : 0;
    }

    public function incrShareView($id) {
        $mysql = Mysql::getInstance();
        $sql = 'update share set view = view + 1 where id = :id';
        $mysql->execute($sql, array(':id' => $id));
        return $mysql->getRowCount() ? 1 : 0;
    }

    public function incrShareDown($id) {
        $mysql = Mysql::getInstance();
        $sql = 'update share set down = down + 1 where id = :id';
        $mysql->execute($sql, array(':id' => $id));
        return $mysql->getRowCount() ? 1 : 0;
    }

    public function incrCollect($ids) {
        $mysql = Mysql::getInstance();
        $sql = 'update share set saveNum = saveNum + 1 where find_in_set(cast(id as char), :ids)';
        $mysql->execute($sql, array(':ids' => $ids));
        return $mysql->getRowCount() ? 1 : 0;
    }

    public function decrCollect($ids) {
        $mysql = Mysql::getInstance();
        $info = $mysql->fetchAll('select mapId from collection where find_in_set(cast(id as char), :ids)', array(':ids' => $ids));
        if ($info) {
            $map = array();
            foreach ($info as $v) {
                $map[] = $v['mapId'];
            }
            $maps = implode(',', $map);
            $sql = 'update share set saveNum = saveNum - 1 where find_in_set(cast(mapId as char), :ids) and saveNum > 0';
            $mysql->execute($sql, array(':ids' => $maps));
        }
        return $mysql->getRowCount() ? 1 : 0;
    }

    public function unCollect($uid, $ids) {
        $mysql = Mysql::getInstance();
        $sql = 'delete from collection where uid = :uid and find_in_set(cast(id as char), :ids)';
        $mysql->execute($sql, array(':uid' => $uid, ':ids' => $ids));
        return $mysql->getRowCount() ? 1 : 0;
    }

    public function unCollectByMapId($ids) {
        $mysql = Mysql::getInstance();
        $sql = 'delete from collection where find_in_set(cast(mapId as char), :ids)';
        $mysql->execute($sql, array(':ids' => $ids));
        return $mysql->getRowCount() ? 1 : 0;
    }

    public function unCollectAll($uid) {
        $mysql = Mysql::getInstance();
        $sql = 'delete from collection where uid = :uid';
        $mysql->execute($sql, array(':uid' => $uid));
        return $mysql->getRowCount() ? 1 : 0;
    }

    public function recycle($param = array()) {
        $mysql = Mysql::getInstance();
        $id = $mysql->fetchColumn('select id from recycle where mapId = :mapId', array(':mapId' => $param['id']));
        if ($id) {
            return 2;
        }
        $sql = 'insert into recycle (mapId, uid, name, pid, path, location, isdir, mime, type, size, origin, time, status)
                values (:mapId, :uid, :name, :pid, :path, :location, :isdir, :mime, :type, :size, :origin, :time, :status)';
        $mysql->execute($sql, array(
            ':mapId' => $param['id'],
            ':uid' => $param['uid'],
            ':name' => $param['name'],
            ':pid' => $param['pid'],
            ':path' => $param['path'],
            ':location' => $param['location'],
            ':isdir' => $param['isdir'],
            ':mime' => $param['mime'],
            ':type' => $param['type'],
            ':size' => $param['size'],
            ':origin' => $param['origin'],
            ':time' => $param['time'],
            ':status' => (int)$param['status']
        ));
        $id = $mysql->lastInsertid();
        return $id ? $id : 0;
    }

    public function recover($uid, $id) {
        $mysql = Mysql::getInstance();
        $param = Mysql::getInstance('slave')->fetchRow('select * from recycle where id = :id and uid = :uid', array(':id' => $id, ':uid' => $uid));
        if (empty($param)) {
            return 0;
        }
        if ($param['isdir']) {
            while (Mysql::getInstance('slave')->fetchColumn('select id from filemap where uid = :uid and path = :path and name = :name and isdir = 1', array(
                ':uid' => $uid,
                ':path' => $param['path'],
                ':name' => $param['name']
            ))) {
                $param['name'] = $this->reName($param['name']);
            }
            $info = Mysql::getInstance('slave')->fetchAll('select * from recycle where uid = :uid and path like :path', array(':uid' => $uid, ':path' => '/' . $param['mapId']  . '/' . '%'));
            if ($info) {
                foreach ($info as $v) {
                    while (Mysql::getInstance('slave')->fetchColumn('select id from filemap where uid = :uid and path = :path and name = :name and isdir = :isdir', array(
                        ':uid' => $uid,
                        ':path' => $v['path'],
                        ':name' => $v['name'],
                        ':isdir' => $v['isdir'],
                    ))) {
                        $v['name'] = $this->reName($v['name']);
                    }
                    $sql = 'insert into filemap (id, uid, name, pid, path, location, isdir, mime, type, size, origin, time)
                            values (:mapId, :uid, :name, :pid, :path, :location, :isdir, :mime, :type, :size, :origin, :time)';
                    $mysql->execute($sql, array(
                        ':mapId' => $v['mapId'],
                        ':uid' => $v['uid'],
                        ':name' => $v['name'],
                        ':pid' => $v['pid'],
                        ':path' => $v['path'],
                        ':location' => $v['location'],
                        ':isdir' => $v['isdir'],
                        ':mime' => $v['mime'],
                        ':type' => $v['type'],
                        ':size' => $v['size'],
                        ':origin' => $v['origin'],
                        ':time' => $v['time']
                    ));
                }
            }
        } else {
            while (Mysql::getInstance('slave')->fetchColumn('select id from filemap where uid = :uid and path = :path and name = :name and isdir = 0', array(
                ':uid' => $uid,
                ':path' => $param['path'],
                ':name' => $param['name']
            ))) {
                $param['name'] = $this->reName($param['name']);
            }
        }
        $sql = 'insert into filemap (id, uid, name, pid, path, location, isdir, mime, type, size, origin, time)
                values (:mapId, :uid, :name, :pid, :path, :location, :isdir, :mime, :type, :size, :origin, :time)';
        $mysql->execute($sql, array(
            ':mapId' => $param['mapId'],
            ':uid' => $param['uid'],
            ':name' => $param['name'],
            ':pid' => $param['pid'],
            ':path' => $param['path'],
            ':location' => $param['location'],
            ':isdir' => $param['isdir'],
            ':mime' => $param['mime'],
            ':type' => $param['type'],
            ':size' => $param['size'],
            ':origin' => $param['origin'],
            ':time' => $param['time']
        ));
        $id = $mysql->lastInsertid();
        return $id ? $id : 0;
    }

    public function setName($mapId, $name) {
        $mysql = Mysql::getInstance();
        $id = $mysql->fetchColumn('select id from filemap where pid = (select pid from filemap where id = :id) and name = :name', array(':id' => $mapId, ':name' => $name));
        if ($id == $mapId) {
            return 1;
        } elseif ($id) {
            return 2;
        }
        $mysql->execute('update filemap set name = :name where id = :mapId', array(':mapId' => $mapId, ':name' => $name));
        return $mysql->getRowCount() ? 1 : 0;
    }

    public function delFileMap($uid, $mapId) {
		$mysql = Mysql::getInstance();
        $mapinfo = $this->getFileMap($mapId);
        $mysql->execute('delete from filemap where uid = :uid and find_in_set(cast(id as char), :mapId)', array(':uid' => $uid, ':mapId' => $mapId));
        $ret = $mysql->getRowCount();
        if ($ret) {
            if ($mapinfo['isdir'] == 0) {
                $uinfo = $mysql->fetchRow('select sum(size) size, count(*) num from filemap where uid = :uid and isdir = 0', array(':uid' => $uid));
                $mysql->execute('update users set fileNum = :fileNum, size = :size where uid = :uid', array(
                    ':uid' => $uid,
                    ':fileNum' => $uinfo['num'],
                    ':size' => $uinfo['size']
                ));
            }
            return 1;
        } else {
            return 0;
        }
    }

    public function delRecycle($uid, $ids) {
        if ($ids) {
            $ids = explode(',', $ids);
            $mysql = Mysql::getInstance('slave');
            foreach ($ids as $v) {
                $info = $mysql->fetchRow('select isdir, mapId from recycle where id = :id and uid = :uid', array(':id' => $v, ':uid' => $uid));
                if ($info['isdir']) {
                    Mysql::getInstance()->execute('delete from recycle where uid = :uid and path like :path', array(':uid' => $uid, ':path'  => '/' . $info['mapId']  . '/' . '%'));
                }
                Mysql::getInstance()->execute('delete from recycle where id = :id and uid = :uid', array(':id' => $v, ':uid' => $uid));
            }
        }
        return Mysql::getInstance()->getRowCount() ? 1 : 0;
    }

    public function getFileMapByPid($uid, $pid, $file = 0) {
        if ($file) {
            $isdir = ' and isdir = 0 ';
        }
        return Mysql::getInstance('slave')->fetchAll('select * from filemap
              where uid = :uid ' . $isdir . ' and path like :path', array(':uid' => $uid, ':path' => '%/' . $pid  . '/' . '%'));
    }

    public function move($uid, $smapId, $dmapId, $dpath, $cover = 0) {
        $mysql = Mysql::getInstance();
        $info = $mysql->fetchRow('select id, name, pid, path, isdir from filemap where id = :id', array(':id' => $smapId));
        if (!$info['id']) {
            return 0;
        }
        if ($info['pid'] == $dmapId) {
            return 2;
        }
        $mysql->execute('update filemap set pid = :pid, path = :path where id = :id', array(':pid' => $dmapId, ':path' => $dpath . $dmapId . '/', ':id' => $smapId));
        if (!$mysql->getRowCount()) {
            return 0;
        }
        if (!$cover) {
            while ($mysql->fetchColumn('select id from filemap where pid = :pid and id != :mapId and name = :name and isdir = :isdir', array(
                ':pid' => $dmapId,
                ':mapId' => $smapId,
                ':name' => $info['name'],
                ':isdir' => $info['isdir']
            ))) {
                $exist = true;
                $info['name'] = $this->reName($info['name']);
            }
        }
        if ($info['isdir']) {
            $mysql->execute('update filemap set path = replace(path, :spath, :dpath) where uid = :uid and path like :path', array(
                ':spath' => $info['path'],
                ':dpath' => $dpath . $dmapId . '/',
                ':uid'   => $uid,
                ':path'  => $info['path'] . $smapId  . '/' . '%'
            ));
        }
        if ($cover) {
            $sid = $mysql->fetchColumn('select id from filemap where pid = :pid and id != :mapId and name = :name and isdir = :isdir', array(
                ':pid' => $dmapId,
                ':mapId' => $smapId,
                ':name' => $info['name'],
                ':isdir' => $info['isdir']
            ));
            if (!$info['isdir']) {
                if ($sid) {
                    $mysql->execute('delete from filemap where id = :id', array(
                        ':id' => $sid
                    ));
                }
            } elseif ($sid) {
                $mysql->execute('delete from filemap where id = :id or pid = :pid', array(
                    ':id' => $sid,
                    ':pid' => $sid
                ));
            }
            if ($mysql->getRowCount()) {
                $uinfo = $mysql->fetchRow('select sum(size) size, count(*) num from filemap where uid = :uid and isdir = 0', array(':uid' => $uid));
                $mysql->execute('update users set fileNum = :fileNum, size = :size where uid = :uid', array(
                    ':uid' => $uid,
                    ':fileNum' => $uinfo['num'],
                    ':size' => $uinfo['size']
                ));
            }
        } elseif ($exist) {
            $mysql->execute('update filemap set name = :name where id = :id', array(':id' => $smapId, ':name' => $info['name']));
        }
        return 1;
    }

    public function duplicate($uid, $smapId, $dmapId, $dpath, $cover = 0) {
        $mysql = Mysql::getInstance();
        $info = $mysql->fetchRow('select * from filemap where id = :id', array(':id' => $smapId));
        if (!$info['id']) {
            return 0;
        }
        $mysql->execute('insert into filemap (uid, name, pid, path, location, isdir, size, mime, origin, time)
                         values (:uid, :name, :pid, :path, :location, :isdir, :size, :mime, :origin, :time)', array(
            ':uid' => $info['uid'],
            ':name' => $info['name'],
            ':pid' => $dmapId,
            ':path' => $dpath,
            ':location' => $info['location'],
            ':isdir' => $info['isdir'],
            ':size' => $info['size'],
            ':mime' => $info['mime'],
            ':origin' => $info['origin'],
            ':time' => $info['time']
        ));
        $id = $mysql->lastInsertid();
        if (!$id) {
            return 0;
        }
        if (!$cover) {
            while ($mysql->fetchColumn('select id from filemap where pid = :pid and id != :mapId and name = :name and isdir = :isdir', array(
                ':pid' => $dmapId,
                ':mapId' => $smapId,
                ':name' => $info['name'],
                ':isdir' => $info['isdir'],
            ))) {
                $exist = true;
                $info['name'] = $this->reName($info['name']);
            }
        }
        if ($info['isdir']) {
            $sids = $mysql->fetchAll('select * from filemap where uid = :uid and path like :path', array(
                ':uid' => $uid,
                ':path' => $info['path'] . $smapId . '/' . '%'
            ));
            if ($sids) {
                $farray = array();
                foreach ($sids as $s) {
                    $search = array($info['path'] . $smapId . '/', '/' . $s['pid'] . '/');
                    $replace = array($dpath . $id . '/', '/' . (int)$farray[$s['id']] . '/');
                    $path = str_replace($search, $replace, $s['path']);
                    $pathArray = explode('/', trim($path, '/'));
                    $pathNum = count($pathArray);
                    $mysql->execute('insert into filemap (uid, name, pid, path, location, isdir, size, mime, origin, time)
                                     values (:uid, :name, :pid, :path, :location, :isdir, :size, :mime, :origin, :time)', array(
                        ':uid' => $s['uid'],
                        ':name' => $s['name'],
                        ':pid' => $pathArray[$pathNum - 1],
                        ':path' => $path,
                        ':location' => $s['location'],
                        ':isdir' => $s['isdir'],
                        ':size' => $s['size'],
                        ':mime' => $s['mime'],
                        ':origin' => $s['origin'],
                        ':time' => $s['time']
                    ));
                    $cid = $mysql->lastInsertid();
                    if ($cid) {
                        $farray[$s['id']] = $cid;
                    }
                }
            }
        }
        unset($farray, $pathArray, $search, $replace);
        if ($cover) {
            $sid = $mysql->fetchColumn('select id from filemap where pid = :pid and id != :mapId and name = :name and isdir = :isdir', array(
                ':pid' => $dmapId,
                ':mapId' => $smapId,
                ':name' => $info['name'],
                ':isdir' => $info['isdir']
            ));
            if (!$info['isdir']) {
                if ($sid) {
                    $mysql->execute('delete from filemap where id = :id', array(
                        ':id' => $sid
                    ));
                }
            } elseif ($sid) {
                $mysql->execute('delete from filemap where id = :id or pid = :id', array(
                    ':id' => $sid
                ));
            }
        } elseif ($exist) {
            $mysql->execute('update filemap set name = :name where id = :id', array(':id' => $id, ':name' => $info['name']));
            if (!$mysql->getRowCount()) {
                return 0;
            }
        }
        if ($mysql->getRowCount()) {
            $uinfo = $mysql->fetchRow('select sum(size) size, count(*) num from filemap where uid = :uid and isdir = 0', array(':uid' => $uid));
            $mysql->execute('update users set fileNum = :fileNum, size = :size where uid = :uid', array(
                ':uid' => $uid,
                ':fileNum' => $uinfo['num'],
                ':size' => $uinfo['size']
            ));
        }
        return $mysql->getRowCount() ? 1 : 0;
    }

    public function getTree($uid) {
        $sql = 'select id, pid, path, name from filemap where uid = :uid and isdir = 1';
        return Mysql::getInstance('slave')->fetchAll($sql, array(':uid' => $uid));
    }

    public function pwd($mapId, $pwd) {
        $sql = 'select pwd, shareTime from share where mapId = :mapId';
        $res = Mysql::getInstance('slave')->fetchRow($sql, array(':mapId' => $mapId));
        if ($res['pwd'] && $res['pwd'] == $pwd) {
            return $res;
        } else {
            return false;
        }
    }

    public function getOfferList($curPage, $perPage, $name = '', $order = '', $by = 'desc') {
        $bind = array();
        if (in_array($order, array('status', 'time'))) {
            $mix = ' order by f.' . $order . ' ' . $by;
        } else {
            $mix = ' order by f.isdir desc, f.status desc ';
        }
        if (trim($name) != '') {
            $like = ' and f.name like :name';
            $bind[':name'] = '%' . $name . '%';
        }
        $sql = 'select f.id, f.uid, f.name, f.isdir, f.size, f.time, f.type, s.id as sid, s.pwd
                  from filemap f, share s
              where f.id = s.mapId ' . $like . ' and f.status > 0 and s.type = 1 and (s.overTime >= now() or s.overTime = "0000-00-00 00:00:00") ' . $mix
            . ' limit ' . $perPage . ' offset ' . ($curPage - 1) * $perPage;
        return Mysql::getInstance('slave')->fetchAll($sql, $bind);
    }

    public function getOfferNum($name = '') {
        $bind = array();
        if (trim($name) != '') {
            $like = ' and f.name like :name';
            $bind[':name'] = '%' . $name . '%';
        }
        $sql = 'select count(*)
                  from filemap f, share s
              where f.id = s.mapId ' . $like . ' and f.status > 0 and s.type = 1 and (s.overTime >= now() or s.overTime = "0000-00-00 00:00:00") ';
        return Mysql::getInstance('slave')->fetchColumn($sql, $bind);
    }
}
?>