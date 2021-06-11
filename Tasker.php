<?php
/**
 * Interface Tasker
 */
abstract class Tasker {
    function before(){}
    /**
     * 任务实现接口
     *
     * @return mixed
     */
    abstract function run();
    function after(){}
    function status(){}
}
