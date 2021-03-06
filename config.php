<?php
/**
 * 配置文件
 */

define('ROOT_PATH', dirname(__FILE__));
define('LOG_PATH', ROOT_PATH . '/logs');
define('DEBUG', true);

define('TASK_FUN', 'FUN');
define('TASK_URL', 'URL');

#开启服务
$config['server']['host'] = "0.0.0.0";
$config['server']['port'] = 9502;

#任务进程数
$config['task']['worker_num'] = 3;
$config['task']['class_path'] = "/Users/winixi/github/leaf/tasks";
$config['task']['curl_timeout'] = 60;

#redis
$config['redis']['host'] = "127.0.0.1";
$config['redis']['port'] = 6379;
$config['redis']['password'] = "";
$config['redis']['keys'] = 'task_queue';

#mysql
$config['mysql']['host'] = "127.0.0.1";
$config['mysql']['port'] = 3306;
$config['mysql']['dbname'] = "leaf";
$config['mysql']['username'] = "leaf";
$config['mysql']['password'] = "QC7BxCw0LejQzuI7";
