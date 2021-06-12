<?php
include_once "config.php";
include_once "libs/mysql.php";
include_once "libs/redis.php";
include_once "domain/Response.php";

/**
 * Class Timer
 *
 * @Auther winixi@qq.com
 */
class Timer
{

    /**
     * @var
     */
    private $table;

    /**
     * Timer constructor.
     * @param $table
     */
    public function __construct($table) {
        $this->table = $table;
        $this->init();
    }

    /**
     * 初始化 - 从数据库里读取到内存表
     */
    public function init()
    {
        echo "初始化数据库中的时间表到内存...\n";
        //todo
    }

    /**
     * 执行请求
     *
     * @param \Swoole\Http\Request $req
     * @param \Swoole\Http\Response $resp
     */
    public function execute(\Swoole\Http\Request $req, \Swoole\Http\Response $resp) {
        $resp->header('Content-Type', 'application/json; charset=UTF-8');
        $resp->header('cross-origin-resource-policy', 'cross-origin');
        $resp->end(new Response("处理定时器!!!!!!!!!!!"));
    }

    /**
     * 运行
     *
     * @param $ticket_id
     */
    public function run($ticket_id)
    {
        $date = new DateTime();
//        echo "检查事件表: " . $date->getTimestamp() . "定时任务条数：" . $this->table->count() . "\n";
    }

    /**
     * 判断是否符合当前执行条件
     *
     * @param $time
     * @return bool
     */
    private function now($time)
    {
        //检查是否符合当前执行条件
        //todo
        return true;
    }

    /**
     * 加入到执行任务队列
     *
     * @param $fun_class
     * @return bool
     */
    private function addTask($fun_class)
    {
        //加入到执行任务队列
        //todo
        return true;
    }

}