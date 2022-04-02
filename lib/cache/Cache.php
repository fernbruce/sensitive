<?php


namespace Refinelib\Sensitive\cache;


abstract class Cache implements CacheInterface
{
    public $keyPrefix;

    public $serializer;

    public $defaultDuration;

    private $_igbinaryAvailable = false;

    public function init()
    {
        $this->_igbinaryAvailable = \extension_loaded('igbinary');
    }

    public function buildKey($key)
    {
        if (is_string($key)) {
            $key = ctype_alnum($key) && \StringHelper::byteLength($key) <= 32 ? $key : md5($key);
        } else {
            if ($this->_igbinaryAvailable) {
                $serializeKey = igbinary_serialize($key);
            } else {
                $serializeKey = serialize($key);
            }

            $key = md5($serializeKey);
        }

        return $this->keyPrefix . $key;
    }

    public function get($key)
    {
        $key = $this->buildKey($key);
        $value = $this->getValue($key);
        if ($value === false || $this->serializer === false) {
            return $value;
        } elseif ($this->serializer === null) {
            $value = unserialize((string)$value);
        } else {
            $value = call_user_func($this->serializer[1], $value);
        }
        if (is_array($value)) {
            return $value[0];
        }

        return false;
    }

    public function exists($key)
    {
        $key = $this->buildKey($key);
        $value = $this->getValue($key);

        return $value !== false;
    }

}