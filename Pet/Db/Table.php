<?php
/**
 * Erweiterung der Zend_Db_Table für Caching
 * Class->getCached()->methode() liefert Aufruf aus Cache
 *
 * @copyright  Copyright (c) 2009 Peter Teich
 * @author     Peter Teich
 */


class Pet_Db_Table extends Zend_Db_Table
{

    /*
     * @var Zend_Db_Table
     */
    protected $_classcache = null;

    public function  __construct($config = array())
    {
        parent::__construct($config);
    }

    protected function _setupCache($object)
    {
        $bootstrap = Zend_Controller_Front::getInstance()->getParam('bootstrap');

        // Aktuelles Cache-Objekt holen
        if ($bootstrap->hasPluginResource('cache')) {
            $cache = $bootstrap->getResource('cache');
        } elseif ($bootstrap->hasPluginResource('cachemanager')) {
            $cache = $bootstrap->getResource('cachemanager')->getCache('database');
        }
        $frontendOptions['cached_entity'] = $object;
        $frontendOptions['non_cached_methods'] = array('getCached', 'cleanCache', 'enableLogging', 'getNonCached', '_setupCache');
        try {
            $this->_classcache = Zend_Cache::factory('Class', $cache->getBackend(), $frontendOptions);
        } catch (Exception $e) {
            throw($e);
        }

        $this->_classcache->setTagsArray(array('db_table_class_cache', get_class($object)));
        
    }

    /**
     * Returns a cached instance of the object
     * @return Zend_Db_Table_Abstract
     */
    public function getCached($enable = true)
    {
        if ($enable===true) {
            if ($this->_classscache===null) {
                $this->_setupCache($this);
            }
            return $this->_classcache;
        } else {
            return $this;
        }
    }

    /**
     * Returns a non-cached instance of the object
     * @return Zend_Db_Table_Abstract
     */
    public function getNonCached()
    {
        return $this;
    }

    /**
     * Returns all columns of the table with a defined prefix (default: table name)
     * @param bool|string $prefix
     * @return array
     */
    public function getColsWithPrefix($prefix=false)
    {        
        if (!$prefix) {
            $prefix = $this->info(Zend_Db_Table::NAME).'_';
        }
        $cols = $this->info(Zend_Db_Table::COLS);
        $prefixed_cols = array();
        foreach($cols as $col) {
            $prefixed_cols[$prefix.$col] = $col;
        }
        return $prefixed_cols;
    }

    /**
     * Enables loggin of cache activities
     * @return Zend_Db_Table_Abstract
     */
    public function enableLogging($logger = false)
    {
        if (!$this->_classscache) {
            $this->_setupCache($this);
        }
        if ($logger instanceof Zend_Log) {
            $this->_classcache->getBackend()->setDirectives(array('logging' => true, 'logger' => $logger));
        } else {
            $bootstrap = Zend_Controller_Front::getInstance()->getParam('bootstrap');
            if (!$bootstrap->hasPluginResource('log')) {
                throw new Zend_Exception('no instance of Zend_Log provides as argument and no log resource in bootstrap');
            }
            $logger = $bootstrap->getResource('log');
            $this->_classcache->getBackend()->setDirectives(array('logging' => true, 'logger' => $logger));
        }
        return $this;
    }

    /**
     * Cleans the object cache
     * @return void
     */
    public function cleanCache()
    {
        if (!$this->_classscache) {
            $this->_setupCache($this);
        }
        $this->_classcache->clean(Zend_Cache::CLEANING_MODE_MATCHING_TAG, array('db_table_class_cache', get_class($this)));
    }

}

