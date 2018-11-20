<?php

class Http
{

    CONST HOST = "0.0.0.0";
    CONST PORT = 8811;

    public $http = null;

    public function __construct()
    {
        $this->http = new swoole_http_server(self::HOST, self::PORT);

        $this->http->set([
            'enable_static_handler' => true,
            'document_root' => "/var/www/swoole-live/public/static",
            'worker_num' => 4,
            'task_worker_num' => 4,
        ]);

        $this->http->on('request', [$this, 'onRequest']);
        $this->http->on('workerstart', [$this, 'onWorkerStart']);
        $this->http->on('task', [$this, 'onTask']);
        $this->http->on('finish', [$this, 'onFinish']);
        $this->http->on('close', [$this, 'onClose']);

        $this->http->start();
    }

    /**
     * request回调，处理输入的请求
     * @param $request
     * @param $response
     */
    public function onRequest($request, $response)
    {
        $_SERVER = [];
        if (isset($request->server)) {
            foreach ($request->server as $k => $v) {
                $_SERVER[strtoupper($k)] = $v;
            }
        }
        if (isset($request->header)) {
            foreach ($request->header as $k => $v) {
                $_SERVER[strtoupper($k)] = $v;
            }
        }
        $_GET = [];
        if (isset($request->get)) {
            foreach ($request->get as $k => $v) {
                $_GET[$k] = $v;
            }
        }
        $_POST = [];
        if (isset($request->post)) {
            foreach ($request->post as $k => $v) {
                $_POST[$k] = $v;
            }
        }
        $_POST['http_server'] = $this->http;

        ob_start();
        // 执行应用并响应
        think\Container::get('app', [APP_PATH])
            ->run()
            ->send();
        $res = ob_get_contents();
        ob_end_clean();
        $response->end($res);
    }

    /**
     * onWorkerStart回调
     * @param $server
     * @param $woker_id
     */
    public function onWorkerStart($server, $woker_id)
    {
        // 定义应用目录
        define('APP_PATH', __DIR__ . '/../application/');
        // 加载框架引导文件
        // require __DIR__ . '/../thinkphp/base.php';
        require __DIR__ . '/../thinkphp/start.php';
    }

    public function onTask($serv, $taskId, $wokerId, $data)
    {
        //分发task任务，不同任务对应不同逻辑(重点）
        $obj = new app \common\lib\task\Task;
        $method = $data['method'];
        $flag = $obj->$method($data['data']);
        return $flag;


//        $Sms = new app\common\lib\ali\Sms();
//        try {
//            $response = $Sms::sendSms($data['phone'], $data['code']);
//        } catch (\Exception $e) {
//            echo $e->getMessage();
//        }
    }

    public function onFinish($serv, $taskId, $data)
    {
        echo "taskId:{$taskId}\n";
        echo "finish-data-success:{$data}\n";//$data为onTask的return内容
    }

    public function onClose($http, $fd)
    {
        echo "Close:{$fd}\n";
    }
}

new Http();