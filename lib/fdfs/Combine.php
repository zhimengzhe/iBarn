<?php
$content = '';
foreach(array(
    'Base.php',
    'Exception.php',
    'Tracker.php',
    'Storage.php',
) as $val) {
    $file = __DIR__ . '/' . $val;
    $content .= file_get_contents($file);
}
$content = str_replace(array('<?php', 'namespace FastDFS;'), '', $content);

//$content = preg_replace('#/\*[\s\S]+?\*/#im', '', $content);
//$content = preg_replace('# +//.*$#im', '', $content);
//$content = preg_replace('#^\s+$#m', '', $content);
//$content = preg_replace('#[\r\n]{2,}#i', "\n", $content);
$content = "<?php \nnamespace FastDFS;\n" . $content;
file_put_contents(__DIR__ . '/FastDFS.php', $content);

