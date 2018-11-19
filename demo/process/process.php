<?php

$process = new swoole_process(function (swoole_process $pro) {
    // todo
    // php redis.php
    $pro->exec("/usr/bin/php", [__DIR__ . '/../server/http_server.php']);
}, false);

$pid = $process->start(); // 子进程id
echo $pid . PHP_EOL;

swoole_process::wait(); //回收结束运行的子进程
