<?php
/**
* CResponseHandler class file
*
* @author Jitse van Ameijde <djitsz@yahoo.com>
* @copyright Copyright &copy; 2013 Quantum Frog Ltd
*
*/

defined('ALL_SYSTEMS_GO') or die;
/**
* CResponseHandler provides functionality for managing the server response
*
*
*/
    class CResponseHandler {

        /**
        * @var mixed The response handler instance
        */
        private static $_instance = null;

        /**
        * @var array Array to send back to the client
        */
        protected $_jsonResponse;

        /**
        * Constructor - initialises variables
        */
        public function __construct() {
            $this->_jsonResponse = array();
        }

        /**
        * getInstance - returns an instance of the response handler
        */
        public static function getInstance() {
            if(self::$_instance == null) {
                self::$_instance = new CResponseHandler();
            }
            return self::$_instance;
        }

        /**
        * mimeType - returns the MIME type for a given file
        *
        * @param string $path The file for which to return the MIME type
        * @return string MIME type
        */

        public static function mimeType($path) {
            preg_match("|\.([a-z0-9]{2,4})$|i", $path, $fileSuffix);

            switch(strtolower($fileSuffix[1])) {
                case 'jpg' :
                case 'jpeg' :
                case 'jpe' :
                    return 'image/jpg';
                case 'png' :
                case 'gif' :
                case 'bmp' :
                case 'tiff' :
                    return 'image/'.strtolower($fileSuffix[1]);
                case 'xml' :
                    return 'application/xml';
                case 'doc' :
                case 'docx' :
                    return 'application/msword';
                case 'xls' :
                case 'xlsx' :
                    return 'application/vnd.ms-excel';
                case 'ppt' :
                case 'pptx' :
                    return 'application/vnd.ms-powerpoint';
                case 'rtf' :
                    return 'application/rtf';
                case 'pdf' :
                    return 'application/pdf';
                case 'html' :
                case 'htm' :
                case 'php' :
                    return 'text/html';
                case 'txt' :
                    return 'text/plain';
                case 'mp3' :
                    return 'audio/mpeg3';
                case 'zip' :
                    return 'application/zip';
                default :
                    return 'unknown/' . trim($fileSuffix[0], '.');
            }

        }

        /**
         * Serves the file indicated by the path to the client as the filename specified by
         * serveAsFilename. Path is relative to the site root.
         *
         * @access    public
         * @param     string $serveAsFilename indicating the filename as presented to the client
         * @param     string $path indicating the path to the file relative to the site root
         * @return    none
         */

         function serveFile($serveAsFilename,$path) {
            /*header('Content-Disposition: attachment;filename=' . $serveAsFilename );
            header('X-Sendfile: ' . $path);*/
            header('Content-type: ' . CResponseHandler::mimeType($path));
            header('Content-Disposition: attachment; filename="' . $serveAsFilename . '"');
            readfile($path);
         }

         /**
         * addJSON - adds the specified array to the JSON response
         *
         * @param array $array - array with information to add
         */
         function addJson($array) {
             $this->_jsonResponse = array_merge_recursive($this->_jsonResponse,$array);
         }

        /**
         * Sends the specified JSON response to the client
         *
         * @access    public
         * @param     string/JSON $json indicating the the JSON to send, provided either as a string or an associative array
         * @return    none
         */

         function serveJson($json) {
            if (isset($_SERVER['HTTP_ACCEPT']) &&
                (strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false)) {
                header('Content-type: application/json');
            }
            else {
                header('Content-type: text/plain');
            }

            header('Expires: Tue, 08 Oct 1991 00:00:00 GMT');
            header('Cache-Control: no-cache, must-revalidate');
            echo(json_encode($json));
         }

         /**
         * returnJSONResponse - returns the generated JSON response to the client
         *
         */
         function returnJsonResponse() {
             $this->serveJson($this->_jsonResponse);
         }

         /**
         * serveCSVLine - serves a CSV file line to the client
         *
         */
         function serveCSVLine($array) {
             $first = true;
             foreach($array as $element) {
                 if($first == false) echo ',';
                 echo '"' . addslashes($element) . '"';
                 $first = false;
             }
             echo "\r\n";
         }

        /**
        * redirect - redirects the user to the specified url
        *
        * @param string $url - the url to redirect the user to
        */
        public function redirect($url) {
            header('Location: ' . CWebApplication::getInstance()->webRoot() . $url);
            die;
        }
    }
