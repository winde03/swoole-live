<?php
//应用场景：日志
//如果文件已存在，底层会覆盖旧的文件内容
$content = date("Ymd H:i:s") . PHP_EOL; // $content < 4M
swoole_async_writefile(__DIR__ . "/1.log", $content, function ($filename) {
    // todo
    echo "success" . PHP_EOL;
}, FILE_APPEND);
// file_put_contents();
echo "start" . PHP_EOL;