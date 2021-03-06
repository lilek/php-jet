<?php
/**
 *
 *
 *
 *
 *
 *
 * @copyright Copyright (c) 2012-2013 Miroslav Marek <mirek.marek.2m@gmail.com>
 * @license http://www.gnu.org/licenses/agpl-3.0.html AGPLv3
 * @author Miroslav Marek <mirek.marek.2m@gmail.com>
 * @version <%VERSION%>
 *
 * @category JetApplicationModule
 * @package JetApplicationModule\Jet\Articles
 */
namespace JetApplicationModule\Jet\Articles;
use Jet;

class Controller_AJAX extends Jet\Mvc_Controller_AJAX {
	/**
	 *
	 * @var Main
	 */
	protected $module_instance = NULL;


	protected static $ACL_actions_check_map = array(
		"default" => false
	);

	function default_Action() {
		$article = new Article();
		$form = $article->getCommonForm();
		$form->enableDecorator("Dojo");

		$this->view->setVar("form", $form);

		$this->render("default-ajax");
	}

}