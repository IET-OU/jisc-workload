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
                 if(is_numeric($this->cookie('visitorId')) && $visitor = CVisitorModel::loadByPk($this->cookie('visitorId'))) {
                     $visitor->setDefaultValues();
                     $visitor->update();
                 }
                 else {
                     $visitor = new CVisitorModel();
                     $visitor->acceptedCookies = 0;
                     $visitor->setDefaultValues();
                     $visitor->insert();
                 }
                 setcookie('visitorId', $visitor->visitorId,time() + 60*60*24*365,'/');
                 $session = new CSessionModel();
                 $session->userId = null;
                 $session->visitorId = $visitor->visitorId;
                 $session->ip = $this->server('REMOTE_ADDR');
                 $session->referrer = $this->server('HTTP_REFERER');
                 $session->setDefaultValues();
                 $session->insert();
                 $_SESSION['session'] = $session;
                 $_SESSION['acceptedCookies'] = $visitor->acceptedCookies;
             }
        }

        protected function server($key, $filter = FILTER_SANITIZE_STRING) {
            return filter_input(INPUT_SERVER, $key, FILTER_SANITIZE_STRING);
        }

        protected function cookie($key, $filter = FILTER_SANITIZE_STRING) {
            return filter_input(INPUT_COOKIE, $key, FILTER_SANITIZE_STRING);
        }
    }
