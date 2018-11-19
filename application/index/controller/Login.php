<?php

namespace app\index\controller;

use app\common\lib\Util;
use app\common\lib\Redis;
use app\common\lib\redis\Predis;

class Login
{
    public function index()
    {
        $phoneNum = intval($_GET['phone_num']);
        $code = intval($_GET['code']);
        if (empty($phoneNum) || empty($code)) {
            return Util::show(config('code.error'), '手机号或者验证码输入为空');
        }

        //redis code
        try {
            $redisCode = Predis::getInstance()->get(Redis::smsKey($phoneNum));
        } catch (\Exception $e) {
            echo $e->getMessage();
        }

        if (intval($redisCode) === $code) {
            //identify correctly
            $data = [
                'user' => $phoneNum,
                'srcKey' => md5(Redis::userKey($phoneNum)),
                'time' => time(),
                'isLogin' => true
            ];
            Predis::getInstance()->set(Redis::userKey($phoneNum), $data);
            return Util::show(config('code.success'), 'ok', $data);
        } else {
            return Util::show(config('code.error'), 'login fail');
        }

    }
}
