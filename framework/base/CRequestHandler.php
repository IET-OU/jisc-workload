<?php
/**
* CRequestHandler class file
* 
* @author Jitse van Ameijde <djitsz@yahoo.com>
* @copyright Copyright &copy; 2013 Quantum Frog Ltd
* 
*/

defined('ALL_SYSTEMS_GO') or die;
/**
* CRequestHandler provides functionality for managing client requests
* 
* 
*/
    class CRequestHandler {
        
        const TYPE_INT = 1;
        const TYPE_STRING = 2;
        /**
        * @var mixed The request handler instance
        */
        private static $_instance = null;
        

        /**
        * Constructor - initialises variables
        */
        public function __construct() {
        }
        
        /**
        * getInstance - returns an instance of the request handler
        */
        public static function getInstance() {
            if(self::$_instance == null) {
                self::$_instance = new CRequestHandler();
            }
            return self::$_instance;
        }
        
        /**
        * requestVar - checks the _GET request variables to see if the specified variable exists and if it has the right type
        * 
        * @param mixed $type - the type of variable (CRequestHandler::TYPE_INT, CRequestHandler::TYPE_STRING, etc.)
        * @param mixed $name - the name of the variable
        */
        public function requestVar($type,$name) {
            switch( $type ) {
                case self::TYPE_INT:
                    if(isset($_GET[$name]) && preg_match('/^[0-9]+$/',$_GET[$name])) {
                        return $_GET[$name];
                    }
                    if(isset($_POST[$name]) && preg_match('/^[0-9]+$/',$_POST[$name])) {
                        return $_POST[$name];
                    }
                    return null;
                    break;
                case self::TYPE_STRING:
                    if(isset($_GET[$name])) {
                        return $_GET[$name];
                    }
                    if(isset($_POST[$name])) {
                        return $_POST[$name];
                    }
                    return null;
                    break;
            }
        }
        
        /**
        * getUriComponents - returns an array holding each of the uri components
        * @return array
        */
        public function getUriComponents() {
            if(isset($_GET['uri'])) {
                $components = explode('/',$_GET['uri']);
                array_pop($components);
                return $components;
            }
            return array();
        }
        
        /**
        * wasFormSubmitted - returns true if a form with the provided formId was submitted
        * 
        * @param mixed $formName - the name of the form
        */
        public function wasFormSubmitted($formName) {
            return isset($_POST['_formId']) && $_POST['formId'] == sha1($formName);
        }
        
    }
?>