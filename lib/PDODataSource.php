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
     * @var array
     */
    private $words;

    /**
     * PDODataSource constructor.
     * @param array $config
     */
    public function __construct($config = [])
    {
        $this->config = $config;
        try {
            $this->pdo = new \PDO($this->config['dsn'], $this->config['username'], $this->config['password']);
            $query = "SELECT `{$this->config['field']}` FROM `{$this->config['table']}`";
            $stmt = $this->pdo->query($query);
            while ($row = $stmt->fetch()) {
                $this->words[] = $row[$this->config['field']];
            }
        } catch (\Exception $e) {
            throw new RuntimeException(sprintf('Loading PDO error:%s', iconv('gbk', 'utf-8', $e->getMessage())));
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
        $stmt = $this->pdo->prepare("SELECT `{$this->config['field']}` FROM `{$this->config['table']}` WHERE `{$this->config['field']}` = :word");
        $stmt->execute(['word' => $word]);
        $search = $stmt->fetch(\PDO::FETCH_COLUMN);
        if ($word && $search === false) {
            if ($this->pdo->prepare("INSERT INTO {$this->config['table']} ({$this->config['field']}) VALUES (?)")->execute([$word])) {
                $this->words[] = $word;
            }
        }
    }

    /**
     * @param string $word
     */
    public function deleteWord(string $word)
    {
        $word = $this->filterWord($word);
        $stmt = $this->pdo->prepare("SELECT `{$this->config['field']}` FROM `{$this->config['table']}` WHERE `{$this->config['field']}` = :word");
        $stmt->execute(['word' => $word]);
        $search = $stmt->fetch(\PDO::FETCH_COLUMN);
        if ($word && $search !== false) {
            $stmt = $this->pdo->prepare("DELETE FROM {$this->config['table']} WHERE {$this->config['field']} = ? LIMIT 1");
            $stmt->execute([$word]);
            if ($stmt->rowCount() === 1) {
                if (($index = array_search($word, $this->words)) !== false) {
                    unset($this->words[$index]);
                }
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

}