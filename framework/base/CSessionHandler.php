<?php
/**
* CSessionHandler class file
* 
* @author Jitse van Ameijde <djitsz@yahoo.com>
* @copyright Copyright &copy; 2013 Quantum Frog Ltd
* 
*/

defined('ALL_SYSTEMS_GO') or die;
/**
* CSessionHandler provides functionality for managing sessions
* 
* 
*/
    class CSessionHandler {

        /**
        * @var mixed The session handler instance
        */
        private static $_instance = null;
        
        /**
        * Constructor - initialises variables
        */
        public function __construct() {
        }
        
        /**
        * getInstance - returns an instance of the session handler
        */
        public static function getInstance() {
            if(self::$_instance == null) {
                self::$_instance = new CSessionHandler();
            }
            return self::$_instance;
        }
        
        /**
        * start - starts the session
        */
        public function start() {
             session_start();
             if(!isset($_SESSION['created']) || !isset($_SESSION['session'])) {
                 $_SESSION['created'] = time();
                 $_SESSION['user'] = null;
                 if(isset($_COOKIE['visitorId']) && is_numeric($_COOKIE['visitorId']) && $visitor = CVisitorModel::loadByPk($_COOKIE['visitorId'])) {
                     $visitor->setDefaultValues();
                     $visitor->update();
                 }
                 else {
                     $visitor = new CVisitorModel();
                     $visitor->acceptedCookies = 0;
                     $visitor->setDefaultValues();
                     $visitor->insert();
                 }
                 setcookie('visitorId',$visitor->visitorId,time() + 60*60*24*365,'/');
                 $session = new CSessionModel();
                 $session->userId = null;
                 $session->visitorId = $visitor->visitorId;
                 $session->ip = $_SERVER['REMOTE_ADDR'];
                 $session->referrer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : null;
                 $session->setDefaultValues();
                 $session->insert();
                 $_SESSION['session'] = $session;
                 $_SESSION['acceptedCookies'] = $visitor->acceptedCookies;
             }            
        }        
    }  
?>