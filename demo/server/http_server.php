<?php

$http = new swoole_http_server("0.0.0.0", 8811);

$http->set(
    [
        'enable_static_handler' => true,
        'document_root' => "/var/www/html/swoole-live/demo/server/data",
    ]
);//加载静态资源，如果有资源直接将其呈现，不走下面的流程 http://ltfnevergiveup.cn:8811/index.html
$http->on('request', function ($request, $response) {
    //print_r($request->get);  终端展示
    $content = [
        'date:' => date("Ymd H:i:s"),
        'get:' => $request->get,
        'post:' => $request->post,
        'header:' => $request->header
    ];

    swoole_async_writefile(__DIR__ . "/access.log", json_encode($content) . PHP_EOL, function ($filename) {
        // todo 文件写入回调函数
    }, FILE_APPEND);
    $response->cookie("cook", "xsssss", time() + 1800);
    $response->end("http server: " . json_encode($request->cookie['cook']) . json_encode($request->get)); //end: 浏览器中展示
});

$http->start();