<?php

class Model_ArticlesMapper extends Pet_Model_Mapper
{

    protected $tableName = 'Model_DbTable_Audioservice';
    protected $entityName = 'Model_Article';
    protected $collectionName = 'Model_Articles';

    protected static $_instance = null;
    protected static $_cachedInstance = null;

    /**
     * @return Model_ArticlesMapper
     */
    public static function getInstance()
    {
        if (null === self::$_instance) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    /**
     * @return Model_ArticlesMapper
     */
    public static function getCachedInstance()
    {
        if (null === self::$_cachedInstance) {
            self::$_cachedInstance = self::setupCache(self::getInstance());
        }

        return self::$_cachedInstance;
    }

    /**
     * Fetches all articles from underlaying DB without depending users
     * @return Model_Articles
     */
    public function fetchAll()
    {
        $select = $this->getDbTable()->select();
        $select->order("datum DESC");
        $rows = $select->query()->fetchAll();

        return $this->initCollection($rows);
    }

    public function fetchAllWithUser()
    {
        $usersMapper = Model_UsersMapper::getCachedInstance();
        $select = $this->getDbTable()->select()->from(array('a'=>$this->getDbTable()->info(Pet_Db_Table::NAME)));
        $select->setIntegrityCheck(false);
        // join the users table with prefixed coloum names - ready for use
        $select->joinLeft(array('u'=>$usersMapper->getDbTable()->info(Pet_Db_Table::NAME)),'u.id=a.user_id',$usersMapper->getDbTable()->getColsWithPrefix());

        $rows = $select->query()->fetchAll(Zend_Db::FETCH_ASSOC);
        return $this->initCollection($rows);
    }

}

