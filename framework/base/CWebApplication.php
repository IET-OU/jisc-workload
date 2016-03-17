<?php
/**
* CWebApplication class file
*
* @author Jitse van Ameijde <djitsz@yahoo.com>
*
*/

defined('ALL_SYSTEMS_GO') or die;
/**
* CWebApplication provides the overall application context from which the site is run
*
*
*/
    class CWebApplication {

        /**
        * @var mixed The web application instance
        */
        private static $_instance = null;

        /**
        * @var array $_javaScriptIncludes - the javascript files to include in the current page
        */
        private $_javaScriptIncludes;

        /**
        * @var array $_javaScript - the javascript snippets to include in the current page
        */
        private $_javaScript;

        /**
        * @var string $documentRoot - the document root folder
        */
        public $documentRoot;

        /**
        * @var string $frameworkRoot - the framework root folder
        */
        public $frameworkRoot;

        /**
        * @var string $viewRoot - the views root folder
        */
        public $viewRoot;

        /**
        * @var string $controllerRoot - the controllers root folder
        */
        public $controllerRoot;

        /**
        * @var string $templateRoot - the templates root folder
        */
        public $templateRoot;

        /**
        * @var string $siteRoot - the site root folder
        */
        public $siteRoot;

        /**
        * @var array The configuration array for this web application instance
        */
        public $config;

        /**
        * @var mixed The database handler instance
        */
        public $db;

        /**
        * @var CWebUser user - the current web user
        */
        public $user;

        /**
        * @var CRequestHandler requestHandler - the request handler
        */
        public $requestHandler;

        /**
        * @var CResponseHandler responseHandler - the response handler
        */
        public $responseHandler;

        /**
        * Constructor - initialises variables
        */
        public function __construct() {
            $this->config = require('site' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'config.php');
            $this->db = CDatabaseHandler::getInstance($this->config['db']['connectionString'], $this->config['db']['username'], $this->config['db']['password']);
            $this->documentRoot = __DIR__ . '/../..';   //Was: $_SERVER['DOCUMENT_ROOT'];
            $this->frameworkRoot = realpath(__DIR__ . DIRECTORY_SEPARATOR . '..' );
            $this->viewRoot = $this->frameworkRoot . DIRECTORY_SEPARATOR . 'views';
            $this->controllerRoot = $this->frameworkRoot . DIRECTORY_SEPARATOR . 'controllers';
            $this->templateRoot = $this->documentRoot . DIRECTORY_SEPARATOR . 'site' . DIRECTORY_SEPARATOR . 'templates';
            $this->siteRoot = $this->documentRoot . DIRECTORY_SEPARATOR . 'site';
            $this->user = new CWebUser();
            $this->requestHandler = CRequestHandler::getInstance();
            $this->responseHandler = CResponseHandler::getInstance();
            $this->_javaScriptIncludes = array('header'=>array(),'body'=>array());
            $this->_javaScript = array('header'=>array(),'body'=>array());
            $this->siteSettings = array();
        }

        /**
        * getInstance - returns an instance of the web application
        */
        public static function getInstance() {
            if(self::$_instance == null) {
                self::$_instance = new CWebApplication();
            }
            return self::$_instance;
        }

        /**
        * run - runs the web application
        */
        public function run() {
            $timeStart = microtime(true);

            $session = CSessionHandler::getInstance();
            $session->start();
            if(isset($_SESSION['user'])) $this->user = $_SESSION['user'];

            if($this->getParam('uri')) $uriComponents = explode('/', $this->getParam('uri'));
            else $uriComponents = array();
            array_pop($uriComponents);

            $this->includeJavaScriptFiles(array(
                'jquery-1.10.2.min.js',
                'bootstrap.min.js',
                'scripts.js'
            ));

            try {
                if(isset($uriComponents[0]) && $uriComponents[0] == 'logout') {
                    $_SESSION['user'] = null;
                    $this->responseHandler->redirect('/');
                }
                else if(isset($uriComponents[0]) && $uriComponents[0] == 'accept-cookies') {
                    $visitor = CVisitorModel::loadByPk($_SESSION['session']->visitorId);
                    if($visitor) {
                        $visitor->acceptedCookies = 1;
                        $visitor->update();
                    }
                    $_SESSION['acceptedCookies'] = true;
                    $this->responseHandler->redirect('/');
                }
                else {
                    if(count($uriComponents) == 2) {
                        $controllerId = $uriComponents[0];
                        $actionId = $uriComponents[1];
                    }
                    else if(count($uriComponents) == 1) {
                        $controllerId = 'default';
                        $actionId = $uriComponents[0];
                    }
                    else if(count($uriComponents) == 0) {
                        $controllerId = 'default';
                        $actionId = 'view';
                    }
                    if($controllerId == 'default' || $this->user->isAdministrator()) {
                        $controllerClass = implode('',array_map(function($elem) {
                            return ucwords($elem);
                        },explode('-', $controllerId)));
                        $controllerClass = 'C' . $controllerClass . 'Controller';
                        $controller = new $controllerClass();
                        $action = 'action' . implode('',array_map(function($elem) {
                            return(ucwords($elem));
                        },explode('-', $actionId)));
                        $controller->onBeforeAction();
                        if(method_exists($controller, $action)) {
                            $controller->$action();
                        }
                        $controller->onAfterAction();
                    }
                    else {
                        throw new CPageNotFoundException("Page not found");
                    }
                }

            }
            catch(CPageNotFoundException $e) {
                http_response_code(404);
                echo $e->getMessage();
            }
            catch(CAccessDeniedException $e) {
                echo $e->getMessage();
            }
            $_SESSION['session']->setDefaultValues();
            $_SESSION['session']->update();
            $timeEnd = microtime(true);
            $responseTime = ($timeEnd - $timeStart) * 1000;
            $pageHit = new CPageHitModel();
            $pageHit->sessionId = $_SESSION['session']->sessionId;
            $pageHit->uri = '/' . implode('/', $uriComponents) . '/';
            $pageHit->errors = CErrorHandler::getInstance()->getNumErrors();
            $pageHit->responseTime = round($responseTime);
            $pageHit->dbHits = $this->db->getNumHits();
            $pageHit->setDefaultValues();
            $pageHit->insert();
        }

        /**
        * authenticateUser - authenticates a user with the provided login and password
        *
        * @param string $login The login or email address of the user
        * @param string $pass The password of the user
        */
        public function authenticateUser($login, $pass) {
            $success = $this->user->authenticate($login, $pass);
            if($success) {
                $_SESSION['user'] = $this->user;
                $_SESSION['session']->userId = $this->user->userId;
                $_SESSION['session']->update();
            }
            return $success;
        }

        /**
        * resetUserPassword - resets the password for a user
        *
        * @param string $login The login or email address of the user
        * @return bool True on success or false on invalid login
        */
        public function resetUserPassword($login, $resetToken = null, $newPassword = null) {
            $params = array();
            if(strpos($login,'@') === false) {
                $params['login'] = $login;
            }
            else {
                $params['email'] = $login;
            }
            if($resetToken != null) {
                $params['resetToken'] = $resetToken;
            }
            $user = CUserModel::loadByAttributes($params)->getFirst();
            if($user) {
                if($newPassword) {
                    $user->password = sha1($newPassword);
                    $user->resetToken = null;
                    $user->update();
                    return true;
                }
                else {
                    $user->resetToken = sha1(rand(0,100000));
                    $user->update();
                    $domain = $this->server('SERVER_NAME');
                    $matches = array();
                    if(preg_match('/^(www\.)?(.+)$/', $domain, $matches)) {
                        $domain = $matches[2];
                    }
                    $address = 'no-reply@' . $domain;
                    CMailer::sendEmail($address, $address, $user->email,'Password reset link',
                        'Please click <a href="http://' . $domain . '/login/?login=' . urlencode($login) . '&resetToken=' . $user->resetToken . '">here</a> to reset your password.',
                        'Please use the following URL to reset your password: http://'. $domain . '/admin/login?login=' . urlencode($login) . '&resetToken=' . $user->resetToken
                    );
                    return true;
                }
            }
            else {
                return false;
            }
        }

        /**
        * _getAggregateJavaScriptFile - if necessary creates and returns a javascript file which combines all javascript files in the provided array
        *
        * @param array $files - array of JavaScript files to combine
        */
        private function _getAggregateJavaScriptFile($files) {
            $filename = sha1(implode('', $files)) . '.js';
            $requireRefresh = false;
            if(file_exists($this->documentRoot . DIRECTORY_SEPARATOR . 'js' . DIRECTORY_SEPARATOR . $filename)) {
                $time = filemtime($this->documentRoot . DIRECTORY_SEPARATOR . 'js' . DIRECTORY_SEPARATOR . $filename);
                foreach($files as $file) {
                    if(filemtime($this->documentRoot . DIRECTORY_SEPARATOR . 'js' . DIRECTORY_SEPARATOR . $file) > $time) {
                        $requireRefresh = true;
                        break;
                    }
                }
                if($requireRefresh == false) return '/js/' . $filename;
            }
            $aggregate = '';
            foreach($files as $file) {
                $aggregate .= file_get_contents($this->documentRoot . DIRECTORY_SEPARATOR . 'js' . DIRECTORY_SEPARATOR . $file) . "\r\n";
            }
            file_put_contents($this->documentRoot . DIRECTORY_SEPARATOR . 'js' . DIRECTORY_SEPARATOR . $filename, $aggregate);
            return '/js/' . $filename;
        }


        /**
        * includeJavaScriptFiles - adds the javascript files to be included at the relevant document position (header or body)
        *
        * @param array $files - array of .js files to add
        * @param string $location - position in the HTML document to load the files in
        */
        public function includeJavaScriptFiles($files, $location = 'body') {
            $this->_javaScriptIncludes[$location] = array_merge($this->_javaScriptIncludes[$location], $files);
        }

        /**
        * includeJavaScriptSnippet - adds the provided javascript snippet to the relevant document position (header or body)
        *
        * @param string $snippet
        * @param string $location
        */
        public function includeJavaScriptSnippet($snippet, $location = 'body') {
            $this->_javaScript[$location][] = $snippet;
        }

        /**
        * renderHeaderScripts - renders the scripts for in the HTML header
        *
        */
        public function renderHeaderScripts() {
            if(count($this->_javaScriptIncludes['header']) > 0) {
                $file = $this->_getAggregateJavaScriptFile($this->_javaScriptIncludes['header']);
                echo '<script src="' . $file . '"></script>';
            }
            if(count($this->_javaScript['header']) > 0) {
                echo '<script>' . implode("\r\n", $this->_javaScript['header']) . '</script>';
            }
        }

        /**
        * renderBodyScripts - renders the scripts for in the HTML body
        *
        */
        public function renderBodyScripts() {
            if(count($this->_javaScriptIncludes['body']) > 0) {
                $file = $this->_getAggregateJavaScriptFile($this->_javaScriptIncludes['body']);
                echo '<script src="' . $this->webRoot() . $file . '"></script>';
            }
            if(count($this->_javaScript['body']) > 0) {
                echo '<script>' . implode("\r\n", $this->_javaScript['body']) . '</script>';
            }
            echo '<!--[if lt IE 9]><script src="'. $this->webRoot() .'/js/respond.min.js"></script><![endif]-->';
        }

        /**
        * webRoot - Get the web root URL path.
        * @return string
        */
        public function webRoot() {
            return $this->config[ 'webroot' ];
        }

        protected function server($key, $filter = FILTER_SANITIZE_STRING) {
            return filter_input(INPUT_SERVER, $key, FILTER_SANITIZE_STRING);
        }

        protected function getParam($key, $filter = FILTER_SANITIZE_STRING) {
            return filter_input(INPUT_POST, $key, FILTER_SANITIZE_STRING);
        }
    }
