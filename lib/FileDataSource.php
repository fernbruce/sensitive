<?php


namespace Refinelib\Sensitive;


use RuntimeException;

/**
 * Class FileDataSource
 * @package Refinelib\Sensitive
 */
class FileDataSource implements DataSourceInterface
{
    /**
     * @var string
     */
    private $file;
    /**
     * @var array
     */
    private $words;

    /**
     * FileDataSource constructor.
     * @param string $file
     */
    public function __construct(string $file)
    {
        if (!file_exists($file)) {
            throw new RuntimeException(sprintf('%s not exist', $file));
        }
        $this->file = $file;
        $this->words = array_filter(array_unique(array_map([$this, 'filterWord'], explode("\n", file_get_contents($file)))));
    }

    /**
     * @return array
     */
    public function getWords(): array
    {
        return $this->words;
    }

    /**
     * @param string $word
     */
    public function addWord(string $word)
    {
        $word = $this->filterWord($word);
        if ($word && (array_search($word, $this->words) === false)) {
            $this->words[] = $word;
            $this->save();
        }
    }

    private function save()
    {
        file_put_contents($this->file, implode("\n", $this->words));
    }

    /**
     * @param string $word
     */
    public function deleteWord(string $word)
    {
        $word = $this->filterWord($word);
        if ($word && (($index = array_search($word, $this->words)) !== false)) {
            unset($this->words[$index]);
            $this->save();
        }
    }

    /**
     * @param string $word
     * @return string
     */
    private function filterWord(string $word): string
    {
        return strtolower(str_replace([' ', '　'], '', $word));
    }

}