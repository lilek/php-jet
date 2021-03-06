<?php
/**
 *
 *
 *
 * ModuleTemplate default admin controller
 *
 *
 * @copyright Copyright (c) 2012-2013 Miroslav Marek <mirek.marek.2m@gmail.com>
 * @license http://www.gnu.org/licenses/agpl-3.0.html AGPLv3
 * @author Miroslav Marek <mirek.marek.2m@gmail.com>
 * @version <%VERSION%>
 *
 * @category JetApplicationModule
 * @package JetApplicationModule_BreadcrumbNavigation
 * @subpackage JetApplicationModule_BreadcrumbNavigation_Controller
 */
namespace JetApplicationModule\Jet\BreadcrumbNavigation;
use Jet;

class Controller_Standard extends Jet\Mvc_Controller_Standard {
	/**
	 *
	 * @var Main
	 */
	protected $module_instance = NULL;

	protected static $ACL_actions_check_map = array(
		"default" => false
	);

	public function default_Action( $view="default" ) {
		//named params emulation
		if(is_array($view)) {
			extract($view, EXTR_IF_EXISTS);
		}

		$this->view->setVar("data", Jet\Mvc::getCurrentUIManagerModuleInstance()->getBreadcrumbNavigation());

		$this->render( $view );
	}
}