<?php
class FileDownload{

    private $_speed = 512;

    public function download($file, $name = '', $size = 0, $reload = false) {
        if ($name == '') {
            $name = basename($file);
        }

        $fp = fopen($file, 'rb');
        if (!$size) {
            $size = filesize($file);
        }
        $ranges = $this->getRange($size);

        if ($reload && $ranges != null) {
            header('HTTP/1.1 206 Partial Content');
            header('Accept-Ranges:bytes');
            header(sprintf('content-length:%u',$ranges['end']-$ranges['start']));
            header(sprintf('content-range:bytes %s-%s/%s', $ranges['start'], $ranges['end'], $size));
            fseek($fp, sprintf('%u', $ranges['start']));
        }else{
            header('HTTP/1.1 200 OK');
            header('content-length:'.$size);
        }
        while(!feof($fp)){
            echo fread($fp, round($this->_speed*1024,0));
            ob_flush();
            flush();
            //sleep(1);
        }
        ($fp!=null) && fclose($fp);
    }

    public function setSpeed($speed) {
        if (is_numeric($speed) && $speed>16 && $speed<4096) {
            $this->_speed = $speed;
        }
    }

    private function getRange($file_size) {
        if (isset($_SERVER['HTTP_RANGE']) && !empty($_SERVER['HTTP_RANGE'])) {
            $range = $_SERVER['HTTP_RANGE'];
            $range = preg_replace('/[\s|,].*/', '', $range);
            $range = explode('-', substr($range, 6));
            if(count($range)<2){
                $range[1] = $file_size;
            }
            $range = array_combine(array('start','end'), $range);
            if(empty($range['start'])){
                $range['start'] = 0;
            }
            if(empty($range['end'])){
                $range['end'] = $file_size;
            }
            return $range;
        }
        return null;
    }
}
?>