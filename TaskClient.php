<?php
include_once "global.php";
include_once "libs/mysql.php";
include_once "libs/redis.php";
include_once "domain/Response.php";

/**
 * Class TaskClient
 *
 * @Auther winixi@qq.com
 */
class TaskClient
{

    //配置
    private $config;

    //redis
    private $redis;

    /**
     * TaskClient constructor.
     * @param $config
     */
    public function __construct($config)
    {
        $this->config = $config;
    }

    /**
     * 添加任务
     *
     * @param \Swoole\Http\Request $req
     * @param \Swoole\Http\Response $resp
     * @return bool|int
     */
    public function addTask(\Swoole\Http\Request $req, \Swoole\Http\Response $resp)
    {
        $redis = $this->getRedis();
        $keys = $this->config['redis']["keys"];
        $task = $req->getContent();
        $result = $redis->lPush($keys, $task);

        $this->header($resp);
        $resp->end(new Response($result));
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
     * 执行
     *
     * @param \Swoole\Http\Request $req
     * @param \Swoole\Http\Response $resp
     */
    public function execute(\Swoole\Http\Request $req, \Swoole\Http\Response $resp)
    {
        switch ($req->getMethod()) {
            case "POST":
                //新增
                $this->addTask($req, $resp);
                break;
            case "GET":
                echo "查询";
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

    /**
     * @param \Swoole\Http\Response $resp
     */
    private function header(\Swoole\Http\Response $resp) {
        $resp->header('Content-Type', 'application/json; charset=UTF-8');
        $resp->header('cross-origin-resource-policy', 'cross-origin');
    }
}
