<?php

namespace app\common\lib;

class Redis
{
    /**
     * 发送验证码前缀
     * @var string
     */
    public static $pre = "sms_";

    /**
     * 用户user的前缀
     * @var string
     */
    public static $userpre = "user_";

    /**
     * 存储redis短信key前缀
     * @param $phone
     * @return string
     */
    public static function smsKey($phone)
    {
        return self::$pre . $phone;
    }

    /**
     * 存储redis用户key
     * @param $phone
     * @return string
     */
    public static function userKey($phone)
    {
        return self::$userpre . $phone;
    }
}