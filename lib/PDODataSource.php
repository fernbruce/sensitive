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
    public function __construct($pdo, $redis, $table, $field, $debug = false)
    {
        $this->pdo = $pdo;
        $this->redis = $redis;
        $this->table = $table;
        $this->field = $field;
        $this->debug = $debug;

        if (!$this->words = $this->getWordsFromRedis()) {
            $query = "SELECT `{$this->field}` FROM `{$this->table}`";
            $stmt = $this->pdo->query($query);
            while ($row = $stmt->fetch()) {
                $this->words[] = $row[$this->field];
            }
            $this->redis->set('badwordsKey', json_encode($this->words, JSON_UNESCAPED_UNICODE));
            echo('get from mysql');
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
        $stmt = $this->pdo->prepare("SELECT `{$this->field}` FROM `{$this->table}` WHERE `{$this->field}` = :word");
        $stmt->execute(['word' => $word]);
        $search = $stmt->fetch(\PDO::FETCH_COLUMN);
        if ($word && $search === false) {
            if ($this->pdo->prepare("INSERT INTO {$this->table} ({$this->field}) VALUES (?)")->execute([$word])) {
                $this->words[] = $word;
                $this->redis->set('badwordsKey', json_encode($this->words, JSON_UNESCAPED_UNICODE));
            }
        }
    }

    /**
     * @param string $word
     */
    public function deleteWord(string $word)
    {
        $word = $this->filterWord($word);
        $stmt = $this->pdo->prepare("SELECT `{$this->field}` FROM `{$this->table}` WHERE `{$this->field}` = :word");
        $stmt->execute(['word' => $word]);
        $search = $stmt->fetch(\PDO::FETCH_COLUMN);
        if ($word && $search !== false) {
            $stmt = $this->pdo->prepare("DELETE FROM {$this->table} WHERE {$this->field} = ? LIMIT 1");
            $stmt->execute([$word]);
        }

        if ($word && ($index = array_search($word, $this->words)) !== false) {
            unset($this->words[$index]);
            $this->redis->set('badwordsKey', json_encode($this->words, JSON_UNESCAPED_UNICODE));
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

    private function getWordsFromRedis()
    {
        if (!$this->debug) {
            return json_decode($this->redis->get('badwordsKey'), true);
        }
        return [];
    }
}