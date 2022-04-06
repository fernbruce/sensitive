<?php

namespace Refinelib\Sensitive\cache;

/**
 * Interface CacheInterface
 * @package Refinelib\Sensitive\cache
 */
interface CacheInterface
{
    /**
     * @param $key
     * @return string|bool
     */
    public function get($key);

    /**
     * @param $key
     * @param $value
     * @param int $duration
     * @return bool
     */
    public function set($key, $value, $duration = 0);

}