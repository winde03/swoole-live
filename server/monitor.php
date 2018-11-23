<?php
/**
 * 监控服务 ws http 8811
 * 定时器检测端口号
 */

class Monitor
{
    CONST PORT = 8811;

    public function port()
    {
        $shell = "netstat -anp 2>/dev/null | grep " . self::PORT . " | grep LISTEN | wc -l";

        $result = shell_exec($shell);
        if ($result != 1) {
            // 发送报警服务 邮件 短信
            /// todo
            echo date("Ymd H:i:s") . "error" . PHP_EOL;
        } else {
            echo date("Ymd H:i:s") . "succss" . PHP_EOL;
        }
    }
}

// nohup 不挂断地运行命令并输出到日志
//eg: nohub php monitor.php > /var/access.txt
// tail -f access.txt 实时刷新txt内容
swoole_timer_tick(2000, function ($timer_id) {
    (new Monitor())->port();
    echo "time-start" . PHP_EOL;
});
