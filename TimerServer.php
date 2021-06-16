<?php
include_once "global.php";
include_once "libs/redis.php";
include_once "libs/mysql.php";
include_once "libs/Crontab.php";
include_once "libs/Logger.php";

/**
 * 定时器服务端
 *
 * Class TimerServer
 *
 * @author winixi@qq.com
 */
class TimerServer
{

    /**
     * @var array
     */
    private $config;

    /**
     * @var \Swoole\Table
     */
    private $table;

    /**
     * @var array
     */
    private $timeIds;

    /**
     * @var PDO
     */
    private $dbh;

    /* @var TaskerClient */
    private $taskClient;

    /**
     * TimerServer constructor.
     * @param array $config
     * @param \Swoole\Table $table
     * @param array $timeIds
     */
    public function __construct(array $config, \Swoole\Table $table, array $timeIds)
    {
        $this->config = $config;
        $this->table = $table;
        $this->timeIds = $timeIds;
        $this->loadDb();
        $this->taskClient = new TaskerClient($config);
    }

    /**
     * 复用连接
     *
     * @return PDO
     */
    private function getDbh(): PDO
    {
        if (empty($this->dbh) || pdo_ping($this->dbh)) {
            $conf = $this->config['mysql'];
            $this->dbh = getDbh($conf['host'], $conf['port'], $conf['dbname'], $conf['username'], $conf['password']);
        }
        return $this->dbh;
    }

    /**
     * 读取数据库
     */
    private function loadDb()
    {
        $dbh = $this->getDbh();
        $sth = $dbh->query("SELECT * FROM s_time");
        foreach ($sth->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $id = $row["id"];
            $record = array('id' => $id, 'time' => $row['time'], 'name' => $row['name'], 'task_type' => $row['task_type']);
            $this->table->set($id, $record);
            $this->timeIds[] = $id;
        }
        Logger::info("从数据库中加载了" . sizeof($this->timeIds) . "条定时任务");
    }

    /**
     * 开始
     */
    public function start()
    {
        echo "启动时间处理器 \n";

        //创建定时器
        \Swoole\Timer::tick(1000, function ($ticket_id) {
            $this->run($ticket_id);
        });

        //回收定时器
        Swoole\Event::wait();
    }

    /**
     * 运行
     *
     * @param $ticket_id
     */
    public function run($ticket_id)
    {
        $now = time();
        foreach ($this->timeIds as $id) {
            if (($time = $this->table->get($id))) {
                if (Crontab::parseCron($time['time'], $now)) {
                    $this->addTask($time);
                }
            }
        }
    }

    /**
     * 加入到执行任务队列
     *
     * @param array $time
     * @throws RedisException
     */
    private function addTask(array $time)
    {
        //加入到执行任务队列
        $task = json_encode(array('name' => $time['name'], 'task_type' => $time['task_type']), true);
        $this->taskClient->addTask($task);

        Logger::info("添加一条执行任务:" . $task);

        //本地累计一次
        $dbh = $this->getDbh();
        $sth = $dbh->prepare('UPDATE s_time SET count=count+1,modify_time=? WHERE id=?');
        $sth->execute(array(date('Y-m-d H:i:s'), $time['id']));
    }

}