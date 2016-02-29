<?php
/**
* CFacultyModel class file
* 
* @author Jitse van Ameijde <djitsz@yahoo.com>
* 
*/

defined('ALL_SYSTEMS_GO') or die;
/**
* CInstitutionModel provides the model for institution data
* 
* 
*/    
    class CInstitutionModel extends CModel {

        /**
        * getTableName - returns the table name for this model (called by the CModel constructor)
        * @return string
        */
        public static function getTableName() {
            return 'institutions';
        }

        /**
        * relations - return models associated to this model by foreign key relationships
        */
        public static function relations() {
            return array(
                'faculties'=>array(self::HAS_MANY,'CFacultyModel','institutionId')
            );
        }
        
    }
?>