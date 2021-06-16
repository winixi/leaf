<?php
/**
 * Simple function to replicate PHP 5 behaviour
 */
function microtime_float()
{
    list($usec, $sec) = explode(" ", microtime());
    return ((float)$usec + (float)$sec);
}

/**
 * @param \Swoole\Http\Response $resp
 */
function add_header(\Swoole\Http\Response $resp)
{
    $resp->header('Content-Type', 'application/json; charset=UTF-8');
    $resp->header('cross-origin-resource-policy', 'cross-origin');
}