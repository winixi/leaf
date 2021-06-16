<?php
include_once "../global.php";
/**
 * 运行
 */
global $config;
include_once "../config.php";

#开启任务队列服务
$taskServer = new TaskerServer($config);
$taskServer->start();
