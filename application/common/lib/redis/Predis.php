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
        // connect redis
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
            return $this->redis->setex($key, $time, $value);  // setex：带失效时间
        }
    }

    public function get($key)
    {
        if (!$key) {
            return '';
        }
        return $this->redis->get($key);
    }

//    /**
//     * 添加元素到redis有序集合
//     * @param $key 集合名称
//     * @param $value 元素
//     * @return mixed
//     */
//    public function sAdd($key, $value)
//    {
//        return $this->redis->sAdd($key, $value);
//    }
//
//    /**
//     * 删除集合元素
//     * @param $key
//     * @param $value
//     * @return int
//     */
//    public function sRem($key, $value)
//    {
//        return $this->redis->sRem($key, $value);
//    }
//
//    public function sMembers($key)
//    {
//        return $this->redis->sMembers($key);
//    }*/

    /**
     * 当调用类中不存在的方法时，就会调用__call()
     * 适用于基础类库的编写
     * @param $name
     * @param $arguments
     */
    public function __call($name, $arguments)
    {
        return $this->redis->$name($arguments[0], $arguments[1]);
    }
}