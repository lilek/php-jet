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

class JetML_Widget_Dojo_Editarea_List extends JetML_Widget_Dojo_Abstract {

	/**
	 *
	 * @var string
	 */
	protected $dojo_type = "dijit.layout.BorderContainer";

	/**
	 * @return \DOMElement
	 */
	public function getReplacement() {
		$this->node->setAttribute("id", $this->getNodeAttribute("id")."_list");
		$this->node->setAttribute("region", "center");

		return parent::getReplacement();
	}

}