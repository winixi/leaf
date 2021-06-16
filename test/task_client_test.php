<?php
include_once "../global.php";

global $config;
include_once "../config.php";
$taskClient = new TaskerClient($config);

while (true) {
    $taskClient->addTask("MyTask");
    echo "添加1个任务\n";
    sleep(1);
}