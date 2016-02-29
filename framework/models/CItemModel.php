<?php
/**
* CItemModel class file
* 
* @author Jitse van Ameijde <djitsz@yahoo.com>
* 
*/

defined('ALL_SYSTEMS_GO') or die;
/**
* CItemModel provides the model for item data
* 
* 
*/    
    class CItemModel extends CModel {

        /**
        * getTableName - returns the table name for this model (called by the CModel constructor)
        * @return string
        */
        public static function getTableName() {
            return 'items';
        }

        /**
        * relations - return models associated to this model by foreign key relationships
        */
        public static function relations() {
            return array(
                'owner'=>array(self::HAS_ONE,'CUserModel','createdBy'),
                'course'=>array(self::HAS_ONE,'CCourseModel','courseId')
            );
        }
        
    }
?>