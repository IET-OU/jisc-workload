<?php
/**
* CForm class file
*
* @author Jitse van Ameijde <djitsz@yahoo.com>
* @copyright Copyright &copy; 2013 Quantum Frog Ltd
*
*/

defined('ALL_SYSTEMS_GO') or die;
/**
* CForm provides functionality for creating and handling web forms
*
*
*/
    class CForm implements IteratorAggregate, ArrayAccess {

        /**
        * @var string $_name The name of this form
        */
        protected $_name;

        /**
        * @var string $_action The action for this form
        */
        protected $_action;

        /**
        * @var array $_fields The fields within this form
        */
        protected $_fields;

        /**
        * @var string $_formId The form id
        */
        protected $_formId;

        /**
        * @var array _validators The validators which check the input of the form fields
        */
        protected $_validators;

        /**
        * @var array $_validatedValues The values which have been validated after form submission
        */
        protected $_validatedValues;

        /**
        * @var string $_formMessage Message to display at the top of the form
        */
        protected $_formMessage;

        /**
        * @var bool $_ajax Whether or not ajax should be used to submit this form
        */
        protected $_ajax;

        /**
        * Constructor - initialises variables
        *
        * @param string $name Name of the form
        * @param string $action Action to perform on form submission
        * @param array $fields Array of fields within this form
        * @param array $validators Array of validators for form fields
        * @param bool $ajax True if this form should be submitted using an Ajax request
        * @return CForm
        */
        public function __construct($name, $action, $fields, $validators, $ajax) {
            $this->_name = $name;
            $this->_action = CWebApplication::getInstance()->webRoot() . $action;
            $this->_fields = $fields;
            $this->_validators = $validators;
            $this->_formMessage = null;
            $this->_formId = sha1($this->_name);
            $this->_ajax = $ajax;
            foreach($fields as $field) {
                if($field['type'] == 'captcha') {
                    $max = count(CWebApplication::getInstance()->config['captcha']);
                    if($max > 1) $index = rand(0,$max);
                    else $index = 0;
                    if(!isset($_SESSION['captchas'])) $_SESSION['captchas'] = array();
                    $_SESSION['captchas'][$name] = array('index'=>$index,'answer'=>CWebApplication::getInstance()->config['captcha'][$index]['answer']);
                }
            }
        }

        /**
        * wasSubmitted - returns true if this form was just submitted
        *
        */
        function wasSubmitted() {
            return $_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['_formId']) && $_POST['_formId'] == sha1($this->_name);
        }

        /**
        * validate - validate the form using the provided validation rules
        * @return true on success, false on failure
        */
        function validate() {
            $overallSuccess = true;
            foreach($this->_fields as $field => $specs) {
                if($specs['type'] == 'checkboxGroup') {
                    foreach($specs['options'] as $name => $option) {
                        if(isset($_POST[$name]) && $_POST[$name] == $option['value']) $this->_fields[$field]['options'][$name]['checked'] = true;
                    }
                }
                if($specs['type'] == 'textinput' && isset($specs['checkboxAddon'])) {
                    $state = isset($_POST[$specs['checkboxAddon']['name']]) && $_POST[$specs['checkboxAddon']['name']] == true;
                    $this->_validatedValues[$specs['checkboxAddon']['name']] = $state;
                    if(isset($this->_fields[$field]['disabled'])) $this->_fields[$field]['disabled'] = $state;
                    $this->_fields[$field]['checkboxAddon']['checked'] = $state;
                }
                if($specs['type'] == 'captcha') {
                    if(!isset($_POST[$field]) || !isset($_SESSION['captchas'][$this->_name]) || strtolower($_POST[$field]) != strtolower($_SESSION['captchas'][$this->_name]['answer'])) {
                        $overallSuccess = false;
                        $this->_fields[$field]['error'] = CWebApplication::getInstance()->config['captcha'][$_SESSION['captchas'][$this->_name]['index']]['incorrect'];
                    }
                }
                if(isset($_POST[$field])) {
                    $this->_fields[$field]['value'] = $_POST[$field];
                }
            }
            foreach($this->_validators as $validator) {
                $fieldNames = explode(',',$validator[0]);
                foreach($fieldNames as $field)  {
                    if(!array_key_exists($field,$this->_fields)) {
                        throw new Exception('Form does not have a field named ' . $field);
                    }
//                    if(isset($_POST[$field])) $this->_fields[$field]['value'] = $_POST[$field];
                    $success = true;
                    switch($validator[1]) {
                        case 'required':
                            if($this->_fields[$field]['type'] == 'fileupload') {
                                if(!isset($_FILES[$field])) {
                                    $success = false;
                                    $this->_fields[$field]['error'] = 'A file must be uploaded';
                                }
                                else if($_FILES[$field]['error']) {
                                    $success = false;
                                    $this->_fields[$field]['error'] = 'Error uploading file';
                                }
                            }
                            else if($this->_fields[$field]['type'] == 'checkboxGroup') {
                                $selected = false;
                                foreach($this->_fields[$field]['options'] as $name => $option) {
                                    if(isset($_POST[$name])) {
                                        $selected = true;
                                        break;
                                    }
                                }
                                $success = $selected;
                                if($success == false) $this->_fields[$field]['error'] = 'At least one option needs to be selected';
                            }
                            else {
                                if(!isset($_POST[$field]) || strlen($_POST[$field]) == 0) {
                                    $success = false;
                                    $this->_fields[$field]['error'] = 'A value is required';
                                }
                            }
                            break;
                        case 'length':
                            if(isset($_POST[$field]) && isset($validator['min']) && strlen($_POST[$field]) > 0 && strlen($_POST[$field]) < $validator['min']) {
                                $success = false;
                                $this->_fields[$field]['error'] = 'Please enter at least ' . $validator['min'] . ' characters';
                            }
                            if(isset($_POST[$field]) && isset($validator['max']) && strlen($_POST[$field]) > $validator['max']) {
                                $success = false;
                                $this->_fields[$field]['error'] = 'Please enter at most ' . $validator['min'] . ' characters';
                            }
                            break;
                        case 'type':
                            switch($validator[2]) {
                                case 'int':
                                    if(isset($_POST[$field]) && strlen($_POST[$field]) > 0 && !is_numeric($_POST[$field])) {
                                        $success = false;
                                        $this->_fields[$field]['error'] = 'Value must be a number';
                                    }
                                    break;
                                case 'float':
                                    if(isset($_POST[$field]) && strlen($_POST[$field]) > 0 && !preg_match('/^[-]?[0-9]+([.][0-9]+)?$/',$_POST[$field])) {
                                        $success = false;
                                        $this->_fields[$field]['error'] = 'Value must be a valid floating point';
                                    }
                                    break;
                                case 'intlist':
                                    if(isset($_POST[$field]) && strlen($_POST[$field]) > 0 && !preg_match('/^[0-9,]+$/',$_POST[$field])) {
                                        $success = false;
                                        $this->_fields[$field]['error'] = 'Invalid value supplied';
                                    }
                            }
                            break;
                        case 'format':
                            switch($validator[2]) {
                                case 'email':
                                if(isset($_POST[$field]) && strlen($_POST[$field]) > 0 && (!filter_var($_POST[$field],FILTER_VALIDATE_EMAIL) || !preg_match('/@.+\./', $_POST[$field]))) {
                                    $success = false;
                                    $this->_fields[$field]['error'] = 'Please enter a valid email address';
                                }
                            }
                            break;
                    }
                    if($success) {
                        $value = '';
                        if($this->_fields[$field]['type'] == 'checkboxGroup') {
                            $selected = array();
                            foreach($this->_fields[$field]['options'] as $name => $option) {
                                if(isset($_POST[$name])) {
                                    $selected[] = $_POST[$name];
                                    $this->_validatedValues[$name] = $_POST[$name];
                                }
                                else {
                                    $this->_validatedValues[$name] = 0;
                                }
                            }
                            $value = implode("\r\n",$selected);
                        }
                        else {
                            if(isset($_POST[$field])) $value = $_POST[$field];
                        }
                        $this->_validatedValues[$field] = $value;
                    }
                    $overallSuccess = $overallSuccess && $success;
                }
            }

            return $overallSuccess;
        }

        /**
        * populateFromModel - populates the form fields with values from the provided model
        *
        * @param mixed $model - the model (or array) from which to populate the form values
        */
        function populateFromModel($model) {
            $checkboxGroupFields = array();
            foreach($this->_fields as $field => $specs) {
                if($specs['type'] == 'checkboxGroup') {
                    foreach($specs['options'] as $name => $option) {
                        $checkboxGroupFields[$name] = &$this->_fields[$field]['options'][$name];
                    }
                }
            }
            foreach($model as $field => $value) {
                if($value === null) continue;
                if(isset($this->_fields[$field]) && $this->_fields[$field]['type'] == 'checkboxGroup') {
                    if(is_string($value)) $values = explode("\r\n",$value);
                    else $values = $value;
                    foreach($this->_fields[$field]['options'] as $name => $specs) {
                        if(in_array($specs['value'],$values)) $this->_fields[$field]['options'][$name]['checked'] = true;
                    }
                }
                else if(isset($checkboxGroupFields[$field])) {
                    if($value == 1) $checkboxGroupFields[$field]['checked'] = true;
                    else $checkboxGroupFields[$field]['checked'] = false;
                }
                else {
                    if(isset($this->_fields[$field])) $this->_fields[$field]['value'] = $value;
                }
            }
        }

        /**
        * populateFromArray - populates the form fields with values from the provided array
        *
        * @param mixed $array - the array from which to populate the form values
        */
        function populateFromArray($array) {
            $this->populateFromModel($array);
        }

        /**
        * setMessage - sets the message to display at the top of the form
        *
        * @param string $message - the message to display at the top of the form
        */
        function setMessage($message) {
            $this->_formMessage = $message;
        }

        /**
        * getMessage - returns the message of the form
        * @return string
        */
        function getMessage() {
            return $this->_formMessage;
        }

        /**
        * getName - returns the name of the form
        * @return string
        */
        function getName() {
            return $this->_name;
        }

        /**
        * getAction - returns the action of the form
        * @return string
        */
        function getAction() {
            return $this->_action;
        }

        /**
        * getAction - returns the ID of the form
        * @return string
        */
        function getFormId() {
            return $this->_formId;
        }

        /**
        * isAjax - returns true if Ajax should be used to submit this form
        * @return bool
        */
        function isAjax() {
            return $this->_ajax;
        }

        /**
        * getValidatedValues - returns the array of validated form values
        * @return array
        */
        function getValidatedValues() {
            return $this->_validatedValues;
        }

        /**
        * __get magic method
        *
        */
        function __get($property) {
            if($this->_validatedValues == null) return null;
            if(array_key_exists($property, $this->_validatedValues)) {
                return $this->_validatedValues[$property];
            }
            else {
                return null;
            }
        }

        /**
        * __set magic method
        *
        */
        function __set($property,$value) {
            $this->_validatedValues[$property] = $value;
            $this->_fields[$property]['value'] = $value;
        }

        /**
        * offsetExists - implements the ArrayAccess interface and returns true if the given offset exists in the array of form elements
        * @return boolean
        */
        function offsetExists($offset) {
            return array_key_exists($offset,$this->_fields);
        }

        /**
        * offsetGet - implements the ArrayAccess interface and returns the item at the given offset
        * @return boolean
        */
        function offsetGet($offset) {
            return $this->_fields[$offset];
        }

        /**
        * offsetSet - implements the ArrayAccess interface and sets the object at the given offset to the provided value
        * @return boolean
        */
        function offsetSet($offset,$value) {
            $this->_fields[$offset] = $value;
        }

        /**
        * offsetUnset - implements the ArrayAccess interface and unsets the object at the given offset
        * @return void
        */
        function offsetUnset($offset) {
            unset($this->_fields[$offset]);
        }
        /**
        * getIterator - implements the IteratorAggregate interface and return an iterator for the form fields
        * @return ArrayIterator
        */
        function getIterator() {
            return new ArrayIterator($this->_fields);
        }
    }
