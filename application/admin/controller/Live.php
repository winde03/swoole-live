<?php

namespace app\admin\controller;

use app\common\lib\Util;

class Live
{
    public function index()
    {
        //赛况信息入库、组装数据、push到直播页面
        $_POST['http_server']->push(2, 'hello-push-data');
    }

}
