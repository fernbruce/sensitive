<?php

namespace Refinelib\Sensitive;

use RuntimeException;

/**
 * Class PDODataSource
 * @package Refinelib\Sensitive
 */
class PDODataSource implements DataSourceInterface
{
    /**
     * @var \PDO
     */
    private $pdo;

    /**
     * @var \Redis
     */
    private $redis;
    /**
     * @var array
     */
    private $words;

    /**
     * @var string
     */
    private $table;

    /**
     * @var string
     */
    private $field;

    /**
     * @var string
     */
    private $key;

    /**
     * PDODataSource constructor.
     * @param $pdo
     * @param $redis
     * @param $table
     * @param $field
     */
    public function __construct($pdo, $redis, $table, $field, $key = 'badwordsKey')
    {
        $this->pdo = $pdo;
        $this->redis = $redis;
        $this->table = $table;
        $this->field = $field;
        $this->key = $key;

        if (!$this->words = $this->getWordsFromRedis()) {
            $query = "SELECT `{$this->field}` FROM `{$this->table}`";
            $stmt = $this->pdo->query($query);
            $words = $stmt->fetch(\PDO::FETCH_COLUMN);
            if (empty($words)) {
                $this->words = [];
            } else {
                $this->words = json_decode($words, true);
            }
            $this->saveWordsToRedis();
        }
    }

    /**
     * @return array
     */
    public function getWords(): array
    {
        return $this->words ?: [];
    }

    /**
     * @param string $word
     */
    public function addWord(string $word)
    {
        $word = $this->filterWord($word);
        if ($word && (array_search($word, $this->words) === false)) {
            try {
                $this->words[] = $word;
                $result = $this->pdo->prepare("REPLACE INTO {$this->table} (`{$this->key}`,{$this->field}) VALUES ('{$this->key}',?)")->execute([json_encode($this->words, JSON_UNESCAPED_UNICODE)]);
                if (!$result) {
                    throw new \PDOException('数据库异常，写入失败');
                }
                $this->saveWordsToRedis();
            } catch (\PDOException $e) {
                $index = array_search($word, $this->words);
                unset($this->words[$index]);
            }
        }
    }

    /**
     * @param string $word
     */
    public function deleteWord(string $word)
    {
        $word = $this->filterWord($word);
        if ($word && (($index = array_search($word, $this->words)) !== false)) {
            try {
                unset($this->words[$index]);
                $result = $this->pdo->prepare("REPLACE INTO {$this->table} (`{$this->key}`,`{$this->field}`) VALUES ('{$this->key}',?)")->execute([json_encode($this->words, JSON_UNESCAPED_UNICODE)]);
                if (!$result) {
                    throw new \PDOException('数据库异常，删除失败');
                }
                $this->saveWordsToRedis();
            } catch (\PDOException $e) {
                $this->words[$index] = $word;
            }
        }
    }

    /**
     * @param string $word
     * @return string
     */
    private function filterWord(string $word): string
    {
        return strtolower(str_replace([' ', '  '], '', $word));
    }

    private function saveWordsToRedis()
    {
        $this->redis->set($this->key, json_encode($this->words, JSON_UNESCAPED_UNICODE));
    }

    /**
     * @return array
     */
    private function getWordsFromRedis()
    {
        return json_decode($this->redis->get($this->key), true) ?: [];
    }
}