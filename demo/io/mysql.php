<?php

class AysMysql
{
    /**
     * @var string
     */
    public $dbSource = "";
    /**
     * mysql的配置
     * @var array
     */
    public $dbConfig = [];

    public function __construct()
    {
        $this->dbSource = new swoole_mysql();

        $this->dbConfig = [
            'host' => '127.0.0.1',
            'port' => 3306,
            'user' => 'root',
            'password' => '',
            'database' => 'swoole',
            'charset' => 'utf8',
        ];
    }

    /**
     * mysql 执行逻辑
     * @param $id
     * @param $username
     * @return bool
     */
    public function execute($id, $username)
    {
        // connect 最后执行（异步
        $this->dbSource->connect($this->dbConfig, function ($db, $result) use ($id, $username) {
            echo "mysql-connect" . PHP_EOL; //3
            if ($result === false) {
                var_dump($db->connect_error);
                // todo
            }

            //$sql = "select * from test where id=1";
            $sql = "update test set `name` = '" . $username . "' where id=" . $id;
            // insert into
            // query (add select update delete)
            $db->query($sql, function ($db, $result) {
                // select => result返回的是 查询的结果内容

                if ($result === false) {
                    // todo
                    var_dump($db->error);
                } elseif ($result === true) {// add update delete
                    // todo
                    var_dump($db->affected_rows); // 4
                } else {
                    print_r($result);
                }
                $db->close();
            });

        });
        return true; // 1
    }
}

$obj = new AysMysql();
$flag = $obj->execute(1, 'usernamsdqwd');
var_dump($flag) . PHP_EOL; // 1 return true; 此处先输出（异步
echo "start" . PHP_EOL; // 2


