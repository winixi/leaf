<?php
/**
 * seaf后端服务
 *
 * @Auther winixi@qq.com
 */
include_once "global.php";
global $config;
include_once "config.php";

//创建内存表
$table = new Swoole\Table(1024);
$table->column('id', Swoole\Table::TYPE_INT);
$table->column('time', Swoole\Table::TYPE_STRING, 64);
$table->column('name', Swoole\Table::TYPE_STRING, 256);
$table->column('task_type', Swoole\Table::TYPE_STRING, 64);
$table->create();

//时间id数组
$timeIds = [];

$spid = fopen("process.spid", "w") or die("不能创建子进程文件");

#开启任务队列服务
$taskerProcess = new swoole_process(function (swoole_process $taskerProcess) use ($config) {
    $taskerServer = new TaskerServer($config);
    $taskerServer->start();
    $taskerProcess->exit(0);
}, false, 0);
$taskerProcess->start();
fwrite($spid, $taskerProcess->pid);

#开启定时任务服务
$timerProcess = new swoole_process(function (swoole_process $timerProcess) use ($config, $table, $timeIds) {
    $timerServer = new TimerServer($config, $table, $timeIds);
    $timerServer->start();
    $timerProcess->exit(0);
}, false, 0);
$timerProcess->start();
fwrite($spid, " ".$timerProcess->pid);

#开启接口服务
$apiProcess = new swoole_process(function (swoole_process $apiProcess) use ($config, $table, $timeIds) {
    $apiServer = new ApiServer($config, $table, $timeIds);
    $apiServer->start();
    $apiProcess->exit(0);
}, false, 0);
$apiProcess->start();
fwrite($spid, " ".$apiProcess->pid);
fclose($spid);

echo "LEAF服务启动完成.\n";
swoole_process::wait();