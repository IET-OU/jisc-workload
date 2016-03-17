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
             if(! $this->session('created') || ! $this->session('session')) {
                 $this->put('created', time());
                 $this->put('user');
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
                 setcookie('visitorId', $visitor->visitorId, time() + 60*60*24*365, '/');
                 $session = new CSessionModel();
                 $session->userId = null;
                 $session->visitorId = $visitor->visitorId;
                 $session->ip = $this->server('REMOTE_ADDR');
                 $session->referrer = $this->server('HTTP_REFERER');
                 $session->setDefaultValues();
                 $session->insert();

                 $this->put('session', $session);
                 $this->put('acceptedCookies', $visitor->acceptedCookies);
             }
        }

        /** Add a key to the session.
        */
        public function put($key, $value = null) {
            //Was: $value = $value ? $value : (object) array();
            $_SESSION[ $key ] = $value;
        }

        /** Get a reference to THE session.
        */
        public function theSession() {
            return $this->session('session');
        }

        /** Get a reference to another key in the session.
        */
        public function session($key, $filter = FILTER_SANITIZE_STRING) {
            $value = isset($_SESSION[ $key ]) ? $_SESSION[ $key ] : null;
            //Was: return filter_var($value, $filter);
            return $value;
        }

        /**
        * @return string  Return a HTTP server value.
        */
        protected function server($key, $filter = FILTER_SANITIZE_STRING) {
            return filter_input(INPUT_SERVER, $key, $filter);
        }

        /**
        * @return string  Return a HTTP cookie value.
        */
        protected function cookie($key, $filter = FILTER_SANITIZE_STRING) {
            return filter_input(INPUT_COOKIE, $key); //, $filter);
        }
    }
