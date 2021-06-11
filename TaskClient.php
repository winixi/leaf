<?php
include_once "global.php";
include_once "libs/mysql.php";
include_once "libs/redis.php";

/**
 * Class TaskClient
 *
 * @Auther winixi@qq.com
 */
class TaskClient
{

    private $config;
    private $redis;

    public function __construct($config)
    {
        $this->config = $config;
    }

    /**
     * 获取redis
     *
     * @return Redis
     */
    private function getRedis()
    {
        if (empty($this->redis) || !$this->redis->ping()) {
            $conf = $this->config['redis'];
            $this->redis = getRedis($conf['host'], $conf['port'], $conf['password']);
        }
        return $this->redis;
    }

    /**
     * 添加任务
     *
     * @param $task_class
     */
    public function addTask($task_class)
    {
        $redis = $this->getRedis();
        $keys = $this->config['redis']["keys"];
        $redis->lPush($keys, $task_class);
    }
}
