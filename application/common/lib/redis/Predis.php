<?php

namespace app\common\lib\redis;
/**
 * Class Predis
 * @package app\common\lib\redis
 * 单例模式，减少连接redis的次数，减轻损耗
 */
class Predis
{
    public $redis = "";
    private static $_instance = null;  //定以单例模式变量

    public static function getInstance()
    {
        if (empty(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    private function __construct()
    {
        $this->redis = new \Redis();
        $result = $this->redis->connect(config('redis.host'), config('redis.port'), config('redis.timeOut'));
        if ($result === false) {
            throw new \Exception('redis connect error');
        }
    }

    public function set($key, $value, $time = 0)
    {
        if (!$key) {
            return '';
        }
        if (is_array($value)) {
            $value = json_encode($value);
        }
        if (!$time) {
            return $this->redis->set($key, $value);
        } else {
            return $this->redis->setex($key, $time, $value);
        }
    }

    public function get($key)
    {
        if (!$key) {
            return '';
        }
        return $this->redis->get($key);
    }
}