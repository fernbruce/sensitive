<?php


namespace Refinelib\Sensitive;

/**
 * Interface DataSourceInterface
 * @package Refinelib\Sensitive
 */
interface DataSourceInterface
{
    /**
     * @return array
     */
    public function getWords(): array;

    /**
     * @param string $word
     */
    public function addWord(string $word);

    /**
     * @param string $word
     */
    public function deleteWord(string $word);

}