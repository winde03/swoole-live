<?php

namespace app\admin\controller;

use app\common\lib\Util;
use app\common\lib\redis\Predis;

class Live
{
    public function push()
    {
        //process:赛况信息入库、组装数据、获取在线用户（https://wiki.swoole.com/wiki/page/427.html）、push到直播页面
        //  管理员端代码部署在不同服务器，并且只允许通过内网访问
        if (empty($_GET)) {
            return Util::show(config('code.error'), 'error');
        }  // admin
        // token    md5(content)
        // => mysql
        $teams = [
            1 => [
                'name' => '马刺',
                'logo' => '/live/imgs/team1.png',
            ],
            4 => [
                'name' => '火箭',
                'logo' => '/live/imgs/team2.png',
            ],
        ];

        $data = [
            'type' => intval($_GET['type']),
            'title' => !empty($teams[$_GET['team_id']]['name']) ? $teams[$_GET['team_id']]['name'] : '直播员',
            'logo' => !empty($teams[$_GET['team_id']]['logo']) ? $teams[$_GET['team_id']]['logo'] : '',
            'content' => !empty($_GET['content']) ? $_GET['content'] : '',
            'image' => !empty($_GET['image']) ? $_GET['image'] : '',
        ];

        //获取用户并push数据
//        $clients = Predis::getInstance()->sMembers(config('redis.live_game_key'));
//        foreach ($clients as $fd) {
//            $_POST['http_server']->push($fd, json_encode($data));
//        }

        // 代码优化： task任务
        $taskData = array(
            'method' => 'pushLive',
            'data' => $data
        );
        $_POST['http_server']->task($taskData); //Swoole-task
        return Util::show('code.success', 'OK');

    }

}
