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
 * @package JetApplicationModule_AdminUsers
 * @subpackage JetApplicationModule_AdminUsers_Controller
 */
namespace JetApplicationModule\Jet\AdminUsers;
use Jet;

class Controller_REST extends Jet\Mvc_Controller_REST {
	/**
	 *
	 * @var Main
	 */
	protected $module_instance = NULL;


	protected static $ACL_actions_check_map = array(
		"get_user" => "get_user",
		"post_user" => "add_user",
		"put_user" => "update_user",
		"delete_user" => "delete_user",
	);

	/**
	 * @param null|string $ID
	 */
	public function get_user_Action( $ID=null ) {
		if($ID) {
			$user = $this->_getUser($ID);
			$this->responseData($user);
		} else {
			$this->responseDataModelsList( Jet\Auth::getUsersListAsData() );
		}

	}

	public function post_user_Action() {
		$user = Jet\Auth::getNewUser();

		$form = $user->getCommonForm();

		if($user->catchForm( $form, $this->getRequestData(), true )) {
			$user->validateData();
			$user->save();
			$this->responseData($user);
		} else {
			$this->responseFormErrors( $form->getAllErrors() );
		}
	}

	public function put_user_Action( $ID ) {
		$user = $this->_getUser($ID);

		$form = $user->getCommonForm();

		if($user->catchForm( $form, $this->getRequestData(), true )) {
			$user->validateData();
			$user->save();
			$this->responseData($user);
		} else {
			$this->responseFormErrors( $form->getAllErrors() );
		}
	}

	public function delete_user_Action( $ID ) {
		$user = $this->_getUser($ID);

		$user->delete();

		$this->responseOK();
	}

	/**
	 * @param $ID
	 * @return Jet\Auth_User_Abstract
	 */
	protected  function _getUser($ID) {
		$user = Jet\Auth::getUser($ID);

		if(!$user) {
			$this->responseUnknownItem($ID);
		}

		return $user;
	}
}