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
 * @subpackage DataModel_Query
 */
namespace Jet;

class DataModel_Query_GroupBy extends Object implements \Iterator {

	/**
	 * @var DataModel_Query_Select_Item[]|DataModel_Definition_Property_Abstract[]
	 */
	protected $items = array();


	/**
	 *
	 * @param DataModel_Query $query
	 * @param string[]|string $group_by
	 *
	 * @throws DataModel_Query_Exception
	 * @return DataModel_Query_GroupBy
	 */
	public function __construct( DataModel_Query $query, $group_by ) {
		if(!is_array($group_by)) {
			$group_by = array($group_by);
		}

		$select = $query->getSelect();
		$this->items = array();
		foreach($group_by as $gb) {

			$property = null;

			if(!$select->getHasItem($gb)) {
				if(strpos($gb, ".")) {
					$property = $query->_getPropertyAndSetRelatedClass($gb);
				} else {
					$properties = $query->getMainDataModel()->getDataModelDefinition()->getProperties();
					if(isset($properties[$gb])) {
						$property = $properties[$gb];
					}
				}
			} else {
				$property = $select->getItem($gb);
			}

			if(!$property) {
				throw new DataModel_Query_Exception(
					"setGroupBy error. Undefined group by property: '{$gb}'",
					DataModel_Query_Exception::CODE_QUERY_PARSE_ERROR
				);
			}

			$this->items[] = $property;
		}
	}

	//------------------------------------------------------------------------------------------------------------
	//------------------------------------------------------------------------------------------------------------
	//------------------------------------------------------------------------------------------------------------
	//------------------------------------------------------------------------------------------------------------
	//------------------------------------------------------------------------------------------------------------
	//------------------------------------------------------------------------------------------------------------
	//------------------------------------------------------------------------------------------------------------
	/**
	 * @see \Iterator
	 * @return DataModel_Query_Select_Item|DataModel_Definition_Property_Abstract
	 */
	public function current() {
		return current($this->items);
	}
	/**
	 * @see \Iterator
	 * @return string
	 */
	public function key() {
		return key($this->items);
	}
	/**
	 * @see \Iterator
	 */
	public function next() {
		return next($this->items);
	}
	/**
	 * @see \Iterator
	 */
	public function rewind() {
		reset($this->items);
	}
	/**
	 * @see \Iterator
	 * @return bool
	 */
	public function valid()	{
		return key($this->items)!==null;
	}
}