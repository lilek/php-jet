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

class DataModel_Definition_Property_BoolTest extends \PHPUnit_Framework_TestCase {
	/**
	 * @var DataModel_Definition_DataModelTestMock
	 */
	protected $data_model;

	/**
	 * @var DataModel_Definition_Property_Bool
	 */
	protected $object;

	protected $property_class_name = "DataModel_Definition_Property_Bool";

	protected $property_name = "bool_property";


	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 */
	protected function setUp() {
		$class_name = __NAMESPACE__."\\".$this->property_class_name;

		$this->data_model = new DataModel_Definition_DataModelTestMock();

		$property_options = $this->data_model->_test_get_property_options($this->property_name);

		$this->object = new $class_name( $this->data_model->getDataModelDefinition(), $this->property_name, $property_options );
	}

	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 */
	protected function tearDown() {
	}

	/**
	 * @covers Jet\DataModel_Definition_Property_Bool::checkValueType
	 */
	public function testCheckValueType() {
		$value = 1;
		$this->object->checkValueType($value);
		$this->assertSame(true, $value);
	}
}
