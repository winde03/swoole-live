<?php

echo "process-start-time:" . date("Ymd H:i:s");
$workers = [];
$urls = [
    'http://baidu.com',
    'http://sina.com.cn',
    'http://qq.com',
    'http://baidu.com?search=github',
    'http://baidu.com?search=gitlab',
    'http://baidu.com?search=git',
];

for ($i = 0; $i < 6; $i++) {
    // 子进程
    $process = new swoole_process(function (swoole_process $worker) use ($i, $urls) {
        // curl
        $content = curlData($urls[$i]);
        //echo $content.PHP_EOL;
        $worker->write($content . PHP_EOL);
    }, true);
    $pid = $process->start();
    $workers[$pid] = $process;
}

foreach ($workers as $process) {
    echo $process->read(); // 从管道中读取数据, 此处均为空
}
/**
 * 模拟请求URL的内容  1s
 * @param $url
 * @return string
 */
function curlData($url)
{
    // curl file_get_contents
    sleep(1);
    return $url . " success" . PHP_EOL;
}

echo "process-end-time:" . date("Ymd H:i:s");