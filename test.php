<?php
require __DIR__ . '/vendor/autoload.php';
try {
    $pdo = new \PDO('mysql:host=192.168.100.27;dbname=www_haolietou_com;charset=utf8;port=3307', 'ruifan', 'ruifan123456');
    $connection = new \Redis();
    $connection->connect('127.0.0.1', 6379);
    $redis = new \Refinelib\Sensitive\cache\RedisCache($connection);
//    $redis = new \Refinelib\Sensitive\cache\DummyCache();
    $table = 'qs_badwords';
    $field = 'badwords';
    $PdoDataSource = new \Refinelib\Sensitive\PDODataSource($pdo, $redis, $table, $field);
//    print_r($PdoDataSource->addWord('敏感词1'));
//    print_r($PdoDataSource->addWord('敏感词2'));
//    print_r($PdoDataSource->addWord('敏感词3'));
//    print_r($PdoDataSource->addWord('敏感词4'));
//    print_r($PdoDataSource->deleteWord('敏感词1'));
//    print_r($PdoDataSource->deleteWord('敏感词2'));
//    print_r($PdoDataSource->deleteWord('敏感词3'));
//    print_r($PdoDataSource->deleteWord('敏感词4'));
    $words = $PdoDataSource->getWords();
    $dfaSensitive = new \Refinelib\Sensitive\DfaSensitive($words);
//    print_r($words);
//    var_dump($dfaSensive);
    var_dump($dfaSensitive->setTree());


} catch (\Exception $e) {
    exit(iconv('gbk', 'utf-8', 'error:' . $e->getMessage()));
}