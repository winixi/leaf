<?php
/**
 * 在logs下每天生成3个日志文件
 * 2021-06-10.error.log
 * 2021-06-10.info.log
 * 2021-06-10.debug.log
 *
 * @author winixi@qq.com
 */
class Logger
{

    /**
     * 错误日志
     *
     * @param $message
     */
    public static function error($message)
    {
        self::add("error", $message);
    }

    /**
     * 信息日志
     *
     * @param $message
     */
    public static function info($message)
    {
        self::add("info", $message);
    }

    /**
     * 调试日志
     *
     * @param $message
     */
    public static function debug($message)
    {
        self::add("debug", $message);
    }

    /**
     * @param string $type
     * @param string $message
     */
    private static function add(string $type, string $message)
    {
        $date_time = new DateTime;        // 日志内容
        $time = $date_time->format('H:i:s');
        $log_file = self::getFile($type, $date_time);
        $trace = array_slice(debug_backtrace(), -1, 1);
        $file = $trace[0]['file'];
        $line = $trace[0]['line'];
        $log_data = sprintf('%s [%s] %s:%s %s' . PHP_EOL, $time, strtoupper($type), $file, $line, $message);        // 写入日志文件
        return file_put_contents($log_file, $log_data, FILE_APPEND);
    }

    /**
     * @param string $type
     * @param DateTime $dateTime
     * @return string
     */
    private static function getFile(string $type, DateTime $dateTime)
    {
        $f_dateTime = $dateTime->format('Y-m-d');
        return LOG_PATH . '/' . $f_dateTime . "." . $type . ".log";
    }
}
