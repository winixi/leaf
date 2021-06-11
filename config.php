<?php
/**
 * 配置文件
 */

#日志
$config['log']['path'] = "/Users/winixi/PhpstormProjects/swoole_test/server/logs";
$config['log']['debug'] = true;

#开启服务
$config['server']['host'] = "127.0.0.1";
$config['server']['port'] = 9502;

#任务进程数
$config['task']['worker_num'] = 3;
$config['task']['class_path'] = "/Users/winixi/PhpstormProjects/swoole_test/server/tasks";

#redis
$config['redis']['host'] = "127.0.0.1";
$config['redis']['port'] = 6379;
$config['redis']['password'] = "";
$config['redis']['keys'] = 'domain.com:task_queue';

#mysql
$config['mysql']['host'] = "127.0.0.1";
$config['mysql']['port'] = 3306;
$config['mysql']['db_name'] = "swoole_test";
