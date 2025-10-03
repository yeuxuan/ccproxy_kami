<?php
/*
 * @Author: yihua
 * @Date: 2025-01-04 17:32:11
 * @LastEditTime: 2025-01-05 13:47:54
 * @LastEditors: yihua
 * @Description: 
 * @FilePath: \ccproxy_end\includes\Scheduler.php
 * 💊物物而不物于物，念念而不念于念🍁
 * Copyright (c) 2025 by yihua, All Rights Reserved. 
 */
/**

 * Class Scheduler

 */
Class Scheduler

{

    /**

     * @var SplQueue

     */

    protected $taskQueue;

    /**

     * @var int

     */

    protected $tid = 0;

 

    /**

     * Scheduler constructor.

     */

    public function __construct()

    {

        /* 原理就是维护了一个队列，

         * 前面说过，从编程角度上看，协程的思想本质上就是控制流的主动让出（yield）和恢复（resume）机制

         * */

        $this->taskQueue = new SplQueue();

    }

 

    /**

     * 增加一个任务

     *

     * @param Generator $task

     * @return int

     */

    public function addTask(Generator $task)

    {

        $tid = $this->tid;

        $task = new Task($tid, $task);

        $this->taskQueue->enqueue($task);

        $this->tid++;

        return $tid;

    }

 

    /**

     * 把任务进入队列

     *

     * @param Task $task

     */

    public function schedule(Task $task)

    {

        $this->taskQueue->enqueue($task);

    }

 

    /**

     * 运行调度器

     */

    public function run()

    {
        $data=null;
        while (!$this->taskQueue->isEmpty()) {
            // 任务出队
            $task = $this->taskQueue->dequeue();

            $res = $task->run(); // 运行任务直到 yield
            //  $task->run();
            if (!$task->isFinished()) {
                $data=$res;
                $this->schedule($task); // 任务如果还没完全执行完毕，入队等下次执行
            }
            // var_dump($res);
        }
        // var_dump($data);
        return $data;
     }
}


?>