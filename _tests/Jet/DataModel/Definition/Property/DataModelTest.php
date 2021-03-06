<?php
/**
 *
 * @copyright Copyright (c) 2012-2013 Miroslav Marek <mirek.marek.2m@gmail.com>
 * @license http://www.gnu.org/licenses/agpl-3.0.html AGPLv3
 * @author Miroslav Marek <mirek.marek.2m@gmail.com>
 * @version <%VERSION%>
 *
 * @category Jet\tests
 * @package DataModel
 */
namespace Jet;

require_once "_mock/Jet/DataModel/Definition/DataModelTestMock.php";

class DataModel_Definition_Property_DataModelTest extends \PHPUnit_Framework_TestCase {
	/**
	 * @var DataModel_Definition_DataModelTestMock
	 */
	protected $data_model;

	/**
	 * @var DataModel_Definition_Property_DataModel
	 */
	protected $object;

	protected $property_class_name = "DataModel_Definition_Property_DataModel";

	protected $property_name = "data_model_property";

	protected $property_options = array();
	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 */
	protected function setUp() {
		$class_name = __NAMESPACE__."\\".$this->property_class_name;

		$this->data_model = new DataModel_Definition_DataModelTestMock();

		$this->property_options = $this->data_model->_test_get_property_options($this->property_name);

		$this->object = new $class_name( $this->data_model->getDataModelDefinition(), $this->property_name, $this->property_options );
	}

	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 */
	protected function tearDown() {
	}

	/**
	 * @covers Jet\DataModel_Definition_Property_Array::setUp
	 * @expectedException \Jet\DataModel_Exception
	 * @expectedExceptionCode \Jet\DataModel_Exception::CODE_DEFINITION_NONSENSE
	 */
	public function testSetUpFailedNotItemType() {
		$class_name = __NAMESPACE__."\\".$this->property_class_name;

		$this->data_model = new DataModel_Definition_DataModelTestMock();

		unset($this->property_options["data_model_class"]);

		$this->object = new $class_name( $this->data_model->getDataModelDefinition(), $this->property_name, $this->property_options );
	}

	/**
	 * @covers Jet\DataModel_Definition_Property_DataModel::getDefaultValue
	 */
	public function testGetDefaultValue() {
		$data_model = new $this->property_options["data_model_class"]();
		$this->assertEquals($data_model, $this->object->getDefaultValue());
	}

	/**
	 * @covers Jet\DataModel_Definition_Property_DataModel::checkValueType
	 */
	public function testCheckValueType() {
		$value = "do not change";
		$this->object->checkValueType($value);
		$this->assertEquals($value, $value);
	}

	/**
	 * @covers Jet\DataModel_Definition_Property_DataModel::getDataModelClass
	 */
	public function testGetDataModelClass() {
		$this->assertEquals($this->property_options["data_model_class"], $this->object->getDataModelClass());
	}
}
