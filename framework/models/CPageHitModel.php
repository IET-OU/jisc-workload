<?php
/**
* CPageHitModel class file
* 
* @author Jitse van Ameijde <djitsz@yahoo.com>
* @copyright Copyright &copy; 2013 Quantum Frog Ltd
* 
*/

defined('ALL_SYSTEMS_GO') or die;
/**
* CPageHitModel provides the model for page hit data
* 
* 
*/    
    class CPageHitModel extends CModel {

        /**
        * getTableName - returns the table name for this model (called by the CModel constructor)
        * @return string
        */
        public static function getTableName() {
            return 'page_hits';
        }

        /**
        * relations - return models associated to this model by foreign key relationships
        */
        public static function relations() {
            return array(
                'session'=>array(self::HAS_ONE,'CSessionModel','sessionId'),
            );
        }

    }
?>