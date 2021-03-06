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
 * @subpackage DataModel_Definition
 */
namespace Jet;

class DataModel_Definition_Property_DataModel extends DataModel_Definition_Property_Abstract {
	/**
	 * @var string
	 */
	protected $_type = DataModel::TYPE_DATA_MODEL;
	/**
	 * @var bool
	 */
	protected $_is_data_model = true;

	/**
	 * @var string
	 */
	protected $data_model_class = null;

	/**
	 * @var DataModel
	 */
	protected $default_value = null;

	/**
	 * @param array $definition_data
	 *
	 * @throws DataModel_Exception
	 */
	public function setUp( $definition_data ) {

		if($definition_data) {
			parent::setUp($definition_data);

			if( !$this->data_model_class ) {
				throw new DataModel_Exception(
					"Property {$this->_data_model_definition->getClassName()}::{$this->_name} is DataModel, but data_model_class is missing in definition data.",
					DataModel_Exception::CODE_DEFINITION_NONSENSE
				);
			}
		}

	}

	/**
	 * @return mixed
	 */
	public function getDefaultValue() {
		$default_value = new $this->data_model_class();
		return $default_value;
	}

	/**
	 * @param mixed $value
	 */
	public function checkValueType( &$value ) {
	}

	/**
	 *
	 * @return string
	 */
	public function getDataModelClass() {
		return $this->data_model_class;
	}
}