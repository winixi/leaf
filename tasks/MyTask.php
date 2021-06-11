<?php

/**
 * Class MyTask
 *
 * 任务执行对象
 */
class myTask extends Tasker
{

    /**
     * 任务实现
     *
     * @return mixed|void
     */
    public function run()
    {
        $id = mt_rand();
        echo "执行任务: pid=$id \n";
        sleep(mt_rand(1, 10));
        echo "-完成任务：pid=$id \n";
    }
}