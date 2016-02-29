<?php
/**
* CErrorHandler class file
* 
* @author Jitse van Ameijde <djitsz@yahoo.com>
* @copyright Copyright &copy; 2013 Quantum Frog Ltd
* 
*/

defined('ALL_SYSTEMS_GO') or die;

/**
* CErrorHandler provides functionality for handling application errors
* 
* 
*/  
  class CErrorHandler {
        /**
        * @var mixed $_instance The instance of the CErrorHandler
        */
        private static $_instance = null;
        
        private $_numErrors;

        /**
        * Constructor
        */
        public function __construct() {
            $this->_numErrors = 0;
        }

        /**
        * getInstance - returns an instance of the request handler
        */
        public static function getInstance() {
            if(self::$_instance == null) {
                self::$_instance = new CErrorHandler();
            }
            return self::$_instance;
        }
        
        /**
        * getNumErrors = returns the number of errors generated during this request
        */
        public function getNumErrors() {
            return $this->_numErrors;
        }

        /**
        * Logs an exception and exits the application
        * 
        * @param Exception $e
        */
        public function logError($errno, $message, $file, $line, $stackTrace = null) {
            $this->_numErrors++;
            $errorTypes = array(E_ERROR =>'Error',E_WARNING=>'Warning',E_PARSE=>'Parse error',E_NOTICE=>'Notice');
            $errorType = isset($errorTypes[$errno]) ? $errorTypes[$errno] : $errno;
            if(strpos($_SERVER['SERVER_NAME'],'.') === false) {
                echo '<p style="color:red;">' . 'Error (' . $errno . '): ' . $message . '(in file ' . $file . ' on line ' . $line . ')' . '<br />' . ($stackTrace != null ? $stackTrace : '') . '</p>';
            }
            $logFile = CWebApplication::getInstance()->siteRoot . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR . 'error_log.json';
            
            $error = array('type'=>$errorType,'timestamp'=>date('d/m/Y H:i:s'),'message'=>$message,'file'=>$file,'line'=>$line);
            if($stackTrace != null) $error['stacktrace'] = $stackTrace;
            if(isset($_GET['uri'])) $error['uri'] = $_GET['uri'];
            else $error['uri'] = '';
            
            if(file_exists($logFile)) $log = json_decode(file_get_contents($logFile),1);
            else $log = array();
            $log[] = $error;
            
            file_put_contents($logFile,json_encode($log));
        }
      
      
  }
?>
