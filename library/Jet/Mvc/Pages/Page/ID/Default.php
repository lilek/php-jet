<?php
/**
 *
 *
 *
 * @copyright Copyright (c) 2011-2012 Miroslav Marek <mirek.marek.2m@gmail.com>
 * @license http://www.gnu.org/licenses/agpl-3.0.html AGPLv3
 * @author Miroslav Marek <mirek.marek.2m@gmail.com>
 * @version <%VERSION%>
 *
 * @abstract
 *
 * @category Jet
 * @package Mvc
 * @subpackage Mvc_Pages
 */
namespace Jet;

class Mvc_Pages_Page_ID_Default extends Mvc_Pages_Page_ID_Abstract {
	/**
	 * @var string
	 */
	protected static $__factory_class_name = "Jet\\Mvc_Factory";
	/**
	 * @var string
	 */
	protected static $__factory_class_method = "getSiteIDInstance";
	/**
	 * @var string
	 */
	protected static $__factory_must_be_instance_of_class_name = "Jet\\Mvc_Sites_Site_ID_Abstract";

	/**
	 * @param string $site_ID
	 */
	public function setSiteID( $site_ID ) {
		$this->values["site_ID"] = $site_ID;
	}

	/**
	 * @param Locale|string $locale
	 *
	 */
	public function setLocale($locale) {
		$this->values["locale"] = $locale;

		if(!($this->values["locale"] instanceof $locale)) {
			$this->values["locale"] = new Locale($this->values["locale"]);
		}

	}

	/**
	 * @param $ID
	 *
	 */
	public function setPageID($ID) {
		$this->values["ID"] = $ID;
	}

	/**
	 * @return string
	 */
	public function getSiteID() {
		return $this->values["site_ID"];
	}

	/**
	 *
	 * @return Locale
	 */
	public function getLocale() {
		return $this->values["locale"];
	}

	/**
	 * @return string
	 */
	public function getPageID() {
		return $this->values["ID"];
	}
}