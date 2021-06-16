<?php
include_once "global.php";
include_once "libs/mysql.php";
include_once "libs/redis.php";
include_once "libs/tools.php";
include_once "domain/Response.php";

/**
 * Class TaskerClient
 *
 * @var $config array
 * @var $redis Redis
 *
 * @Auther winixi@qq.com
 */
class TaskerClient
{

    /* @var array */
    private $config;

    /* @var Redis */
    private $redis;

    /* @var PDO */
    private $dbh;

    /**
     * TaskerClient constructor.
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * 添加任务
     *
     * @param \Swoole\Http\Request $req
     * @param \Swoole\Http\Response $resp
     * @return bool|int
     * @throws RedisException
     */
    private function add(\Swoole\Http\Request $req, \Swoole\Http\Response $resp)
    {
        $task = $req->getContent();
        $result = $this->addTask($task);

        add_header($resp);
        $resp->end(new Response($result));
    }

    /**
     * 添加任务
     *
     * @param $task
     * @return false|int
     * @throws RedisException
     */
    public function addTask($task) {
        $redis = $this->getRedis();
        $keys = $this->config['redis']["keys"];
        return $redis->lPush($keys, $task);
    }

    /**
     * 查询
     *
     * @param \Swoole\Http\Request $req
     * @param \Swoole\Http\Response $resp
     * @throws RedisException
     */
    private function get(\Swoole\Http\Request $req, \Swoole\Http\Response $resp)
    {
        $uri = $req->server["request_uri"];
        $redis = $this->getRedis();
        $dbh = $this->getDbh();
        $result = "";

        //队列条数
        if ($uri == "/tasker/count") {
            $keys = $this->config['redis']["keys"];
            $result = $redis->Llen($keys);
        }
        //历史记录
        elseif ($uri == "/tasker/page") {
            $size = $req->get["size"];
            $number = $req->get["number"];
            $offset = ($number - 1) * $size;
            $sth = $dbh->prepare("SELECT * FROM s_task ORDER BY id DESC LIMIT ?, ?");
            $sth->bindParam(1, $offset, PDO::PARAM_INT);
            $sth->bindParam(2, $size, PDO::PARAM_INT);
            $sth->execute();
            $result = $sth->fetchAll(PDO::FETCH_ASSOC);
        }
        //指定记录
        elseif (strpos($uri, "/tasker/id/") === 0) {
            $id = str_replace("/tasker/id/", "", $uri);
            $sth = $dbh->prepare("SELECT * FROM s_task WHERE id=?");
            $sth->execute(array($id));
            $result = $sth->fetch(PDO::FETCH_ASSOC);
        }

        add_header($resp);
        $resp->end(new Response($result));
    }

    /**
     * 获取redis
     *
     * @return Redis
     * @throws RedisException
     */
    private function getRedis(): Redis
    {
        if (empty($this->redis) || !$this->redis->ping()) {
            $conf = $this->config['redis'];
            $this->redis = getRedis($conf['host'], $conf['port'], $conf['password']);
        }
        return $this->redis;
    }

    /**
     * 获取一个mysql连接对象
     *
     * @return PDO
     */
    private function getDbh(): PDO
    {
        if (empty($this->dbh) || !pdo_ping($this->dbh)) {
            $conf = $this->config['mysql'];
            $this->dbh = getDbh($conf['host'], $conf['port'], $conf['dbname'], $conf['username'], $conf['password']);
        }
        return $this->dbh;
    }

    /**
     * 执行
     *
     * @param \Swoole\Http\Request $req
     * @param \Swoole\Http\Response $resp
     * @throws RedisException
     */
    public function execute(\Swoole\Http\Request $req, \Swoole\Http\Response $resp)
    {
        switch ($req->getMethod()) {
            case "POST":
                $this->add($req, $resp);
                break;
            case "GET":
                $this->get($req, $resp);
                break;
            case "PUT":
                echo "修改";
                break;
            case "DELETE";
                echo "删除";
                break;
            default:
        }
    }

}
