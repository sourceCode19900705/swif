<?php

namespace Macc\Laravel\SWIF;

class CacheFatory
{
    public static function build($cacheName){

        $cache = app('cache');
        if($cacheName == 'memcached') {
            return $cache->store('memcached')->getStore()->getMemcached();
        }
        if($cacheName == 'redis') {
            return $cache->store('redis')->getStore()->getRedis();
        }
    }

    public static function buildStorageAdapter($cacheName,$params){

        if($cacheName == 'memcached') {
            return  new \SIWF\Storage\MemcachedStorageAdapter($params['builder'],$params['cacheObj']);
        }
        if($cacheName == 'redis') {
            return  new \SIWF\Storage\RedisStorageAdapter($params['builder'],$params['cacheObj']);
        }
    }

    public static function buildAdapter($cacheName,$params){

        if($cacheName == 'memcached') {
            return  new \SIWF\Filter\Result\MemcachedAdapter($params['cacheObj']);
        }
        if($cacheName == 'redis') {
            return  new \SIWF\Filter\Result\RedisAdapter($params['cacheObj']);
        }
    }
}