<?php


namespace Refinelib\Sensitive;


class DfaSensitive implements SensitiveInterface
{
    /**
     * @var array
     */
    private $wordTree;


    public function addSensitiveWord(string $word) {
        $tree = &$this->wordTree;

        $wordLength = mb_strlen($word, 'utf-8');
        for ($i = 0; $i < $wordLength; $i++) {
            $keyChar = mb_substr($word, $i, 1, 'utf-8');

            // 获取子节点树结构
            $tempTree = isset($tree[$keyChar]) ?? $tree[$keyChar];

            if ($tempTree) {
                $tree = $tempTree;
            } else {
                // 设置标志位
                $newTree = [];
                $newTree[$keyChar] = false;

                // 添加到集合
                $tree[$keyChar] = $newTree;
                $tree = $newTree;
            }

            // 到达最后一个节点
            if ($i == $wordLength - 1) {
//                $tree->put('ending', true);
            }
        }

        return;
    }

    public function getSensitiveWords(string $content, int $matchType = 1, int $wordNum = 0): array
    {
        // TODO: Implement getSensitiveWords() method.
    }

    public function mark(string $content, string $sTag, string $eTag, int $matchType = 1): string
    {
        // TODO: Implement mark() method.
    }

    public function replace(string $content, string $replaceChar = '', bool $repeat = false, int $matchType = 1): string
    {
        // TODO: Implement replace() method.
    }

    public function isLegal(string $content): bool
    {
        // TODO: Implement isLegal() method.
    }


}