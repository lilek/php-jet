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
 * @package JetApplicationModule_TestModule
 * @subpackage JetApplicationModule_TestModule_Controller
 */
namespace JetApplicationModule\Jet\TestModule;
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


	public function default_Action() {
		$obj = new DataModelT1();

		$form = $obj->getCommonForm();

		if($form->catchValues()) {
			var_dump($form->validateValues());
			die();
		}
		$form->enableDecorator("Dojo");
		$this->view->setVar("form", $form);

		$this->render("ajax-test-action");
	}
}