<?php
include_once "global.php";
include_once "libs/mysql.php";
include_once "libs/redis.php";
include_once "libs/tools.php";
include_once "domain/Response.php";

/**
 * 定时器客户端
 *
 * Class TimerClient
 *
 * @Auther winixi@qq.com
 */
class TimerClient
{
    /* @var array */
    private $config;

    /* @var \Swoole\Table */
    private $table;

    /* @var PDO */
    private $dbh;

    /**
     * TimerClient constructor.
     *
     * @param array $config
     * @param \Swoole\Table $table
     */
    public function __construct(array $config, \Swoole\Table $table)
    {
        $this->config = $config;
        $this->table = $table;
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
     * 执行请求
     *
     * @param \Swoole\Http\Request $req
     * @param \Swoole\Http\Response $resp
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
                $this->modify($req, $resp);
                break;
            case "DELETE";
                $this->remove($req, $resp);
                break;
            default:
        }
    }

    /**
     * 新增
     *
     * @param \Swoole\Http\Request $req
     * @param \Swoole\Http\Response $resp
     */
    private function add(\Swoole\Http\Request $req, \Swoole\Http\Response $resp)
    {
        $timer = json_decode($req->getContent(), true);
        //todo 参数校验

        $dbh = $this->getDbh();
        $sql = 'INSERT INTO s_time (time,name,params,task_type,status,memo,create_time) VALUES (:time,:name,:params,:task_type,:status,:memo,:create_time)';
        $sth = $dbh->prepare($sql);
        $values[':time'] = $timer['time'];
        $values[':name'] = $timer['name'];
        $values[':status'] = $timer['status'];
        $values[':params'] = json_encode($timer['params'], true);
        $values[':task_type'] = $timer['task_type'];
        $values[':memo'] = $timer['memo'];
        $values[':create_time'] = date('Y-m-d H:i:s');
        $sth->execute($values);

        $id = $dbh->lastInsertId();
        if ($timer['status'] == 1) $this->_add($id, $timer);

        add_header($resp);
        $resp->end(new Response($id));
    }

    /**
     * 查询
     *
     * @param \Swoole\Http\Request $req
     * @param \Swoole\Http\Response $resp
     */
    private function get(\Swoole\Http\Request $req, \Swoole\Http\Response $resp)
    {
        $uri = $req->server["request_uri"];
        $dbh = $this->getDbh();

        //历史记录
        if ($uri == "/timer/page") {
            $size = $req->get["size"];
            $number = $req->get["number"];
            $offset = ($number - 1) * $size;
            $sth = $dbh->prepare("SELECT * FROM s_time ORDER BY id DESC LIMIT ?, ?");
            $sth->bindParam(1, $offset, PDO::PARAM_INT);
            $sth->bindParam(2, $size, PDO::PARAM_INT);
            $sth->execute();
            foreach ($sth->fetchAll(PDO::FETCH_ASSOC) as $row) {
                $row['params'] = json_decode($row['params'], true);
                $result[] = $row;
            }
        } //指定记录
        elseif (strpos($uri, "/timer/id/") === 0) {
            $id = str_replace("/timer/id/", "", $uri);
            $sth = $dbh->prepare("SELECT * FROM s_time WHERE id=?");
            $sth->execute(array($id));
            $row = $sth->fetch(PDO::FETCH_ASSOC);
            $row['params'] = json_decode($row['params'], true);
            $result = $row;
        }

        add_header($resp);
        $resp->end(new Response($result));
    }

    /**
     * 修改
     *
     * @param \Swoole\Http\Request $req
     * @param \Swoole\Http\Response $resp
     */
    private function modify(\Swoole\Http\Request $req, \Swoole\Http\Response $resp)
    {
        $dbh = $this->getDbh();
        $uri = $req->server['request_uri'];
        if (strpos($uri, '/timer/id/') === 0) {
            $id = str_replace('/timer/id/', '', $uri);
            $sql = 'UPDATE s_time SET time=:time,name=:name,params=:params,task_type=:task_type,status=:status,memo=:memo,modify_time=:modify_time WHERE id=:id';
            $sth = $dbh->prepare($sql);

            $timer = json_decode($req->getContent(), true);
            $values[':id'] = $id;
            $values[':time'] = $timer['time'];
            $values[':name'] = $timer['name'];
            $values[':params'] = json_encode($timer['params'], true);
            $values[':task_type'] = $timer['task_type'];
            $values[':status'] = $timer['status'];
            $values[':memo'] = $timer['memo'];
            $values[':modify_time'] = date('Y-m-d H:i:s');
            $sth->execute($values);

            //增加
            if ($timer['status'] == 1) $this->_add($id, $timer);

            //删除
            if ($timer['status'] == 0) $this->_del($id);

            add_header($resp);
            $resp->end(new Response(true));
        }
    }

    /**
     * 删除
     *
     * @param \Swoole\Http\Request $req
     * @param \Swoole\Http\Response $resp
     */
    private function remove(\Swoole\Http\Request $req, \Swoole\Http\Response $resp)
    {
        $dbh = $this->getDbh();
        $uri = $req->server['request_uri'];
        if (strpos($uri, '/timer/id/') === 0) {
            $id = str_replace('/timer/id/', '', $uri);
            $sth = $dbh->prepare('DELETE FROM s_time WHERE id=?');
            $sth->execute(array($id));

            #存在就删除，防止是历史已经不再执行的任务
            $this->_del($id);

            add_header($resp);
            $resp->end(new Response(true));
        }
    }

    /**
     * 添加
     *
     * @param int $id
     * @param array $timer
     */
    private function _add(int $id, array $timer)
    {
        $values = array('id' => $id, 'time' => $timer['time'], 'name' => $timer['name'], 'task_type' => $timer['task_type'], 'params' => json_encode($timer['params'], true));
        $this->table->set($id, $values);
    }

    /**
     * 从内存中删除
     *
     * @param int $id
     */
    private function _del(int $id)
    {
        if ($this->table->exist($id)) $this->table->del($id);
    }
}