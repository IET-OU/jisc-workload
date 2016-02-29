<?php
/**
* CWebUser class file
* 
* @author Jitse van Ameijde <djitsz@yahoo.com>
* @copyright Copyright &copy; 2013 Quantum Frog Ltd
* 
*/

defined('ALL_SYSTEMS_GO') or die;
/**
* CWebUser provides functionality for managing web users using the site
* 
* 
*/    
    class CWebUser {
        
        private $_user;

        /**
        * Constructor
        */
        public function __construct() {
            $this->_user = null;
        }
        
        /**
        * Authenticates the user with the provided login details
        * 
        * @param string $login - login or email
        * @param string $pass - password
        * @return true if successful, false on failed login
        */
        public function authenticate($login, $pass) {
            $params = array();
            $params['password'] = sha1($pass);
            if(strpos($login,'@') === false) {
                $params['login'] = $login;
            }
            else {
                $params['email'] = $login;
            }
            $users = CUserModel::loadByAttributes($params)->getObjectArray();
            $this->_user = array_pop($users);
            if($this->_user != null) {
                $this->_user->lastLogin = CDatabaseHandler::now();
                $this->_user->update();
            }
            return $this->_user != null;
        }
        
        /**
        * checkAccess - checks if the current user has access to the specified model
        * 
        * @param CModel $model
        * @return boolean
        */
        public function checkAccess($model) {
            if($this->_user == null) return $model->access == 1 || $model->access == 3;
            else if($model->access == 0) return $this->isEditor();
            else return $model->access == 1 || $model->access == 2;            
        }
        
        
        /**
        * isAuthenticated - returns true if the current web user has been authenticated
        * 
        */
        public function isAuthenticated() {
            return $this->_user != null;
        }

        /**
        * isSuperAdministrator - returns true if the current web user is a super administrator
        * 
        */
        public function isSuperAdministrator() {
            if($this->_user != null) {
                return $this->_user->accessLevel == 0;
            }
            return false;
        }

        /**
        * isAdministrator - returns true if the current web user is an administrator
        * 
        */
        public function isAdministrator() {
            if($this->_user != null) {
                return $this->_user->accessLevel <= 1;
            }
            return false;
        }
        
        /**
        * isEditor - returns true if the current web user is an editor
        * 
        */
        public function isEditor() {
            if($this->_user != null) {
                return $this->_user->accessLevel <= 2;
            }
            return false;
        }

        /**
        * isMember - returns true if the current web user is a member
        * 
        */
        public function isMember() {
            if($this->_user != null) {
                return $this->_user->accessLevel <= 3;
            }
            return false;
        }
        
        /**
        * __get magic method
        * 
        */
        function __get($property) {
            return $this->_user->$property;
        }
        
    }
?>