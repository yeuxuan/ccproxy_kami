<?php
/*
 * @Author: yihua
 * @Date: 2025-01-04 17:32:11
 * @LastEditTime: 2025-01-05 13:47:40
 * @LastEditors: yihua
 * @Description: 
 * @FilePath: \ccproxy_end\includes\Task.php
 * 💊物物而不物于物，念念而不念于念🍁
 * Copyright (c) 2025 by yihua, All Rights Reserved. 
 */

/**

 * Task任务类

 */

class Task

{

    protected $taskId;

    protected $coroutine;

    protected $beforeFirstYield = true;

    protected $sendValue;



    /**

     * Task constructor.

     * @param $taskId

     * @param Generator $coroutine

     */

    public function __construct($taskId, Generator $coroutine)

    {

        $this->taskId = $taskId;

        $this->coroutine = $coroutine;
    }



    /**

     * 获取当前的Task的ID

     * 

     * @return mixed

     */

    public function getTaskId()

    {

        return $this->taskId;
    }



    /**

     * 判断Task执行完毕了没有

     * 

     * @return bool

     */

    public function isFinished()

    {

        return !$this->coroutine->valid();
    }



    /**

     * 设置下次要传给协程的值，比如 $id = (yield $xxxx)，这个值就给了$id了

     * 

     * @param $value

     */

    public function setSendValue($value)

    {

        $this->sendValue = $value;
    }
    /**
     * 获取执行的结果
     */
    public function getresult()
    {
        while ($this->coroutine->valid()) {
            var_dump($this->coroutine->current());
            $this->coroutine->next();
        }
    }

    /**

     * 运行任务

     * 

     * @return mixed

     */

    public function run()

    {
        // while ($this->coroutine->valid()) {
        //     print_r($this->coroutine->current());
        //     $this->coroutine->next();
        // }
        // 这里要注意，生成器的开始会reset，所以第一个值要用current获取

        if ($this->beforeFirstYield) {

            $this->beforeFirstYield = false;
            //var_dump($this->coroutine->current());
            //print_r($this->coroutine->current());
            return $this->coroutine->current();
        } else {

            // 我们说过了，用send去调用一个生成器

            $retval = $this->coroutine->send($this->sendValue);

            $this->sendValue = null;

            return $retval;
        }
    }
}
