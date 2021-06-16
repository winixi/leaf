<?php
/**
 * Interface BaseTask
 */
abstract class BaseTask {
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
