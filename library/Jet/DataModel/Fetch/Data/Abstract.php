<?php
/**
 *
 *
 *
 * @copyright Copyright (c) 2011-2012 Miroslav Marek <mirek.marek.2m@gmail.com>
 * @license http://www.gnu.org/licenses/agpl-3.0.html AGPLv3
 * @author Miroslav Marek <mirek.marek.2m@gmail.com>
 * @version <%VERSION%>
 *
 * @category Jet
 * @package DataModel
 * @subpackage DataModel_Fetch
 */
namespace Jet;

abstract class DataModel_Fetch_Data_Abstract extends DataModel_Fetch_Abstract implements Data_Paginator_DataSource_Interface,\ArrayAccess, \Iterator, \Countable,Object_Serializable_REST  {
	/**
	 * @var string
	 */
	protected $backend_fetch_method = "";

	/**
	 * @var array
	 */
	protected $data = null;


	/**
	 *
	 * @param string[] $select_items
	 * @param array|DataModel_Query $query
	 * @param DataModel $data_model
	 *
	 * @throws DataModel_Query_Exception
	 */
	final public function __construct( array $select_items, $query, DataModel $data_model  ) {
		if(is_array($query)) {
			$query = DataModel_Query::createQuery($data_model, $query);
		}

		if(!$query instanceof DataModel_Query) {
			throw new DataModel_Query_Exception(
				"Query must be an instance of \\Jet\\DataModel_Query (or valid query as an array) " ,
				DataModel_Query_Exception::CODE_QUERY_NONSENSE
			);
		}

		$this->query = $query;

		$this->query->setSelect( $select_items );

		$this->data_model = $data_model;
	}

	/**
	 * @return array
	 */
	public function toArray() {
		$result = array();

		foreach($this as $key=>$val) {
			$result[$key] = $val;
		}

		return $result;
	}

	/**
	 * @return array
	 */
	public function jsonSerialize() {
		$result = array();

		foreach($this as $key=>$val) {
			foreach($val as $k=>$v) {
				if(is_object($v)) {
					$val[$k] = (string)$v;
				}
			}
			$result[$key] = $val;
		}

		return $result;
	}

	/**
	 * @return string
	 */
	public function toXML() {
		$model_name = $this->data_model->getDataModelDefinition()->getModelName();

		$result = "";
		$result .= "<list model_name=\"{$model_name}\">\n";

		foreach($this->jsonSerialize() as $val) {
			$result .= "\t<item>\n";
			foreach($val as $k=>$v) {
				$result .= "\t\t<{$k}>".htmlspecialchars($v)."</{$k}>\n";
			}
			$result .= "\t</item>\n";

		}

		$result .= "</list>\n";

		return $result;
	}

	/**
	 * @return string
	 */
	public function toJSON() {
		$this->_fetch();

		return json_encode( $this->jsonSerialize() );
	}


	/**
	 * Fetches data
	 *
	 */
	public function _fetch() {

		if($this->data!==null) {
			return;
		}

		$this->data = $this->data_model->getBackendInstance()->{$this->backend_fetch_method}( $this->query );
	}

//--------------------------------------------------------------------------------------------------
//--------------------------------------------------------------------------------------------------
//--------------------------------------------------------------------------------------------------
//--------------------------------------------------------------------------------------------------
//--------------------------------------------------------------------------------------------------
//--------------------------------------------------------------------------------------------------
//--------------------------------------------------------------------------------------------------
//--------------------------------------------------------------------------------------------------

	/**
	 * @see \Countable
	 *
	 * @return int
	 */
	public function count() {
		$this->_fetch();
		return count($this->data);
	}

	/**
	 * @see \ArrayAccess
	 * @param int $offset
	 * @return bool
	 */
	public function offsetExists( $offset  ) {
		$this->_fetch();
		return array_key_exists($offset, $this->data);
	}
	/**
	 * @see \ArrayAccess
	 * @param int $offset
	 *
	 * @return DataModel
	 */
	public function offsetGet( $offset ) {
		$this->_fetch();
		return $this->data[$offset];
	}

	/**
	 *
	 * @see \ArrayAccess
	 * @param int $offset
	 * @param mixed $value
	 */
	public function offsetSet( $offset , $value ) {
		$this->data[$offset] = $value;
	}

	/**
	 * @see \ArrayAccess
	 * @param int $offset
	 */
	public function offsetUnset( $offset )	{
		$this->_fetch();
		unset( $this->data[$offset] );
	}

	/**
	 * @see \Iterator
	 *
	 * @return DataModel
	 */
	public function current() {
		$this->_fetch();

		return current($this->data);
	}
	/**
	 * @see \Iterator
	 * @return string
	 */
	public function key() {
		$this->_fetch();
		return key($this->data);
	}
	/**
	 * @see \Iterator
	 */
	public function next() {
		$this->_fetch();
		return next($this->data);
	}
	/**
	 * @see \Iterator
	 */
	public function rewind() {
		$this->_fetch();
		reset($this->data);
	}
	/**
	 * @see \Iterator
	 * @return bool
	 */
	public function valid()	{
		$this->_fetch();
		return key($this->data)!==null;
	}

}