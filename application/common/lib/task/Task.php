<?php

namespace app\common\lib\task;

use app\common\lib\ali\Sms;
use app\common\lib\redis\Predis;
use app\common\lib\Redis;

//处理swoole后续task异步任务
class Task
{
    /**
     * 异步发送验证码
     * @param $data
     * @param $serv swoole_server对象
     */
    public function sendSms($data, $serv)
    {
        try {
            $response = Sms::sendSms($data['phone'], $data['code']);
        } catch (\Exception $e) {
            return false;
        }

        //发送成功，验证码存入redis
        if ($response->Code === "OK") {
            //redis
            Predis::getInstance()->set(Redis::smsKey($data['phone']), $data['code'], config('redis.out_time'));
        } else {
            return false;
        }

        return true;
    }

    /**
     * 发送赛况数据
     * @param $data
     * @param $serv swoole_server对象
     */
    public function pushLive($data, $serv)
    {
        $clients = Predis::getInstance()->sMembers(config('redis.live_game_key'));
        foreach ($clients as $fd) {
            $serv->push($fd, json_encode($data));
        }
    }
}