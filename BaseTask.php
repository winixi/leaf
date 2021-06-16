<?php
/**
 * 任务基本抽象类
 *
 * Interface BaseTask
 */
abstract class BaseTask {

    /**
     * @todo
     */
    function before(){}

    /**
     * 任务实现接口
     *
     * @return string
     */
    abstract function run(): string;

    /**
     * @todo
     */
    function after(){}

    /**
     * @todo
     */
    function status(){}
}
