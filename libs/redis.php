<?php
/**
 * 获取redis
 *
 * @param $host
 * @param $port
 * @param $password
 * @return Redis
 */
function getRedis($host, $port, $password)
{
    $redis = new Redis();
    $redis->connect($host, $port);
    if (!empty($password)) {
        $redis->auth($password);
    }
    return $redis;
}
