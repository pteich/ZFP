<?php
/**
 * Einzelnes echtes Model
 *
 * @category   Eigene
 * @package    Pet
 * @subpackage Pet_Domain
 * @copyright  Copyright (c) 2009 Peter Teich
 * @abstract
 */

abstract class Pet_Domain_Entity
{
    /**
     * Class properties
     * @var array
     */
    protected $properties = array();

    protected $data = array();

    /**
     * Constructor.
     *
     * @param array $properties
     * @return void
     */
    public function __construct($properties)
    {
        if (is_object($properties)) {
            $properties = $properties->toArray();
        }
        $this->define($properties);
    }

    /**
     * Define class properties.
     *
     * @param array $properties
     * @return void
     */
    public function define(array $properties)
    {
        foreach ($properties as $property => $value) {
            if (property_exists($this, $property)) {
                $message = sprintf('Class property "%s" already defined.', $property);
                throw new Pet_Domain_Exception($message);
            }
            $this->properties[$property] = $value;
        }
    }

    /**
     * Get property value.
     *
     * @param string $property
     * @return mixed
     * @throws App_Domain_Exception
     */
    public function __get($property)
    {
        if (array_key_exists($property, $this->properties)) {
            return $this->properties[$property];
        } elseif (property_exists($this, $property)) {
            return $this->$property;
        }
        throw new Pet_Domain_Exception('Undefined property: ' . $property);
    }

    /**
     * Set property value.
     *
     * @param string $property
     * @param mixed $value
     * @return void
     * @throws App_Domain_Exception
     */
    public function __set($property, $value)
    {
        if (array_key_exists($property, $this->properties)) {
            $this->properties[$property] = $value;
        } elseif (property_exists($this, $property)) {
            $this->$property = $value;
        } else {
            throw new Pet_Domain_Exception('Invalid property: ' . $property);
        }
    }

    /**
     * Check if property exists
     *
     * @param string $property
     * @return boolean
     */
    public function __isset($property)
    {
        if (isset($this->properties[$property]) || property_exists($this, $property)) {
            return true;
        }
        return false;
    }

    /**
     * Enter description here...
     *
     * @param string $property
     * @return void
     */
    public function __unset($property)
    {
        if (isset($this->$property)) {
            $this->$property = null;
        }
    }

    /**
     * Create setter and getter methods.
     *
     * @param string $method
     * @param array $args
     * @return mixed
     * @throws App_Domain_Exception
     */
    public function __call($method, $args)
    {
        $type = substr($method, 0, 3);
        $property = strtolower($method[3]) . substr($method, 4);

        if ('get' === $type) {
            if (array_key_exists($property, $this->properties)) {
                return $this->properties[$property];
            } elseif (property_exists($this, $property)) {
                return $this->$property;
            }
        } elseif ('set' === $type) {
            if (array_key_exists($property, $this->properties)) {
                $this->properties[$property] = $args[0];
                return;
            } elseif (property_exists($this, $property)) {
                $this->$property = $args[0];
                return;
            }
        }

        $message = 'Invalid method call: ' . get_class($this).'::'.$method.'()';
        throw new Pet_Domain_Exception($message);
    }

    public function toArray()
    {
        return $this->properties;
    }

    /**
     * Returns a submodel by checking for existing coloumns with prefix or initializing a new object
     * @param  string $mapper name of mapper class
     * @param  int $id id of sub model
     * @return Pet_Domain_Entity
     */
    protected function initSubModel($mapper,$id)
    {
        $classname = get_class($mapper);
        if (!$this->data[$classname.'_'.$id]) {
            // get coloumn names with prefix
            try {
                $cols = $mapper->getDbTable()->getColsWithPrefix();
            } catch (Exception $e) {
                $cols = array();
            }
            // check if coloumns exist in model data
            if(count(array_diff_key($cols,$this->properties))<=0) {
                $data = array();
                foreach($cols as $key=>$col) {
                    $data[$col] = $this->properties[$key];
                }
                // init model with existing data
                $this->data[$classname.'_'.$id] = $mapper->initModel($data);
            } else {
                // load model from database using provided id
                $this->data[$classname.'_'.$id] = $mapper->fetchById($id);
            }
        }
        return $this->data[$classname.'_'.$id];
    }

}
