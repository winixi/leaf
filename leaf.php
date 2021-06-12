<?php
/**
 * swoole后端服务
 *
 * @Auther winixi@qq.com
 */
include_once "global.php";
global $config;
include_once "config.php";

#开启任务队列服务
$task_process = new swoole_process(function (swoole_process $task_process) use($config) {
    $taskServer = new TaskServer($config);
    $taskServer->start();
}, false, 0);
$task_process->start();

#开启定时任务服务
$time_process = new swoole_process(function (swoole_process $time_process) use($config) {
    $timeServer = new TimeServer($config);
    $timeServer->start();
}, false, 0);
$time_process->start();

echo "swoole后端服务启动完成 \n";
swoole_process::wait();