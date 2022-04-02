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
     * PDODataSource constructor.
     * @param array $config
     */
    public function __construct($pdo, $redis, $table, $field)
    {
        $this->pdo = $pdo;
        $this->redis = $redis;
        $this->table = $table;
        $this->field = $field;

        if (!$this->words = $this->getWordsFromRedis()) {
            $query = "SELECT `{$this->field}` FROM `{$this->table}`";
            $stmt = $this->pdo->query($query);
            $this->words = $stmt->fetchAll(\PDO::FETCH_COLUMN);
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
                $result = $this->pdo->prepare("INSERT INTO {$this->table} ({$this->field}) VALUES (?)")->execute([$word]);
                if (!$result) {
                    throw new \PDOException('数据库异常，写入失败');
                }
                $this->words[] = $word;
                $this->saveWordsToRedis();
            } catch (\PDOException $e) {
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
                $stmt = $this->pdo->prepare("DELETE FROM {$this->table} WHERE {$this->field} = ? LIMIT 1");
                $stmt->execute([$word]);
                if (!$stmt->rowCount()) {
                    throw new \PDOException('数据库异常，删除失败');
                }
                unset($this->words[$index]);
                $this->saveWordsToRedis();
            } catch (\PDOException $e) {
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
        $this->redis->set('badwordsKey', json_encode($this->words, JSON_UNESCAPED_UNICODE));
    }

    /**
     * @return array
     */
    private function getWordsFromRedis()
    {
        return json_decode($this->redis->get('badwordsKey'), true) ?: [];
    }
}