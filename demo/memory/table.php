<?php
// 使用场景：多进程数据共享

// 创建内存表
$table = new swoole_table(1024);

// 内存表增加一列
$table->column('id', $table::TYPE_INT, 4);
$table->column('name', $table::TYPE_STRING, 64);
$table->column('age', $table::TYPE_INT, 3);
$table->create();

//数据的key，相同的key对应同一行数据，如果set同一个key，会覆盖上一次的数据
// []必须是一个数组，必须与字段定义的$name完全相同
$table->set('singwa_imooc', ['id' => 1, 'name'=> 'singwa', 'age' => 30]);
// 另外一种方案
$table['singwa_imooc_2'] = [
    'id' => 2,
    'name' => 'singwa2',
    'age' => 31,
];

$table->decr('singwa_imooc_2', 'age', 2); // age - 2 = 29
print_r($table->get('singwa_imooc_2'));

echo "delete start:".PHP_EOL;
$table->del('singwa_imooc_2');

print_r($table->get('singwa_imooc_2'));
