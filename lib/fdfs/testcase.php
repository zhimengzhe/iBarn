<?php
set_time_limit(10);

include(__DIR__ . '/Exception.php');
include(__DIR__ . '/Base.php');
include(__DIR__ . '/Tracker.php');
include(__DIR__ . '/Storage.php');

$time_start = microtime(TRUE);

$tracker_addr = '10.21.3.101';
$tracker_port = 806;

$tracker      = new FastDFS\Tracker($tracker_addr, $tracker_port);
$storage_info = $tracker->applyStorage('group1');

//var_dump($storage_info);

$group_name = 'group1';
$file_path = 'M00/00/00/ChUDZVX2blCAPsBOAAAKWhzQa64704.png';
//$file_path = 'M00/00/00/CgAABVFYZgmAQ_9nAKnrXobBHdI433.rar';
//$appender_file_path = 'M00/00/00/CgAABVFc8duEOo6HAAAAAD1cKVQ817.txt';

$storage = new FastDFS\Storage($storage_info['storage_addr'], $storage_info['storage_port']);

var_dump(
    //$storage->downloadFile('group1', 'M00/00/00/ChUDZVX2blCAPsBOAAAKWhzQa64704.png', dirname(__DIR__) . '/test.png')
    $storage->uploadFile($storage_info['storage_index'], dirname(__DIR__) . '/test.png')
    //$storage->getFileInfo($group_name, $file_path)
    //$storage->deleteFile($group_name, $file_path),
    //$storage->setFileMetaData($group_name, $file_path, array(
    //    'time' => time()
    //), 2),
    //$storage->uploadSlaveFile('I:\\FastDFS_v4.06\\FastDFS\\HISTORY', $file_path, 'randdom', 'txt'),
    //$storage->getFileInfo($group_name, $file_path)
    //$storage->getFileMetaData($group_name, $file_path)
    //$storage->downloadFile($group_name, $file_path)
    //$storage->uploadAppenderFile($storage_info['storage_index'], 'I:\\FastDFS_v4.06\\FastDFS\\HISTORY', 'txt')
    //$storage->appendFile('TEST' . time() . PHP_EOL, $appender_file_path)
    //$storage->modifyFile('I:\\FastDFS_v4.06\\FastDFS\\INSTALL', $appender_file_path, 0)
);

$time_end = microtime(TRUE);

printf("[内存最终使用: %.2fMB]\r\n", memory_get_usage() /1024 /1024 ); 
printf("[内存最高使用: %.2fMB]\r\n", memory_get_peak_usage()  /1024 /1024) ; 
printf("[页面执行时间: %.2f毫秒]\r\n", ($time_end - $time_start) * 1000 );