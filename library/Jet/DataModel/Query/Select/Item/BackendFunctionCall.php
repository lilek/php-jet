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

class DataModel_Query_Select_Item_BackendFunctionCall extends Object {

	/**
	 * Property instance
	 *
	 * @var DataModel_Definition_Property_Abstract[]
	 */
	protected $properties = array();

	/**
	 * Example:
	 *
	 * count(%PROPERTY%)
	 *
	 * @var string
	 */
	protected $backend_function = "";

	/**
	 * @var string
	 */
	protected $value = "";


	/**
	 * @param DataModel_Definition_Property_Abstract|DataModel_Definition_Property_Abstract[] $properties
	 * @param string $backend_function
	 *
	 * @throws DataModel_Query_Exception
	 */
	public function  __construct( $properties, $backend_function  ) {
		if(!is_array($properties)) {
			$properties = array($properties);
		}

		foreach($properties as $property) {
			/**
			 * @var DataModel_Definition_Property_Abstract $property
			 */
			if(
				!is_object($property) ||
				!$property instanceof DataModel_Definition_Property_Abstract
			) {
				throw new DataModel_Query_Exception(
					"Property must be instance of DataModel_Definition_Property_Abstract ",
					DataModel_Query_Exception::CODE_QUERY_PARSE_ERROR
				);
			}

			$property_name = $property->getName();

			if( strpos($backend_function, "%$property_name%")===false ) {
				throw new DataModel_Query_Exception(
					"There is not property '{$property_name}' placeholder in the backend function call. Example: count(%{$property_name}%) ",
					DataModel_Query_Exception::CODE_QUERY_PARSE_ERROR
				);
			}

		}

		$this->properties = $properties;
		$this->backend_function = $backend_function;
	}

	/**
	 * @return DataModel_Definition_Property_Abstract[]
	 */
	public function getProperties() {
		return $this->properties;
	}

	/**
	 * Example:
	 *
	 * count(%PROPERTY%)
	 *
	 * @return string
	 */
	public function getBackendFunction() {
		return $this->backend_function;
	}

	/**
	 * $property_name_to_backend_column_name_callback example:
	 * <code>
	 * function( DataModel_Definition_Property_Abstract $property ) {
	 *      $table_name = $property->getDataModelDefinition()->getModelName();
	 *      $column_name = $property->getName();
	 *
	 *      return "`{$table_name}`.`{$column_name}`";
	 * }
	 * </code>
	 *
	 * @param callable $property_name_to_backend_column_name_callback
	 *
	 * @return string
	 */
	public function toString( callable $property_name_to_backend_column_name_callback=null ) {
		if(!$property_name_to_backend_column_name_callback) {
			$property_name_to_backend_column_name_callback = function( DataModel_Definition_Property_Abstract $property ) {
				return $property->getDataModelDefinition()->getModelName()."::".$property->getName();
			};
		}
		$function_call = $this->backend_function;

		foreach($this->properties as $property) {
			/**
			 * @var DataModel_Definition_Property_Abstract $property
			 */
			$column_name = $property_name_to_backend_column_name_callback( $property );
			$function_call = str_replace("%{$property->getName()}%", $column_name, $function_call);

		}

		return $function_call;
	}
}