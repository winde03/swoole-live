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
     */
    public function sendSms($data)
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
}