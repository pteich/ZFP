<?php

abstract class Pet_Model_Mapper {

    /**
     * @var Zend_Db_Table associated DAO for internal use only
     */
    protected $_dbTable = null;

    /**
     * Class names of DAO, model entity and model collection
     * @var string
     */
    protected $tableName = null;
    /** @var string */
    protected $entityName = null;
    /** @var string */
    protected $collectionName = null;

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

    abstract protected static function getInstance();
    abstract protected static function getCachedInstance();

    /**
     * @static
     * @param  $object
     * @return Zend_Cache_Frontend_Class
     */
    protected static function setupCache($object)
    {
        $cache = self::getCache();
        $frontendOptions['cached_entity'] = $object;
        $frontendOptions['non_cached_methods'] = array('getCached', 'cleanCache', 'enableLogging', 'getNonCached', '_setupCache');
        try {
            $cache = Zend_Cache::factory('Class', $cache->getBackend(), $frontendOptions);
            // set tags for cleaning object data only
            $cache->setTagsArray(array('mapper',get_class($object)));
            return $cache;
        } catch (Exception $e) {
            // in case of error return the original object
            return $object;
        }
    }

    /**
     * Returns a cache instance from bootstrap
     * @static
     * @return Zend_Cache_Core
     */
    protected static function getCache()
    {
        $cache = null;
        $bootstrap = Zend_Controller_Front::getInstance()->getParam('bootstrap');
        if ($bootstrap->hasPluginResource('cache')) {
            $cache = $bootstrap->getResource('cache');
        } elseif ($bootstrap->hasPluginResource('cachemanager')) {
            $cache = $bootstrap->getResource('cachemanager')->getCache('database');
        }
        return $cache;
    }

    /**
     * Cleans the object cache data
     * @return void
     */
    public function cleanCache()
    {
        $cache = self::getCache();
        $cache->clean(Zend_Cache::CLEANING_MODE_MATCHING_TAG,array(get_class($this)));
    }

    /**
     * @throws Exception
     * @param  Zend_Db_Adapter_Abstract $dbTable
     * @return Pet_Model_Mapper
     */
    public function setDbTable($dbTable) {
        if (is_string($dbTable)) {
            $dbTable = new $dbTable();
        }
        if (!$dbTable instanceof Zend_Db_Table_Abstract) {
            throw new Exception('Invalid table data gateway provided');
        }
        $this->_dbTable = $dbTable;
        return $this;
    }

    /**
     * @return Pet_Db_Table
     */
    public function getDbTable() {
        if (null === $this->_dbTable) {
            $this->setDbTable($this->tableName);
        }
        return $this->_dbTable;
    }

    /**
     * Returns associated entity model
     * @param array|object $data
     * @return Pet_Domain_Entity
     */
    public function initModel($data)
    {
        return new $this->entityName($data);
    }

    /**
     * @param array|object $rows
     * @return
     */
    public function initCollection($rows)
    {
        $objs = array();
        foreach ($rows as $row) {
            $objs[] = $this->initModel($row);
        }
        return new $this->collectionName($objs);
    }

    /**
     * Fetches model by primary key of associated DAO
     * @param int $id
     * @return Pet_Domain_Entity
     */
    public function fetchById($id)
    {
        return $this->initModel($this->getDbTable()->find($id)->current());
    }

    /**
     * @param  $data array|Pet_Domain_Entity
     * @return Pet_Domain_Entity
     */
    public function save($data)
    {
        if (key_exists('id',$data) && $data['id']>0) {
            $row = $this->getDbTable()->find($data['id'])->current();
        } else {
            $row = $this->getDbTable()->createRow();
        }

        foreach($data as $key=>$value) {
            if ($value) {
                $row->{$key} = $value;
            }
        }

        $row->save();
        $this->cleanCache();

        return $this->initModel($row);
    }


}