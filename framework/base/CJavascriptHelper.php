<?php
/**
* CJavascriptHelper class file
* 
* @author Jitse van Ameijde <djitsz@yahoo.com>
* @copyright Copyright &copy; 2013 Quantum Frog Ltd
* 
*/

defined('ALL_SYSTEMS_GO') or die;
/**
* CJavascriptHelper provides functionality for managing javascript elements
* 
* 
*/
    class CJavascriptHelper {
        
        /**
        * initialiseTaginputScript - returns the Javascript for initialising a taginput control
        * 
        * @param string $class - the class of message (success, info, warning, danger)
        * @param string $message - the message
        * @return string
        */
        public static function initialiseTaginputScript($inputName, $tags) {
            return '$(\'input[name="' . $inputName . '"]\').tagsinput({tagClass:\'tag\'}); $(\'input[name="' . $inputName . '"]\').siblings(\'.bootstrap-tagsinput\').children(\'input\').typeahead({local:' . json_encode($tags) . '});';
        }                                                                                                                                   
        
    }  
?>