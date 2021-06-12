<?php
/**
 * 配置文件
 */

#日志
$config['log']['path'] = "/Users/winixi/github/leaf/logs";
$config['log']['debug'] = true;

#开启服务
$config['server']['host'] = "0.0.0.0";
$config['server']['port'] = 9502;

#WEB服务
$config['web']['host'] = "0.0.0.0";
$config['web']['port'] = 9501;

#任务进程数
$config['task']['worker_num'] = 3;
$config['task']['class_path'] = "/Users/winixi/github/leaf/tasks";

#redis
$config['redis']['host'] = "127.0.0.1";
$config['redis']['port'] = 6379;
$config['redis']['password'] = "";
$config['redis']['keys'] = 'task_queue';

#mysql
$config['mysql']['host'] = "127.0.0.1";
$config['mysql']['port'] = 3306;
$config['mysql']['db_name'] = "leaf";
$config['mysql']['password'] = "QC7BxCw0LejQzuI7";
