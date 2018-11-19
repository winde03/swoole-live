<?php
//协程非常适合编写
//高并发服务，如秒杀系统、高性能API接口、RPC服务器，使用协程模式，服务的容错率会大大增加，某些接口出现故障时，不会导致整个服务崩溃
//爬虫，可实现非常巨大的并发能力，即使是非常慢速的网络环境，也可以高效地利用带宽
//即时通信服务，如IM聊天、游戏服务器、物联网、消息服务器等等，可以确保消息通信完全无阻塞，每个消息包均可即时地被处理
$http = new swoole_http_server('0.0.0.0', 8001);

$http->on('request', function($request, $response) {
    // 获取redis 里面 的key的内容， 然后输出浏览器

    $redis = new Swoole\Coroutine\Redis();
    $redis->connect('127.0.0.1', 6379);
    $value = $redis->get($request->get['a']);

    $response->header("Content-Type", "text/plain");
    $response->end($value); //向客户端浏览器发送HTML内容
});

$http->start();
