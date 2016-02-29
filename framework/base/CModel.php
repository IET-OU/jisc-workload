<?php
/**
* CModel class file
* 
* @author Jitse van Ameijde <djitsz@yahoo.com>
* @copyright Copyright &copy; 2013 Quantum Frog Ltd
* 
*/

defined('ALL_SYSTEMS_GO') or die;
/**
* CModel constitutes the base class from which all models are derived
* 
* 
*/    
    abstract class CModel implements IteratorAggregate {
        
        const HAS_ONE = 1;
        const HAS_MANY = 2;
        const MANY_MANY = 3;
        const CUSTOM_FUNCTION = 4;
        
        /**
        * @var string $_tableName Name of the database table (needs to be defined in the "tableSpecifications" section 
        * of the configuration array)
        */
        protected $_tableName;

        /**
        * @var array $_tableSpecification Array containing the specifications of the database table for this model
        */
        protected $_tableSpecification;
        
        /**
        * @var array $_fields Array with the values of each of the database table fields
        */
        protected $_fields;
        
        /**
        * @var array $_derivedFields Array with derived values
        */
        protected $_derivedFields;
        
        /**
        * @var array $_relations Array with wth any loaded models associated to CModel by foreign key relationships
        */
        protected $_relations;
        
        /**
        * Constructor - initialises variables
        */
        public function __construct() {
            $class = get_called_class();
            $this->_tableName = $class::getTableName();
            $this->_tableSpecification = CWebApplication::getInstance()->config['tableSpecifications'][$this->_tableName];
            $this->_fields = array();
            $this->_derivedFields = null;
            foreach($this->_tableSpecification['fields'] as $field => $specs) {
                $this->_fields[$field] = null;
            }
            $this->_relations = $class::relations();
        }
        
        /**
        * function to return the table name of this model
        * @return string the table name
        */
        public static function getTableName() {
            return null;
        }
        
        /**
        * relations - return key=>value pairs for any entities (class names) with which this model has foreign key associations
        */
        public static function relations() {
            return array();
        }
        
        /**
        * loadFromForm - load the model properties from a submitted form
        * 
        * @param CForm $form - the form from which to populate the model properties
        * @param CModel $template - the template to use to create the object - to pre-populate fields
        * @return CModel - instance of the model
        */
        static function loadFromForm($form,$template = null) {
            $model = get_called_class();
            $object = new $model();
            if($template != null) {
                foreach($template->_fields as $field => $value) {
                    $object->_fields[$field] = $value;
                }
            }
            foreach($object->_tableSpecification['fields'] as $field => $specs) {
                $value = $form->$field;
                if(!is_null($value)) $object->_fields[$field] = $value;
            }
            return $object;
        }
        
        /**
        * loadFromArray - creates a new object using the array values supplied
        * 
        * @param array $array - The array holding the property values
        */
        static function loadFromArray($array) {
            $model = get_called_class();
            $object = new $model;
            foreach($object->_tableSpecification['fields'] as $field => $specs) {
                $object->_fields[$field] = $array[$field];
            }
            return $object;            
        }
        
        /**
        * loadByPk - loads an instance of this model from the database using the primary key
        * 
        * @param integer $pk The primary key of the database row to load
        * @return CModel - instance of the model
        */
        static function loadByPk($pk) {
            $db = CDatabaseHandler::getInstance();
            $model = get_called_class();
            $object = new $model();
            $row = $db->select($object->_tableName,'*',array($object->_tableSpecification['keys']['primary']=>$pk));
            $values = array_shift($row);
            if($values) $object->_fields = $values;
            else return null;
            return $object;
        }
        
        /**
        * loadByAttributes - loads a collection of instances of this model from the database using 
        * the provided attributes as filters
        * 
        * @param array $atributes The attributes of the database rows to load
        * @return CModelCollection 
        */
        static function loadByAttributes($attributes,$options = null) {
            $db = CDatabaseHandler::getInstance();
            $model = get_called_class();
            $object = new $model();
            $rows = $db->select($object->_tableName,'*',$attributes,$options);
            $objectCollection = new CModelCollection(get_class($object));
            foreach($rows as $row) {
                $object = new $model();
                $object->_fields = $row;
                $objectCollection->add($object);
            }
            return $objectCollection;
        }
        
        /**
        * loadAll - loads a collection of instances of this model from the database
        * 
        * @return CModelCollection 
        */
        static function loadAll() {
            $db = CDatabaseHandler::getInstance();
            $model = get_called_class();
            $object = new $model();
            $rows = $db->select($object->_tableName,'*');
            $objectCollection = new CModelCollection(get_class($object));
            foreach($rows as $row) {
                $object = new $model();
                $object->_fields = $row;
                $objectCollection->add($object);
            }
            return $objectCollection;
        }
        
        /**
        * If the database table has a 'deleted' field, set the specfied row to the current date time. If not, 
        * delete the database row matching the provided primary key. 
        */
        static function deleteByPk($pk) {
            $db = CDatabaseHandler::getInstance();
            $class = get_called_class();
            $tableName = $class::getTableName();
            $tableSpecs = CWebApplication::getInstance()->config['tableSpecifications'][$tableName];
            if(isset($tableSpecs['fields']['deleted'])) {
                $values = array('deleted'=>$db->now(),'deletedBy'=>CWebApplication::getInstance()->user->userId);
                return $db->update($tableName,array($tableSpecs['keys']['primary']=>$pk),$values);
            }
            else {
                return $db->delete($tableName,array($tableSpecs['keys']['primary']=>$pk));
            }
        }

        /**
        * If the database table has a 'deleted' field, set all matching rows to the current date time. If not, 
        * deletes any database rows matching the provided attributes. 
        */
        static function deleteByAttributes($attributes) {
            $db = CDatabaseHandler::getInstance();
            $class = get_called_class();
            $tableName = $class::getTableName();
            $tableSpecs = CWebApplication::getInstance()->config['tableSpecifications'][$tableName];
            if(isset($tableSpecs['fields']['deleted'])) {
                $values = array('deleted'=>$db->now(),'deletedBy'=>CWebApplication::getInstance()->user->userId);
                return $db->update($tableName,$attributes,$values);
            }
            else {
                return $db->delete($tableName,$attributes);
            }
        }
        
        /**
        * loadRelations - loads the relations of each model in the provided model collection.
        * If $relations is left null, it will load all relations, otherwise $relations should be a
        * string with coma-separated list of relations to load
        * 
        * @param CModelCollection $modelCollection
        * @param string $relations
        */
        static function loadRelations($modelCollection,$relationNames = null) {
            if(count($modelCollection) == 0) return;
            $db = CDatabaseHandler::getInstance();
            $model = $modelCollection->getModel();
            $relations = $model::relations();
            if($relationNames == null) $relationNames = array_keys($relations);
            else $relationNames = explode(',',$relationNames);
            foreach($relationNames as $relation) {
                switch($relations[$relation][0]) {
                    case self::HAS_ONE:
                        $model = $relations[$relation][1];
                        $property = $relations[$relation][2];
                        $ids = array();
                        foreach($modelCollection as $object) {
                            if($object->$property != null) $ids[$object->$property] = true;
                        }
                        $ids = array_keys($ids);
                        if(count($ids) > 0) {
                            $tableName = $model::getTableName();
                            $primaryKey = CWebApplication::getInstance()->config['tableSpecifications'][$tableName]['keys']['primary'];
                            $rows = $db->select($tableName,'*',array($primaryKey=>array('in',$ids),'deleted'=>null),null,$primaryKey);
                            foreach($modelCollection as $object) {
                                if($object->$property) $object->_relations[$relation][3] = $model::loadFromArray($rows[$object->$property]);
                                else $object->_relations[$relation][3] = null;
                            }
                        }
                        break;
                    case self::HAS_MANY:
                        throw new Exception('CModel::HAS_MANY not implemented yet');
                        break;
                    case self::MANY_MANY:
                        foreach($modelCollection as $object) {
                            $object->_relations[$relation][3] = array();
                        }
                        $model = $relations[$relation][1];
                        $matches = array();
                        preg_match('/^([a-zA-Z0-9_]+)\(([a-zA-Z0-9_]+),([a-zA-Z0-9_]+)\)$/',$relations[$relation][2],$matches);
                        $tableName = $matches[1];
                        $foreignKey = CWebApplication::getInstance()->config['tableSpecifications'][$model::getTableName()]['keys']['primary'];
                        $thisModel = get_called_class();
                        $primaryKey = CWebApplication::getInstance()->config['tableSpecifications'][$thisModel::getTableName()]['keys']['primary'];
                        $ids = array_keys($modelCollection->getObjectArray());
                        $rows = $db->select($tableName,'*',array($primaryKey=>array('in',$ids)));
                        $ids = array();
                        foreach($rows as $row) {
                            $ids[$row[$foreignKey]] = true;
                        }
                        $ids = array_keys($ids);
                        if(count($ids) > 0) {
                            $associatedObjects = $model::loadByAttributes(array($foreignKey=>array('in',$ids)));
                            foreach($rows as $row) {
//                                if(!isset($modelCollection[$row[$primaryKey]]->_relations[$relation][3])) $modelCollection[$row[$primaryKey]]->_relations[$relation][3] = array();
                                $modelCollection[$row[$primaryKey]]->_relations[$relation][3][$row[$foreignKey]] = $associatedObjects[$row[$foreignKey]];
                            }
                        }
                        break;
                }
            }
        }
        
        /**
        * getRowCount - return the total number of rows matching the specified attributes stored in the database
        * 
        */
        static function getRowCount($attributes = null) {
            $db = CDatabaseHandler::getInstance();
            $class = get_called_class();
            $tableName = $class::getTableName();
            $result = $db->executeSelectQuery('select count(*) as num from `' . $tableName . '` ' . $db->whereClause($attributes),$attributes);
            return $result[0]['num'];
        }

        /**
        * Returns true if a row with the selected attributes exists in the database
        * 
        * @param array $attributes - the attributes by which to filter the rows
        */
        static function exists($attributes) {
            $class = get_called_class();
            $tableName = $class::getTableName();
            $db = CDatabaseHandler::getInstance();
            $params = array();
            foreach($attributes as $key => $value) {
                if(substr($key,0,1) != ':') $params[':' . $key] = $value;
                else $params[$key] = $value;
            }
            $result = $db->executeSelectQuery('select count(`' . CWebApplication::getInstance()->config['tableSpecifications'][$tableName]['keys']['primary'] . '`) as num from `' . $tableName . '` ' .
                $db->whereClause($params) .';', $params );
            return $result[0]['num'] > 0;
        }
        
        /**
        * Inserts the model instance as a new row into the database
        */
        function insert() {
            $db = CDatabaseHandler::getInstance();
            foreach($this->_fields as $field => $value) {
                if($value == '' && $this->_tableSpecification['fields'][$field]['null']==true) {
                    $this->_fields[$field] = null;
                }
            }
            $id = $db->insert($this->_tableName,$this->_fields);
            $this->_fields[$this->_tableSpecification['keys']['primary']] = $id;
            return $id;
        }
        
        /**
        * Updates the database row corresponding to the model instance
        */
        function update() {
            $db = CDatabaseHandler::getInstance();
            foreach($this->_fields as $field => $value) {
                if($value == '' && $this->_tableSpecification['fields'][$field]['null']==true) {
                    $this->_fields[$field] = null;
                }
            }
            $key = $this->_tableSpecification['keys']['primary'];
            return $db->update($this->_tableName,array($key => $this->_fields[$key]),$this->_fields);
        }
        
        /**
        * Deletes the the database row corresponding to the model instance or if a 'deleted' field is available within the table, set it to the current date time
        */
        function delete() {
            $db = CDatabaseHandler::getInstance();
            $key = $this->_tableSpecification['keys']['primary'];
            if(array_key_exists('deleted',$this->_fields)) {
                $this->_fields['deleted'] = $db->now();
                if(array_key_exists('deletedBy',$this->_fields)) $this->_fields['deletedBy'] = CWebApplication::getInstance()->user->userId;
                return $this->update();
            }
            else {
                $primary = $this->_tableSpecification['keys']['primary'];
                return $db->delete($this->_tableName,array($primary => $this->_fields[$primary]));
            }
        }
        
        /**
        * getUniqueAlias - returns a unique alias based on the given string
        * 
        * @param string $string - the string from which to create the alias
        * @return string
        */
        static function getUniqueAlias($string) {
            $db = CDatabaseHandler::getInstance();
            $class = get_called_class();
            $tableName = $class::getTableName();
            $alias = CStringHelper::createAlias($string);
            $rows = $db->executeSelectQuery('select `alias` from `' . $tableName . '` where `alias` like \'' . $alias . '%\'');
            if(count($rows) == 0) return $alias;
            $aliases = array();
            foreach($rows as $row) {
                $aliases[$row['alias']] = true;
            }
            if(!isset($aliases[$alias])) return $alias;
            $postfix = 2;
            while(isset($aliases[$alias . '-' . $postfix])) $postfix++;
            return $alias . '-' . $postfix;
        }

        /**
        * setDefaultValues - complete a number of default values (such as createdBy, lastUpdatedBy etc.)
        * as well as any unsepecified values which have default values set in the table specification
        * 
        */
        function setDefaultValues() {
            foreach($this->_tableSpecification['fields'] as $field=>$specs) {
                if(is_null($this->_fields[$field]) || $field == 'lastUpdated' || $field=='lastUpdatedBy') {
                    switch($field) {
                        case 'created':
                            $this->_fields[$field] = CDatabaseHandler::now();
                            break;
                        case 'createdBy':
                            if(CWebApplication::getInstance()->user->isAuthenticated()) {
                                $this->_fields[$field] = CWebApplication::getInstance()->user->userId;
                            }
                            else $this->_fields[$field] = null;
                            break;
                        case 'lastUpdated':
                            $this->_fields[$field] = CDatabaseHandler::now();
                            break;
                        case 'lastUpdatedBy':
                            if(CWebApplication::getInstance()->user->isAuthenticated()) {
                                $this->_fields[$field] = CWebApplication::getInstance()->user->userId;
                            }
                            else $this->_fields[$field] = null;
                            break;
                        default:
                            if(isset($specs['default'])) $this->_fields[$field] = $specs['default'];
                            break;
                    }
                }
            }
        }
        
        /**
        * setDerivedFields - initialises derived fields for the model
        * 
        */
        protected function setDerivedFields() {
            $this->_derivedFields = array();
            if(!$this->created) return;
            $this->_derivedFields['createdDate'] = DateTime::createFromFormat('Y-m-d H:i:s',$this->created)->format('d/m/Y H:i:s');
        }
        
        /**
        * hasProperty - checks if the model has a property with the provided name
        * 
        * @param string $property
        * @return bool true if the property exists, false otherwise
        */
        function hasProperty($property) {
            if(array_key_exists($property, $this->_fields)) {
                return true;
            }
            if(array_key_exists($property, $this->_relations)) {
                return true;
            }
            if($this->_derivedFields == null) {
                $this->setDerivedFields();
            }
            if(array_key_exists($property, $this->_derivedFields)) {
                return true;
            }
            return false;
        }
        
        /**
        * __get magic method
        * 
        */
        function __get($property) {
            //  Is the property one of the database fields?
            if(array_key_exists($property, $this->_fields)) {
                return $this->_fields[$property];
            }
            //  Is it one of the models related by foreign key relationships?
            if(array_key_exists($property, $this->_relations)) {
                //  Check if this relation has already been loaded
                if(!isset($this->_relations[$property][3])) {
                    switch($this->_relations[$property][0]) {
                        case self::HAS_ONE:
                            if($this->_fields[$this->_relations[$property][2]] == null) return null;
                            $model = $this->_relations[$property][1];
                            $this->_relations[$property][3] = $model::loadByPk($this->_fields[$this->_relations[$property][2]]);
                            break;
                        case self::HAS_MANY:
                            $model = $this->_relations[$property][1];
                            if(isset(CWebApplication::getInstance()->config['tableSpecifications'][$model::getTableName()]['fields']['ordering'])) $order = array('order by' => '`ordering` asc');
                            else $order = null;
                            $params = array($this->_relations[$property][2]=>$this->_fields[$this->_tableSpecification['keys']['primary']]);
                            if(isset(CWebApplication::getInstance()->config['tableSpecifications'][$model::getTableName()]['fields']['deleted'])) {
                                $params['deleted'] = null;
                            }
                            $this->_relations[$property][3] = $model::loadByAttributes($params,$order);
                            break;
                        case self::MANY_MANY:
                            $db = CDatabaseHandler::getInstance();
                            $model = $this->_relations[$property][1];
                            $matches = array();
                            preg_match('/^([a-zA-Z0-9_]+)\(([a-zA-Z0-9_]+),([a-zA-Z0-9_]+)\)$/',$this->_relations[$property][2],$matches);
                            $tableName = $matches[1];
                            $foreignKey = CWebApplication::getInstance()->config['tableSpecifications'][$model::getTableName()]['keys']['primary'];
                            $primaryKey = $this->_tableSpecification['keys']['primary'];
                            $rows = $db->select($tableName,'*',array($primaryKey=>$this->_fields[$primaryKey]));
                            $ids = array();
                            foreach($rows as $row) {
                                $ids[$row[$foreignKey]] = true;
                            }
                            $ids = array_keys($ids);
                            if(count($ids) > 0) {
                                $this->_relations[$property][3] = $model::loadByAttributes(array('deleted'=>null,$foreignKey=>array('in',$ids)));
                            }
                            else $this->_relations[$property][3] = array();
                            break;
                        case self::CUSTOM_FUNCTION:
                            $functionName = $this->_relations[$property][1];
                            $this->$functionName();
                            break;
                    }
                }
                return $this->_relations[$property][3];
            }
            if($this->_derivedFields == null) {
                $this->setDerivedFields();
            }
            if(array_key_exists($property, $this->_derivedFields)) {
                return $this->_derivedFields[$property];
            }
            //No property on this model, throw an exception
            throw new Exception('Model ' . get_class($this) . ' does not have a property called "' . $property . '".');
        }

        /**
        * __set magic method
        * 
        */
        function __set($property,$value) {
            //  Is the property one of the database fields?
            if(array_key_exists($property, $this->_fields)) {
               $this->_fields[$property] = $value;
            }
            //  Is the property one of the relations?
            else if(array_key_exists($property, $this->_relations)) {
               $this->_relations[$property] = $value;
            }
            else {
                //No property on this model, throw an exception
                throw new Exception('Model ' . get_class($this) . ' does not have a property called "' . $property . '".');
            }
        }

        /**
        * getId - returns the id of this object
        * 
        */
        function getId() {
            $keys = explode(',',$this->_tableSpecification['keys']['primary']);
            if(count($keys) == 1) {
                return $this->_fields[$this->_tableSpecification['keys']['primary']];
            }
            else {
                $index = '';
                foreach($keys as $key) {
                    $index .= $this->_fields[$key];
                }
                return $index;
            }
        }

        /**
        * getIterator - implements the IteratorAggregate interface and return an iterator for the form fields
        * @return ArrayIterator
        */
        function getIterator() {
            return new ArrayIterator($this->_fields);
        }
        
    }
?>