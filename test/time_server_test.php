<?php
include_once "../global.php";

global $config;
include_once "../config.php";

$timeServer = new TimeServer($config);
$timeServer->start();