<?php
require __DIR__ . '/vendor/autoload.php';
$config = [
    'username' => 'ruifan',
    'password' => 'ruifan123456',
    'dsn' => 'mysql:host=192.168.100.27;dbname=www_haolietou_com;charset=utf8;port=3307',
    'table' => 'qs_badword',
    'field' => 'badword',
];
$PdoDataSource = new \Refinelib\Sensitive\PDODataSource($config);
//print_r($PdoDataSource->addWord('敏感词1'));
print_r($PdoDataSource->deleteWord('敏感词1'));
print_r($PdoDataSource->getWords());