<?php

/**
 * Class MyTask
 *
 * 任务执行对象
 */
class myTask extends BaseTask
{

    /**
     * 任务实现
     *
     * @params string params
     * @return mixed|void
     */
    public function run(array $params = []): string
    {
        $id = mt_rand();
        echo "执行任务: pid=$id \n";
        sleep(1);
        echo "-完成任务：pid=$id \n";
        return "success";
    }
}