<?php

namespace app\index\controller;

use app\common\lib\Util;

class Chart
{
    public function index()
    {
        $gameId = $_POST['game_id'];
        $content = $_POST['content'];
        $data = [
            'user' => '用户' . rand(0, 2000),
            'content' => $content
        ];

//        获取所有连接用户id并推送消息
//        和pushLive()方法作用一样
        foreach ($_POST['http_server']->ports[1]->connections as $fd) {
            $_POST['http_server']->push($fd, json_encode($data));
        }

        return Util::show(config('code.success'), 'ok', $data);
    }
}
