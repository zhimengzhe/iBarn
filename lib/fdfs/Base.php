<?php
/**
 * PHP-FastDFS-Client (FOR FastDFS v4.0.6)
 *
 * 用PHP Socket实现的FastDFS客户端
 *
 * @author: $Author: QPWOEIRU96
 * @version: $Rev: 521 $
 * @date: $Date: 2014-01-17 09:54:45 +0800 (周五, 17 一月 2014) $
 */

namespace FastDFS;

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