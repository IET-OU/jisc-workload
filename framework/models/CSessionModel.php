<?php
/**
* CSessionModel class file
* 
* @author Jitse van Ameijde <djitsz@yahoo.com>
* @copyright Copyright &copy; 2013 Quantum Frog Ltd
* 
*/

defined('ALL_SYSTEMS_GO') or die;
/**
* CSessionModel provides the model for session data
* 
* 
*/    
    class CSessionModel extends CModel {

        /**
        * getTableName - returns the table name for this model (called by the CModel constructor)
        * @return string
        */
        public static function getTableName() {
            return 'sessions';
        }
        
        /**
        * relations - return models associated to this model by foreign key relationships
        */
        public static function relations() {
            return array(
                'user'=>array(self::HAS_ONE,'CUserModel','userId'),
                'visitor'=>array(self::HAS_ONE,'CVisitorModel','visitorId'),
                'browser'=>array(self::HAS_ONE,'CBrowserModel','browserId')
            );
        }

    }
?>