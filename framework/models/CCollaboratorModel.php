<?php
/**
* CCollaboratorModel class file
* 
* @author Jitse van Ameijde <djitsz@yahoo.com>
* 
*/

defined('ALL_SYSTEMS_GO') or die;
/**
* CCollaboratorModel provides the model for course collaborators
* 
* 
*/    
    class CCollaboratorModel extends CRelationalModel {

        /**
        * getTableName - returns the table name for this model (called by the CModel constructor)
        * @return string
        */
        public static function getTableName() {
            return 'collaborators';
        }

        /**
        * relations - return models associated to this model by foreign key relationships
        */
        public static function relations() {
            return array(
                'user'=>array(self::HAS_ONE,'CUserModel','userId'),
                'course'=>array(self::HAS_ONE,'CCourseModel','courseId')
            );
        }
        
        
    }
?>