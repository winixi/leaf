<?php
include_once 'global.php';
include_once 'config.php';
include_once 'libs/Logger.php';

function test() {
    Logger::info("测试内容");
}

Logger::error("为什么有这么多错误呢");