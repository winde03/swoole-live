<?php
// 异步redis
$redisClient = new swoole_redis;// Swoole\Redis
$redisClient->connect('127.0.0.1', 6379, function (swoole_redis $redisClient, $result) {
    echo "connect" . PHP_EOL;
    var_dump($result);

    $redisClient->set('key', 'va', function (swoole_redis $redisClient, $result) {
        var_dump($result);
    });
    $redisClient->get('key', function (swoole_redis $redisClient, $result) {
        var_dump($result);
    });

    // 所有key列表
    $redisClient->keys('*', function (swoole_redis $redisClient, $result) {
        var_dump($result);
        $redisClient->close();
    });

});

echo "start" . PHP_EOL;