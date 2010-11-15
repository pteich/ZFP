<?php

class Model_UsersMapper extends Pet_Model_Mapper
{

    protected $tableName = 'Model_DbTable_Users';
    protected $entityName = 'Model_User';
    protected $collectionName = 'Model_Users';

    protected static $_instance = null;
    protected static $_cachedInstance = null;

    /**
     * @return Model_UsersMapper
     */
    public static function getInstance()
    {
        if (null === self::$_instance) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    /**
     * @return Model_UsersMapper
     */
    public static function getCachedInstance()
    {
        if (null === self::$_cachedInstance) {
            self::$_cachedInstance = self::setupCache(self::getInstance());
        }

        return self::$_cachedInstance;
    }

}

