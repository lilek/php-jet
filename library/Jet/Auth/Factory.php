<?php
/**
 *
 *
 *
 * @see Factory
 *
 *
 * @copyright Copyright (c) 2011-2012 Miroslav Marek <mirek.marek.2m@gmail.com>
 * @license http://www.gnu.org/licenses/agpl-3.0.html AGPLv3
 * @author Miroslav Marek <mirek.marek.2m@gmail.com>
 * @version <%VERSION%>
 *
 * @category Jet
 * @package Auth
 * @subpackage Auth_Factory
 */
namespace Jet;

class Auth_Factory extends Factory {
	
	const DEFAULT_USER_CLASS = "Jet\\Auth_User_Default";
	const DEFAULT_USER_ROLES_CLASS = "Jet\\Auth_User_Roles";
	const DEFAULT_ROLE_CLASS = "Jet\\Auth_Role_Default";
	const DEFAULT_PRIVILEGE_CLASS = "Jet\\Auth_Role_Privilege_Default";

	/**
	 * Returns instance of Auth User class @see Factory
	 *
	 * @return Auth_User_Abstract
	 */
	public static function getUserInstance() {
		$class_name =  self::getClassName( self::DEFAULT_USER_CLASS );
		$instance = new $class_name();

		self::checkInstance(self::DEFAULT_USER_CLASS, $instance);
		return $instance;
	}


	/**
	 * Returns instance of Auth Role class @see Factory
	 *
	 * @return Auth_Role_Abstract
	 */
	public static function getRoleInstance() {
		$class_name =  self::getClassName( self::DEFAULT_ROLE_CLASS );
		$instance = new $class_name();
		self::checkInstance(self::DEFAULT_ROLE_CLASS, $instance);
		return $instance;
	}

	/**
	 * Returns instance of Auth Privilege class @see Factory
	 *
	 * @return Auth_Role_Privilege_Abstract
	 */
	public static function getPrivilegeInstance() {
		$class_name =  self::getClassName( self::DEFAULT_PRIVILEGE_CLASS );
		$instance = new $class_name();
		self::checkInstance(self::DEFAULT_PRIVILEGE_CLASS, $instance);
		return $instance;
	}

	/**
	 * @see Factory
	 *
	 * @param string $class_name
	 */
	public static function setUserClass( $class_name ) {
		self::setClassName(self::DEFAULT_USER_CLASS, $class_name);
	}

	/**
	 * @see Factory
	 *
	 * @param string $class_name
	 */
	public static function setUserRolesClass( $class_name ) {
		self::setClassName(self::DEFAULT_USER_ROLES_CLASS, $class_name);
	}


	/**
	 * @see Factory
	 *
	 * @param string $class_name
	 */
	public static function setRoleClass( $class_name ) {
		self::setClassName(self::DEFAULT_ROLE_CLASS, $class_name);
	}

	/**
	 * @see Factory
	 *
	 * @param string $class_name
	 */
	public static function setPrivilegeClass( $class_name ) {
		self::setClassName(self::DEFAULT_PRIVILEGE_CLASS, $class_name);
	}

}