<?php


namespace Refinelib\Sensitive\cache;

/**
 * Class RedisCache
 * @package Refinelib\Sensitive\cache
 */
class RedisCache implements CacheInterface
{
    /**
     * @var \Redis
     */
    private $connection;

    /**
     * RedisCache constructor.
     * @param $connection
     */
    public function __construct($connection)
    {
        $this->connection = $connection;
    }

    /**
     * @param $key
     * @return false|mixed|string
     */
    public function get($key)
    {
        return $this->connection->get($key);
    }

    /**
     * @param $key
     * @param $value
     * @param int $duration
     * @return bool
     */
    public function set($key, $value, $duration = 0)
    {
        $result = $this->connection->set($key, $value);
        if ($duration > 0) {
            $this->connection->expire($key, $duration);
        }
        return $result;
    }

}