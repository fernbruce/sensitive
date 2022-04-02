<?php

namespace Refinelib\Sensitive\cache;

interface CacheInterface extends \ArrayAccess
{
    public function buildKey($key);

    public function get($key);

    public function exists($key);

    public function set($key, $value, $duration = null);

    public function add($key, $value, $duration);

    public function delete($key);

    public function flush();

    public function getOrSet($key, $callable, $duration);

}