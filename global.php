<?php
ini_set('default_socket_timeout', -1);
date_default_timezone_set( 'Asia/Shanghai' );
spl_autoload_register(function ($class_name) {
    require_once $class_name . '.php';
});

define('TASK_FUN', 'FUN');
define('TASK_URL', 'URL');