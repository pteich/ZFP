<?php
/**
 * Collection von Pet_Domain_Entities
 *
 * @category   Domain
 * @package    Pet
 * @subpackage Pet_Domain
 * @abstract
 */

abstract class Pet_Domain_Collection implements Countable, Iterator, ArrayAccess
{
    /**
     * @var array elements.
     */
    protected $_elements = array();

    /**
     * @var integer The current Iterator index.
     */
    protected $_iteratorIndex = 0;

    /**
     * @var integer The total number of elements.
     */
    protected $_iteratorCount = 0;

    /**
     * Class constructor.
     *
     * @param array $elements
     * @param string|null $elementType
     */
    public function __construct($elements = array(), $elementType = null)
    {
        if (is_array($elements)) {
            foreach ($elements as $element) {
                if (null !== $elementType && !($element instanceof $elementType)) {
                    $message = sprintf('%s is not an instance of %s', get_class($element), $elementType);
                    throw new Pet_Domain_Exception($message);
                }
                $this->append($element);
            }
        }
    }

    /**
     * Returns the number of elements.
     *
     * @return integer Option count
     */
    public function count()
    {
        return $this->_iteratorCount;
    }

    /**
     * Returns the current element.
     *
     * @return mixed The current element or FALSE if at the end of elements array.
     */
    public function current()
    {
        return current($this->_elements);
    }

    /**
     * Returns the key of the current element.
     *
     * @return mixed The current array index or FALSE if at the end of elements array.
     */
    public function key()
    {
        return key($this->_elements);
    }

    /**
     * Returns the next element or FALSE if at the end of elements array.
     *
     * @return mixed the next element
     */
    public function next()
    {
        $this->_iteratorIndex ++;
        return next($this->_elements);
    }

    /**
     * Rewinds the iterator index.
     *
     * @return mixed the first element or FALSE if elements array is empty.
     */
    public function rewind()
    {
        $this->_iteratorIndex = 0;
        reset ($this->_elements);
    }

    /**
     * Checks if the current index is valid.
     *
     * @return boolean TRUE If the current index is valid, otherwise FALSE.
     */
    public function valid()
    {
        return $this->_iteratorIndex < $this->_iteratorCount;
    }

    /**
     * Offset check for the ArrayAccess interface.
     *
     * @param mixed $key
     * @return boolean TRUE if the key exists in elements array otherwise FALSE
     */
    public function offsetExists($key)
    {
        return array_key_exists($key, $this->_elements);
    }

    /**
     * Getter for the ArrayAccess interface.
     *
     * @param mixed $key Index of the element to retrieve
     * @return mixed The element at the given index
     */
    public function offsetGet($key)
    {
        return $this->_elements[$key];
    }

    /**
     * Setter for the ArrayAccess interface.
     *
     * @param mixed $key index of the element to set
     * @param mixed the element. Must be an instance of the type returned by getElementType()
     * @return void
     */
    public function offsetSet($key, $element)
    {
        $this->_elements[$key] = $element;
        $this->_iteratorCount = count($this->_elements);
    }

    /**
     * Unsetter for the ArrayAccess interface.
     *
     * @param mixed $key index of the element to unset
     * @return void
     */
    public function offsetUnset($key)
    {
        unset($this->_elements[$key]);
        $this->_iteratorCount = count($this->_elements);
    }

    /**
     * Adds an element to the end of the internal elements array.
     *
     * @param mixed the element. Must be an instance of the type returned by getElementType()
     * @return void
     */
    public function append($element)
    {
        $this->_elements[] = $element;
        $this->_iteratorCount = count($this->_elements);
        reset($this->_elements);
    }
}
