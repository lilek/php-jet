<?php
/**
 *
 * @copyright Copyright (c) 2012-2013 Miroslav Marek <mirek.marek.2m@gmail.com>
 * @license http://www.gnu.org/licenses/agpl-3.0.html AGPLv3
 * @author Miroslav Marek <mirek.marek.2m@gmail.com>
 * @version <%VERSION%>
 *
 * @category Jet\tests
 * @package Config
 */
namespace Jet;

require_once "_mock/Jet/Config/ConfigTestMock.php";

class Config_Definition_Property_IntTest extends \PHPUnit_Framework_TestCase {
	/**
	 * @var Config_Definition_Property_Int
	 */
	protected $object;

	protected $property_name = "IntTest";

	protected $property_type = Config::TYPE_INT;

	protected $property_class_name = "Config_Definition_Property_Int";

	protected $property_default_form_field_type = "Int";

	protected $default_value = 10;

	protected $property_options = array(
		"description" => "Description",
		"default_value" => "",
		"is_required" => true,
		"error_message" => "Error Message",
		"label" => "Label",
		"form_field_label" => "Form field label"
	);

	/**
	 * @var ConfigTestMock
	 */
	protected $config;

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 */
	protected function setUp() {

		$class_name = __NAMESPACE__."\\".$this->property_class_name;
		$this->property_options["default_value"] = $this->default_value;

		$this->config = new ConfigTestMock("test");
		$this->object = new $class_name( $this->config, $this->property_name, $this->property_options  );
	}

	/**
	 * @covers Jet\Config_Definition_Property_Int::setMinValue
	 * @covers Jet\Config_Definition_Property_Int::getMinValue
	 */
	public function testSetGetMinValue() {
		$this->object->setMinValue( 10 );
		$this->assertEquals(10, $this->object->getMinValue());
	}


	/**
	 * @covers Jet\Config_Definition_Property_Int::setMaxValue
	 */
	public function testSetGetMaxValue() {
		$this->object->setMaxValue( 100 );
		$this->assertEquals(100, $this->object->getMaxValue());
	}


	/**
	 * @covers Jet\Config_Definition_Property_Int::checkValueType
	 */
	public function testCheckValueType() {

		$value = "123.4";

		$this->object->checkValueType( $value );

		$this->assertSame(123, $value);
	}

	/**
	 * @covers Jet\Config_Definition_Property_Abstract::setUp
	 * @covers Jet\Config_Definition_Property_Abstract::checkValue
	 *
	 * @expectedException \Jet\Config_Exception
	 * @expectedExceptionCode \Jet\Config_Exception::CODE_CONFIG_CHECK_ERROR
	 */
	public function testCheckValueFailedUnder() {
		$this->object->setMinValue(10);
		$this->object->setMaxValue(100);

		$value = 1;
		$this->object->checkValue( $value );
	}


	/**
	 * @covers Jet\Config_Definition_Property_Abstract::setUp
	 * @covers Jet\Config_Definition_Property_Abstract::checkValue
	 *
	 * @expectedException \Jet\Config_Exception
	 * @expectedExceptionCode \Jet\Config_Exception::CODE_CONFIG_CHECK_ERROR
	 */
	public function testCheckValueFailedAbove() {
		$this->object->setMinValue(10);
		$this->object->setMaxValue(100);

		$value = 110;
		$this->object->checkValue( $value );
	}

	/**
	 * @covers Jet\Config_Definition_Property_Abstract::setUp
	 * @covers Jet\Config_Definition_Property_Abstract::checkValue
	 *
	 * @expectedException \Jet\Config_Exception
	 * @expectedExceptionCode \Jet\Config_Exception::CODE_CONFIG_CHECK_ERROR
	 */
	public function testCheckValueFailedEmpty() {
		$value = null;

		$this->object->checkValue( $value );
	}

	/**
	 * @covers Jet\Config_Definition_Property_Abstract::setUp
	 * @covers Jet\Config_Definition_Property_Abstract::checkValue
	 *
	 */
	public function testCheckValue() {
		$this->object->setMinValue(10);
		$this->object->setMaxValue(100);

		$value = 50;
		$this->object->checkValue( $value );
	}

	/**
	 * @covers Jet\Config_Definition_Property_Int::getTechnicalDescription
	 */
	public function testGetTechnicalDescription() {
		$this->object->setMinValue(10);
		$this->object->setMaxValue(100);

		$this->assertEquals(
			"Type: Int, required: yes, default value: 10, min. value: 10, max. value: 100\n\nDescription",
			$this->object->getTechnicalDescription()
		);
	}

	/**
	 * @covers Jet\Config_Definition_Property_Abstract::setUp
	 * @covers Jet\Config_Definition_Property_Abstract::getFormField
	 */
	public function testGetFormField() {
		$this->object->setMinValue(10);
		$this->object->setMaxValue(100);

		$field = new Form_Field_Int("");

		$field->__test_set_state(array(
			'_type' => 'Int',
			'_value' => 10,
			'_value_raw' => 10,
			'_name' => 'IntTest',
			'min_value' => 10,
			'max_value' => 100,
			'default_value' => $this->default_value,
			'label' => 'Form field label',
			'is_required' => true,
			'select_options' => array (
			),
		));

		$this->assertEquals($field, $this->object->getFormField());
	}


}
