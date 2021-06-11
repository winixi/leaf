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
function getDbh($host, $port, $dbname, $username, $password)
{
    $dsn = 'mysql:host=' . $host . ';port=' . $port . ';dbname=' . $dbname;
    $dbh = new PDO($dsn, $username, $password);
    return $dbh;
}

/* 通过传递一个含有插入值的数组执行一条预处理语句 */
//$calories = 150;
//$colour = 'red';
//$sth = $dbh->prepare('SELECT name, colour, calories
//    FROM fruit
//    WHERE calories < :calories AND colour = :colour');
//$sth->execute(array(':calories' => $calories, ':colour' => $colour));
