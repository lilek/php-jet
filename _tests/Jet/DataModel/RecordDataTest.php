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

require_once "_mock/Jet/DataModel/Query/DataModelTestMock.php";

class DataModel_RecordDataTest extends \PHPUnit_Framework_TestCase {
	/**
	 * @var DataModel_Query_DataModelTestMock
	 */
	protected $data_model;

	/**
	 * @var DataModel_Definition_Property_Abstract[]
	 */
	protected $properties = array();

	/**
	 * @var DataModel_RecordData
	 */
	protected $object;

	protected $values = array(
		"ID" => "ID123",
		"ID_property" => "myID",
		"string_property" => "My Test",
		"int_property" => 1234,
		"float_property" => 3.14,
		"bool_property" => true,
		"array_property" => array("a","b","c")

	);

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 */
	protected function setUp() {
		$this->data_model = new DataModel_Query_DataModelTestMock();

		$this->properties = $this->data_model->getDataModelDefinition()->getProperties();

		$this->object =  DataModel_RecordData::createRecordData($this->data_model, $this->values );

	}

	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 */
	protected function tearDown() {
	}

	/**
	 * @covers Jet\DataModel_RecordData::getDataModelDefinition
	 */
	public function testGetDataModelDefinition() {
		$this->assertSame( $this->data_model->getDataModelDefinition(), $this->object->getDataModelDefinition() );
	}

	/**
	 * @covers Jet\DataModel_RecordData::createRecordData
	 *
	 * @covers Jet\DataModel_RecordData::addItem
	 *
	 * @covers Jet\DataModel_RecordData::current
	 * @covers Jet\DataModel_RecordData::key
	 * @covers Jet\DataModel_RecordData::next
	 * @covers Jet\DataModel_RecordData::rewind
	 * @covers Jet\DataModel_RecordData::valid
	 */
	public function testMain() {
		$data = array();
		foreach($this->object as $item) {
			/**
			 * @var DataModel_RecordData_Item $item
			 */
			$k = $item->getPropertyDefinition()->getName();
			$v = $item->getValue();
			$data[$k] = $v;
		}

		$this->assertEquals($this->values, $data);
	}

	/**
	 * @covers Jet\DataModel_RecordData::createRecordData
	 *
	 * @expectedException \Jet\DataModel_Exception
	 * @expectedExceptionCode \Jet\DataModel_Exception::CODE_UNKNOWN_PROPERTY
	 */
	public function testCreateRecordDataFailed() {

		$values = $this->values;
		$values["imaginary"] = "hoax";

		DataModel_RecordData::createRecordData($this->data_model, $values );
	}
}
