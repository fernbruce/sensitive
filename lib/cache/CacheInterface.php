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
     * @return mixed
     */
    public function get($key);

    /**
     * @param $key
     * @param $value
     * @param int $duration
     * @return mixed
     */
    public function set($key, $value, $duration = 0);

}