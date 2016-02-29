<?php
/**
* CCourseModel class file
*
* Jisc / OU Student Workload Tool.
*
* @license   http://gnu.org/licenses/gpl.html GPL-3.0+
* @author    Jitse van Ameijde <djitsz@yahoo.com>
* @copyright 2015 The Open University.
*/

defined('ALL_SYSTEMS_GO') or die;
/**
* CCourseModel provides the model for course data
*
*
*/
    class CCourseModel extends CModel {

        /**
        * getTableName - returns the table name for this model (called by the CModel constructor)
        * @return string
        */
        public static function getTableName() {
            return 'courses';
        }

        /**
        * setDerivedFields - initialises derived fields for the model
        *
        */
        protected function setDerivedFields() {
            parent::setDerivedFields();
            $this->_derivedFields['statusText'] = CStringHelper::arrayValue(array(0=>'Draft',1=>'In presentation',2=>'Retired'),$this->status);
            $this->_derivedFields['defaultWpmText'] = CStringHelper::arrayValue(array(0=>'Low',1=>'Med',2=>'Hi'),$this->defaultWpm);
        }

        /**
        * relations - return models associated to this model by foreign key relationships
        */
        public static function relations() {
            return array(
                'owner'=>array(self::HAS_ONE,'CUserModel','createdBy'),
                'faculty'=>array(self::HAS_ONE,'CFacultyModel','facultyId')
            );
        }

    }
?>
