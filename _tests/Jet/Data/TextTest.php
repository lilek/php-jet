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
 * @subpackage Data_Text
 */
namespace Jet;

class Data_TextTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @covers Jet\Data_Text::removeAccents
	 */
	public function testRemoveAccents() {

		$this->assertEquals(
				                         "aacdeegghiijkkllmnnooprrsstuuuuuwyzzAACDEEGGHIIJKKLLMNNOOPRRSSTUUUWZZ",
				Data_Text::removeAccents("áǎčďéěǧǵȟíǐǰǩḱľĺḿńňóǒṕřŕśšťúǔůǘǚẃýžźÁǍČĎÉĚǦǴȞÍǏJǨḰĽĹḾŃŇÓǑṔŘŔŚŠŤÚǓŮẂŽŹ")
			);
	}

	/**
	* @covers Jet\Data_Text::shorten
	*/
	public function testShorten() {
		$text = "Lorem ipsum dolor sit amet, consectetur adipiscing elit. In lacus libero, volutpat nec vehicula quis, convallis mollis ante. Nulla elementum, lectus non aliquet feugiat, augue nunc iaculis arcu, non elementum nulla metus sed metus. Duis viverra porta risus, in pharetra sapien commodo sed. Praesent non turpis non nisi dictum bibendum id sed eros. Fusce sollicitudin risus eu mi pretium vehicula. Cras blandit erat ut risus commodo vitae vulputate enim semper. Integer varius mauris quis sem viverra et hendrerit mauris egestas. Mauris mattis mi vitae ante congue ullamcorper. Integer et ligula a erat pharetra convallis. Sed id dui elit, a dictum massa. In et metus est, sit amet rhoncus risus. Phasellus porta purus nec lorem euismod posuere. Donec massa massa, varius sit amet porta id, semper lacinia dolor. Vivamus interdum velit in dolor aliquet vehicula.";

		$this->assertEquals(
			"Lorem ipsum dolor sit amet, consectetur adipiscing elit. In lacus libero, volutpat nec vehicula...",
			Data_Text::shorten($text, 100)
		);
		$this->assertEquals(
			"Lorem ipsum dolor sit amet, consectetur adipiscing elit. In lacus libero, volutpat nec vehicula",
			Data_Text::shorten($text, 100, false)
		);
		$this->assertEquals(
			"Lorem ipsum dolor sit amet, consectetur adipiscing elit. In lacus libero, volutpat nec.........",
			Data_Text::shorten($text, 100, true, ".........")
		);

	}

	/**
	* @covers Jet\Data_Text::replaceData
	*/
	public function testReplaceData() {
		$text = "Value 1: %VALUE_1%, Value 2: %VALUE_2%";

		$data = array( "VALUE_1"=>"Value 1", "VALUE_2"=>"Value 2" );

		$this->assertEquals("Value 1: Value 1, Value 2: Value 2", Data_Text::replaceData($text, $data) );

	}

	/**
	* @covers Jet\Data_Text::replaceSystemConstants
	*/
	public function testReplaceSystemConstants() {
	}
}
