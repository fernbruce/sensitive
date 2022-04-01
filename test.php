<?php
require __DIR__ . '/vendor/autoload.php';
$config = [
    'table' => 'qs_badword',
    'field' => 'badword',
    'debug' => true,
];
$pdo = new \PDO('mysql:host=192.168.100.27;dbname=www_haolietou_com;charset=utf8;port=3307', 'ruifan', 'ruifan123456');
$redis = new \Redis();
$redis->connect('127.0.0.1', 6379);
extract($config);
$PdoDataSource = new \Refinelib\Sensitive\PDODataSource($pdo, $redis, $table, $field);
//print_r($PdoDataSource->addWord('敏感词1'));
//print_r($PdoDataSource->addWord('敏感词2'));
//print_r($PdoDataSource->addWord('敏感词3'));
//print_r($PdoDataSource->addWord('敏感词4'));
//print_r($PdoDataSource->deleteWord('敏感词1'));
//print_r($PdoDataSource->deleteWord('敏感词2'));
print_r($PdoDataSource->deleteWord('敏感词3'));
print_r($PdoDataSource->getWords());