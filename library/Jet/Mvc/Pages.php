<?php
/**
 *
 *
 *
 *
 *
 * @copyright Copyright (c) 2011-2012 Miroslav Marek <mirek.marek.2m@gmail.com>
 * @license http://www.gnu.org/licenses/agpl-3.0.html AGPLv3
 * @author Miroslav Marek <mirek.marek.2m@gmail.com>
 * @version <%VERSION%>
 *
 * @category Jet
 * @package Mvc
 * @subpackage Mvc_Pages
 */
namespace Jet;

class Mvc_Pages extends Object {
	const HOMEPAGE_ID = "_homepage_";


	/**
	 *
	 * @var Mvc_Pages_Handler_Abstract
	 */
	protected static $_handler = null;

	/**
	 * Returns a list of site pages
	 *
	 * @param string $site_ID
	 * @param Locale $locale
	 *
	 * @return Mvc_Pages_Page_Abstract[]
	 */
	public static function getPagesList( $site_ID, Locale $locale ) {
		$page = Mvc_Factory::getPageInstance();
		return $page->getList( $site_ID, $locale );
	}

	/**
	 * Returns instance of new site page data object
	 *
	 * @see Mvc_Sites_Page_Abstract
	 * @see Mvc_Sites_Page_Factory
	 *
	 * @param string $site_ID
	 * @param Locale $locale
	 * @param string $name
	 * @param string $parent_ID (optional)
	 * @param string $ID (optional)
	 *
	 * @return Mvc_Pages_Page_Abstract
	 */
	public static function getNewPage( $site_ID, Locale $locale , $name, $parent_ID="", $ID=null ) {

		$page = Mvc_Factory::getPageInstance();
		$page->initNew( $site_ID, $locale , $name, $parent_ID, $ID);

		return $page;
	}


	/**
	 * Return site page data object (or null if does not exist)
	 *
	 * @see Mvc_Pages_Page_Abstract
	 * @see Mvc_Pages_Page_Factory
	 *
	 * @param Mvc_Pages_Page_ID_Abstract $ID
	 *
	 * @return Mvc_Pages_Page_Abstract
	 */
	public static function getPage( Mvc_Pages_Page_ID_Abstract $ID ) {
		return Mvc_Factory::getPageInstance()->load( $ID );
	}

	/**
	 * Create new site page
	 *
	 * @param Mvc_Pages_Page_Abstract $page_data
	 *
	 * @throws Mvc_Sites_Handler_Exception
	 *
	 */
	public static function createPage( Mvc_Pages_Page_Abstract $page_data ) {
		if(!$page_data->validateData()) {
			$errors = $page_data->getValidationErrors();
			foreach($errors as $i=>$error) {
				$errors[$i] = (string)$error;
			}
			$errors = implode(", ", $errors);

			throw new Mvc_Sites_Handler_Exception(
				"Page validation failed. Errors: {$errors}",
				Mvc_Sites_Handler_Exception::CODE_INVALID_PAGE_DATA
			);
		}

		self::getHandler()->createPage($page_data);
	}

	/**
	 * @param Mvc_Pages_Page_ID_Abstract $page_ID
	 */
	public static function dropPage( Mvc_Pages_Page_ID_Abstract $page_ID ) {
		self::getHandler()->dropPage( $page_ID );
	}

	/**
	 * @param string $site_ID
	 * @param Locale $locale
	 */
	public static function dropPages(  $site_ID, Locale $locale ) {

		self::getHandler()->dropPages( $site_ID, $locale );
	}

	/**
	 * Actualize site pages (example: actualize pages by project definition)
	 *
	 * @param string $site_ID
	 * @param Locale $locale
	 *
	 */
	public static function actualizePages( $site_ID, Locale $locale ) {
		self::getHandler()->actualizePages( $site_ID, $locale );
	}


	/**
	 *
	 * @return Mvc_Pages_Handler_Abstract
	 */
	public static function getHandler() {
		if(!self::$_handler) {
			self::$_handler = Mvc_Factory::getPageHandlerInstance();
		}

		return self::$_handler;
	}


	/**
	 *
	 * @param string$page_ID
	 * @param null|string $locale (optional, default: auto)
	 * @param null|string $site_ID (optional, default: auto)
	 *
	 * @return string
	 */
	public static function getURI( $page_ID, $locale=null, $site_ID=null ) {
		return Mvc_Router::getCurrentRouterInstance()->getUIManagerModuleInstance()->generateURI( $page_ID, $locale, $site_ID );
	}

	/**
	 *
	 * @param string$page_ID
	 * @param null|string $locale (optional, default: auto)
	 * @param null|string $site_ID (optional, default: auto)
	 *
	 * @return string
	 */
	public static function getURL( $page_ID,  $locale=null, $site_ID=null ) {
		return Mvc_Router::getCurrentRouterInstance()->getUIManagerModuleInstance()->generateURL( $page_ID, $locale, $site_ID );
	}

	/**
	 *
	 * @param string$page_ID
	 * @param null|string $locale (optional, default: auto)
	 * @param null|string $site_ID (optional, default: auto)
	 *
	 * @return string
	 */
	public static function getSslURL( $page_ID,  $locale=null, $site_ID=null ) {
		return Mvc_Router::getCurrentRouterInstance()->getUIManagerModuleInstance()->generateSslURL( $page_ID, $locale, $site_ID );
	}
}