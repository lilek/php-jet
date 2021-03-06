<?php
/**
 *
 *
 *
 * Class describes one URL
 *
 *
 * @copyright Copyright (c) 2011-2012 Miroslav Marek <mirek.marek.2m@gmail.com>
 * @license http://www.gnu.org/licenses/agpl-3.0.html AGPLv3
 * @author Miroslav Marek <mirek.marek.2m@gmail.com>
 * @version <%VERSION%>
 *
 * @category Jet
 * @package Mvc
 * @subpackage Mvc_Sites
 */
namespace Jet;

class Mvc_Sites_Site_LocalizedData_URL_Default extends Mvc_Sites_Site_LocalizedData_URL_Abstract {
	/**
	 * @var string
	 */
	protected static $__data_model_model_name = "Jet_Mvc_Sites_Site_LocalizedData_URL";
	/**
	 * @var string
	 */
	protected static $__data_model_parent_model_class_name = "Jet\\Mvc_Sites_Site_LocalizedData_Default";
	/**
	 * @var array
	 */
	protected static $__data_model_properties_definition = array(
		"URL" => array(
			"type" => self::TYPE_STRING,
			"max_len" => 100
		),
		"is_default" => array(
			"type" => self::TYPE_BOOL
		),
		"is_SSL" => array(
			"type" => self::TYPE_BOOL
		),
	);

	/**
	 * @var string
	 */
	protected $Jet_Mvc_Sites_Site_ID = "";
	/**
	 * @var string
	 */
	protected $Jet_Mvc_Sites_Site_LocalizedData_ID = "";

	/**
	 * @var string
	 */
	protected $ID = "";

	/**
	 * @var string
	 */
	protected $URL = "";
	/**
	 * @var bool
	 */
	protected $is_default = false;
	/**
	 * @var bool
	 */
	protected $is_SSL = false;

	/**
	 * @var array|null|bool
	 */
	protected $parsed_URL_data = null;

	/**
	 * @param string $URL (optional)
	 * @param bool $is_default (optional)
	 */
	public function __construct($URL="", $is_default=false) {
		if($URL) {
			$this->generateID();

			$this->setURL($URL);
			$this->is_default = (bool)$is_default;
		}
	}

	/**
	 * @return string
	 */
	public function  __toString() {
		return $this->toString();
	}

	/**
	 * @return string
	 */
	public function  toString() {
		return $this->URL;
	}

	/**
	 * @return string
	 */
	public function getURL() {
		return $this->URL;
	}

	/**
	 * @param string $URL
	 * @throws Mvc_Sites_Site_Exception
	 */
	public function setURL($URL) {
		if(!$URL) {
			throw new Mvc_Sites_Site_Exception(
				"URL is not defined",
				Mvc_Sites_Site_Exception::CODE_URL_NOT_DEFINED
			);
		}

		$parse_data = parse_url($URL);
		if(
			$parse_data===false ||
			!empty($parse_data["user"]) ||
			!empty($parse_data["pass"]) ||
			!empty($parse_data["query"]) ||
			!empty($parse_data["fragment"])
		) {
			throw new Mvc_Sites_Site_Exception(
				"URL format is not valid! Valid format examples: http://host/, https://host/, http://host:80/, http://host/path/, .... ",
				Mvc_Sites_Site_Exception::CODE_URL_INVALID_FORMAT
			);
		}

		if(empty($parse_data["path"]) || $parse_data["path"][strlen($parse_data["path"])-1]!="/") {
			$URL .= "/";
		}

		$this->is_SSL = $parse_data["scheme"]=="https";

		$this->URL = $URL;
		$this->parsed_URL_data = $parse_data;
	}

	/**
	 * @return bool
	 */
	public function getIsDefault() {
		return $this->is_default;
	}

	/**
	 * @param bool $is_default
	 */
	public function setIsDefault($is_default) {
		$this->is_default = (bool)$is_default;
	}

	/**
	 * @return bool|string
	 */
	public function getSchemePart() {
		return $this->parseURL( "scheme" );
	}

	/**
	 * @return bool|string
	 */
	public function getHostPart() {
		return $this->parseURL( "host" );
	}

	/**
	 * @return bool|string
	 */
	public function getPostPart() {
		return $this->parseURL( "port" );
	}

	/**
	 * @return bool|string
	 */
	public function getPathPart() {
		return $this->parseURL( "path" );
	}


	/**
	 * @return bool
	 */
	public function getIsSSL() {
		return $this->is_SSL;
	}

	/**
	 * @param bool $is_SSL
	 */
	public function setIsSSL( $is_SSL ) {
		$this->is_SSL = (bool)$is_SSL;
	}


	/**
	 * Example:
	 *
	 * URL: http://my-domain.tld/path/
	 *
	 * Base URL: http://my-domain.tld
	 *
	 * URL: https://my-domain.tld:8443/path/
	 *
	 * Base URL: https://my-domain.tld:8443
	 *
	 *
	 * @return string
	 */
	public function getBaseURL() {
		if(!$this->parsed_URL_data) {
			$this->parsed_URL_data = parse_url($this->URL);
		}

		$res = $this->parsed_URL_data["scheme"]."://";
		$res .= $this->parsed_URL_data["host"];
		if(!empty($this->parsed_URL_data["port"])) {
			$res .= ":".$this->parsed_URL_data["port"];
		}

		return $res;
	}

	/**
	 * @see parse_url
	 *
	 * @param string $return_what (scheme, host, port, user, pass, path, query, fragment)
	 *
	 * @return string|bool
	 */
	protected function parseURL( $return_what ) {
		if(!$this->parsed_URL_data) {
			$this->parsed_URL_data = parse_url($this->URL);
		}

		if(!$this->parsed_URL_data) {
			return false;
		}

		return $this->parsed_URL_data[$return_what];
	}
}