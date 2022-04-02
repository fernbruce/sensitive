<?php

namespace Refinelib\Sensitive\cache;

interface CacheInterface extends \ArrayAccess
{

    public function get($key);

    public function set($key, $value, $duration = null);

}