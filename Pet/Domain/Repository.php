<?php
/**
 * Das Repository führt die DAOs (DBTable-Objekte) zusammen
 * Über das Array $inject wird definiert, welche DAOs geladen werden sollen (Lazy Loading)
 * Die DAOs stehen über die magischen Methoden getDaoXXX() zur Verfügung-
 *
 * @category   Eigene
 * @package    Pet
 * @subpackage Pet_Domain
 * @copyright  Copyright (c) 2009 Peter Teich
 */
abstract class Pet_Domain_Repository
{

    /* @var array Injects für DAO-Elemente */
    protected $inject = array();

    /* @var array DAO Array */
    protected $_daoRepository = array();

    /**
     * Singleton instance
     *
     * @var Pet_Domain_Repository
     */
    protected static $_instance = null;

    public function  __construct($inject = false)
    {
        if (is_array($inject)) {
            $this->inject = $inject;
        }
    }

    /**
     * Get property value.
     *
     * @param string $property
     * @return mixed
     * @throws Pet_Domain_Exception
     */
    public function __get($property)
    {
        $dao = strtolower(substr($property, 3));
        if (property_exists($this, $dao)) {
            return $this->$dao;
        } elseif (array_key_exists($dao, $this->_daoRepository)) {
            return $this->_daoRepository[$dao];
        } elseif (array_key_exists($dao, $this->inject)) {
            return $this->{
            "getDao" . $dao
            }();
        }
        throw new Pet_Domain_Exception('Undefined property: ' . $property);
    }

    /**
     * Catches calls of getDaoxxx - previously defined in inject array
     *
     * @param string $method
     * @param mixed $arguments
     * @return object
     * @throws Pet_Domain_Exception
     */
    public function __call($method, $arguments)
    {

        $type = strtolower(substr($method, 0, 6));
        $dao = strtolower(substr($method, 6));

        if ($type == 'getdao') {
            if (!array_key_exists($dao, $this->_daoRepository)) {
                if (array_key_exists($dao, $this->inject)) {
                    $this->_daoRepository[$dao] = new $this->inject[$dao];
                } else {
                    throw new Pet_Domain_Exception(sprintf('DAO %s has to be defined in inject property', $dao));
                }
            }
            return $this->_daoRepository[$dao];
        }

        $message = 'Invalid method call: ' . get_class($this) . '::' . $method . '()';
        throw new Pet_Domain_Exception($message);
    }

}
