<?php

namespace app\index\controller;

use app\common\lib\ali\Sms;
use app\common\lib\Util;
use app\common\lib\Redis;

class Send
{
    /**
     * 发送验证码
     */
    public function index()
    {
        $phoneNum = request()->get('phone_num', 0, 'intval');//没有数据默认0
        if (empty($phoneNum)) {
            return Util::show(config('code.error'), '手机号不能为空');
        }

        //校验手机号
        if (!preg_match("/^1[34578]{1}\d{9}$/", $phoneNum)) {
            return Util::show(config('code.error'), '手机号格式错误');
        } else {
            //生成随机数
            $code = rand(1000, 9999);

            $taskData = array(
                'method' => 'sendSms',
                'data' => [
                    'phone' => $phoneNum,
                    'code' => $code,
                ]
            );
            $_POST['http_server']->task($taskData); //Swoole-task
            return Util::show('code.success', 'OK');

            /*try {
                $response = Sms::sendSms($phoneNum, $code);
            } catch (\Exception $e) {
                return Util::show(config('code.error'), '内部异常');
            }*/

            /*if ($response->Code === "OK") {
                //redis
                $redis = new \Swoole\Coroutine\Redis();
                $redis->connect(config('redis.host'), config('redis.port'));
                $redis->set(Redis::smsKey($phoneNum), $code, config('redis.out_time'));

                //异步redis
                $redisClient = new swoole_redis;// Swoole\Redis
                $redisClient->connect(config('redis.host'), config('redis.port'), function (swoole_redis $redisClient, $result) {
                    echo "connect" . PHP_EOL;

                    $redisClient->set(Redis::smsKey($phoneNum), $code, function (swoole_redis $redisClient, $result) {
                    });

                });

                return Util::show(config('code.success'), 'success');

            } else {
                return Util::show(config('code.error'), '验证码发送失败');
            }*/
        }
    }
}
