<?php

namespace Refinelib\Sensitive;

use Refinelib\Sensitive\cache\CacheInterface;
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
     * @param \PDO $pdo
     * @param CacheInterface $redis
     * @param string $table
     * @param string $field
     * @param string $key
     */
    public function __construct(\PDO $pdo, CacheInterface $redis, string $table, string $field, string $key = 'badwordsKey')
    {
        $this->pdo = $pdo;
        $this->redis = $redis;
        $this->table = $table;
        $this->field = $field;
        $this->key = $key;
    }

    /**
     * @return array
     */
    public function getWords(): array
    {
        if (!$words = json_decode($this->redis->get($this->key), true) ?: []) {
            $query = "SELECT `{$this->field}` FROM `{$this->table}`";
            $stmt = $this->pdo->query($query);
            $words = $stmt->fetch(\PDO::FETCH_COLUMN) ?: [];
            if (!empty($words)) {
                $words = json_decode($words, true);
            }
            $this->saveWordsToRedis($words);
        }
        return $words;
    }

    /**
     * @param string $word
     */
    public function addWord(string $word)
    {
        $word = $this->filterWord($word);
        $words = $this->getWords();
        if ($word && (array_search($word, $words) === false)) {
            $words[] = $word;
            if ($this->saveWordsToDb($words)) {
                $this->saveWordsToRedis($words);
            }
        }
    }

    /**
     * @param string $word
     */
    public function deleteWord(string $word)
    {
        $word = $this->filterWord($word);
        $words = $this->getWords();
        if ($word && (($index = array_search($word, $words)) !== false)) {
            unset($words[$index]);
            if ($this->saveWordsToDb($words)) {
                $this->saveWordsToRedis($words);
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

    /**
     * @param $words
     */
    private function saveWordsToRedis($words)
    {
        $this->redis->set($this->key, json_encode($words, JSON_UNESCAPED_UNICODE));
    }

    /**
     * @param $words
     * @return bool
     */
    private function saveWordsToDb($words)
    {
        return $this->pdo->prepare("REPLACE INTO {$this->table} (`{$this->key}`,`{$this->field}`) VALUES ('{$this->key}',?)")->execute([json_encode($words, JSON_UNESCAPED_UNICODE)]);
    }
}