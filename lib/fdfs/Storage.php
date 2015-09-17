<?php
/**
 * PHP-FastDFS-Client (FOR FastDFS v4.0.6)
 *
 * 用PHP Socket实现的FastDFS客户端
 *
 * @author: $Author: QPWOEIRU96
 * @version: $Rev: 525 $
 * @date: $Date: 2014-01-17 15:02:03 +0800 (周五, 17 一月 2014) $
 */

namespace FastDFS;

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