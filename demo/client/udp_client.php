<?php
/**
 * Created by PhpStorm.
 * User: 18484
 * Date: 2018/11/10
 * Time: 10:58
 */

$client = new swoole_client(SWOOLE_SOCK_UDP);

if(!$client->connect("127.0.0.1", 9501)) {
    echo "连接失败";
    exit();
}

/// php cli常量
fwrite(STDOUT, "输入消息：");
$msg = trim(fgets(STDIN));

// 发送消息给 tcp server服务器
$client->sendto('127.0.0.1', 9502, $msg);

// 接受来自server 的数据
$result = $client->recv();
echo $result;