<?php
/**
* CDatabaseHandler class file
* 
* @author Jitse van Ameijde <djitsz@yahoo.com>
* @copyright Copyright &copy; 2013 Quantum Frog Ltd
* 
*/
defined('ALL_SYSTEMS_GO') or die;
/**
* CDatabaseHandler provides functionality for interacting with a database
* 
* 
*/
    class CDatabaseHandler {
        /**
        * @var mixed The database handler instance
        */
        private static $_instance = null;
        
        /**
        * @var mixed The MYSQLi connection
        */
        private $PDO;
        
        /**
        * @var integer Number of queries performed on the database this request
        */
        private $numDatabaseHits;
        
        /**
        * @var string The connnection string representing the database connection
        * For example: mysql:dbname=mydatabase;host=127.0.0.1;
        */
        private $connectionString;
        
        /**
        * @var string The username for this database connection
        */
        private $userName;
        
        /**
        * @var string The password for this database connection
        */
        private $password;
        
        /**
        * CDatabaseHandler constructor - initialises variables
        * 
        * @param string $connectionString The connection string representing the database connection
        * @param string $username The username for the database connection
        * @param string $password The password for the database connection
        */
        public function __construct($connectionString, $username, $password) {
            $this->mysqliConnection = null;
            $this->connectionString = $connectionString;
            $this->username = $username;
            $this->password = $password;
            $this->numDatabaseHits = 0;
            $this->connect();
        }
        
        /**
        * getInstance - returns an instance of the database handler, creating it first if necessary
        * 
        * @param string $connectionString The connection string for this connection
        * @param string $username The username for the database connection
        * @param string $password The password for the database connection
        */
        public static function getInstance($connectionString = '', $username = '', $password = '') {
            if(self::$_instance == null) {
                self::$_instance = new CDatabaseHandler($connectionString,$username,$password);
            }
            return self::$_instance;
        }
        
        /**
        * getNumHits - returns the number of database hits incurred this request
        */
        public function getNumHits() {
            return $this->numDatabaseHits;
        }
        
        /**
        * connect - connects to the database
        */
        public function connect() {
            try {
                $this->PDO = new PDO($this->connectionString, $this->username, $this->password);
                $this->PDO->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $this->PDO->exec('SET NAMES \'utf8\'');
            }
            catch(Exception $e) {
                $handler = new CErrorHandler();
                $handler->logError('Exception',$e->getMessage(),$e->getFile(),$e->getLine(),$e->getTraceAsString());
                return false;
            }
        }
        
        /**
        * executeSafeQuery - executes a query on the database without performing any further sanitation
        * 
        * @param mixed $query The query to execute
        * @return array of rows for select/show/describe/explain query or true for other query on success, false on error
        */
        public function executeSafeQuery($query) {
            try {
                $result = $this->PDO->exec($query);
                $this->numDatabaseHits++;
            }
            catch(Exception $e) {
                $handler = new CErrorHandler();
                $handler->logError('Exception',$e->getMessage(),$e->getFile(),$e->getLine(),$e->getTraceAsString());
                return false;
            }
            return $result;
        }
        
        /**
        * select - performs a select query against the specfied database table
        * 
        * @param string $table The table from which to select rows
        * @param string $fields The fields to select
        * @param array $params The filters
        * @param array $options Any additional options to customise the query
        * @param string $indexKey The field to use as the keys for the returned array
        */
        public function select($table,$fields,$filters = null,$options = null,$indexKey = null) {
            $query = 'select ' . $fields . ' from ' . '`' . $table . '`';
            $params = array();
            if($filters) {
                $query .= ' ' . $this->_generateWhereClause($params,$filters);
            }
            if(isset($options['order by'])) $query .= ' order by ' . $options['order by'];
            if(isset($options['limit'])) $query .= ' limit ' . $options['limit'];
            $query .= ';';
            try {
                $stmt = $this->PDO->prepare($query);
                $stmt->execute($params);
                $this->numDatabaseHits++;
                $results = array();
                $key = 0;
                while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    if($indexKey != null) $key = $row[$indexKey];
                    else $key++;
                    $results[$key] = $row;
                }
                return $results;
            }
            catch(Exception $e) {
                $handler = new CErrorHandler();
                $handler->logError('Exception',$e->getMessage(),$e->getFile(),$e->getLine(),$e->getTraceAsString());
                return false;
            }
        }
        
        /**
        * insert - inserts a row with the specified values into the database table specified
        * 
        * @param string $table - name of the table to insert the row into
        * @param array $values - array of key=>value pairs
        * @return int the id of the row just inserted
        */
        public function insert($table,$values) {
            $query = 'insert into `' . $table . '` (';
            $valueString = ' values (';
            $params = array();
            $first = true;
            foreach($values as $field => $value) {
                if(!$first) {
                    $query .= ',';
                    $valueString .= ',';
                }
                $first = false;
                $query .= '`' . $field . '`';
                if(is_null($value)) {
                    $valueString .= 'NULL';
                }
                else {
                    $valueString .= ':' . $field;
                    $params[':' . $field] = $value;
                }
            }
            $query .= ')' . $valueString . ');';
            try {
                $stmt = $this->PDO->prepare($query);
                $stmt->execute($params);
                $this->numDatabaseHits++;
                return $this->PDO->lastInsertId();
            }
            catch(Exception $e) {
                $handler = new CErrorHandler();
                $handler->logError('Exception',$e->getMessage(),$e->getFile(),$e->getLine(),$e->getTraceAsString());
                return false;
            }
        }

        /**
        * insertMultiple - inserts multiple rows with the specified values into the database table specified
        * 
        * @param string $table - name of the table to insert the row into
        * @param array $values - array of arrays of key=>value pairs
        * @return int the id of the last row inserted
        */
        public function insertMultiple($table,$values) {
            $query = 'insert into `' . $table . '` (';
            $valueString = ' values (';
            $params = array();
            $first = true;
            $index = 1;
            $topRow = true;
            foreach($values as $row) {
                if(!$topRow) $valueString .= ',(';
                foreach($row as $field => $value) {
                    if(!$first) {
                        if($topRow) $query .= ',';
                        $valueString .= ',';
                    }
                    $first = false;
                    if($topRow) $query .= '`' . $field . '`';
                    if(is_null($value)) {
                        $valueString .= 'NULL';
                    }
                    else {
                        $valueString .= ':' . $field . $index;
                        $params[':' . $field . $index] = $value;
                    }
                }
                $valueString .= ')';
                $first = true;
                $topRow = false;
                $index++;
            }
            $query .= ')' . $valueString . ';';
            try {
                $stmt = $this->PDO->prepare($query);
                $stmt->execute($params);
                $this->numDatabaseHits++;
                return $this->PDO->lastInsertId();
            }
            catch(Exception $e) {
                $handler = new CErrorHandler();
                $handler->logError('Exception',$e->getMessage(),$e->getFile(),$e->getLine(),$e->getTraceAsString());
                return false;
            }
        }

        /**
        * update - updates a row with the specified values into the database table specified
        * 
        * @param string $table - name of the table to update the row in
        * @param array $filters - the filters to select the rows to be updated by
        * @param array $values - array of key=>value pairs
        * @return int the number of rows affected
        */
        public function update($table,$filters,$values) {
            $query = 'update `' . $table . '` set ';
            $params = array();
            $first = true;
            foreach($values as $field => $value) {
                if(!$first) {
                    $query .= ', ';
                }
                $first = false;
                $query .= '`' . $field . '`=';
                
                if(is_null($value)) {
                    $query .= 'NULL';
                }
                else {
                    $query .= ':value_' . $field;
                    $params[':value_' . $field] = $value;
                }
            }
            if($filters) {
                $query .= ' ' . $this->_generateWhereClause($params,$filters);
            }
            $query .= ';';
            try {
                $stmt = $this->PDO->prepare($query);
                $stmt->execute($params);
                $this->numDatabaseHits++;
                return $stmt->rowCount();
            }
            catch(Exception $e) {
                $handler = new CErrorHandler();
                $handler->logError('Exception',$e->getMessage(),$e->getFile(),$e->getLine(),$e->getTraceAsString());
                return false;
            }
        }

        /**
        * delete - deletes any database rows which match the provided filters
        * 
        * @param string $table - name of the table to update the row in
        * @param array $filters - the filters to select the rows to be updated by
        * @return int the number of rows deleted
        */
        public function delete($table,$filters) {
            $query = 'delete from `' . $table . '`';
            $params = array();
            $first = true;
            $query .= ' ' . $this->_generateWhereClause($params,$filters);
            $query .= ';';
            try {
                $stmt = $this->PDO->prepare($query);
                $stmt->execute($params);
                $this->numDatabaseHits++;
                return $stmt->rowCount();
            }
            catch(Exception $e) {
                $handler = new CErrorHandler();
                $handler->logError('Exception',$e->getMessage(),$e->getFile(),$e->getLine(),$e->getTraceAsString());
                return false;
            }
        }

        /**
        * executeSelectQuery - performs a select query against the specfied database table
        * 
        * @param string $query The query to execute
        * @param array $params The filters
        * @param string $indexKey The field to use as the keys for the returned array
        */
        public function executeSelectQuery($query,$params = null,$indexKey = null) {
            try {
                $validatedParams = array();
                if($params) {
                    foreach($params as $key => $value) {
                        if(!is_null($value) && (is_string($value) || is_int($value))) {
                            if(substr($key,0,1) == ':') $key = substr($key,1);
                            $validatedParams[':' . $key] = $value;
                        }
                        if(is_array($value)) {
                            if(substr($key,0,1) == ':') $key = substr($key,1);
                            $operations = array('<','>','<=','>=','!=');
                            if(in_array($value[0],$operations)) $validatedParams[':' . $key] = $value[1];
                        }
                    }
                }
                $stmt = $this->PDO->prepare($query);
                $stmt->execute($validatedParams);
                $this->numDatabaseHits++;
                $results = array();
                $key = 0;
                while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    if($indexKey != null) $key = $row[$indexKey];
                    $results[$key] = $row;
                    if($indexKey == null) $key++;
                }
                return $results;
            }
            catch(Exception $e) {
                $handler = new CErrorHandler();
                $handler->logError('Exception',$e->getMessage(),$e->getFile(),$e->getLine(),$e->getTraceAsString());
                return false;
            }
        }
        
        /**
        * generates a where clause and updates the supplied $params array
        * 
        * @param array $params - empty array to populate with query parameters
        * @param array $attributes - attributes to match the where clause against
        */
        private function _generateWhereClause(&$params,$attributes) {
            $first = true;
            $clause = 'where ';
            $legalOperations = array('in','not in','like','not like','<','>','<=','>=','!=','is null','is not null');
            if($attributes == null || count($attributes) == 0) {
                $clause .= '1';
                return $clause;
            }
            foreach($attributes as $key => $value) {
                if(substr($key,0,1) == ':') $key = substr($key,1);
                if(!$first) $clause .= ' and';
                if(is_null($value)) {
                    $clause .= '`' . $key . '` is null';
                }
                else {
                    if(is_array($value)) {
                        if(!in_array(strtolower($value[0]),$legalOperations)) {
                            throw new Exception('Illegal operation in where clause: ' . $value[0]);
                        }
                        $clause .= '`' . $key . '` ' . $value[0];
                        if(strcasecmp($value[0],'like' ) == 0 || strcasecmp($value[0],'not like' ) == 0) {
                            $clause .= ' \'' . $value['value'] . '\'';
                        }
                        else if(strcasecmp($value[0],'in' ) == 0 || strcasecmp($value[0],'not in' ) == 0) {
                            $allInts = true;
                            foreach($value[1] as $item) {
                                if(!is_numeric($item)) {
                                    $allInts = false;
                                    break;
                                }
                            }
                            if($allInts == true) {                            
                                $clause .= '(' . implode(',',$value[1]) . ')';
                            }
                            else {
                                $clause .= '(';
                                $i = 1;
                                foreach($value[1] as $item) {
                                    if($i > 1) $clause .= ',';
                                    $clause .= ':' . $key . $i;
                                    $params[':' . $key . $i] = $item;
                                    $i++;
                                }
                                $clause .= ')'; 
                            }
                        }
                        else if(strcasecmp($value[0],'is null' ) != 0 && strcasecmp($value[0],'is not null' ) != 0) {
                            $clause .= ' :' . $key;
                            $params[':' . $key] = $value[1];
                        }
                    }
                    else {
                        $clause .= ' `' . $key . '` = :' . $key;
                        $params[':' . $key] = $value;
                    }
                }
                $first = false;
            }
            return $clause;
        }
        
        /**
        * whereClause - creates a where clause based on the provided attributes
        * 
        * @param array $params The filters
        */
        public function whereClause($attributes) {
            $void = array();
            $clause = $this->_generateWhereClause($void,$attributes);
            return $clause;
        }

        /**
        * now - returns the current datetime in the datetime format Year-Month-Day Hour:Minute:Second
        * 
        * @return string datetime
        */
        public static function now() {
            return date('Y-m-d H:i:s');
        }

        /**
        * formatDateTime - returns the the provided datetime as a database-compatible string in the datetime format Year-Month-Day Hour:Minute:Second
        * 
        * @param DateTime $dateTime - the datetime object to format
        * @return string datetime
        */
        public static function formatDateTime($datetime) {
            return $datetime->format('Y-m-d H:i:s');
        }
        
        /**
        * convertDateTime - converts a database formatted datetime string into a PHP date object
        * @param string $datetime - the string representation of a datetime to convert into a datetime object
        * @return DateTime
        */
        public static function convertDateTime($datetime) {
            return DateTime::createFromFormat('Y-m-d H:i:s',$datetime);
        }
        
        /**
        * relativeDateTime - converts a database formatted datetime string into a relative date string (i.e., yesterday, 1 month ago, etc.)
        * @param string $datetime - the string representation of a datetime to convert into a datetime object
        * @return DateTime
        */
        public static function relativeDateTime($datetime) {
            
            $date = DateTime::createFromFormat('Y-m-d H:i:s', $datetime);
            $now = new DateTime();
            $interval = $date->diff($now);
            $years = $interval->y;
            $months = $interval->m;
            $days = $interval->d;
            $hours = $interval->h;
            $minutes = $interval->i;
            
            if($years > 1) return $years . ' years ago';
            if($years == 1) return '1 year ago';
            if($months > 1) return $months . ' months ago';
            if($months == 1) return '1 month';
            if($days > 1) return $days . ' days ago';
            if($days == 1) return '1 day ago';
            if($hours > 1) return $hours . ' hours ago';
            if($hours == 1) return '1 hour ago';
            if($minutes > 1) return $minutes . ' minutes ago';
            return 'less than a minute ago';                                                      
/*            $yearDiff = $now->format('Y') - $date->format('Y');
            $monthDiff = $now->format('m') - $date-format('m');
            $dayDiff = $now->format('d') - $date->format('d');
            $hourDiff = $now->format('H') - $date->format('H');
            $minuteDiff = $now->format('i') - $date->format('i');
            $secondDiff = $now->format('s') - $date->format('s');*/
        }
        
}
?>