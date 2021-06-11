<?php
/**
 * 服务器
 *
 * @author winixi@qq.com
 */
include_once "global.php";

/**
 * Class TimeServer
 *
 * @Auther winixi@qq.com
 */
class TimeServer
{

    private $config;
    private $table;
    private $taskClient;
    private $timer;

    public function __construct($config)
    {
        $this->config = $config;
        $this->initTable();
        $this->initTimer();
        $this->initTaskClient();
    }

    /**
     * 初始化内存表
     */
    private function initTable()
    {
        //创建内存表
        $table = new Swoole\Table(1024);
        $table->column('id', Swoole\Table::TYPE_INT);
        $table->column('time', Swoole\Table::TYPE_STRING, 64);
        $table->column('function', Swoole\Table::TYPE_STRING, 64);
        $table->create();
        $this->table = $table;
    }

    /**
     * 初始化执行器
     */
    private function initTimer()
    {
        $timer = new Timer($this->table);
        $timer->init();
        $this->timer = $timer;
    }

    /**
     * 初始化任务客户端
     */
    private function initTaskClient()
    {
        $taskClient = new TaskClient($this->config);
        $this->taskClient = $taskClient;
    }

    /**
     * 开始
     */
    public function start()
    {
        //启动时间处理器
        $this->startTimeProcess();

        //启动HTTP服务
        $this->startHttpServer();

        //回收所有进程
        Swoole\Process::wait();
    }

    /**
     * 启动时间处理器
     */
    private function startTimeProcess()
    {
        $timer = &$this->timer;
        //创建定时器进程
        $time_process = new Swoole\Process(function (swoole_process $time_process) use ($timer) {
            //创建定时器
            \Swoole\Timer::tick(1000, function ($ticket_id) use ($timer) {
                $timer->run($ticket_id);
            });
            //回收定时器
            Swoole\Event::wait();
        }, false, 0);

        echo "启动时间处理器 \n";
        $time_process->start();
    }

    /**
     * 启动http服务器
     */
    private function startHttpServer()
    {
        $conf = $this->config['server'];

        //创建http服务协程 --------------------------
        $http = new Swoole\Http\Server($conf['host'], $conf['port']);

        //接收请求
        $http->on('Request', function ($request, $response) {
            list($controller, $action) = explode('/', trim($request->server['request_uri'], '/'));
            (new $controller)->$action($request, $response);
        });

        echo "启动HTTP服务 \n";
        $http->start();
    }
}

////模拟插入一条数据
//$table->set(1, ['id' => 1, 'time' => '* * 01 * * *', 'function' => 'MyTask']);