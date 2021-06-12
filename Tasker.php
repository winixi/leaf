<?php
/**
 * Interface Tasker
 */
abstract class Tasker {
    function before(){}
    /**
     * 任务实现接口
     *
     * @return string
     */
    abstract function run(): string;
    function after(){}
    function status(){}
}
