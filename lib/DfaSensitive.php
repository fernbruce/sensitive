<?php


namespace Refinelib\Sensitive;


class DfaSensitive implements SensitiveInterface
{
    /**
     * @var array
     */
    private $wordTree;


    public function setTree($sensitiveWords = null)
    {
        $sensitiveWords = [
            '葛优躺',
//            '葛优瘫',
//            '最',
//            '最好',
//            '最优',
//            '最爱'
        ];
        $this->wordTree = [];
        foreach ($sensitiveWords as $word) {
            $this->addSensitiveWord($word);
        }
        return $this;
    }

    public function addSensitiveWord(string $word)
    {
        $tree = &$this->wordTree;

        $wordLength = mb_strlen($word, 'utf-8');
        for ($i = 0; $i < $wordLength; $i++) {
            $keyChar = mb_substr($word, $i, 1, 'utf-8');

            // 获取子节点树结构
            $tempTree = $tree[$keyChar] ?? [];

            if ($tempTree) {
                $tree = &$tempTree;
            } else {
                // 设置标志位
                $newTree = [];
                $newTree['ending'] = false;

                // 添加到集合
                $tree[$keyChar] = $newTree;
                $tree = &$newTree;
            }

            // 到达最后一个节点
            if ($i == $wordLength - 1) {
                $tree[$keyChar] = true;
            }
        }

        return;
        $arr = [
            '葛' => [
                'ending' => false,
                '优' => [
                    'ending' => false,
                    '躺' => [
                        'ending' => true,
                    ],
                    '瘫' => [
                        'ending' => true
                    ]
                ],
            ],
            '最' => [
                'ending' => true,
                '优' => [
                    'ending' => true,
                ],
                '爱' => [
                    'ending' => true,
                ],
            ],


        ];
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