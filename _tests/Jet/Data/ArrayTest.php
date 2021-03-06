<?php
/**
 *
 * @copyright Copyright (c) 2012-2013 Miroslav Marek <mirek.marek.2m@gmail.com>
 * @license http://www.gnu.org/licenses/agpl-3.0.html AGPLv3
 * @author Miroslav Marek <mirek.marek.2m@gmail.com>
 * @version <%VERSION%>
 *
 * @category Jet\tests
 * @package Data
 * @subpackage Data_Array
 */
namespace Jet;

class Data_ArrayTest extends \PHPUnit_Framework_TestCase {
	/**
	 * @var Data_Array
	 */
	protected $object;

	protected $data = array(
		"int" => 1,
		"float" => 3.14,
		"string" => '<script>alert("Shady!");</script>',
		"bool" => true,
		"sub1" => array(
			"int" => 2,
			"float" => 6.28,
			"string" => '<script>alert("Shady!!");</script>',
			"bool" => false,

			"sub2" => array(
				"int" => 4,
				"float" => 12.56,
				"string" => '<script>alert("Shady!!!");</script>',
				"bool" => true,

			)
		)
	);

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 */
	protected function setUp() {
		$this->object = new Data_Array( $this->data );
	}

	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 */
	protected function tearDown() {
	}

	/**
	 * @covers Jet\Data_Array::getRawData
	 */
	public function testGetRawData() {
		$this->assertEquals( $this->data, $this->object->getRawData() );
	}

	/**
	 * @covers Jet\Data_Array::appendData
	 */
	public function testAppendData() {
		$new_data = array("merge_test"=>"test");

		$this->object->appendData( $new_data );
		$this->assertEquals(
			array_merge($this->data, $new_data ),
			$this->object->getRawData()
		);
	}

	/**
	* @covers Jet\Data_Array::setData
	*/
	public function testSetData() {
		$new_data = array("merge_test"=>"test");

		$this->object->setData( $new_data );
		$this->assertEquals(
			$new_data,
			$this->object->getRawData()
		);
	}

	/**
	* @covers Jet\Data_Array::clearData
	*/
	public function testClearData() {
		$this->object->clearData();
		$this->assertEquals(
			array(),
			$this->object->getRawData()
		);
	}

	/**
	* @covers Jet\Data_Array::exists
	*/
	public function testExists() {
		$this->assertTrue( $this->object->exists("int") );
		$this->assertTrue( $this->object->exists("float") );
		$this->assertTrue( $this->object->exists("string") );
		$this->assertTrue( $this->object->exists("sub1") );
		$this->assertTrue( $this->object->exists("/int") );
		$this->assertTrue( $this->object->exists("/float") );
		$this->assertTrue( $this->object->exists("/string") );
		$this->assertTrue( $this->object->exists("/sub1") );
		$this->assertTrue( $this->object->exists("/sub1/int") );
		$this->assertTrue( $this->object->exists("/sub1/float") );
		$this->assertTrue( $this->object->exists("/sub1/string") );
		$this->assertTrue( $this->object->exists("/sub1/sub2") );
		$this->assertTrue( $this->object->exists("/sub1/sub2/int") );
		$this->assertTrue( $this->object->exists("/sub1/sub2/float") );
		$this->assertTrue( $this->object->exists("/sub1/sub2/string") );

		$this->assertFalse( $this->object->exists("int-na") );
		$this->assertFalse( $this->object->exists("float-na") );
		$this->assertFalse( $this->object->exists("string-na") );
		$this->assertFalse( $this->object->exists("sub1-na") );
		$this->assertFalse( $this->object->exists("/int-na") );
		$this->assertFalse( $this->object->exists("/float-na") );
		$this->assertFalse( $this->object->exists("/string-na") );
		$this->assertFalse( $this->object->exists("/sub1-na") );
		$this->assertFalse( $this->object->exists("/sub1/int-na") );
		$this->assertFalse( $this->object->exists("/sub1/float-na") );
		$this->assertFalse( $this->object->exists("/sub1/string-na") );
		$this->assertFalse( $this->object->exists("/sub1/sub2-na") );
		$this->assertFalse( $this->object->exists("/sub1/sub2/int-na") );
		$this->assertFalse( $this->object->exists("/sub1/sub2/float-na") );
		$this->assertFalse( $this->object->exists("/sub1/sub2/string-na") );

	}

	/**
	 * @covers Jet\Data_Array::set
	 * @covers Jet\Data_Array::getRaw
	 */
	public function testSet() {
		$this->object->set("int", 54321);
		$this->object->set("/sub1/sub2/int", 12345);

		$this->assertEquals(54321, $this->object->getRaw("int") );
		$this->assertEquals(12345, $this->object->getRaw("/sub1/sub2/int") );
	}

	/**
	 * @covers Jet\Data_Array::remove
	 */
	public function testRemove() {
		$this->assertTrue($this->object->exists("/sub1/sub2/int"));

		$this->object->remove("/sub1/sub2/int");

		$this->assertFalse($this->object->exists("/sub1/sub2/int"));
	}

	/**
	 * @covers Jet\Data_Array::getRaw
	 */
	public function testGetRaw() {
		$this->assertEquals( $this->data["string"], $this->object->getRaw("string") );
		$this->assertEquals( $this->data["string"], $this->object->getRaw("/string") );
		$this->assertEquals( $this->data["sub1"]["sub2"]["string"], $this->object->getRaw("/sub1/sub2/string") );
	}

	/**
	* @covers Jet\Data_Array::getInt
	*/
	public function testGetInt() {
		$this->assertEquals( $this->data["int"], $this->object->getRaw("int") );
		$this->assertEquals( $this->data["int"], $this->object->getRaw("/int") );
		$this->assertEquals( $this->data["sub1"]["sub2"]["int"], $this->object->getRaw("/sub1/sub2/int") );
	}

	/**
	 * @covers Jet\Data_Array::getFloat
	 */
	public function testGetFloat() {
		$this->assertEquals( $this->data["float"], $this->object->getRaw("float") );
		$this->assertEquals( $this->data["float"], $this->object->getRaw("/float") );
		$this->assertEquals( $this->data["sub1"]["sub2"]["float"], $this->object->getRaw("/sub1/sub2/float") );
	}

	/**
	 * @covers Jet\Data_Array::getBool
	 */
	public function testGetBool() {
		$this->assertEquals( $this->data["bool"], $this->object->getRaw("bool") );
		$this->assertEquals( $this->data["bool"], $this->object->getRaw("/bool") );
		$this->assertEquals( $this->data["sub1"]["sub2"]["bool"], $this->object->getRaw("/sub1/sub2/bool") );
	}

	/**
	 * @covers Jet\Data_Array::getString
	 */
	public function testGetString() {
	    $this->assertEquals( htmlspecialchars( $this->data["string"] ), $this->object->getString("string") );
	    $this->assertEquals( htmlspecialchars( $this->data["string"] ), $this->object->getString("/string") );
	    $this->assertEquals( htmlspecialchars( $this->data["sub1"]["sub2"]["string"] ), $this->object->getString("/sub1/sub2/string") );
	}
}
