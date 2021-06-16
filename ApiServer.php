<?php
/**
 * 服务器
 *
 * @author winixi@qq.com
 */
include_once "global.php";

/**
 * Class ApiServer
 *
 * @Auther winixi@qq.com
 */
class ApiServer
{

    /* @var array */
    private $config;

    /* @var TaskerClient */
    private $taskClient;

    /* @var TimerClient */
    private $timerClient;

    /**
     * ApiServer constructor.
     * @param array $config
     * @param \Swoole\Table $table
     * @param array $timeIds
     */
    public function __construct(array $config, \Swoole\Table $table, array $timeIds)
    {
        $this->config = $config;
        $this->timerClient = new TimerClient($config, $table, $timeIds);;
        $this->taskClient = new TaskerClient($config);
    }

    /**
     * 开始
     */
    public function start()
    {
        //启动接口服务
        \Co\run(function () {
            $this->startApiServer();
        });

        //回收所有进程
        Swoole\Process::wait();
    }

    /**
     * 启动工作服务器
     */
    private function startApiServer()
    {
        $conf = $this->config['server'];
        $host = $conf['host'];
        $port = $conf['port'];

        //创建http服务协程 --------------------------
        $http = new Swoole\Coroutine\Http\Server($host, $port);

        //处理定时器
        $http->handle('/timer', function (\Swoole\Http\Request $req, \Swoole\Http\Response $resp) {
            //调用timer处理
            $this->timerClient->execute($req, $resp);
        });

        //处理任务队列
        $http->handle('/tasker', function (\Swoole\Http\Request $req, \Swoole\Http\Response $resp) {
            //调用taskClient处理
            $this->taskClient->execute($req, $resp);
        });

        echo "启动服务： $host:$port\n";
        $http->start();
    }

}