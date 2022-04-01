<?php


namespace Refinelib\Sensitive;

/**
 * Interface SensitiveInterface
 * @package Refinelib\Sensitive
 */
interface SensitiveInterface
{
    /**
     * @param string $content
     * @param int $matchType
     * @param int $wordNum
     * @return array
     */
    public function getSensitiveWords(string $content, int $matchType = 1, int $wordNum = 0): array;

    /**
     * @param string $content
     * @param string $sTag
     * @param string $eTag
     * @param int $matchType
     * @return string
     */
    public function mark(string $content, string $sTag, string $eTag, int $matchType = 1): string;

    /**
     * @param string $content
     * @param string $replaceChar
     * @param bool $repeat
     * @param int $matchType
     * @return string
     */
    public function replace(string $content, string $replaceChar = '', bool $repeat = false, int $matchType = 1): string;

    /**
     * @param string $content
     * @return bool
     */
    public function isLegal(string $content): bool;
}