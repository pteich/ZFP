<?php

class Model_Article extends Pet_Domain_Entity
{

    /**
     * @return Audioservice_Model_User
     */
    public function getUser()
    {
        return $this->initSubModel(Model_UsersMapper::getCachedInstance(),$this->getUser_id());
    }

    /**
     * Example for an overwritten magic getter
     * @return Zend_Date
     */
    public function getDate()
    {
        return new Zend_Date($this->date,Zend_Date::ISO_8601);
    }

}

