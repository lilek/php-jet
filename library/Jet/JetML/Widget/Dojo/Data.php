<?php
/**
 *
 *
 * @copyright Copyright (c) 2011-2012 Miroslav Marek <mirek.marek.2m@gmail.com>
 * @license http://www.gnu.org/licenses/agpl-3.0.html AGPLv3
 * @author Miroslav Marek <mirek.marek.2m@gmail.com>
 * @version <%VERSION%>
 *
 * @category Jet
 * @package JetML
 */
namespace Jet;

class JetML_Widget_Dojo_Data extends JetML_Widget_Abstract {


	/**
	 * @return \DOMElement
	 */
	public function getReplacement() {

		$replacement  = $this->parser->getDOMDocument()->createElement( "textarea", $this->node->nodeValue );

		foreach($this->node->attributes as $attribute){
			/**
			 * @var \DOMAttr $attribute
			 */

			$replacement->setAttribute($attribute->name, $attribute->value);

		}


		$replacement->setAttribute("style", "display:none;width:0px;height:0px;");

		return $replacement;
	}

}