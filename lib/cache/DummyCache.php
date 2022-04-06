<?php


namespace Refinelib\Sensitive\cache;

/**
 * Class DummyCache
 * @package Refinelib\Sensitive\cache
 */
class DummyCache implements CacheInterface
{
    /**
     * @param $key
     * @return bool
     */
    public function get($key)
    {
        return false;
    }

    /**
     * @param $key
     * @param $value
     * @param null $duration
     * @return bool
     */
    public function set($key, $value, $duration = null)
    {
        return true;
    }

}