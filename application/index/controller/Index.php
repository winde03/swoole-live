<?php

namespace app\index\controller;

use app\common\lib\ali\Sms;

class Index
{
    public function index()
    {
        print_r($_GET);
        return '12e121561e2165';
    }

    public function hello($name = 'ThinkPHP5')
    {
        return 'hello,' . $name;
    }

    public function sms()
    {
        try {
            Sms::sendSms(15606073818, 615702);
        } catch (\Exception $e) {

        }
    }
}
