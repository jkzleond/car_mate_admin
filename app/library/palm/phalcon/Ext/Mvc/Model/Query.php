<?php
namespace Palm\Phalcon\Ext\Mvc\Model;
use \Phalcon\Mvc\Model\Query as PhalconQuery;
use \Phalcon\Mvc\Model\Row;
use \Phalcon\Mvc\Model\Resultset\Simple;
use \Phalcon\Mvc\Model\Resultset\Complex;

class Query extends PhalconQuery
{
	// public function __construct($phal=null, $di=null, $options=null)
	// {
	// 	parent::__construct($phal, $di, $options);
	// }

	public function execute($bindParams=null, $bindTypes=null)
	{
		$uniqueRow = $this->_uniqueRow;

		$cacheOptions = $this->_cacheOptions;
		if (!is_null($cacheOptions)) {

			if (!is_array($cacheOptions)) {
				throw new \Exception("Invalid caching options");
			}

			/**
			 * The user must set a cache key
			 */
			$key = isset($cacheOptions['key']) ? $cacheOptions['key'] : null;
			if (!$key) {
				throw new \Exception("A cache key must be provided to identify the cached resultset in the cache backend");
			}

			/**
			 * By default use use 3600 seconds (1 hour) as cache lifetime
			 */
			$lifetime = isset($cacheOptions['lifetime']) ? $cacheOptions['lifetime'] : 3600;

			/**
			 * "modelsCache" is the default name for the models cache service
			 */
			$cacheService = isset($cacheOptions['service']) ? $cacheOptions['service'] : "modelsCache";

			$cache = $this->_dependencyInjector->getShared($cacheService);
			if (!is_object($cache)) {
				throw new \Exception("Cache service must be an object");
			}

			$result = $cache->get($key, $lifetime);
			if (!is_null($result)) {

				if (!is_object($result)) {
					throw new \Exception("Cache didn't return a valid resultset");
				}

				$result->setIsFresh(false);

				/**
				 * Check if only the first row must be returned
				 */
				if ($uniqueRow) {
					$preparedResult = $result->getFirst();
				} else {
					$preparedResult = $result;
				}

				return $preparedResult;
			}

			$this->_cache = $cache;
		}

		/**
		 * The statement is parsed from its PHQL string or a previously processed IR
		 */
		$intermediate = $this->parse();

		/**
		 * Check for default bind parameters and merge them with the passed ones
		 */
		$defaultBindParams = $this->_bindParams;
		if (is_array($defaultBindParams)) {
			if (is_array($bindParams)) {
				$mergedParams = array_merge($defaultBindParams, $bindParams);
			} else {
				$mergedParams = $defaultBindParams;
			}
		} else {
			$mergedParams = $bindParams;
		}

		/**
		 * Check for default bind types and merge them with the passed ones
		 */
		$defaultBindTypes = $this->_bindTypes;
		if (is_array($defaultBindTypes)) {
			if (is_array($bindTypes)) {
				$mergedTypes = array_merge($defaultBindTypes, $bindTypes);
			} else {
				$mergedTypes = $defaultBindTypes;
			}
		} else {
			$mergedTypes = $bindTypes;
		}

		if ( (!is_null($mergedParams)) && (!is_array($mergedParams)) ) {
			throw new \Exception("Bound parameters must be an array");
		}

		if ( (!is_null($mergedTypes)) && (!is_array($mergedTypes)) ) {
			throw new \Exception("Bound parameter types must be an array");
		}

		$type = $this->_type;
		// echo $type;
		// exit;

		switch ($type) {

			case self::TYPE_SELECT:
				$result = $this->_execute_select($intermediate, $mergedParams, $mergedTypes);
				break;

			case self::TYPE_INSERT:
				$result = $this->_executeInsert($intermediate, $mergedParams, $mergedTypes);
				break;

			case self::TYPE_UPDATE:
				$result = $this->_executeUpdate($intermediate, $mergedParams, $mergedTypes);
				break;

			case self::TYPE_DELETE:
				$result = $this->_executeDelete($intermediate, $mergedParams, $mergedTypes);
				break;

			default:
				throw new \Exception("Unknown statement " . $type);
		}

		/**
		 * We store the resultset in the cache if any
		 */
		if (!is_null($cacheOptions)) {

			/**
			 * Only PHQL SELECTs can be cached
			 */
			if ( $type != self::TYPE_SELECT ) {
				throw new \Exception("Only PHQL statements that return resultsets can be cached");
			}

			$cache->save($key, $result, $lifetime);
		}

		/**
		 * Check if only the first row must be returned
		 */
		if ($uniqueRow) {
			$preparedResult = $result->getFirst();
		} else {
			$preparedResult = $result;
		}

		return $preparedResult;	
	}

	protected function _execute_select($intermediate, $bindParams, $bindTypes, $simulate=false)
	{
		$manager =$this->_manager;

		/**
		 * Get a database connection
		 */
		$connectionTypes = array();
		$models = $intermediate["models"];

		foreach ($models as $modelName) {

			// Load model if it is not loaded
			$model = isset($this->_modelsInstances[$modelName]) ? $this->_modelsInstances[$modelName] : null;
			if (is_null($model)) {
				$model = $manager->load($modelName, true);
				$this->_modelsInstances[$modelName] = $model;
			}

			// Get database connection
			if ( method_exists($model, "selectReadConnection") ) {
				// use selectReadConnection() if implemented in extended Model class
				$connection = $model->selectReadConnection($intermediate, $bindParams, $bindTypes);
				if ( !is_object($connection) ) {
					throw new \Exception("'selectReadConnection' didn't return a valid connection");
				}
			} else {
				$connection = $model->getReadConnection();
			}

			// More than one type of connection is not allowed
			$connectionTypes[$connection->getType()] = true;
			if ( count($connectionTypes) == 2 ) {
				throw new \Exception("Cannot use models of different database systems in the same query");
			}
		}

		$columns = $intermediate["columns"];

		$haveObjects = false;
		$haveScalars = false;
		$isComplex = false;

		// Check if the resultset have objects and how many of them have
		$numberObjects = 0;
		$columns1 = $columns;

		foreach ( $columns as $column ) {

			if ( !is_array($column) ) {
				throw new \Exception("Invalid column definition");
			}

			if ( $column["type"] == "scalar" ) {
				if ( !isset($column["balias"]) ) {
					$isComplex = true;
				}
				$haveScalars = true;
			} else {
				$haveObjects = true;
				$numberObjects++;
			}
		}

		// Check if the resultset to return is complex or simple
		if ( $isComplex === false ) {
			if ( $haveObjects === true ) {
				if ( $haveScalars === true ) {
					$isComplex = true;
				} else {
					if ( $numberObjects == 1 ) {
						$isSimpleStd = false;
					} else {
						$isComplex = true;
					}
				}
			} else {
				$isSimpleStd = true;
			}
		}

		// Processing selected columns
		$instance = null;
		$selectColumns = array();
		$simpleColumnMap = array();
		$metaData =$this->_metaData;

		foreach ( $columns as $aliasCopy => $column ) {

			$sqlColumn = $column["column"];

			// Complete objects are treated in a different way
			if ( $column["type"] == "object" ) {

				$modelName = $column["model"];

				/**
				 * Base instance
				 */
				$instance = isset($this->_modelsInstances[$modelName]) ? $this->_modelsInstances[$modelName] : null;
				if (is_null(null)) {
					$instance = $manager->load($modelName);
					$this->_modelsInstances[$modelName] = $instance;
				}

				$attributes = $metaData->getAttributes($instance);
				if ( $isComplex === true ) {

					// If the resultset is complex we open every model into their columns
					if ( $GLOBALS['orm.column_renaming'] ) {
						$columnMap = $metaData->getColumnMap($instance);
					} else {
						$columnMap = null;
					}

					// Add every attribute in the model to the generated select
					foreach ( $attributes as $attribute ) {
						$selectColumns[] = array($attribute, $sqlColumn, "_" . $sqlColumn . "_" . $attribute);
					}

					// We cache required meta-data to make its future access faster
					$columns1[$aliasCopy]["instance"] = $instance;
					$columns1[$aliasCopy]["attributes"] = $attributes;
					$columns1[$aliasCopy]["columnMap"] = $columnMap;

					// Check if the model keeps snapshots
					$isKeepingSnapshots = $manager->isKeepingSnapshots($instance);
					if ( $isKeepingSnapshots ) {
						$columns1[$aliasCopy]["keepSnapshots"] = $isKeepingSnapshots;
					}

				} else {

					/**
					 * Query only the columns that are registered as attributes in the metaData
					 */
					foreach ( $attributes as $attribute ) {
						$selectColumns[] = [$attribute, $sqlColumn];
					}
				}
			} else {

				/**
				 * Create an alias if the column doesn't have one
				 */
				if ( is_int($aliasCopy) ) {
					$columnAlias = array($sqlColumn, null);
				} else {
					$columnAlias = array($sqlColumn, null, $aliasCopy);
				}
				$selectColumns[] = $columnAlias;
			}

			/**
			 * Simulate a column map
			 */
			if ( ( $isComplex === false ) && $isSimpleStd === true ) {
				$sqlAlias = isset( $column['sqlAlias'] ) ? $column['sqlAlias'] : null;
				if ( $sqlAlias ) {
					$simpleColumnMap[$sqlAlias] = $aliasCopy;
				} else {
					$simpleColumnMap[$aliasCopy] = $aliasCopy;
				}
			}
		}

		$bindCounts = array();
		$intermediate["columns"] = $selectColumns;

		/**
		 * Replace the placeholders
		 */
		if ( is_array($bindParams) ) {
			$processed = array();
			foreach ( $bindParams as $wildcard => $value) {

				if ( is_int($wildcard) ) {
					$wildcardValue = ":" . $wildcard;
				} else {
					$wildcardValue = $wildcard;
				}

				$processed[$wildcardValue] = $value;
				if ( is_array( $value ) ) {
					$bindCounts[$wildcardValue] = count($value);
				}
			}
		} else {
			$processed = $bindParams;
		}

		/**
		 * Replace the bind Types
		 */
		if ( is_array($bindTypes) ) {
			$processedTypes = array();
			foreach ( $bindTypes as $typeWildcard => $value) {
				if ( is_int($typeWildcard) ) {
					$processedTypes[":" . $typeWildcard] = $value;
				} else {
					$processedTypes[$typeWildcard] = $value;
				}
			}
		} else {
			$processedTypes = $bindTypes;
		}

		if ( count($bindCounts) ) {
			$intermediate["bindCounts"] = $bindCounts;
		}

		/**
		 * The corresponding SQL dialect generates the SQL statement based accordingly with the database system
		 */
		$dialect = $connection->getDialect();
		$sqlSelect = $dialect->select($intermediate);
		if ( $this->_sharedLock ) {
			$sqlSelect = $dialect->sharedLock($sqlSelect);
		}

		/**
		 * Return the SQL to be executed instead of execute it
		 */
		if ( $simulate ) {
			return array(
				"sql"       => $sqlSelect,
				"bind"      => $processed,
				"bindTypes" => $processedTypes
			);
		}

		/**
		 * Execute the query
		 */
		$result = $connection->query($sqlSelect, $processed, $processedTypes);
		
		/**
		 * Check if the query has data
		 */
		if ( $result->numRows() ) {
			// if (strpos($sqlSelect, '= 2') and !strpos($sqlSelect, 'COUNT(*)'))
			// {
			// 	//echo get_class($result);
			// 	//echo $sqlSelect;
			// 	//print_r($result->numRows($result));
			// 	//print_r($result->fetchArray());
			// 	//print_r($result);
			// 	//exit;
			// }
			$resultData = $result;
		} else {
			$resultData = false;
		}

		/**
		 * Choose a resultset type
		 */
		$cache = $this->_cache;

		if ( $isComplex === false ) {

			/**
			 * Select the base object
			 */
			if ( $isSimpleStd === true ) {

				/**
				 * If the result is a simple standard object use an Phalcon\Mvc\Model\Row as base
				 */
				$resultObject = new Row();

				/**
				 * Standard objects can't keep snapshots
				 */
				$isKeepingSnapshots = false;

			} else {

				if ( is_object( $instance ) ) {
					$resultObject = $instance;
				} else {
					$resultObject = $model;
				}

				/**
				 * Get the column map
				 */
				if ( !isset($GLOBALS['orm.cast_on_hydrate']) or !$GLOBALS['orm.cast_on_hydrate'] ) {
					$simpleColumnMap = $metaData->getColumnMap($model);
				} else {

					$columnMap = $metaData->getColumnMap($model);
					$typesColumnMap = $metaData->getDataTypes($model);

					if ( is_null($columnMap) ) {
						$simpleColumnMap = array();
						foreach ( $metaData->getAttributes($model) as $attribute ) {
							$simpleColumnMap[$attribute] = array($attribute, $typesColumnMap[$attribute]);
						}
					} else {
						$simpleColumnMap = array();
						foreach ( $columnMap as $column => $attribute) {
							$simpleColumnMap[$column] = [$attribute, $typesColumnMap[$column]];
						}
					}
				}

				/**
				 * Check if the model keeps snapshots
				 */
				$isKeepingSnapshots = $manager->isKeepingSnapshots($model);
			}

			/**
			 * Simple resultsets contains only complete objects
			 */
			return new Simple($simpleColumnMap, $resultObject, $resultData, $cache, $isKeepingSnapshots);
		}

		/**
		 * Complex resultsets may contain complete objects and scalars
		 */
		return new Complex($columns1, $resultData, $cache);
	}
}