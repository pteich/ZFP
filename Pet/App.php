<?php
/**
 * Abstract class for a service class that should use caching
 * Used for service classes, API access
 */
 
abstract class Pet_App {

    /**
     * Singleton pattern implementation makes "new" unavailable
     *
     * @return void
     */
    protected function __construct()
    {}

    /**
     * Singleton pattern implementation makes "clone" unavailable
     *
     * @return void
     */
    protected function __clone()
    {}

    // without late static binding both methods have to be implemanted in final class
    abstract protected static function getInstance();
    abstract protected static function getCachedInstance();

    /**
     * Returns the cached version of this object or as fallback the object itself
     * @static
     * @param $object
     * @return Zend_Cache_Core|Pet_App
     */
    protected static function setupCache($object)
    {
        $cache = self::getCache();
        $frontendOptions['cached_entity'] = $object;
        $frontendOptions['non_cached_methods'] = array('getCached', 'cleanCache', 'enableLogging', 'getNonCached', '_setupCache');
        try {
            $cache = Zend_Cache::factory('Class', $cache->getBackend(), $frontendOptions);
            $cache->setTagsArray(array('mapper',get_class($object)));
            return $cache;
        } catch (Exception $e) {
            return $object;
        }
    }

    /**
     * Returns an instance of Zend_Cache
     * It first looks for a cache resource if that not exists cachemanager ist checked for an app cache
     * @return Zend_Cache_Core
     */
    protected static function getCache()
    {
        $cache = null;
        $bootstrap = Zend_Controller_Front::getInstance()->getParam('bootstrap');
        if ($bootstrap->hasPluginResource('cache')) {
            $cache = $bootstrap->getResource('cache');
        } elseif ($bootstrap->hasPluginResource('cachemanager')) {
            $cache = $bootstrap->getResource('cachemanager')->getCache('app');
        }
        return $cache;
    }

    /**
     * Cleans the cache for this class
     * @return void
     */
    public function cleanCache()
    {
        $cache = self::getCache();
        $cache->clean(Zend_Cache::CLEANING_MODE_MATCHING_TAG,array(get_class($this)));
    }


}
