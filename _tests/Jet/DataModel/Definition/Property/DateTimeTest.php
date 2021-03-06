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

class DataModel_Definition_Property_DateTimeTest extends \PHPUnit_Framework_TestCase {
	/**
	 * @var DataModel_Definition_DataModelTestMock
	 */
	protected $data_model;

	/**
	 * @var DataModel_Definition_Property_Date
	 */
	protected $object;

	protected $property_class_name = "DataModel_Definition_Property_DateTime";

	protected $property_name = "date_time_property";

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
	 * @covers Jet\DataModel_Definition_Property_Date::checkValueType
	 */
	public function testCheckValueType() {
		$date = "1980-08-13 05:55:55";
		$this->object->checkValueType($date);

		$date_object = new DateTime("1980-08-13 05:55:55");
		$this->assertEquals($date_object, $date);
	}

	/**
	 * @covers Jet\DataModel_Definition_Property_Date::getValueForJsonSerialize
	 */
	public function testGetValueForJsonSerialize() {
		$date_object = new DateTime("1980-08-13 05:55:55");
		$value = $this->object->getValueForJsonSerialize($date_object);
		$this->assertEquals($date_object->toString(), $value);
	}
}
