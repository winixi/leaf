<?php
include_once "global.php";
include_once "libs/redis.php";

/**
 * Class TaskServer
 *
 * @Auther winixi@qq.com
 */
class TaskServer
{
    //配置
    private $config;

    /**
     * TaskServer constructor.
     *
     * @param $config
     */
    public function __construct($config)
    {
        $this->config = $config;
    }

    /**
     * 获取池
     *
     * @return \Swoole\Process\Pool
     */
    private function getPool()
    {
        $workerNum = $this->config['task']['worker_num'];
        echo "启动任务队列服务（队列线程池:" . $workerNum . ")... \n";

        return new \Swoole\Process\Pool($workerNum);
    }

    /**
     * 获取redis
     *
     * @return Redis
     */
    private function getRedis()
    {
        $conf = $this->config['redis'];
        return getRedis($conf['host'], $conf['port'], $conf['password']);
    }

    /**
     * 运行
     */
    public function start()
    {
        $pool = $this->getPool();
        $this->workStart($pool);
        $this->workStop($pool);
        $pool->start();
    }

    /**
     * 进程开始
     *
     * @param $pool
     */
    private function workStart($pool)
    {
        $pool->on("WorkerStart", function ($pool, $workerId) {
            echo "Worker#{$workerId} is started\n";
            //队列中读取任务
            $keys = $this->config['redis']['keys'];
            while (true) {
                $result = $this->getRedis()->brpop($keys, 1800);
                if (empty($result)) continue;
                //加载类
                $className = $this->config['task']['class_path'] . "/" . $result[1] . ".php";
                if (!file_exists($className)) {
                    echo "任务类文件不存在:$className \n";
                    return;
                }
                require_once $className;
                $business = new $result[1]();//反射工作类
                $business->run();//运行主方法
            }
        });
    }

    /**
     * 进程停止
     *
     * @param $pool
     */
    private function workStop($pool)
    {
        $pool->on("WorkerStop", function ($pool, $workerId) {
            echo "Worker#{$workerId} is stopped\n";
        });
    }

}
