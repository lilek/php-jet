<?php
/**
 *
 * Available object definition backend options:
 *      @see DataModel_Backend_MySQL_Config
 *
 * Available property definition backend options:
 *		column_type:
 *				string, default: null (auto)
 *				Force MySQL column type and options definition
 *		key:
 *				string, default: null
 *				Key on column(s)
 *		key_type:
 *				string: default: null
 *				Type of the key. Options: INDEX(default), UNIQUE
 *		autoincrement:
 *				bool, default: false
 *				Use auto increment for ID
 *
 *
 * @copyright Copyright (c) 2011-2012 Miroslav Marek <mirek.marek.2m@gmail.com>
 * @license http://www.gnu.org/licenses/agpl-3.0.html AGPLv3
 * @author Miroslav Marek <mirek.marek.2m@gmail.com>
 * @version <%VERSION%>
 *
 * @category Jet
 * @package DataModel
 * @subpackage DataModel_Backend
 */
namespace Jet;

class DataModel_Backend_MySQL extends DataModel_Backend_Abstract {
	const PRIMARY_KEY_NAME = "PRIMARY";

	/**
	 * @var DataModel_Backend_MySQL_Config
	 */
	protected $config;

	/**
	 *
	 * @var Db_Adapter_Abstract
	 */
	private $_db_read = NULL;
	/**
	 *
	 * @var Db_Adapter_Abstract
	 */
	private $_db_write = NULL;

	/**
	 * @var array
	 */
	protected static $valid_key_types = array(
		DataModel::KEY_TYPE_PRIMARY,
		DataModel::KEY_TYPE_INDEX,
		DataModel::KEY_TYPE_UNIQUE,
		//DataModel::KEY_TYPE_FULLTEXT
	);


	/**
	 *
	 */
	public function initialize() {
		$this->_db_read = Db::get( $this->config->getConnectionRead() );
		$this->_db_write = Db::get( $this->config->getConnectionWrite() );
	}

	/**
	 * @param DataModel $data_model
	 * @param string|null $force_table_name (optional)
	 *
	 * @return string
	 */
	public function helper_getCreateCommand( DataModel $data_model, $force_table_name=null ) {

		$data_model_definition = $data_model->getDataModelDefinition();
		$options = array();

		$options["ENGINE"] = $this->config->getEngine();
		$options["DEFAULT CHARSET"] = $this->config->getDefaultCharset();
		$options["COLLATE"] = $this->config->getCollate();

		$_options = array();

		foreach($options as $o=>$v) {
			$_options[] = "{$o}={$v}";
		}

		$_options = implode(" ", $_options);


		$_columns = array();
		$_keys = array();

		foreach( $data_model_definition->getProperties() as $name=>$property ) {
			if( !$property->getIsID() ) {
				continue;
			}

			$_columns[] = "\t`{$name}` ".$this->_getSQLType( $data_model, $property, $_keys );
		}

		foreach( $data_model_definition->getProperties() as $name=>$property ) {
			if(
				$property->getIsID() ||
				$property->getIsDataModel()
			) {
				continue;
			}

			$_columns[] = "\t`{$name}` ".$this->_getSQLType( $data_model, $property, $_keys );
		}

		foreach($_keys as $key_name=>$key) {
			switch( $key["type"] ) {
				case DataModel::KEY_TYPE_PRIMARY:
					$_keys[$key_name] = "\n\t,PRIMARY KEY (`".implode("`, `", $key["columns"])."`)";
				break;
				case DataModel::KEY_TYPE_INDEX:
					$_keys[$key_name] = "\n\t,KEY `{$key_name}`  (`".implode("`, `", $key["columns"])."`)";
				break;
				default:
					$_keys[$key_name] = "\n\t,{$key["type"]} KEY `{$key_name}`  (`".implode("`, `", $key["columns"])."`)";
				break;
			}
		}

		$table_name = $this->_getTableName( $data_model_definition );

		$table_name = $force_table_name ? $force_table_name : $table_name;
		
		$q = "CREATE TABLE IF NOT EXISTS `{$table_name}` (\n";
		$q .= implode(",\n", $_columns);
		$q .= implode("", $_keys);
		$q .= "\n) {$_options};\n\n";
		
		return $q;
	}

	/**
	 * @param DataModel $data_model
	 */
	public function helper_create( DataModel $data_model ) {
		$this->_db_write->query( $this->helper_getCreateCommand( $data_model ) );
	}

	/**
	 * @param DataModel $data_model
	 *
	 * @return string
	 */
	public function helper_getDropCommand( DataModel $data_model ) {
		$table_name = $this->_getTableName( $data_model->getDataModelDefinition() );
		$ui_prefix = "_d".date("YmdHis");

		return "RENAME TABLE `{$table_name}` TO `{$ui_prefix}{$table_name}`";
	}

	/**
	 * @param DataModel $data_model
	 */
	public function helper_drop( DataModel $data_model ) {
		$this->_db_write->query( $this->helper_getDropCommand( $data_model ) );
	}

	/**
	 * @param DataModel $data_model
	 *
	 * @return array
	 */
	public function helper_getUpdateCommand( DataModel $data_model ) {
		$data_model_definition = $data_model->getDataModelDefinition();
		$table_name = $this->_getTableName($data_model_definition);

		$update_prefix = "_UP".date("YmdHis")."_";
		$exists_cols = $this->_db_write->fetchCol("DESCRIBE `{$table_name}`");

		$updated_table_name = $update_prefix.$table_name;

		$create_command = $this->helper_getCreateCommand( $data_model, $updated_table_name );


		$properties = $data_model_definition->getProperties();
		$actual_cols = array();
		foreach($properties as $property_name=>$property) {
			if( $property->getIsDataModel() ) {
				continue;
			}
			$actual_cols[$property_name] = $property;
		}

		$common_cols = array_intersect(array_keys($actual_cols), $exists_cols);
		$_new_cols = array_diff( array_keys($actual_cols), $exists_cols );
		$new_cols = new DataModel_RecordData($data_model_definition);
		foreach($_new_cols as $new_col) {
			$new_cols->addItem($properties[$new_col], $properties[$new_col]->getDefaultValue());
		}
		$new_cols = $this->_getRecord($new_cols);

		$data_migration_command = "INSERT INTO `$updated_table_name`
					(`".implode("`,`", $common_cols)."`)
				SELECT
					`".implode("`,`", $common_cols)."`
				FROM `{$table_name}`;";

		$update_default_values = "";
		if($new_cols) {
			$_new_cols = array();
			foreach($new_cols as $c=>$v) {
				$_new_cols[] = "`{$c}`='".addslashes($v)."'";
			}
			$update_default_values = "UPDATE `$updated_table_name` SET ".implode(",\n", $_new_cols);
		}


		$rename_command1 = "RENAME TABLE `{$table_name}` TO `{$update_prefix}b_{$table_name}` ;\n";
		$rename_command2 = "RENAME TABLE `{$updated_table_name}` TO  `{$table_name}`; ";

		$update_command = array();
		$update_command[] = $create_command;
		$update_command[] = $data_migration_command;
		if($update_default_values) {
			$update_command[] = $update_default_values;
		}
		$update_command[] = $rename_command1;
		$update_command[] = $rename_command2;

		return $update_command;
	}

	/**
	 * @param DataModel $data_model
	 *
	 * @throws \Exception|Exception
	 */
	public function helper_update( DataModel $data_model ) {
		$this->transactionStart();
		try {
			foreach($this->helper_getUpdateCommand( $data_model ) as $q) {
				$this->_db_write->query( $q );
			}
		} catch (Exception $e) {
			$this->transactionRollback();
			throw $e;
		}
		$this->transactionCommit();
	}


	/**
	 * @param DataModel_Query $query
	 *
	 * @return string
	 */
	public function getBackendSelectQuery( DataModel_Query $query ) {

		return "SELECT\n\t"
			.$this->_getSQLQuerySelectPart($query)
			."\nFROM\n\t"
			.$this->_getSQLQueryTableName($query)
			.$this->_getSQLQueryJoinPart($query)

			.$this->_getSQLqueryWherePart($query->getWhere())
			.$this->_getSQLQueryGroupPart($query)
			.$this->_getSQLQueryHavingPart($query->getHaving())
			.$this->_getSQLQueryOrderByPart($query)
			.$this->_getSQLQueryLimitPart($query);

	}


	/**
	 * @param DataModel_Query $query
	 *
	 * @return string
	 */
	public function getBackendCountQuery( DataModel_Query $query ) {
		return "SELECT count(*) FROM\n\t"
			.$this->_getSQLQueryTableName($query)
			.$this->_getSQLQueryJoinPart($query)
			.$this->_getSQLqueryWherePart($query->getWhere())
			.$this->_getSQLQueryGroupPart($query)
			.$this->_getSQLQueryHavingPart($query->getHaving());
	}

	/**
	 * @param DataModel_RecordData $record
	 *
	 *
	 * @return string
	 */
	public function getBackendInsertQuery( DataModel_RecordData $record ) {

		$data_model_definition = $record->getDataModelDefinition();

		$table_name = $this->_getTableName($data_model_definition);

		$set = array();

		foreach($this->_getRecord($record) as $k=>$v) {
			if($v===null) {
				$set[] = "`{$k}`=null";
			} else
			if(is_string($v)) {
				$set[] = "`{$k}`='".addslashes($v)."'";
			} else {
				$set[] = "`{$k}`=".$v;
			}
		}

		$set = implode(",\n", $set);

		return "INSERT INTO `{$table_name}` SET \n{$set}";
	}

	/**
	 * @param DataModel_RecordData $record
	 * @param DataModel_Query $where
	 *
	 * @return string
	 */
	public function getBackendUpdateQuery( DataModel_RecordData $record, DataModel_Query $where ) {
		$data_model_definition = $record->getDataModelDefinition();
		$table_name = $this->_getTableName($data_model_definition);

		$set = array();

		foreach($this->_getRecord($record) as $k=>$v) {
			if($v===null) {
				$set[] = "`{$k}`=null";
			} else
			if(is_string($v)) {
				$set[] = "`{$k}`='".addslashes($v)."'";
			} else {
				$set[] = "`{$k}`=".$v;
			}
		}

		$set = implode(",\n", $set);

		$where = $this->_getSQLqueryWherePart($where->getWhere());

		return "UPDATE `{$table_name}` SET \n{$set}{$where}";

	}

	/**
	 * @param DataModel_Query $where
	 *
	 * @return string
	 */
	public function getBackendDeleteQuery( DataModel_Query $where ) {
		$table_name = $this->_getTableName($where->getMainDataModel()->getDataModelDefinition());
		return "DELETE FROM `{$table_name}`".$this->_getSQLqueryWherePart($where->getWhere());
	}

	/**
	 * @param DataModel_RecordData $record
	 *
	 * @return mixed
	 */
	public function save( DataModel_RecordData $record ) {
		$this->_db_write->query( $this->getBackendInsertQuery($record) );

		return $this->_db_write->lastInsertId();
	}

	/**
	 * @param DataModel_RecordData $record
	 * @param DataModel_Query $where
	 *
	 * @return int
	 */
	public function update( DataModel_RecordData $record, DataModel_Query $where) {
		return $this->_db_write->query( $this->getBackendUpdateQuery($record, $where) );
	}

	/**
	 * @param DataModel_Query $where
	 *
	 * @return int
	 */
	public function delete( DataModel_Query $where ) {
		return $this->_db_write->query( $this->getBackendDeleteQuery($where) );
	}

	/**
	 * @param DataModel_Query $query
	 *
	 * @return int
	 */
	public function getCount( DataModel_Query $query ) {
		return (int)$this->_db_read->fetchOne( $this->getBackendCountQuery($query) );
	}

	/**
	 * @param DataModel_Query $query
	 *
	 * @return mixed
	 */
	public function fetchAll( DataModel_Query $query ) {
		return $this->_fetch($query, "fetchAll");
	}

	/**
	 * @param DataModel_Query $query
	 *
	 * @return mixed
	 */
	public function fetchAssoc( DataModel_Query $query ) {
		return $this->_fetch($query, "fetchAssoc");
	}

	/**
	 * @param DataModel_Query $query
	 *
	 * @return mixed
	 */
	public function fetchPairs( DataModel_Query $query ) {
		return $this->_fetch($query, "fetchPairs");
	}

	/**
	 * @param DataModel_Query $query
	 *
	 * @return mixed
	 */
	public function fetchRow( DataModel_Query $query ) {
		return $this->_fetch($query, "fetchRow");
	}

	/**
	 * @param DataModel_Query $query
	 *
	 * @return mixed
	 */
	public function fetchOne( DataModel_Query $query ) {
		return $this->_fetch($query, "fetchOne");
	}

	/**
	 * @param DataModel_Query $query
	 * @param string $fetch_method
	 *
	 * @return mixed
	 */
	protected function _fetch( DataModel_Query $query, $fetch_method ) {

		$data = $this->_db_read->$fetch_method(
			$this->getBackendSelectQuery( $query )
		);

		if(!is_array($data)) {
			return $data;
		}

		foreach($data as $i=>$d) {
			foreach($query->getSelect() as $item) {
				/**
				 * @var DataModel_Query_Select_Item $item
				 * @var DataModel_Definition_Property_Abstract $property
				 */
				$property = $item->getItem();

				if( ! ($property instanceof DataModel_Definition_Property_Abstract) ) {
					continue;
				}

				$key = $item->getSelectAs();

				if($property->getIsArray()) {
					$data[$i][$key] = unserialize( $data[$i][$key] );
				}

				$property->checkValueType( $data[$i][$key] );

			}
		}

		return $data;
	}

	/**
	 *
	 */
	public function transactionStart() {
		$this->_db_write->beginTransaction();
	}

	/**
	 *
	 */
	public function transactionCommit() {
		$this->_db_write->commit();
	}

	/**
	 *
	 */
	public function transactionRollback() {
		$this->_db_write->rollBack();
	}


	/**
	 *
	 * @param DataModel_Query $query
	 *
	 * @return string
	 */
	protected function _getSQLQuerySelectPart( DataModel_Query $query ) {
		$columns_qp = array();


		$mapper = function(DataModel_Definition_Property_Abstract $property) {
			return $this->_getColumnName($property);
		};

		foreach( $query->getSelect() as $item ) {
			/**
			 * @var DataModel_Query_Select_Item $item
			 */
			$property = $item->getItem();
			$select_as = $item->getSelectAs();

			if($property instanceof DataModel_Definition_Property_Abstract) {
				/**
				 * @var DataModel_Definition_Property_Abstract $property
				 */
				$columns_qp[] = $this->_getColumnName($property)." AS `{$select_as}`";

				continue;
			}

			if($property instanceof DataModel_Query_Select_Item_BackendFunctionCall) {

				/**
				 * @var DataModel_Query_Select_Item_BackendFunctionCall $property
				 */

				$backend_function_call = $property->toString( $mapper );

				$columns_qp[] = "{$backend_function_call} AS `{$select_as}`";
				continue;
			}

		}

		return implode(",\n\t", $columns_qp);
	}

	/**
	 * @param DataModel_Query $query
	 *
	 * @return string
	 */
	protected function _getSQLQueryTableName( DataModel_Query $query ) {
		$main_model_definition = $query->getMainDataModel()->getDataModelDefinition();
		return "`".$this->_getTableName( $main_model_definition )."`";

	}

	/**
	 * @param DataModel_Query $query
	 *
	 * @throws DataModel_Backend_Exception
	 * @return string
	 */
	protected  function _getSQLQueryJoinPart( DataModel_Query $query ) {
		$join_qp = "";

		foreach($query->getRelations() as $relation) {
			$r_ID_properties = $relation->getJoinByProperties();
			$r_table_name = $this->_getTableName( $relation->getRelatedDataModelDefinition() );


			switch( $relation->getJoinType() ) {
				case DataModel_Query::JOIN_TYPE_LEFT_JOIN:
					$join_qp .= "\n\t\tJOIN `{$r_table_name}` ON\n";
				break;
				case DataModel_Query::JOIN_TYPE_LEFT_OUTER_JOIN:
					$join_qp .= "\n\t\tLEFT OUTER JOIN `{$r_table_name}` ON\n";
				break;
				default:
					throw new DataModel_Backend_Exception(
						"MySQL backend: unknown join type '{$relation->getJoinType()}'",
						DataModel_Backend_Exception::CODE_BACKEND_ERROR
					);
				break;
			}


			$j = array();

			foreach( $r_ID_properties as $r_property_definition ) {
				/**
				 * @var DataModel_Definition_Property_Abstract $r_property_definition
				 */
				$rt_property = $r_property_definition->getRelatedToProperty();

				$j[] = "\t\t\t".$this->_getColumnName($r_property_definition)." = ".$this->_getColumnName($rt_property);

			}

			$join_qp .= implode(" AND \n", $j);
		}

		return $join_qp;
	}


	/**
	 * @param DataModel_Query_Where $query
	 *
	 * @param int $level (optional)
	 *
	 * @return string
	 */
	protected function _getSQLQueryWherePart( DataModel_Query_Where $query=null, $level=0 ) {
		if(!$query) {
			return "";
		}
		$res = "";

		$next_level = $level+1;
		$tab = str_repeat("\t", $next_level);

		foreach( $query as $qp ) {
			if( $qp instanceof DataModel_Query_Where ) {
				/**
				 * @var DataModel_Query_Where $qp
				 */
				$res .= $tab."(\n".$this->_getSQLqueryWherePart($qp, $next_level)." \n\t)";
				continue;
			}

			if($qp===DataModel_Query::L_O_AND || $qp===DataModel_Query::L_O_OR) {
				/**
				 * @var string $qp
				 */
				$res .= "\n{$tab}$qp\n ";
				continue;
			}

			/**
			 * @var DataModel_Query_Where_Expression $qp
			 */

			/**
			 * @var DataModel_Definition_Property_Abstract $prop
			 */
			$prop = $qp->getProperty();

			$table_name = $this->_getTableName( $prop->getDataModelDefinition() );
			$property_name = "`{$table_name}`.`".$prop->getName()."`";


			$res .= $tab.$this->_getSQLQueryWherePart_handleExpression(
				$property_name,
				$qp->getOperator(),
				$qp->getValue()
			);

		}

		if($res && !$level) {
			$res = "\nWHERE\n{$res}\n";
		}

		return $res;
	}

	/**
	 * @param DataModel_Query_Having $query
	 *
	 * @param int $level (optional)
	 *
	 * @return string
	 */
	protected function _getSQLQueryHavingPart( DataModel_Query_Having $query=null, $level=0 ) {
		if(!$query) {
			return "";
		}
		$res = "";

		$next_level = $level+1;
		$tab = str_repeat("\t", $next_level);

		foreach( $query as $qp ) {
			if( $qp instanceof DataModel_Query_Having ) {
				/**
				 * @var DataModel_Query_Having $qp
				 */
				$res .="$tab(\n".$this->_getSQLQueryHavingPart($qp, $next_level )." \n\t)";
				continue;
			}

			if($qp===DataModel_Query::L_O_AND || $qp===DataModel_Query::L_O_OR) {
				/**
				 * @var string $qp
				 */
				$res .= "\n$tab$qp\n ";
				continue;
			}

			/**
			 * @var DataModel_Query_Having_Expression $qp
			 */

			/**
			 * @var DataModel_Definition_Property_Abstract $prop
			 */
			$item = $qp->getProperty()->getSelectAs();


			$res .= $tab.$this->_getSQLQueryWherePart_handleExpression(
				$item,
				$qp->getOperator(),
				$qp->getValue()
			);
		}

		if($res && !$level ) {
			$res = "\nHAVING\n{$res}\n";
		}

		return $res;
	}

	/**
	 * @param $item
	 * @param $operator
	 * @param $value
	 *
	 * @return string
	 */
	protected function _getSQLQueryWherePart_handleExpression($item, $operator, $value) {
		$res = "";

		if(is_array($value)) {
			$sq = array();

			/**
			 * @var array $value
			 */
			foreach($value as $v) {

				$sq[] = "\t\t{$item}".$this->_getSQLQueryWherePart_handleOperator( $operator, $v );
			}

			$res .= "(\n".implode(" OR\n", $sq)."\n\t) ";
		} else {
			$res .= "{$item}".$this->_getSQLQueryWherePart_handleOperator($operator, $value);

		}

		return $res;

	}


	/**
	 * @param string $operator
	 * @param mixed $value
	 *
	 * @throws DataModel_Backend_Exception
	 * @return string
	 */
	protected function _getSQLQueryWherePart_handleOperator($operator, $value) {
		if(is_bool($value)) {
			$value = $value ? 1 : 0;
		} else {
			$value = "'".addslashes($value)."'";
		}

		$res = "";

		switch($operator) {
			case DataModel_Query::O_EQUAL:
				$res .="={$value} ";
				break;
			case DataModel_Query::O_NOT_EQUAL:
				$res .="<>{$value} ";
				break;
			case DataModel_Query::O_LIKE:
				$res .=" LIKE {$value}";
				break;
			case DataModel_Query::O_NOT_LIKE:
				$res .=" NOT LIKE {$value}";
				break;
            case DataModel_Query::O_GREATER_THAN:
                $res .=">{$value} ";
                break;
            case DataModel_Query::O_LESS_THAN:
                $res .="<{$value} ";
                break;
            case DataModel_Query::O_GREATER_THAN_OR_EQUAL:
                $res .=">={$value} ";
                break;
            case DataModel_Query::O_LESS_THAN_OR_EQUAL:
                $res .="<={$value} ";
                break;

			default:
				throw new DataModel_Backend_Exception(
					"Unknown operator {$operator}! ",
					DataModel_Backend_Exception::CODE_BACKEND_ERROR
				);



		}

		return $res;
	}


	/**
	 * @param DataModel_Query $query
	 *
	 * @return string
	 */
	protected function _getSQLQueryGroupPart( DataModel_Query $query=null ) {
		$group_by = $query->getGroupBy();
		if( !$group_by ) {
			return "";
		}

		$group_by_qp = array();

		foreach($group_by as $val) {
			/**
			 * @var DataModel_Query_Select_Item $val
			 */
			if($val instanceof DataModel_Definition_Property_Abstract) {
				/**
				 * @var DataModel_Definition_Property_Abstract $val
				 */
				$val = $this->_getColumnName($val);
			} else
			if($val instanceof DataModel_Query_Select_Item) {
				$val = $val->getSelectAs();
			}

			$group_by_qp[] = $val;
		}

		$group_by_qp = "\nGROUP BY\n\t".implode(",\n\t", $group_by_qp)."\n";

		return $group_by_qp;
	}

	/**
	 * @param DataModel_Query $query
	 *
	 * @return string
	 */
	protected function _getSQLQueryOrderByPart( DataModel_Query $query ) {
		$order_by = $query->getOrderBy();

		if(!$order_by) {
			return "";
		}

		$order_qp = array();

		foreach($order_by  as $ob ) {
			/**
			 * @var DataModel_Query_OrderBy_Item $ob
			 */
			$item = $ob->getItem();
			if($item instanceof DataModel_Definition_Property_Abstract) {
				$item = $this->_getColumnName($item);
			} else
			if($item instanceof DataModel_Query_Select_Item) {
				$item = $item->getSelectAs();
			}
			$order_by_desc = $ob->getDesc();


			if($order_by_desc) {
				$order_qp[] = "{$item} DESC";
			} else {
				$order_qp[] = "{$item} ASC";
			}
		}

		if(!$order_qp) {
			return "";
		}

		return "\nORDER BY\n\t".implode(",\n\t", $order_qp)."\n";

	}

	/**
	 * @param DataModel_Query $query
	 *
	 * @return string
	 */
	protected function _getSQLQueryLimitPart( DataModel_Query $query ) {
		$limit_qp = "";

		$offset = (int)$query->getOffset();
		$limit = (int)$query->getLimit();

		if($limit) {
			if($offset) {
				$limit_qp = "\nLIMIT {$offset},{$limit}\n";
			} else {
				$limit_qp = "\nLIMIT {$limit}\n";
			}
		}

		return $limit_qp;
	}

	/**
	 * @param DataModel $data_model
	 * @param DataModel_Definition_Property_Abstract $column
	 * @param array $keys
	 *
	 * @throws DataModel_Exception
	 * @throws DataModel_Backend_Exception
	 * @return string
	 */
	protected function _getSQLType( DataModel $data_model, DataModel_Definition_Property_Abstract $column, &$keys ) {
		$backend_options = $column->getBackendOptions();

		$name = $column->getName();
		$related_to_column = false;

		if($column->getIsID()) {
			$related_to_column = $column->getRelatedToProperty();

			if(!$related_to_column) {
				$key_name = self::PRIMARY_KEY_NAME;
				$key_type = DataModel::KEY_TYPE_PRIMARY;
			} else {

				$key_name = self::PRIMARY_KEY_NAME;
				$key_type = DataModel::KEY_TYPE_PRIMARY;

				if(!isset($keys[$key_name])) {
					$keys[$key_name] = array(
						"type" => $key_type,
						"columns" => array()
					);
				}
				$keys[$key_name]["columns"][] = $name;


				$key_type = DataModel::KEY_TYPE_INDEX;
				$key_name = $this->_getTableName( $related_to_column->getDataModelDefinition() );
			}

			if(!isset($keys[$key_name])) {
				$keys[$key_name] = array(
					"type" => $key_type,
					"columns" => array()
				);
			}
			$keys[$key_name]["columns"][] = $name;
		}

		if( isset($backend_options["key"]) && $backend_options["key"] ) {


			$key_name = $backend_options["key"] ;
			if(is_bool($key_name)) {
				$key_name = $name;
			}

			if(!isset($keys[$key_name])) {
				$key_type = DataModel::KEY_TYPE_INDEX;
				if(isset($backend_options["key_type"])) {
					$key_type = $backend_options["key_type"];
				}

				if(!in_array($key_type, self::$valid_key_types )) {
					throw new DataModel_Backend_Exception(
						"MySQL backend: unknown key type '{$key_type}'",
						DataModel_Backend_Exception::CODE_BACKEND_ERROR
					);
				}

				$keys[$key_name] = array(
					"type" => $key_type,
					"columns" => array()
				);
			}

			$keys[$key_name]["columns"][] = $name;
		}


		if( isset($backend_options["column_type"]) && $backend_options["column_type"] ) {
			return $backend_options["column_type"];
		}

		switch($column->getType()) {
			case DataModel::TYPE_ID:

				if( isset($backend_options["autoincrement"]) && $backend_options["autoincrement"]  ) {
					$options = "";
					if(!$related_to_column) {
						$options = " auto_increment";
					}

					return "bigint UNSIGNED{$options}";
				} else {
					$max_len = (int)$data_model->getEmptyIDInstance()->getMaxLength();

					return "varchar({$max_len}) COLLATE utf8_bin NOT NULL";
				}

				break;
			case DataModel::TYPE_STRING:
				$max_len = (int)$column->getMaxLen();

				if($max_len<=255) {
					if($column->getIsID()) {
						return "varchar(".((int)$max_len).") COLLATE utf8_bin NOT NULL";
					} else {
						return "varchar(".((int)$max_len).")";
					}
				}

				if($max_len<=65535) {
					return "text";
				}

				return "longtext";
				break;
			case DataModel::TYPE_BOOL:
				return "tinyint(1)";
				break;
			case DataModel::TYPE_INT:
				return "int";
				break;
			case DataModel::TYPE_FLOAT:
				return "float";
				break;
			case DataModel::TYPE_LOCALE:
				return "varchar(20) COLLATE utf8_bin NOT NULL";
				break;
			case DataModel::TYPE_DATE:
				return "date";
				break;
			case DataModel::TYPE_DATE_TIME:
				return "datetime";
				break;
			case DataModel::TYPE_ARRAY:
				return "longtext";
				break;
			default:
				throw new DataModel_Exception(
					"Unknown column type '".$column->getType()."'! Column '{$name}' ",
					DataModel_Exception::CODE_DEFINITION_NONSENSE
				);
				break;

		}
	}

	/**
	 * @param DataModel_RecordData $record
	 *
	 * @return array
	 */
	protected function _getRecord( DataModel_RecordData $record ) {
		$_record = array();

		foreach($record as $item) {
			/**
			 * @var DataModel_RecordData_Item $item
			 */
			$value = $item->getValue();

			if( is_bool($value) ) {
				$value = $value ? 1 : 0;
			} else {
				if(is_array($value)) {
					$value = serialize($value);
				} else {
					if(is_object($value)) {
						$value = (string)$value;
					}
				}
			}

			$_record[$item->getPropertyDefinition()->getName()] = $value;
		}

		return $_record;
	}

	/**
	 * @param DataModel_Definition_Model_Abstract $model_definition
	 *
	 * @return string
	 */
	protected function _getTableName(DataModel_Definition_Model_Abstract $model_definition) {
		return strtolower($model_definition->getModelName());
	}

	/**
	 * @param DataModel_Definition_Property_Abstract $property
	 *
	 * @return string
	 */
	protected function _getColumnName(DataModel_Definition_Property_Abstract $property) {
		$property_table_name = $this->_getTableName( $property->getDataModelDefinition() );
		$property_name = $property->getName();

		return "`{$property_table_name}`.`{$property_name}`";
	}

}