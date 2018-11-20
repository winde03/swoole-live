<?php
//建立于TCP协议
//性能开销小
//客户端可以与任意服务器通信
//持久化网络通信协议
$server = new swoole_websocket_server("0.0.0.0", 8812);

$server->set(
    [
        'enable_static_handler' => true,
        'document_root' => "/var/www/html/swoole-live/demo/server/data",
    ]
);
//监听websocket连接打开事件
$server->on('open', 'onOpen');
function onOpen($server, $request)
{
    print_r($request->fd);
}

// 监听ws消息事件
$server->on('message', function (swoole_websocket_server $server, $frame) {
    echo "receive from {$frame->fd}:{$frame->data},opcode:{$frame->opcode},finish:{$frame->finish}\n";
//$frame->fd，客户端的socket id，使用$server->push推送数据时需要用到
//$frame->data，数据内容，可以是文本内容也可以是二进制数据，可以通过opcode的值来判断
//$frame->opcode，WebSocket的OpCode类型，可以参考WebSocket协议标准文档
//$frame->finish， 表示数据帧是否完整，一个WebSocket请求可能会分成多个数据帧进行发送（底层已经实现了自动合并数据帧，现在不用担心接收到的数据帧不完整）
    $server->push($frame->fd, "message-push-sucesss");// 推送给客户端。 html->js->websocket.onmessage  接收
});

$server->on('close', function ($ser, $fd) {
    echo "client {$fd} closed\n";
});

$server->start();