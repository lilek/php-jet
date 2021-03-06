<?php
/**
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
 * @package Javascript
 * @subpackage Javascript_Lib_TinyMCE
 */
namespace Jet;

class Javascript_Lib_TinyMCE_Config extends Config_Application {

	/**
	 * @var string
	 */
	protected static $__config_data_path = "/js_libs/TinyMCE";
	/**
	 * @var bool
	 */
	protected static $__config_section_is_obligatory = false;

	/**
	 * @var array
	 */
	protected static $__config_properties_definition = array(
		"version" => array(
			"type" => self::TYPE_STRING,
			"description" => "Version of TinyMCE",
			"default_value" => "3.5.6",
			"is_required" => false
		),
		"URI" => array(
			"type" => self::TYPE_STRING,
			"description" => "TinyMCE scripts URI or URL",
			"default_value" => "%JET_PUBLIC_SCRIPTS_URI%tiny_mce/%VERSION%/tiny_mce.js",
			"is_required" => false
		),
		"wrapper_URI" => array(
			"type" => self::TYPE_STRING,
			"is_required" => false,
			"default_value" => "%JET_PUBLIC_SCRIPTS_URI%Jet/WYSIWYG/TinyMCE.js"
		),
		"editor_configs" => array(
			"type" => self::TYPE_ARRAY,
			"item_type" => self::TYPE_STRING,
			"description" => "Editor configurations. See http://www.tinymce.com/wiki.php/Configuration. Language directive is set according to current language. For content_css directive can be used Jet\\* constants.",
			"is_required" => false,
			"default_value" => array(
				"default" => array(
					"mode" => "exact",
					"theme" => "advanced",
					"apply_source_formatting" => true,
					"remove_linebreaks" => false,
					"entity_encoding" => "raw",

					"convert_urls" => false,
					"verify_html" => true,

					"content_css" => "%JET_SITE_STYLES_URI%wysiwyg.css"
				)
			)
		),

	);

	/**
	 * @var array
	 */
	protected $_editor_configs = null;

	/**
	 * @var array
	 */
	protected $editor_configs = array();

	/**
	 * Version of Dojo
	 *
	 * @var string
	 */
	protected $version;

	/**
	 * TinyMCE scripts URI or URL
	 *
	 * @var string
	 */
	protected $URI;

	/**
	 * Editor wrapper URI
	 *
	 * @var string
	 */
	protected $wrapper_URI;


	/**
	 * @return string
	 */
	public function getVersion() {
		return $this->version;
	}

	/**
	 * Get URI/URL where TinyMCE script is placed
	 *
	 * @return string
	 */
	public function getURI(){
		$replacements = array(
			"VERSION" => $this->version,
		);

		return Data_Text::replaceSystemConstants($this->URI, $replacements);
	}


	/**
	 * @return array
	 * @throws Javascript_Lib_TinyMCE_Exception
	 */
	public function getEditorConfigs() {
		if( $this->_editor_configs===null ) {

			$this->_editor_configs = array();

			if(!$this->editor_configs) {
				throw new Javascript_Lib_TinyMCE_Exception(
					"Main configuration /js_libs/TinyMCE/editor_configs/* is missing. ",
					Javascript_Lib_TinyMCE_Exception::CODE_EDITOR_CONFIGURATION_MISSING
				);

			}

			foreach($this->editor_configs as $name=>$cfg) {
				$cfg["language"] = Mvc::getCurrentLocale()->getLanguage();

				if(isset($cfg["content_css"])) {
					$cfg["content_css"] = Data_Text::replaceSystemConstants($cfg["content_css"]);
				}

				$this->_editor_configs[$name] = $cfg;
			}
		}

		return $this->_editor_configs;
	}

	/**
	 * @param string $editor_config_name
	 *
	 * @return array
	 *
	 * @throws Javascript_Lib_TinyMCE_Exception
	 */
	public function getEditorConfig( $editor_config_name ) {
		if( $this->_editor_configs===null ) {
			$this->getEditorConfigs();
		}

		if(!isset($this->_editor_configs[$editor_config_name])) {
			throw new Javascript_Lib_TinyMCE_Exception(
				"Unknown editor configuration '{$editor_config_name}'. Main configuration /js_libs/TinyMCE/editor_configs/{$editor_config_name} is missing. ",
				Javascript_Lib_TinyMCE_Exception::CODE_UNKNOWN_EDITOR_CONFIGURATION
			);
		}

		return $this->_editor_configs[$editor_config_name];
	}

	/**
	 * @return string
	 */
	public function getWrapperURI() {
		return Data_Text::replaceSystemConstants($this->wrapper_URI);
	}
}