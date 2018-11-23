<?php
require_once __DIR__ . "/../application/common/lib/redis/Predis.php";

/**
 * Class Ws
 * websocket基于http，执行WS可拥有HTTP的特性，可通过http形式访问
 */
class Ws
{

    CONST HOST = "0.0.0.0";
    CONST PORT = 8811;
    CONST CHART_PORT = 8812;

    public $ws = null;

    public function __construct()
    {
        // 获取sMembers 有值应清空
        $clients = \app\common\lib\redis\Predis::getInstance()->sMembers('live_game_key');
        foreach ($clients as $fd) {
            \app\common\lib\redis\Predis::getInstance()->sRem("live_game_key", $fd);
        }

        $this->ws = new swoole_websocket_server(self::HOST, self::PORT);

        $this->ws->listen(self::PORT, self::CHART_PORT, SWOOLE_SOCK_TCP);

        $this->ws->set([
            'enable_static_handler' => true,
            'document_root' => "/var/www/html/swoole-live/public/static",
            'task_worker_num' => 4,
            'worker_num' => 4
        ]);

        $this->ws->on("open", [$this, 'onOpen']);
        $this->ws->on('message', [$this, 'onMessage']);
        $this->ws->on('request', [$this, 'onRequest']);
        $this->ws->on('workerstart', [$this, 'onWorkerStart']);
        $this->ws->on('task', [$this, 'onTask']);
        $this->ws->on('finish', [$this, 'onFinish']);
        $this->ws->on('close', [$this, 'onClose']);

        $this->ws->start();
    }

    public function onOpen($ws, $request)
    {
        var_dump($request->fd);
        //记录连接用户：fd放入redis有序集合里
        \app\common\lib\redis\Predis::getInstance()->sAdd(config("redis.live_game_key"), $request->fd);
    }

    public function onMessage($ws, $frame)
    {
        echo "ser-push-message:{$frame->data}\n";
        $ws->push($frame->fd, "server-push:" . date("Y-m-d H:i:s"));
    }

    public function onRequest($request, $response)
    {
//        去除favicon.ico 请求，减少资源损耗
        if ($request->server['request_uri'] == '/favicon.ico') {
            $response->status(404);
            $response->end();
            return ;
        }

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
        $_FILES = [];
        if (isset($request->files)) {
            foreach ($request->files as $k => $v) {
                $_FILES[$k] = $v;
            }
        }
        $_POST = [];
        if (isset($request->post)) {
            foreach ($request->post as $k => $v) {
                $_POST[$k] = $v;
            }
        }

//        $this->writeLog(); //取消注释则开启日志功能

        $_POST['http_server'] = $this->ws;

        ob_start();
        // 执行应用并响应
        try {
            think\Container::get('app', [APP_PATH])
                ->run()
                ->send();
        } catch (\Exception $e) {

        }

        $res = ob_get_contents();
        ob_end_clean();
        $response->end($res);
    }

    public function onStart($server) {
        swoole_set_process_name("live_master");// 设置主进程名
    }

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
        //分发task任务，不同任务对应不同逻辑
        $obj = new app\common\lib\task\Task();
        $method = $data['method'];
        $flag = $obj->$method($data['data'], $serv);
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

    public function onClose($ws, $fd)
    {
        echo "Close:{$fd}\n";
        // fd从redis集合删除
        \app\common\lib\redis\Predis::getInstance()->sRem(config("redis.live_game_key"), $fd);
    }

    // 用户行为记录到日志
    public function writeLog()
    {
        $datas = array_merge(['date' => date('Y-m-d H:i:s')], $_GET, $_POST, $_SERVER);
        $logs = '';
        foreach ($datas as $key => $value) {
            $logs = $key . ':' . $value . " ";
            // 异步io写入
            swoole_async_writefile(APP_PATH . "../runtime/log/" . date('Ymd') . '_access.log', $logs . PHP_EOL, function ($filename) {
                // todo
            }, FILE_APPEND);
        }
    }
}

new Ws();

// 平滑重启：再不重启服务器的情况下使修改的代码生效（更新修改的文件）   执行 reload.sh
// https://wiki.swoole.com/wiki/page/p-server/reload.html