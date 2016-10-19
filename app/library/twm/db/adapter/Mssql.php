<?php
namespace Twm\Db\Adapter\Pdo;

use \Phalcon;
use \Phalcon\Db;
use \Phalcon\Db\Column;
use \Phalcon\Db\Adapter\Pdo as AdapterPdo;
use \Phalcon\Events\EventsAwareInterface;
use \Phalcon\Db\AdapterInterface;

class Mssql extends AdapterPdo implements EventsAwareInterface, AdapterInterface
{

    protected $_type = 'mssql';
    //	protected $_dialectType = 'sqlsrv';

    public function __construct( array $descriptor)
    {
        $this->connect($descriptor);
        parent::__construct($descriptor);
    }

    /**
     * Escapes a column/table/schema name
     *
     *<code>
     *	$escapedTable = $connection->escapeIdentifier('robots');
     *	$escapedTable = $connection->escapeIdentifier(array('store', 'robots'));
     *</code>
     *
     * @param string identifier
     * @return string
     */
    public function escapeIdentifier($identifier)
    {
        if (is_array($identifier)) {
            return "[" . $identifier[0] . "].[" . $identifier[1] . "]";
        }
        return "[" . $identifier . "]";
    }


    public function describeColumns($table, $schema = null)
    {
        $describe;
        $columns;
        $columnType;
        $field;
        $definition;
        $oldColumn;
        $dialect;
        $sizePattern;
        $matches;
        $matchOne;
        $columnName;

        /**
         * Get the SQL to describe a table
         * We're using FETCH_NUM to fetch the columns
         * Get the describe
         */

        //1. get pk
        $primaryKeys = array();
        $describeKeys = $this->fetchAll("exec sp_pkeys @table_name = '$table'");
        foreach ($describeKeys as $field) {
            $primaryKeys[$field['COLUMN_NAME']] = true;
        }

        //2.get column description
        $dialect = $this->_dialect;
        $describe = $this->fetchAll($dialect->describeColumns($table, $schema), \Phalcon\Db::FETCH_ASSOC);

        $oldColumn = null;
        $sizePattern = "#\\(([0-9]+)(,[0-9]+)*\\)#";

        $columns = array();

        /**
         * Field Indexes: 0:name, 1:type, 2:not null, 3:key, 4:default, 5:extra
         */
        foreach ($describe as $field) {

            /**
             * By default the bind types is two
             */
            $definition = array(
                    "bindType"	=> 2,
                    "unsigned"	=> false,
                    );

            /**
             * By checking every column type we convert it to a Phalcon\Db\Column
             */
            $columnType = $field['TYPE_NAME'];

            $autoIncrement = false;
            switch ($field['TYPE_NAME']) {
                case 'int identity':
                    $definition['type'] = Column::TYPE_INTEGER;
                    $definition["isNumeric"] = true;
                    $definition['bindType'] = Column::BIND_PARAM_INT;
                    $autoIncrement = true;
                    break;
                case 'int':
                    $definition['type'] = Column::TYPE_INTEGER;
                    $definition["isNumeric"] = true;
                    $definition['bindType'] = Column::BIND_PARAM_INT;
                    break;
                case 'nchar':
                    $definition['type'] = Column::TYPE_VARCHAR;
                    break;
                case 'char':
                    $definition['type'] = Column::TYPE_CHAR;
                    break;
                case 'smallint':
                    $definition['type'] = Column::TYPE_INTEGER;
                    $definition["isNumeric"] = true;
                    $definition['bindType'] = Column::BIND_PARAM_INT;
                    break;
                case 'float':
                    $definition['type'] = Column::TYPE_DECIMAL;
                    $definition["isNumeric"] = true;
                    $definition['bindType'] = Column::BIND_SKIP;
                    break;
                case 'datetime':
                    $definition["type"] = Column::TYPE_DATETIME;
                    break;
                case 'date':
                    $definition["type"] = Column::TYPE_DATE;
                    break;
                case 'decimal':
                    $definition["type"] = Column::TYPE_DECIMAL;
                    $definition["isNumeric"] = true;
                    $definition["bindType"] = Column::BIND_PARAM_DECIMAL;
                    break;
                case 'text':
                    $definition["type"] = Column::TYPE_TEXT;
                    break;
                case 'numeric':
                    $definition["type"] = Column::TYPE_FLOAT;
                    $definition["isNumeric"] = true;
                    $definition["bindType"] = Column::TYPE_DECIMAL;
                    break;
                default:
                    //echo $field['COLUMN_NAME'] . 'has no match type: ' .  $field['TYPE_NAME'] . PHP_EOL;
                    $definition['type'] = Column::TYPE_VARCHAR;
                    //$definition['bindType'] = Column::BIND_PARAM_STR;
            }

            /**
             * If the column type has a parentheses we try to get the column size from it
             */
            $definition["size"] = (int)$field['LENGTH'];
            $definition["precision"] = (int)$field['PRECISION'];

            /**
             * Positions
             */
            if (!$oldColumn) {
                $definition["first"] = true;
            } else {
                $definition["after"] = $oldColumn;
            }

            /**
             * Check if the field is primary key
             */
            if (isset($primaryKeys[$field['COLUMN_NAME']])) {
                $definition["primary"] = true;
            }

            /**
             * Check if the column allows null values
             */
            $definition["notNull"] = ($field['NULLABLE'] == 0);

            /*
             */
            if ($field['SCALE'] || $field['SCALE'] == '0') {
                //$definition["scale"] = (int)$field['SCALE'];  Phalcon/Db/Column type does not support scale parameter
                $definition["size"] = $definition['precision'];
            }
            /**
             * Check if the column is auto increment
             */
            if ($autoIncrement) {
                $definition["autoIncrement"] = true;
            }

            /**
             * Every route is stored as a Phalcon\Db\Column
             */
            $columnName = $field['COLUMN_NAME'];
            //echo $columnName  . PHP_EOL;
            //print_r($definition);
            $columns[] = new \Phalcon\Db\Column($columnName, $definition);
            $oldColumn = $columnName;
        }

        return $columns;
    }

    public function connect(array $descriptor = null)
    {
        $dsn_setting = $descriptor;
        $pdoType = $dsn_setting['pdoType'];
        $username = $dsn_setting['username'];
        $password = $dsn_setting['password'];
        unset($dsn_setting['pdoType']);
        unset($dsn_setting['username']);
        unset($dsn_setting['password']);
        unset($dsn_setting['dialectClass']);
        $DSN = "$pdoType:";
        foreach ($dsn_setting as $key => $value)
        {
            $DSN .= $key.'='.$value.';';
        }
        $DSN = rtrim($DSN, ';');
        //$DSN .= 'MultipleActiveResultSets=1';
        $this->_pdo = new \PDO($DSN,
            $username,
            $password,
            array(
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_STRINGIFY_FETCHES => true,
                )
        );

        //use odbc
//        $host = explode(':', $descriptor['host']);
//        $dns = "{$descriptor['pdoType']}:Driver=FreeTDS;Server={$host[0]};Port={$host[1]};Database={$descriptor['dbname']};UID={$descriptor['username']};PWD={$descriptor['password']}";

        /*$this->_pdo = new \PDO("odbc:Driver=FreeTDS;Server=116.55.248.76;Port=31433;Database=IAMisDB;UID=sa_iamis;PWD=pl0871iamis");*/
        //ini_set('pdo.dsn.MSSQL', 'odbc:IAMisDB');
//        $this->_pdo = new \PDO("odbc:IAMisDB", 'sa_iamis', 'pl0871iamis');


        $this->execute('SET QUOTED_IDENTIFIER ON');

        //$this->_dialect = new \Twm\Db\Dialect\Mssql();
    }

    public function query($sqlStatement, $bindParams = null, $bindTypes = null)
    {
        if (is_string($sqlStatement)) {
            //check sql server keyword
            if (!strpos($sqlStatement, '[rowcount]')) {
                $sqlStatement = str_replace('rowcount', '[rowcount]', $sqlStatement);	//sql server keywords
            }

            //case 1. select count(query builder)
            $countString = 'SELECT COUNT(*)';
            if (strpos($sqlStatement, $countString)) {
                  //$sql = str_replace('"', '', $sql);
                  return parent::query($sqlStatement, $bindParams, $bindTypes);
            }


            //case 2. subquery need alais name (model find)
            $countString = 'SELECT COUNT(*) "numrows"';
            if (strpos($sqlStatement, $countString) !== false) {
                $sqlStatement .= ' dt ';

                //subquery need TOP
                if (strpos($sqlStatement, 'TOP') === false) {
                    if (strpos($sqlStatement, 'ORDER') !== false) {
                        $offset = count($countString);
                        $pos = strpos($sqlStatement, 'SELECT', $offset) + 7; //'SELECT ';
                        $sqlStatement = substr($sqlStatement, 0, $pos) .  'TOP 100 PERCENT '. substr($sqlStatement, $pos);
                    }
                }
            }

            //sql server(dblib) does not accept " as escaper
            //$sql = str_replace('"', '', $sql);
        }


        // if (strpos($sqlStatement, '= 2') and !strpos($sqlStatement, 'COUNT(*)'))
        // {
        //     echo $sqlStatement.PHP_EOL;
        //     // print_r($bindParams);
        //     // print_r($bindTypes);
        //     $result = parent::query($sqlStatement, $bindParams, $bindTypes);
        //     print_r($result->numRows());
        //     print_r($result->fetchArray());
        //     return $result;
        //     //return array('1' => '1', '2' => '2');
        //     //exit;
        // }

        // //override parent query method

        // $eventsManager = $this->_eventsManager;

        // /**
        //  * Execute the beforeQuery event if an EventsManager is available
        //  */
        // if ( is_object($eventsManager) ) {
        //     $this->_sqlStatement = $sqlStatement;
        //     $this->_sqlVariables = $bindParams;
        //     $this->_sqlBindTypes = $bindTypes;
        //     if ( $eventsManager->fire("db:beforeQuery", $this) === false ) {
        //         return false;
        //     }
        // }

        // $pdo = $this->_pdo;
        // if ( is_array($bindParams) ) {
        //     $statement = $pdo->prepare($sqlStatement);
        //     if ( is_object($statement) ) {
        //         $statement = $this->executePrepared($statement, $bindParams, $bindTypes);
        //     }
        // } else {
        //     $statement = $pdo->query($sqlStatement);
        // }

        // /**
        //  * Execute the afterQuery event if an EventsManager is available
        //  */
        // if ( is_object($statement) ) {
        //     if ( is_object($eventsManager) ) {
        //         $eventsManager->fire("db:afterQuery", $this);
        //     }
        //     return new ResultPdo($this, $statement, $sqlStatement, $bindParams, $bindTypes);
        // }

        // return $statement;

        $result = parent::query($sqlStatement, $bindParams, $bindTypes);
        $result = new \Palm\Phalcon\Ext\Db\Result\PdoWrapper($result);
        return $result;
    }

    /**
     * Appends a LIMIT clause to $sqlQuery argument
     *
     * <code>
     *	echo $connection->limit("SELECT * FROM robots", 5);
     * </code>
     *
     * @param	string sqlQuery
     * @param	int number
     * @return	string
     */
    public function limit($sqlQuery, $number)
    {
        $dialect = $this->_dialect;
        return $dialect->limit($sqlQuery, $number);
    }

//    public function execute($sqlStatement, $bindParams=null, $bindTypes=null)
//	{
//
//		/**
//         * Execute the beforeQuery event if an EventsManager is available
//         */
//		$eventsManager = $this->_eventsManager;
//		if ( is_object($eventsManager) )
//		{
//			$this->_sqlStatement = $sqlStatement;
//            $this->_sqlVariables = $bindParams;
//            $this->_sqlBindTypes = $bindTypes;
//			if ( $eventsManager->fire("db:beforeQuery", $this) === false ) {
//				return false;
//			}
//        }
//
///**
// * Initialize affectedRows to 0
// */
//        $affectedRows = 0;
//
//        $pdo = $this->_pdo;
//        if ( is_array($bindParams) ) {
//            $statement = $pdo->prepare($sqlStatement);
//            if ( is_object($statement) ) {
//                $newStatement = $this->executePrepared($statement, $bindParams, $bindTypes);
//                $affectedRows = $newStatement->rowCount();
//            }
//            } else {
//                $affectedRows = $pdo->exec($sqlStatement);
//            }
//
//            /**
//             * Execute the afterQuery event if an EventsManager is available
//             */
//            if ( is_int($affectedRows) ) {
//                $this->_affectedRows = affectedRows;
//                if ( is_object($eventsManager) ) {
//                    $eventsManager->fire("db:afterQuery", $this);
//                }
//            }
//
//            return true;
//	}



    //insert miss parameters, need to do this
    // public function executePrepared(\PdoStatement $statement, array $placeholders, $dataTypes)
    // {
    //     //return $this->_pdo->prepare($statement->queryString, $placeholders);//not working

    //     if (!is_array($placeholders)) {
    //         throw new \Phalcon\Db\Exception("Placeholders must be an array");
    //     }

    //     // print_r($placeholders);
    //     // print_r($dataTypes);
    //     // exit;

    //     foreach ($placeholders as $wildcard => $value) {
    //         $parameter = '';

    //         if (is_int($wildcard)) {
    //             $parameter = $wildcard + 1;
    //         } else {
    //             if (is_string($wildcard)) {
    //                 $parameter = $wildcard;
    //             } else {
    //                 throw new \Phalcon\Db\Exception("Invalid bind parameter");
    //             }
    //         }

    //         if (is_array($dataTypes) && !empty($dataTypes)) {
    //             if (!isset($dataTypes[$wildcard])) {
    //                 throw new \Phalcon\Db\Exception("Invalid bind type parameter");
    //             }
    //             $type = $dataTypes[$wildcard];

    //             /**
    //              * The bind type is double so we try to get the double value
    //              */
    //             $castValue;
    //             if ($type == \Phalcon\Db\Column::BIND_PARAM_DECIMAL) {
    //                 $castValue = doubleval($value);
    //                 $type = \Phalcon\Db\Column::BIND_SKIP;
    //             } else {
    //                 $castValue = $value;
    //             }

    //             /**
    //              * 1024 is ignore the bind type
    //              */
    //             if ($type == \Phalcon\Db\Column::BIND_SKIP) {
    //                 $statement->bindParam($parameter, $castValue);
    //                 $statement->bindValue($parameter, $castValue);
    //             } else {
    //                 $statement->bindParam($parameter, $castValue, $type);
    //                 $statement->bindParam($parameter, $castValue, $type);
    //                 $statement->bindValue($parameter, $castValue, $type);
    //             }

    //         } else {
    //             $statement->bindParam($parameter, $value);		//TODO: works for model, but not pdo - all column with the latest parameter value
    //             $statement->bindValue($parameter, $value);	//works for pdo , but not model
    //         }
    //     }

    //     //echo PHP_EOL . $statement->queryString . PHP_EOL;
    //     $statement->execute();
    //     return $statement;
    // }

    public function insert($table, array $values, $fields = null, $dataTypes = null)
    {
        $placeholders;
        $insertValues;
        $bindDataTypes;
        $bindType;
        $position;
        $value;
        $escapedTable;
        $joinedValues;
        $escapedFields;
        $field;
        $insertSql;

        if (!is_array($values)) {
            throw new \Phalcon\Db\Exception("The second parameter for insert isn't an Array");
        }

        /**
         * A valid array with more than one element is required
         */
        if (!count($values)) {
            throw new \Phalcon\Db\Exception("Unable to insert into " . $table . " without data");
        }

        $placeholders = array();
        $insertValues = array();

        if (!is_array($dataTypes)) {
            $bindDataTypes = array();
        } else {
            $bindDataTypes = $dataTypes;
        }

        /**
         * Objects are casted using __toString, null values are converted to string "null", everything else is passed as "?"
         */
        //echo PHP_EOL;	var_dump($dataTypes);
        foreach ($values as $position => $value) {
            if (is_object($value)) {
                $placeholders[] = (string) $value;
            } else {
                if ($value === null) { // (0 ==) null is true
                    $placeholders[] = "default";
                } else {
                    $placeholders[] = "?";
                    $insertValues[] = $value;
                    if (is_array($dataTypes)) {
                        if (!isset($dataTypes[$position])) {
                            throw new \Phalcon\Db\Exception("Incomplete number of bind types");
                        }
                        $bindType = $dataTypes[$position];
                        $bindDataTypes[] = $bindType;
                    }
                }
            }
        }
        //var_dump($placeholders);

        if (true) { //globals_get("db.escape_identifiers") {
            $escapedTable = $this->escapeIdentifier($table);
        } else {
            $escapedTable = $table;
        }

        /**
         * Build the final SQL INSERT statement
         */
        $joinedValues = join(", ", $placeholders);
        if (is_array($fields)) {

            if (true) {//globals_get("db.escape_identifiers") {
                $escapedFields = array();
                foreach ($fields as $field) {
                    $escapedFields[] = $this->escapeIdentifier($field);
                }
            } else {
                $escapedFields = $fields;
            }

            $insertSql = "INSERT INTO " . $escapedTable . " (" . join(", ", $escapedFields) . ") VALUES (" . $joinedValues . ")";
        } else {
            $insertSql = "INSERT INTO " . $escapedTable . " VALUES (" . $joinedValues . ")";
        }

        /**
         * Perform the execution via PDO::execute
         */
        //echo PHP_EOL . $insertSql;
        return $this->execute($insertSql, $insertValues, $bindDataTypes);
        //return $this->_pdo->execute($insertSql, $insertValues, $bindDataTypes);
    }

    public function update($table, $fields, $values, $whereCondition = null, $dataTypes = null)
    {
        $placeholders = array();
        $updateValues = array();

        if (is_array($dataTypes)) {
            $bindDataTypes = array();
        } else {
            $bindDataTypes = $dataTypes;
        }

        /**
         * Objects are casted using __toString, null values are converted to string 'null', everything else is passed as '?'
         */
        foreach ($values as $position => $value) {

            if (!isset($fields[$position])) {
                throw new \Phalcon\Db\Exception("The number of values in the update is not the same as fields");
            }
            $field = $fields[$position];

            if (true){//globals_get("db.escape_identifiers") {
                $escapedField = $this->escapeIdentifier($field);
            } else {
                $escapedField = $field;
            }

            if (is_object($value)) {
                $placeholders[] = $escapedField . " = " . $value;
            } else {
                if ($value === null) { // (0 ==) null is true
                    $placeholders[] = $escapedField . " = null";
                } else {
                    $updateValues[] = $value;
                    if (is_array($dataTypes)) {
                        if (!isset($dataTypes[$position])) {
                            throw new \Phalcon\Db\Exception("Incomplete number of bind types");
                        }
                        $bindType = $dataTypes[$position];
                        $bindDataTypes[] = $bindType;
                    }
                    $placeholders[] = $escapedField . " = ?";
                }
            }
        }

        if (true){//globals_get("db.escape_identifiers") {
            $escapedTable = $this->escapeIdentifier($table);
        } else {
            $escapedTable = $table;
        }


        $setClause = join(", ", $placeholders);

        if ($whereCondition !== null) {

            $updateSql = "UPDATE " . $escapedTable . " SET " . $setClause . " WHERE ";

            /**
             * String conditions are simply appended to the SQL
             */
            if (!is_array($whereCondition)) {
                $updateSql .= $whereCondition;
            } else {

                /**
                 * Array conditions may have bound params and bound types
                 */
                if (!is_array($whereCondition)) {
                    throw new \Phalcon\Db\Exception("Invalid WHERE clause conditions");
                }

                /**
                 * If an index 'conditions' is present it contains string where conditions that are appended to the UPDATE sql
                 */
                if (isset($whereCondition["conditions"])) {
                    $conditions = $whereCondition['conditions'];
                    $updateSql .= $conditions;
                }

                /**
                 * Bound parameters are arbitrary values that are passed by separate
                 */
                if (isset($whereCondition["bind"])) {
                    $whereBind = $whereCondition["bind"];
                    $updateValues = array_merge($updateValues, $whereBind);
                }

                /**
                 * Bind types is how the bound parameters must be casted before be sent to the database system
                 */
                if (isset($whereCondition["bindTypes"])) {
                    $whereTypes = $whereCondition['bindTypes'];
                    $bindDataTypes = array_merge($bindDataTypes, $whereTypes);
                }
            }
        } else {
            $updateSql = "UPDATE " . $escapedTable . " SET " . $setClause;
        }

        /**
         * Perform the update via PDO::execute
         */
        //					echo PHP_EOL . $updateSql;
        //					var_dump($updateValues);
        return $this->execute($updateSql, $updateValues, $bindDataTypes);
    }



    public function lastInsertId($tableName = null, $primaryKey = null)
    {
        //$sql = 'SELECT SCOPE_IDENTITY()';
        $sql = 'SELECT @@IDENTITY';
        $result = $this->fetchOne($sql);
        return array_pop($result);
    }


    public function delete($table, $whereCondition = null, $placeholders = null, $dataTypes = null)
    {
        $sql;
        $escapedTable;

        if (true) { // globals_get("db.escape_identifiers") {
            $escapedTable = $this->escapeIdentifier($table);
        } else {
            $escapedTable = $table;
        }


        if (!empty($whereCondition)) {
            $sql = "DELETE FROM " . $escapedTable . " WHERE " . $whereCondition;
        } else {
            $sql = "DELETE FROM " . $escapedTable;
        }

        /**
         * Perform the update via PDO::execute
         */
        return $this->execute($sql, $placeholders, $dataTypes);
    }

    public function fetchOne($sqlQuery, $fetchMode = Db::FETCH_ASSOC, $bindParams=null, $bindTypes=null)
	{

		$result = $this->query($sqlQuery, $bindParams, $bindTypes);
		if ( is_object($result) ) {
            if ( is_null($fetchMode) ) {
                $result->setFetchMode($fetchMode);
			}
			$data = $result->fetch();
            $result->getInternalResult()->closeCursor();
            return $data;
		}
		return array();
	}

    /**
     * Lists table indexes
     *
     *<code>
     *	print_r($connection->describeIndexes('robots_parts'));
     *</code>
     *
     * @param	string table
     * @param	string schema
     * @return	Phalcon\Db\Index[]
     */
    public function describeIndexes($table, $schema = null)
    {

        $dialect = $this->_dialect;

        $indexes = array();
        $temps = $this->fetchAll($dialect->describeIndexes($table, $schema), \Phalcon\Db::FETCH_ASSOC);
        foreach ($temps as $index) {
            $keyName = $index['index_id'];
            if (!isset($indexes[$keyName])) {
                $indexes[$keyName] = array();
            }

            //let indexes[keyName][] = index[4];
        }

        $indexObjects = array();
        foreach ($indexes as $name => $indexColumns) {

            /**
             * Every index is abstracted using a Phalcon\Db\Index instance
             */
            $indexObjects[$name] = new \Phalcon\Db\Index($name, $indexColumns);
        }

        return $indexObjects;
    }

    /**
     * Lists table references
     *
     *<code>
     * print_r($connection->describeReferences('robots_parts'));
     *</code>
     *
     * @param	string table
     * @param	string schema
     * @return	Phalcon\Db\Reference[]
     */
    public function describeReferences($table, $schema = null)
    {

        $dialect = $this->_dialect;

        $emptyArr = array();
        $references = array();

        $temps = $this->fetchAll($dialect->describeReferences($table, $schema), \Phalcon\Db::FETCH_NUM);
        foreach ($temps as $reference) {

            $constraintName = $reference[2];
            if (!isset($references[$constraintName])) {
                $references[$constraintName] = array(
                        "referencedSchema"  => $reference[3],
                        "referencedTable"   => $reference[4],
                        "columns"           => $emptyArr,
                        "referencedColumns" => $emptyArr
                        );
            }

            //let references[constraintName]["columns"][] = reference[1],
            //	references[constraintName]["referencedColumns"][] = reference[5];
        }

        $referenceObjects = array();
        foreach ($references as $name => $arrayReference) {
            $referenceObjects[$name] = new \Phalcon\Db\Reference($name, array(
                        "referencedSchema"	=> $arrayReference["referencedSchema"],
                        "referencedTable"	=> $arrayReference["referencedTable"],
                        "columns"			=> $arrayReference["columns"],
                        "referencedColumns" => $arrayReference["referencedColumns"]
                        ));
        }

        return $referenceObjects;
    }

    /**
     * Gets creation options from a table
     *
     *<code>
     * print_r($connection->tableOptions('robots'));
     *</code>
     *
     * @param	string tableName
     * @param	string schemaName
     * @return	array
     */
    public function tableOptions($tableName, $schemaName = null)
    {
        $dialect = $this->_dialect;
        $sql = $dialect->tableOptions($tableName, $schemaName);
        if ($sql) {
            $describe = $this->fetchAll($sql, \Phalcon\DB::FETCH_NUM);
            return $describe[0];
        }
        return array();
    }

    /**
     * Creates a PDO DSN for the adapter from $this->_config settings.
     *
     * @return string
     */
    protected function _dsn()
    {
        // baseline of DSN parts
        $dsn = $this->_config;

        // don't pass the username and password in the DSN
        unset($dsn['username']);
        unset($dsn['password']);
        unset($dsn['options']);
        unset($dsn['persistent']);
        unset($dsn['driver_options']);

        if (isset($dsn['port'])) {
            $seperator = ':';
            if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                $seperator = ',';
            }
            $dsn['host'] .= $seperator . $dsn['port'];
            unset($dsn['port']);
        }

        // this driver supports multiple DSN prefixes
        // @see http://www.php.net/manual/en/ref.pdo-dblib.connection.php
        if (isset($dsn['pdoType'])) {
            switch (strtolower($dsn['pdoType'])) {
                case 'freetds':
                case 'sybase':
                    $this->_pdoType = 'sybase';
                    break;
                case 'mssql':
                    $this->_pdoType = 'mssql';
                    break;
                case 'dblib':
                default:
                    $this->_pdoType = 'dblib';
                    break;
            }
            unset($dsn['pdoType']);
        }

        // use all remaining parts in the DSN
        foreach ($dsn as $key => $val) {
            $dsn[$key] = "$key=$val";
        }

        $dsn = $this->_pdoType . ':' . implode(';', $dsn);
        return $dsn;
    }


    /**
     * Begin a transaction.
     *
     * It is necessary to override the abstract PDO transaction functions here, as
     * the PDO driver for MSSQL does not support transactions.
     */
    public function begin($nesting = false)
    {
        //						$this->execute('SET QUOTED_IDENTIFIER OFF');
        //						$this->execute('SET NOCOUNT OFF');
        $this->execute('BEGIN TRANSACTION;');
        return true;
    }

    /**
     * Commit a transaction.
     *
     * It is necessary to override the abstract PDO transaction functions here, as
     * the PDO driver for MSSQL does not support transactions.
     */
    public function commit($nesting = false)
    {
        $this->execute('COMMIT TRANSACTION');
        return true;
    }

    /**
     * Roll-back a transaction.
     *
     * It is necessary to override the abstract PDO transaction functions here, as
     * the PDO driver for MSSQL does not support transactions.
     */
    public function rollBack($nesting = false)
    {
        $this->execute('ROLLBACK TRANSACTION');
        return true;
    }

    public function getTransactionLevel()
    {
        return (int)$this->fetchOne('SELECT @@TRANCOUNT as level');
    }
}
