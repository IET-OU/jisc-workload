<?php
/**
* CFacultyModel class file
* 
* @author Jitse van Ameijde <djitsz@yahoo.com>
* 
*/

defined('ALL_SYSTEMS_GO') or die;
/**
* CFacultyModel provides the model for faculty data
* 
* 
*/    
    class CFacultyModel extends CModel {

        /**
        * getTableName - returns the table name for this model (called by the CModel constructor)
        * @return string
        */
        public static function getTableName() {
            return 'faculties';
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