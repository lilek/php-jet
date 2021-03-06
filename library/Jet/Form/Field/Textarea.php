<?php 
/**
 *
 *
 *
 * class representing single form field - type string
 *
 *
 * @copyright Copyright (c) 2011-2012 Miroslav Marek <mirek.marek.2m@gmail.com>
 * @license http://www.gnu.org/licenses/agpl-3.0.html AGPLv3
 * @author Miroslav Marek <mirek.marek.2m@gmail.com>
 * @version <%VERSION%>
 *
 * @category Jet
 * @package Form
 */
namespace Jet;

class Form_Field_Textarea extends Form_Field_Abstract {
	/**
	 * @var string
	 */
	protected $_type = "Textarea";

	/**
	 * @param Form_Parser_TagData $tag_data
	 *
	 * @return string
	 */
	protected function _getReplacement_field( Form_Parser_TagData $tag_data ) {
		$tag_data->setProperty( "name", $this->getName() );
		$tag_data->setProperty( "id", $this->getID() );

		return "<textarea {$this->_getTagPropertiesAsString( $tag_data )}>{$this->getValue()}</textarea>";
	}
	
}