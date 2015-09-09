<?php
/**
 * 从数组生成树形结构
 * 可指定自身主键key,父级关联key，层级key,更加灵活。
 */
class tree {
    public $key;//主键id
    public $pkey;//父级id
    public $lvkey;//系统自动添加的，代表层级的数组key
    public $list;//原始数组，二维
    public $tree;//树形数组

    function __construct($list,$key='id',$pkey='pid',$lvkey='lv'){
        $this->list=!empty($list)?$list:array();
        $this->key=$key;
        $this->pkey=$pkey;
        $this->lvkey=$lvkey;
        $this->tree=array();
        $this->getTree();
    }

    /**
     * 将原始数组按树型结构排序
     * @staticvar int $lv 这是一个用于记录当前单元所处层级的静态变量，顶层单元从1开始
     * @param type $pid 指定从哪个父级id开始
     * @return bool
     */
    function getTree($pid=0){
        if(empty($this->list)){
            return "";
        }
        static $lv=0;
        $lv++;
        foreach($this->list as $v){
            if(!isset($v[$this->key]) || !isset($v[$this->pkey])){
                continue;
            }
            if($v[$this->pkey]==$pid){
                $v[$this->lvkey]=$lv;
                $this->tree[$v[$this->key]]=$v;
                $this->getTree($v[$this->key]);
            }
        }
        $lv--;
        return true;
    }
}