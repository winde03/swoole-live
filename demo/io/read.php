<?php

/**
 * swoole_async_readfile
 * filesize < 4M    if filesize > 4M -> swoole_async_read
 */
$result = Swoole\Async::readfile(__DIR__ . "/1.log", function ($filename, $fileContent) {
    echo "filename:" . $filename . PHP_EOL;
    echo "content:" . $fileContent . PHP_EOL;
});

var_dump($result);
echo "start" . PHP_EOL;



/*
 * 先读取文件，然后往下进行var_dump()、echo , 最后在执行回调函数function ($filename, $fileContent){}
 * */