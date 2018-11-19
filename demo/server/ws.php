<?php

class Ws
{

    CONST HOST = "0.0.0.0";
    CONST PORT = 8812;

    public $ws = null;

    public function __construct()
    {
        $this->ws = new swoole_websocket_server("0.0.0.0", 8812);

        $this->ws->set(
            [
                'worker_num' => 2,
                'task_worker_num' => 2,
            ]
        );
        $this->ws->on("open", [$this, 'onOpen']);
        $this->ws->on("message", [$this, 'onMessage']);
        $this->ws->on("task", [$this, 'onTask']);
        $this->ws->on("finish", [$this, 'onFinish']);
        $this->ws->on("close", [$this, 'onClose']);

        $this->ws->start();
    }

    /**
     * 监听ws连接事件
     * @param $ws
     * @param $request
     */
    public function onOpen($ws, $request)
    {
        var_dump($request->fd);
        if ($request->fd == 1) {
            // 间隔时钟定时器 每2秒执行
            swoole_timer_tick(2000, function ($timer_id) {
                echo "2s: timerId:{$timer_id}\n";
            });
        }
    }

    /**
     * 监听ws消息事件
     * @param $ws
     * @param $frame
     */
    public function onMessage($ws, $frame)
    {
        echo "ser-push-message:{$frame->data}\n";// websocket.send("hello!");
        // todo
        $data = [
            'task' => 1,
            'fd' => $frame->fd,
        ];
        $ws->task($data);
        // $serv->task($data , -1 );  -1代表不指定task进程

        // 指定的时间后执行函数，异步，不同于sleep()
        swoole_timer_after(5000, function () use ($ws, $frame) {
            echo "5s-after\n";
            $ws->push($frame->fd, "server-time-after:");
        });
        $ws->push($frame->fd, "server-push:" . date("Y-m-d H:i:s"));
    }

    /**
     * 处理耗时任务（邮件、广播）
     * @param $serv
     * @param $taskId 任务id
     * @param $workerId 投递任务的worker_id
     * @param $data
     */
    public function onTask($serv, $taskId, $workerId, $data)
    {
        print_r($data);
        // 耗时场景 5s
        sleep(5);
        return "on task finish"; // 告诉worker
    }

    /**
     * @param $serv
     * @param $taskId
     * @param $data 任务处理的结果内容(return "on task finish";)
     */
    public function onFinish($serv, $taskId, $data)
    {
        echo "taskId:{$taskId}\n";
        echo "finish-data-sucess:{$data}\n";
    }

    /**
     * close
     * @param $ws
     * @param $fd
     */
    public function onClose($ws, $fd)
    {
        echo "clientid:{$fd} closed\n";
    }
}

$obj = new Ws();