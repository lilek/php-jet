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

class DataModel_Query_Select_Item extends Object {

	/**
	 *
	 * @var DataModel_Definition_Property_Abstract|DataModel_Query_Select_Item_BackendFunctionCall
	 */
	protected $item;

	/**
	 * @var string
	 */
	protected $select_as = "";


	/**
	 * @param DataModel_Definition_Property_Abstract|DataModel_Query_Select_Item_BackendFunctionCall $item
	 * @param string $select_as
	 *
	 * @throws DataModel_Query_Exception
	 */
	public function  __construct( $item, $select_as  ) {
		if(
			!($item instanceof DataModel_Definition_Property_Abstract) &&
			!($item instanceof DataModel_Query_Select_Item_BackendFunctionCall)
		) {
			throw new DataModel_Query_Exception(
				"Item must be instance of DataModel_Definition_Property_Abstract or DataModel_Query_Select_Item_BackendFunctionCall",
				DataModel_Query_Exception::CODE_QUERY_PARSE_ERROR
			);
		}

		$this->item = $item;
		$this->select_as = $select_as;
	}

	/**
	 * @return DataModel_Definition_Property_Abstract|DataModel_Query_Select_Item_BackendFunctionCall
	 */
	public function getItem() {
		return $this->item;
	}

	/**
	 * @return bool
	 */
	public function getSelectAs() {
		return $this->select_as;
	}

}