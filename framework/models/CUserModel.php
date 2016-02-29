<?php
/**
* CUserModel class file
* 
* @author Jitse van Ameijde <djitsz@yahoo.com>
* @copyright Copyright &copy; 2013 Quantum Frog Ltd
* 
*/

defined('ALL_SYSTEMS_GO') or die;
/**
* CUserModel provides the model for user data
* 
* 
*/    
    class CUserModel extends CModel {

        /**
        * getTableName - returns the table name for this model (called by the CModel constructor)
        * @return string
        */
        public static function getTableName() {
            return 'users';
        }

        /**
        * setDerivedFields - initialises derived fields for the model
        * 
        */
        protected function setDerivedFields() {
            parent::setDerivedFields();
            $this->_derivedFields['accessLevelText'] = CStringHelper::arrayValue(array(0=>'Super administrator',1=>'Administrator',2=>'User'),$this->accessLevel);
        }
        
        /**
        * relations - return models associated to this model by foreign key relationships
        */
        public static function relations() {
            return array(
                'institution'=>array(self::HAS_ONE,'CInstitutionModel','institutionId')
            );
        }
        
    }
?>