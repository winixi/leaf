<?php
include_once "libs/Crontab.php";

$cron_list = [
    //['cron' => '秒 分 时 日 月 周', 'echo' => '任务名称'],
    ['cron' => '0 */1 * * * *', 'echo' => '每分钟执行1次：第一个任务执行了'],
    ['cron' => '0 */2 * * * *', 'echo' => '每2分钟执行1次www.phpernote.com'],
    ['cron' => '20 */3 * * * *', 'echo' => '每三分钟的第20秒：第三个任务执行了'],
    ['cron' => '5 */5 * * * *', 'echo' => '每5分钟的第5秒：第四个任务执行了'],
    ['cron' => '0 41 16 29 * *', 'echo' => '每29号的下午4点41分0秒：第5个任务执行了']
];

echo date('Y-m-d H:i:s', time()) . "\r\n";
while (true) {
    $time = time();
    foreach ($cron_list as &$cron) {
        $result = Crontab::parseCron($cron['cron'], $time);
        if ($result) {
            echo date('Y-m-d H:i:s', $time) . ' ' . $cron['cron'] . ' ' . $cron['echo'] . "\r\n";
        }
    }
    //echo date('Y-m-d H:i:s', $time) . "\r\n";
    sleep(1);
}