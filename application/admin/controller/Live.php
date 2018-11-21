<?php

namespace app\admin\controller;

use app\common\lib\Util;
use app\common\lib\redis\Predis;

class Live
{
    public function push()
    {
        //process:赛况信息入库、组装数据、获取在线用户（https://wiki.swoole.com/wiki/page/427.html）、push到直播页面

        //获取用户并push数据
        $clients = Predis::getInstance()->sMembers(config('redis.live_game_key'));
        foreach ($clients as $fd) {
            $_POST['http_server']->push($fd, 'hello');
        }
    }

}
