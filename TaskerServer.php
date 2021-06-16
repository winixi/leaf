<?php
include_once "global.php";
include_once "libs/redis.php";
include_once "libs/mysql.php";
include_once "libs/tools.php";
include_once "libs/Logger.php";

/**
 * Class TaskerServer
 *
 * @Auther winixi@qq.com
 */
class TaskerServer
{
    /**
     * @var array
     */
    private $config;

    /**
     * TaskerServer constructor.
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * 获取池
     *
     * @return \Swoole\Process\Pool
     */
    private function getPool(): \Swoole\Process\Pool
    {
        $workerNum = $this->config['task']['worker_num'];
        echo "启动任务队列服务（队列线程池:" . $workerNum . ")... \n";

        return new \Swoole\Process\Pool($workerNum);
    }

    /**
     * 获取redis对象
     *
     * @return Redis
     */
    private function getRedis(): Redis
    {
        $conf = $this->config['redis'];
        return getRedis($conf['host'], $conf['port'], $conf['password']);
    }

    /**
     * 获取一个mysql连接对象
     *
     * @return PDO
     */
    private function getDbh(): PDO
    {
        $conf = $this->config['mysql'];
        return getDbh($conf['host'], $conf['port'], $conf['dbname'], $conf['username'], $conf['password']);
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
            Logger::info("Worker#{$workerId} is started");
            //队列中读取任务
            $redis = $this->getRedis();
            $dbh = $this->getDbh();
            $keys = $this->config['redis']['keys'];
            while (true) {
                $result = $redis->brpop($keys, 1800);
                if (empty($result)) continue;
                $task = json_decode($result[1], true);
                switch ($task['task_type']) {
                    case TASK_FUN:
                        $this->runFun($task, $dbh);
                        break;
                    case TASK_URL:
                        $this->runUrl($task, $dbh);
                        break;
                    default:
                }
            }
        });
    }

    /**
     * 执行本地类
     *
     * @param array $task
     * @param PDO $dbh
     */
    private function runFun(array $task, PDO $dbh)
    {
        $name = $task['name'];
        $className = $this->config['task']['class_path'] . "/" . $name . ".php";
        if (!file_exists($className)) {
            Logger::error("任务类文件不存在:$className");
            return;
        }
        require_once $className;
        $startTime = microtime_float();
        $result = (new $name)->run();
        //记录到数据库
        $this->save($task, $startTime, $result, $dbh);
    }

    /**
     * 保存到数据库
     *
     * @param array $task
     * @param float $startTime
     * @param string $result
     * @param PDO $dbh
     */
    private function save(array $task, float $startTime, string $result, PDO $dbh)
    {
        $endTime = microtime_float();
        $sql = "INSERT INTO s_task (name, start, end, duration, task_type, result, create_time) values (:name, :start, :end, :duration, :task_type, :result, :create_time)";
        $sth = $dbh->prepare($sql);

        $values[":name"] = $task['name'];
        $values[":start"] = $startTime;
        $values[":end"] = $endTime;
        $values[":duration"] = $endTime - $startTime;
        $values[":task_type"] = $task['task_type'];
        $values[":result"] = $result;
        $values[":create_time"] = date("Y-m-d H:i:s");

        $sth->execute($values);
    }

    /**
     * 执行url请求
     *
     * @param array $task
     * @param PDO $dbh
     */
    private function runUrl(array $task, PDO $dbh)
    {
        $startTime = microtime_float();
        $url = $task['name'];
        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, array("timestamp" => microtime_float()));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $result = curl_exec($ch);
        curl_close($ch);

        //记录到数据库
        $this->save($task, $startTime, $result, $dbh);
    }

    /**
     * 进程停止
     *
     * @param $pool
     */
    private function workStop($pool)
    {
        $pool->on("WorkerStop", function ($pool, $workerId) {
            Logger::info("Worker#{$workerId} is stopped");
        });
    }

}
