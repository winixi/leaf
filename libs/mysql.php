<?php
/**
 * mysql
 *
 * @param $host
 * @param $port
 * @param $dbname
 * @param $username
 * @param $password
 * @return PDO
 */
function getDbh($host, $port, $dbname, $username, $password): PDO
{
    $dsn = 'mysql:host=' . $host . ';port=' . $port . ';dbname=' . $dbname;
    $dbh = new PDO($dsn, $username, $password);
    return $dbh;
}

/**
 * 检查连接有效
 *
 * @param $dbh
 * @return bool
 */
function pdo_ping($dbh): bool
{
    try {
        $dbh->getAttribute(PDO::ATTR_SERVER_INFO);
    } catch (PDOException $e) {
        if (strpos($e->getMessage(), 'MySQL server has gone away') !== false) {
            return false;
        }
    }
    return true;
}
