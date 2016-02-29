<?php
/**
* CModelCollection class file
* 
* @author Jitse van Ameijde <djitsz@yahoo.com>
* @copyright Copyright &copy; 2013 Quantum Frog Ltd
* 
*/

defined('ALL_SYSTEMS_GO') or die;
/**
* CModelCollection provides functionality for managing a collection of model instances
* 
* 
*/    
    class CModelCollection implements IteratorAggregate, Countable, ArrayAccess {
        
        private $_objectClass;
        private $_objects;
        
        /**
        * Constructor - initialises variables
        */
        public function __construct($objectClass) {
            $this->_objectClass = $objectClass;
            $this->_objects = array();
        }
        
        /**
        * add - adds an object to the collection
        */
        public function add($object) {
            if(is_a($object,$this->_objectClass)) {
                $this->_objects[$object->getId()] = $object;
            }
            else throw new Exception('Attempt to add an object of an invalid type to a collection of type ' . $this->_objectClass);
        }
        
        /**
        * getModel - returns the model name of this collection
        * 
        */
        function getModel() {
            return $this->_objectClass;
        }

        /**
        * getObjectArray - returns the array of objects stored in this collection
        * @return array
        */
        function getObjectArray() {
            return $this->_objects;
        }
        
        /**
        * getFirst - returns the first object in this collection
        * @return CModel
        */
        function getFirst() {
            if(count($this->_objects) == 0) return null;
            return($this->_objects[key($this->_objects)]);            
        }
                
        /**
        * getIterator - implements the IteratorAggregate interface and return an iterator for the object collection
        * @return ArrayIterator
        */
        function getIterator() {
            return new ArrayIterator($this->_objects);
        }
        
        /**
        * count - implements the Countable interface and returns the number of objects in this collection
        * @return int
        */
        function count() {
            return count($this->_objects);
        }
        
        /**
        * offsetExists - implements the ArrayAccess interface and returns true if the given offset exists in the array
        * @return boolean 
        */
        function offsetExists($offset) {
            return array_key_exists($offset,$this->_objects);
        }
        
        /**
        * offsetGet - implements the ArrayAccess interface and returns the item at the given offset
        * @return boolean 
        */
        function offsetGet($offset) {
            return $this->_objects[$offset];
        }

        /**
        * offsetSet - implements the ArrayAccess interface and sets the object at the given offset to the provided value
        * @return boolean 
        */
        function offsetSet($offset,$value) {
            if(is_a($value,$this->_objectClass)) {
                $this->_objects[$offset] = $value;
            }
            else throw new Exception('Attempt to add an object of an invalid type to a collection of type ' . $this->_objectClass);
        }

        /**
        * offsetUnset - implements the ArrayAccess interface and unsets the object at the given offset
        * @return void
        */
        function offsetUnset($offset) {
            unset($this->_objects[$offset]);
        }
    }
?>