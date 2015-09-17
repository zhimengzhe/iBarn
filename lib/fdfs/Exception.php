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