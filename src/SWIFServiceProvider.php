<?php

namespace Macc\Laravel\SWIF;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Cache;
use Macc\Laravel\SWIF\CacheFatory;

class SWIFServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'swif.php' => app()->path().'/config/'.('swif.php'),
        ]);
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('macc.swif.filter', function ($app) {
            //update words list
            if(file_exists(config('swif.blacklist.path')))
            {
                $cache = app('cache');
                $defaultCacheName = env('DEFAULT_CACHE_NAME','redis');

                $cacheObj = CacheFatory::build($defaultCacheName);
                $wordsAdapter = new \SIWF\Words\FileWordsAdapter(config('swif.blacklist.path'));
                $builder = new \SIWF\Tree\Builder($wordsAdapter);
                $storage = CacheFatory::buildStorageAdapter($defaultCacheName,['builder'=>$builder,'cacheObj'=>$cacheObj]);
                $resStorage = CacheFatory::buildAdapter($defaultCacheName,['cacheObj'=>$cacheObj]);
                $mtime = filemtime(config('swif.blacklist.path'));

                if($mtime - $cacheObj->get('siwf_blacklist_create_time') >0)
                {
                    $cacheObj->set('siwf_blacklist_create_time',time());
                    $storage->clear();
                    $resStorage->clear();
                }
            }else{
                throw new \Exception('Not found black list dic file!');
            }


            //$filter = new \SIWF\Filter\Filter($storage);
            $filter = new \SIWF\Filter\CachedFilter($storage,$resStorage);

            return $filter;
        });
    }
}
