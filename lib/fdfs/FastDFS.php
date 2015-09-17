<?php 
namespace FastDFS;

/**
 * PHP-FastDFS-Client (FOR FastDFS v4.0.6)
 *
 * 用PHP Socket实现的FastDFS客户端
 *
 * @author: $Author: QPWOEIRU96
 * @version: $Rev: 525 $
 * @date: $Date: 2014-01-17 15:02:03 +0800 (周五, 17 一月 2014) $
 */



abstract class Base {

    /**
     * FastDFS数据包大小
     */
    const PROTO_PKG_LEN_SIZE = 8;
     
     /**
     * FastDFS头部大小
     */
    const HEADER_LENGTH      = 10;
     
     /**
     * FastDFS IP地址长度
     */
    const IP_ADDRESS_SIZE       = 16;
    const FILE_EXT_NAME_MAX_LEN = 6;
    const GROUP_NAME_MAX_LEN    = 16;
    const OVERWRITE_METADATA    = 1;
    const MERGE_METADATA        = 2;
     // 连接超时时间
    const CONNECT_TIME_OUT      = 5;
    const FILE_PREFIX_MAX_LEN   = 16;
     //传输超时时间
    const TRANSFER_TIME_OUT     = 5;

    //socket套接字
    protected $_sock;

    //连接主机地址
    protected $_host;

    //连接端口
    protected $_port;

    //错误代码
    protected $_errno;

    //错误信息
    protected $_errstr;

    public function __construct($host, $port) {

        $this->_host = $host;
        $this->_port = $port;
        $this->_sock = $this->_connect();
        
    }

    private function _connect() {

        $sock = @fsockopen(
            $this->_host,
            $this->_port,
            $this->_errno, 
            $this->_errstr,
            self::CONNECT_TIME_OUT
        );

        if( !is_resource($sock) )
            throw new Exception($this->_errstr, $this->_errno);

        return $sock;

    }

    protected function send($data, $length = 0) {

        if(!$length) $length = strlen($data);

        if ( feof($this->_sock) 
            || fwrite( $this->_sock, $data, $length ) !== $length ) {

            $this->_error = 'connection unexpectedly closed (timed out?)';
            $this->_errno = 0;
            throw new Exception($this->_errstr, $this->_errno);
        }

        return TRUE;
    }

    protected function read($length) {

        if( feof($this->_sock) ) {

            $this->_error = 'connection unexpectedly closed (timed out?)';
            $this->_errno = 0;
            throw new Exception($this->_errstr, $this->_errno);
        }

        $data = stream_get_contents($this->_sock, $length);

        assert( $length === strlen($data) );

        return $data;

    }

    final static function padding($str, $len) {

        $str_len = strlen($str);

        return $str_len > $len
            ? substr($str, 0, $len)
            : $str . pack('x'. ($len - $str_len));
    }

    final static function buildHeader($command, $length = 0) {

        return self::packU64($length) . pack('Cx', $command);

    }

    final static function buildMetaData($data) {
           
        $list = array();
        foreach($data as $key => $val) {
            $list[] = $key . "\x02" . $val;
        };

        return implode("\x01", $list);
    }

    final static function parseMetaData($data) {

        $arr    = explode("\x01", $data);
        $result = array();

        foreach($arr as $val) {
            list($k, $v) = explode("\x02", $val);
            $result[$k] = $v;
        }

        return $result;

    }    

    final static function parseHeader($str) {

        assert(strlen($str) === self::HEADER_LENGTH);


        $result = unpack('C10', $str);

        $length  = self::unpackU64(substr($str, 0, 8));
        $command = $result[9];
        $status  = $result[10];

        return array(
            'length'  => $length,
            'command' => $command,
            'status'  => $status
        );

    }

    /**
     * From: sphinxapi.php
     */
    final static function unpackU64($v) {
        list ( $hi, $lo ) = array_values ( unpack ( "N*N*", $v ) );

        if ( PHP_INT_SIZE>=8 )
        {
            if ( $hi<0 ) $hi += (1<<32); // because php 5.2.2 to 5.2.5 is totally fucked up again
            if ( $lo<0 ) $lo += (1<<32);

            // x64, int
            if ( $hi<=2147483647 )
                return ($hi<<32) + $lo;

            // x64, bcmath
            if ( function_exists("bcmul") )
                return bcadd ( $lo, bcmul ( $hi, "4294967296" ) );

            // x64, no-bcmath
            $C = 100000;
            $h = ((int)($hi / $C) << 32) + (int)($lo / $C);
            $l = (($hi % $C) << 32) + ($lo % $C);
            if ( $l>$C )
            {
                $h += (int)($l / $C);
                $l  = $l % $C;
            }

            if ( $h==0 )
                return $l;
            return sprintf ( "%d%05d", $h, $l );
        }

        // x32, int
        if ( $hi==0 )
        {
            if ( $lo>0 )
                return $lo;
            return sprintf ( "%u", $lo );
        }

        $hi = sprintf ( "%u", $hi );
        $lo = sprintf ( "%u", $lo );

        // x32, bcmath
        if ( function_exists("bcmul") )
            return bcadd ( $lo, bcmul ( $hi, "4294967296" ) );
        
        // x32, no-bcmath
        $hi = (float)$hi;
        $lo = (float)$lo;
        
        $q = floor($hi/10000000.0);
        $r = $hi - $q*10000000.0;
        $m = $lo + $r*4967296.0;
        $mq = floor($m/10000000.0);
        $l = $m - $mq*10000000.0;
        $h = $q*4294967296.0 + $r*429.0 + $mq;

        $h = sprintf ( "%.0f", $h );
        $l = sprintf ( "%07.0f", $l );
        if ( $h=="0" )
            return sprintf( "%.0f", (float)$l );
        return $h . $l;
    }

    /**
     * From: sphinxapi.php
     */
    final static function packU64 ($v) {

       
        assert ( is_numeric($v) );
        
        // x64
        if ( PHP_INT_SIZE>=8 )
        {
            assert ( $v>=0 );
            
            // x64, int
            if ( is_int($v) )
                return pack ( "NN", $v>>32, $v&0xFFFFFFFF );
                              
            // x64, bcmath
            if ( function_exists("bcmul") )
            {
                $h = bcdiv ( $v, 4294967296, 0 );
                $l = bcmod ( $v, 4294967296 );
                return pack ( "NN", $h, $l );
            }
            
            // x64, no-bcmath
            $p = max ( 0, strlen($v) - 13 );
            $lo = (int)substr ( $v, $p );
            $hi = (int)substr ( $v, 0, $p );
        
            $m = $lo + $hi*1316134912;
            $l = $m % 4294967296;
            $h = $hi*2328 + (int)($m/4294967296);

            return pack ( "NN", $h, $l );
        }

        // x32, int
        if ( is_int($v) )
            return pack ( "NN", 0, $v );
        
        // x32, bcmath
        if ( function_exists("bcmul") )
        {
            $h = bcdiv ( $v, "4294967296", 0 );
            $l = bcmod ( $v, "4294967296" );
            return pack ( "NN", (float)$h, (float)$l ); // conversion to float is intentional; int would lose 31st bit
        }

        // x32, no-bcmath
        $p = max(0, strlen($v) - 13);
        $lo = (float)substr($v, $p);
        $hi = (float)substr($v, 0, $p);
        
        $m = $lo + $hi*1316134912.0;
        $q = floor($m / 4294967296.0);
        $l = $m - ($q * 4294967296.0);
        $h = $hi*2328.0 + $q;

        return pack ( "NN", $h, $l );
    }

    public function __destruct() {

        if(is_resource($this->_sock))
            fclose($this->_sock);
    }
}
/**
 * PHP-FastDFS-Client (FOR FastDFS v4.0.6)
 *
 * 用PHP Socket实现的FastDFS客户端
 *
 * @author: $Author: QPWOEIRU96
 * @version: $Rev: 525 $
 * @date: $Date: 2014-01-17 15:02:03 +0800 (周五, 17 一月 2014) $
 */



/**
 * 异常类
 */
class Exception extends \Exception {

    /**
     * 构造器
     *
     * @param string = 
     */
    public function __construct($message = '', $code = 0) {}
}
/**
 * PHP-FastDFS-Client (FOR FastDFS v4.0.6)
 *
 * 用PHP Socket实现的FastDFS客户端
 *
 * @author: $Author: QPWOEIRU96
 * @version: $Rev: 525 $
 * @date: $Date: 2014-01-17 15:02:03 +0800 (周五, 17 一月 2014) $
 */




class Tracker extends Base{

    /**
     * 根据GroupName申请Storage地址
     *
     * @command 104
     * @param string $group_name 组名称
     * @return array/boolean
     */
    public function applyStorage($group_name) {

        $req_header = self::buildHeader(104, Base::GROUP_NAME_MAX_LEN);
        $req_body   = self::padding($group_name, Base::GROUP_NAME_MAX_LEN);

        $this->send($req_header . $req_body);

        $res_header = $this->read(Base::HEADER_LENGTH);        
        $res_info   = self::parseHeader($res_header);

        if($res_info['status'] !== 0) {

            throw new FastDFSException(
                'something wrong with get storage by group name', 
                $res_info['status']);

            return FALSE;
        }

        $res_body = !!$res_info['length'] 
            ? $this->read($res_info['length'])
            : '';

        $group_name   = trim(substr($res_body, 0, Base::GROUP_NAME_MAX_LEN));
        $storage_addr = trim(substr($res_body, Base::GROUP_NAME_MAX_LEN, 
            Base::IP_ADDRESS_SIZE - 1));

        list(,,$storage_port)  = unpack('N2', substr($res_body, 
            Base::GROUP_NAME_MAX_LEN + Base::IP_ADDRESS_SIZE - 1, 
            Base::PROTO_PKG_LEN_SIZE));

        $storage_index  = ord(substr($res_body, -1));


        return array(
            'group_name'    => $group_name,
            'storage_addr'  => $storage_addr,
            'storage_port'  => $storage_port,
            'storage_index' => $storage_index
        );


    }

}




/**
 * PHP-FastDFS-Client (FOR FastDFS v4.0.6)
 *
 * 用PHP Socket实现的FastDFS客户端
 *
 * @author: $Author: QPWOEIRU96
 * @version: $Rev: 525 $
 * @date: $Date: 2014-01-17 15:02:03 +0800 (周五, 17 一月 2014) $
 */



class Storage extends Base {

    /**
     * 上传文件
     *
     * @command 11
     * @param string $index 索引
     * @param string $filename
     * @param string $ext
     * @throws Exception
     * @internal param string $文件扩展名
     * @return array
     */
    public function uploadFile($index, $filename, $ext = '') {


        if(!file_exists($filename))
            return FALSE;

        $pathInfo = pathinfo($filename);

        if(strlen($ext) > Base::FILE_EXT_NAME_MAX_LEN)
            throw new Exception('file ext too long.', 0);

        if($ext === '') {
            $ext = $pathInfo['extension'];
        }

        $fp = fopen($filename, 'rb');
        flock($fp, LOCK_SH);

        $fileSize = filesize($filename);

        $requestBodyLength = 1 + Base::PROTO_PKG_LEN_SIZE + 
            Base::FILE_EXT_NAME_MAX_LEN + $fileSize;

        $requestHeader = self::buildHeader(11, $requestBodyLength);
        $requestBody   = pack('C', $index) . self::packU64($fileSize) . self::padding($ext, Base::FILE_EXT_NAME_MAX_LEN);
        $this->send($requestHeader . $requestBody);

        stream_copy_to_stream($fp, $this->_sock, $fileSize);

        flock($fp, LOCK_UN);
        fclose($fp);

        $responseHeader = $this->read(Base::HEADER_LENGTH);
        $responseInfo   = self::parseHeader($responseHeader);

        if($responseInfo['status'] !== 0)

            throw new Exception(
                'something wrong with uplode file', 
                $responseInfo['status']);


        $responseBody  = $responseInfo['length'] 
            ? $this->read($responseInfo['length']) 
            : FALSE;

        $groupName = trim(substr($responseBody, 0, Base::GROUP_NAME_MAX_LEN));
        $filePath  = trim(substr($responseBody, Base::GROUP_NAME_MAX_LEN));

        return array(
            'group' => $groupName,
            'path'  => $filePath
        );

    }

    /**
     * 上传Slave文件
     *
     * @command 21
     * @param string $filename 待上传的文件名称
     * @param string $masterFilePath 主文件名称
     * @param string $prefix_name 后缀的前缀名
     * @param string $ext 后缀名称
     * @throws Exception
     * @return array/boolean
     */
    public function uploadSlaveFile($filename, $masterFilePath, $prefix_name, $ext = '') {

        if(!file_exists($filename))
            return FALSE;

        $pathInfo = pathinfo($filename);

        if(strlen($ext) > Base::FILE_EXT_NAME_MAX_LEN)
            throw new Exception('file ext too long.', 0);

        if($ext === '') {
            $ext = $pathInfo['extension'];
        }

        $fp = fopen($filename, 'rb');
        flock($fp, LOCK_SH);

        $fileSize = filesize($filename);
        $masterFilePathLen = strlen($masterFilePath);

        $requestBodyLength = 16 + Base::FILE_PREFIX_MAX_LEN + 
            Base::FILE_EXT_NAME_MAX_LEN + $masterFilePathLen + $fileSize;

        $requestHeader = self::buildHeader(21, $requestBodyLength);
        $requestBody   = pack('x4N', $masterFilePathLen) . self::packU64($fileSize) . self::padding($prefix_name, Base::FILE_PREFIX_MAX_LEN);
        $requestBody  .= self::padding($ext, Base::FILE_EXT_NAME_MAX_LEN) . $masterFilePath;

        $this->send($requestHeader . $requestBody);

        stream_copy_to_stream($fp, $this->_sock, $fileSize);

        flock($fp, LOCK_UN);
        fclose($fp);

        $responseHeader = $this->read(Base::HEADER_LENGTH);
        $responseInfo   = self::parseHeader($responseHeader);

        if($responseInfo['status'] !== 0) {

            if($responseInfo['status'] == 17) {
                $msg = 'target slave file already existd';
            } else {
                $msg = 'something in upload slave file';
            }

            throw new Exception(
                $msg, 
                $responseInfo['status']);
        }

        $responseBody  = $responseInfo['length'] 
            ? $this->read($responseInfo['length']) 
            : FALSE;

        $groupName = trim(substr($responseBody, 0, Base::GROUP_NAME_MAX_LEN));
        $filePath  = trim(substr($responseBody, Base::GROUP_NAME_MAX_LEN));

        return array(
            "group" => $groupName,
            'path'  => $filePath
        );

    }

    /**
     * 
     * @command 23 
     */
    public function uploadAppenderFile($index, $filename, $ext = '') {

        if(!file_exists($filename))
            return FALSE;

        $pathInfo = pathinfo($filename);

        if(strlen($ext) > Base::FILE_EXT_NAME_MAX_LEN)
            throw new Exception('file ext too long.', 0);

        if($ext === '') {
            $ext = $pathInfo['extension'];
        }

        $fp = fopen($filename, 'rb');
        flock($fp, LOCK_SH);

        $fileSize = filesize($filename);

        $requestBodyLength = 1 + Base::PROTO_PKG_LEN_SIZE + 
            Base::FILE_EXT_NAME_MAX_LEN + $fileSize;

        $requestHeader = self::buildHeader(23, $requestBodyLength);
        $requestBody   = pack('C', $index) . self::packU64($fileSize) . self::padding($ext, Base::FILE_EXT_NAME_MAX_LEN);
        $this->send($requestHeader . $requestBody);

        stream_copy_to_stream($fp, $this->_sock, $fileSize);

        flock($fp, LOCK_UN);
        fclose($fp);

        $responseHeader = $this->read(Base::HEADER_LENGTH);
        $responseInfo   = self::parseHeader($responseHeader);

        if($responseInfo['status'] !== 0) {

            throw new Exception(
                'something wrong with uplode file', 
                $responseInfo['status']);

            return FALSE;
        }

        $responseBody  = $responseInfo['length'] 
            ? $this->read($responseInfo['length']) 
            : FALSE;

        $groupName = trim(substr($responseBody, 0, Base::GROUP_NAME_MAX_LEN));
        $filePath  = trim(substr($responseBody, Base::GROUP_NAME_MAX_LEN));

        return array(
            "group" => $groupName,
            'path'  => $filePath
        );
    }

    /**
     * @command 24
     */
    public function appendFile($content, $appenderFilePath) {

        $appenderFilePathLength = strlen($appenderFilePath);
        $content_length            = strlen($content);
        $requestBodyLength                =  (2 *  Base::PROTO_PKG_LEN_SIZE) + $appenderFilePathLength + $content_length;

        $requestHeader = self::buildHeader(24, $requestBodyLength);
        $requestBody = pack('x4N', $appenderFilePathLength) . self::packU64($content_length) . $appenderFilePath . $content;
        $this->send($requestHeader . $requestBody);

        $responseHeader = $this->read(Base::HEADER_LENGTH);
        $responseInfo   = self::parseHeader($responseHeader);

        
        return !$responseInfo['status'];

    }

    /**
     * @command 34
     */
    public function modifyFile($filename, $filePath, $offset = 0) {

        if(!file_exists($filename))
            return FALSE;

        $filePathLength  = strlen($filePath);
        $fileSize        = filesize($filename);
        $requestBodyLength       =  (3 *  Base::PROTO_PKG_LEN_SIZE) + $filePathLength + $fileSize;

        $requestHeader = self::buildHeader(34, $requestBodyLength);
        $requestBody   = pack('x4N', $filePathLength) . self::packU64($offset) . self::packU64($fileSize) . $filePath;

        $this->send($requestHeader . $requestBody);

        $fp = fopen($filename, 'rb');
        flock($fp, LOCK_SH);

        stream_copy_to_stream($fp, $this->_sock, $fileSize);

        flock($fp, LOCK_UN);
        fclose($fp);

        $responseHeader = $this->read(Base::HEADER_LENGTH);
        $responseInfo   = self::parseHeader($responseHeader);

        return !$responseInfo['status'];

    }

    /**
     * 删除文件
     *
     * @command 12
     * @param string $groupName 组名称
     * @param string $filePath 文件路径 
     * @return boolean 删除成功与否
     */
    public function deleteFile($groupName, $filePath) {

        $requestBodyLength = strlen($filePath) + Base::GROUP_NAME_MAX_LEN;
        $requestHeader      = self::buildHeader(12, $requestBodyLength);        
        $requestBody        = self::padding($groupName, Base::GROUP_NAME_MAX_LEN) . $filePath;

        $this->send($requestHeader . $requestBody);

        $responseHeader = $this->read(Base::HEADER_LENGTH);
        $responseInfo   = self::parseHeader($responseHeader);

        return !$responseInfo['status'];

    }

    /**
     * 获取文件元信息
     *
     * @command 15
     * @param string $groupName 组名称
     * @param string $filePath 文件路径 
     * @return array 元信息数组
     */
    public function getFileMetaData($groupName, $filePath) {

        $requestBodyLength = strlen($filePath) + Base::GROUP_NAME_MAX_LEN;
        $requestHeader      = self::buildHeader(15, $requestBodyLength);        
        $requestBody        = self::padding($groupName, Base::GROUP_NAME_MAX_LEN) . $filePath;

        $this->send($requestHeader . $requestBody);

        $responseHeader = $this->read(Base::HEADER_LENGTH);
        $responseInfo   = self::parseHeader($responseHeader);

        if(!!$responseInfo['status'])
            return FALSE;

        $responseBody  = $responseInfo['length'] 
            ? $this->read($responseInfo['length']) 
            : FALSE;

        return self::parseMetaData($responseBody);

    }

    /**
     * 设置文件元信息
     *
     * @command 13
     * @param string $groupName 组名称
     * @param string $filePath 文件路径
     * @param array $meta_data 元信息数组
     * @param $flag
     * @return boolean 设置成功与否
     */
    public function setFileMetaData($groupName, $filePath, array $meta_data, $flag =  Base::OVERWRITE_METADATA) {

        $meta_data        = self::buildMetaData($meta_data);
        $meta_data_length = strlen($meta_data);
        $filePathLength = strlen($filePath);
        $flag = $flag === Base::OVERWRITE_METADATA ? 'O' : 'M';

        $requestBodyLength = (Base::PROTO_PKG_LEN_SIZE * 2) + 1 + $meta_data_length + $filePathLength + Base::GROUP_NAME_MAX_LEN;

        $requestHeader      = self::buildHeader(13, $requestBodyLength);

        $requestBody =  self::packU64($filePathLength) . self::packU64($meta_data_length);
        $requestBody .= $flag . self::padding($groupName, Base::GROUP_NAME_MAX_LEN) . $filePath . $meta_data;

        $this->send($requestHeader . $requestBody);

        $responseHeader = $this->read(Base::HEADER_LENGTH);
        $responseInfo   = self::parseHeader($responseHeader);

        return !$responseInfo['status'];

    }

    /**
     * 读取文件(不建议对大文件使用)
     *
     * @command 14
     * @param string $groupName 组名称
     * @param string $filePath 文件路径
     * @param int $offset 下载文件偏移量
     * @param int $length 下载文件大小
     * @return string 文件内容
     */
    public function readFile($groupName, $filePath, $offset = 0, $length = 0) {

        $filePathLength = strlen($filePath);
        $requestBodyLength  = (Base::PROTO_PKG_LEN_SIZE * 2) + $filePathLength + Base::GROUP_NAME_MAX_LEN;
        
        $requestHeader       = self::buildHeader(14, $requestBodyLength);

        $requestBody         = self::packU64($offset) . self::packU64($length) . self::padding($groupName, Base::GROUP_NAME_MAX_LEN);
        $requestBody        .= $filePath;

        $this->send($requestHeader . $requestBody);

        $responseHeader = $this->read(Base::HEADER_LENGTH);
        $responseInfo   = self::parseHeader($responseHeader);

        if(!!$responseInfo['status']) return FALSE;

        return $this->read($responseInfo['length']);
    }

    /**
     * 下载文件
     *
     * @param $groupName
     * @param $filePath
     * @param $targetPath
     * @param int $offset
     * @param int $length
     * @return bool
     */
    public function downloadFile($groupName, $filePath, $targetPath, $offset = 0, $length = 0)
    {
        $filePathLength      = strlen($filePath);
        $requestBodyLength   = (Base::PROTO_PKG_LEN_SIZE * 2) + $filePathLength + Base::GROUP_NAME_MAX_LEN;        
        $requestHeader       = self::buildHeader(14, $requestBodyLength);
        $requestBody         = self::packU64($offset) . self::packU64($length) . self::padding($groupName, Base::GROUP_NAME_MAX_LEN);
        $requestBody        .= $filePath;

        $this->send($requestHeader . $requestBody);

        $responseHeader = $this->read(Base::HEADER_LENGTH);
        $responseInfo   = self::parseHeader($responseHeader);

        if($responseInfo['status']) return FALSE;

        $fp = fopen($targetPath, 'w+b');
        stream_copy_to_stream($this->_sock, $fp, $responseInfo['length'], 0);
        fclose($fp);


        return true;
    }

    /**
     * 检索文件信息
     *
     * @command 22
     * @param string $groupName 组名称
     * @param string $filePath 文件路径
     * @return array
     */
    public function getFileInfo($groupName, $filePath) {

        $requestBodyLength = strlen($filePath) + Base::GROUP_NAME_MAX_LEN;
        $requestHeader      = self::buildHeader(22, $requestBodyLength);        
        $requestBody        = self::padding($groupName, Base::GROUP_NAME_MAX_LEN) . $filePath;

        $this->send($requestHeader . $requestBody);

        $responseHeader = $this->read(Base::HEADER_LENGTH);
        $responseInfo   = self::parseHeader($responseHeader);

        if(!!$responseInfo['status']) return FALSE;

        $responseBody  = $responseInfo['length'] 
            ? $this->read($responseInfo['length']) 
            : FALSE;

        $fileSize     = self::unpackU64(substr($responseBody, 0, Base::PROTO_PKG_LEN_SIZE));
        $timestamp     = self::unpackU64(substr($responseBody, Base::PROTO_PKG_LEN_SIZE, Base::PROTO_PKG_LEN_SIZE));
        list(,,$crc32) = unpack('N2', substr($responseBody, 2 * Base::PROTO_PKG_LEN_SIZE, Base::PROTO_PKG_LEN_SIZE));
        $crc32         = base_convert(sprintf('%u', $crc32), 10, 16);
        $storageId    = trim(substr($responseBody, -16));

        return array(
            'file_size'  => $fileSize,
            'timestamp'  => $timestamp,
            'crc32'      => $crc32,
            'storage_id' => $storageId
        );

    }

}