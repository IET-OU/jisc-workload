<?php
/**
* CStringHelper class file
* 
* @author Jitse van Ameijde <djitsz@yahoo.com>
* @copyright Copyright &copy; 2013 Quantum Frog Ltd
* 
*/

defined('ALL_SYSTEMS_GO') or die;
/**
* CStringHelper provides functionality for managing strings
* 
* 
*/
    class CStringHelper {
        
        /**
        * transliterateUtf8 - converts a UTF-8 string to it ASCII equivalent, removing
        * any special characters
        * 
        * @param mixed $string - the string to transliterate
        */
        static function transliterateUtf8($string) {
            $result = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $string);
            $result = preg_replace("/[^a-zA-Z0-9\/_| -]/", '', $result);
            $result = trim($result, '-');
            $result = preg_replace("/[\/_| -]+/", ' ', $result);        
            return $result;
        }
        
        /**
        * createAlias - creates an ASCII URL-safe alias for a given string
        * 
        * @param string $string - the string to create an alias for
        * @param string $separator - the separator to use for separating words
        */
        static function createAlias($string,$separator = '-') {
            $result = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $string);
            $result = str_replace('\'','',$result);
            $result = preg_replace("/[^a-zA-Z0-9\/_| -]/", '', $result);
            $result = strtolower(trim($result, '-'));
            $result = preg_replace("/[\/_| -]+/", $separator, $result);        
            return $result;
        }
        
        /**
        * Generates a valid login name based on a full name
        * 
        * @param mixed $fullName - the full name to convert
        */
        static function generateLoginName($fullName) {
            $result = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $fullName);
            $result = preg_replace('/\s+/','.',$result);
            $result = preg_replace("/[^a-zA-Z0-9\/_| .-]/", '', $result);
            $result = strtolower(trim($result, '-'));
            $result = preg_replace("/[\/_| -]+/", '-', $result);        
            return $result;
        }
        
        /**
        * Generates a valid tag
        * 
        * @param mixed $string - the string to convert into a valid tag
        */
        static function generateValidTag($string) {
            $result = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $string);
            $result = preg_replace("/[^a-zA-Z0-9\/_|. +&@-]/", '', $result);
            $result = strtolower(trim($result, '-'));
            $result = preg_replace("/[\/_|-]+/", '-', $result);
            if(!preg_match('/^[a-zA-Z@][a-zA-Z0-9&@. +-]+$/',$result)) return false;
            return $result;
        }
        
        /**
        * createExcerpt - creates an excerpt with a maxiumum length of $length characters
        * 
        * @param string $text
        * @param int $length
        */
        static function createExcerpt($text,$length) {
            if(strlen($text) > $length) {
                $result = substr($text,0,$length - 3);
                $result = preg_replace('/\s+\S*$/','',$result) . '...';
                return $result;
            }
            else return $text;
        }
        
        /**
        * generateUrl - generates a valid URL based on the provided string
        * 
        * @param mixed $string
        */
        static function generateUrl($string) {
            return trim(preg_replace('/\s+/','-',CStringHelper::transliterateUtf8($string)),'-');
        }
        
        /**
        * arrayValue - returns the value at the specified index - useful for shorthand $value = CStringHelper::arrayValue(array(1=>'yes',0=>'no'),$response)
        * 
        * @param array $array
        * @param mixed $index
        */
        static function arrayValue($array,$index) {
            return $array[$index];
        }
    }  
?>