<?php

/**
 * 任务基本抽象类
 *
 * Interface BaseTask
 */
abstract class BaseTask
{

    /**
     * @todo
     */
    function before()
    {
    }

    /**
     * 任务实现接口
     *
     * @param array params
     * @return string
     */
    abstract function run(array $params = []): string;

    /**
     * @todo
     */
    function after()
    {
    }

    /**
     * @todo
     */
    function status()
    {
    }
}
