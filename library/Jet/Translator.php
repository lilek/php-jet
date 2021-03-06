<?php
/**
 *
 *
 *
 * Texts translation class.
 * All the texts displayed somewhere on site should pass through
 * the translator to display in proper language given by actual mode
 *
 * Proper translation module/class should be installed/defined for correct function, default
 * class does not provide translation functionality.
 *
 * Class @see \Jet\Tr is recommended to use instead of \Jet\Translator for shorter code.
 *
 *
 * @copyright Copyright (c) 2011-2012 Miroslav Marek <mirek.marek.2m@gmail.com>
 * @license http://www.gnu.org/licenses/agpl-3.0.html AGPLv3
 * @author Miroslav Marek <mirek.marek.2m@gmail.com>
 * @version <%VERSION%>
 *
 * @category Jet
 * @package Translator
 */

namespace Jet;

class Translator extends Object {
	
	const COMMON_NAMESPACE = "_COMMON_";

	/**
	 * @var string
	 */
	protected static $current_namespace = self::COMMON_NAMESPACE;

	/**
	 * @var Locale
	 */
	protected static $current_locale;


	/**
	 *
	 * @var Translator_Backend_Config_Abstract
	 */
	protected static $config;

	/**
	 * 
	 * @var Translator_Backend_Abstract
	 */
	protected static $backend_instance;

	/**
	 * @var Translator_Dictionary[]
	 */
	protected static $dictionaries = array();

	/**
	 * Gets translator backend instance
	 *
	 * @param bool $soft_mode (optional) @see Config
	 *
	 * @return Translator_Config
	 */
	public static function getConfig( $soft_mode=false ){
		if(static::$config === null){
			static::$config = new Translator_Config( $soft_mode );
		}
		return static::$config;
	}

	/**
	 * @static
	 *
	 * @param Translator_Config $config
	 */
	public static function setConfig( Translator_Config $config ) {
		static::$config = $config;
	}

	/**
	 * Gets translator backend instance
	 * 
	 * @return Translator_Backend_Abstract
	 */
	public static function getBackendInstance(){
		if(static::$backend_instance === null){
			static::$backend_instance = Translator_Factory::getBackendInstance( static::getConfig()->getBackendType() );

			register_shutdown_function( array("\\Jet\\Translator", "saveUpdatedDictionaries") );
		}
		return static::$backend_instance;
	}

	/**
	 * @static
	 *
	 * @param Translator_Backend_Abstract $backend_instance
	 */
	public static function setBackendInstance( Translator_Backend_Abstract $backend_instance ) {
		if(static::$backend_instance === null){
			register_shutdown_function( array("\\Jet\\Translator", "saveUpdatedDictionaries") );
		}
		static::$backend_instance = $backend_instance;
	}

	/**
	 * @static
	 *
	 * Shutdown - save all update dictionaries
	 *
	 */
	public static function saveUpdatedDictionaries() {
		$backend = static::getBackendInstance();

		foreach(static::$dictionaries as $dictionary) {
			if($dictionary->getNeedToSave()) {
				$backend->saveDictionary($dictionary);
			}
		}
	}
	
	/**
	 * Gets translation of given text
	 * 
	 *
	 * @param string $phrase
	 * @param array $data(optional) - data that replace parts of text; input array("KEY1"=>"value1") replaces %KEY1% in text for value1 
	 * @param string $namespace(optional)
	 * @param string|Locale $locale(optional) - target locale
	 * @return string
	 */
	public static function getTranslation($phrase, $data=array(), $namespace=null, $locale=null ){

		if(!$namespace){
			$namespace = static::$current_namespace;
		}

		if($locale === null){
			$locale = static::$current_locale;
		}

		if(is_string($locale)) {
			$locale = new Locale($locale);
		}

		$dictionary = static::loadDictionary($namespace, $locale);

		$translation = $dictionary->getTranslation($phrase, static::getConfig()->getAutoAppendUnknownPhrase() );


		if($data){
			$translation = Data_Text::replaceData($translation, $data);
		}

		return $translation;

	}

	/**
	 * Gets translation of given text
	 *
	 * @param string $text
	 * @param array $data(optional) - data that replace parts of text; input array("KEY1"=>"value1") replaces %KEY1% in text for value1
	 * @param string $namespace(optional)
	 * @param string $locale(optional) - target locale
	 * @return string
	 */
	public static function _($text, $data=array(), $namespace=null, $locale=null){
		return static::getTranslation($text, $data, $namespace, $locale);
	}


	/**
	 * @static
	 * @return string
	 */
	public static function getCurrentNamespace() {
		return static::$current_namespace;
	}

	/**
	 * @static
	 * @param string $current_namespace
	 */
	public static function setCurrentNamespace($current_namespace) {
		static::$current_namespace = $current_namespace;
	}

	/**
	 * @static
	 * @return Locale
	 */
	public static function getCurrentLocale() {
		return static::$current_locale;
	}

	/**
	 * @static
	 * @param Locale $current_locale
	 */
	public static function setCurrentLocale( Locale $current_locale ) {
		static::$current_locale = $current_locale;
	}

	/**
	 * @static
	 *
	 * @param string $namespace
	 * @param Locale $locale
	 * @param bool $force_load (optional, default: false)
	 *
	 * @return Translator_Dictionary
	 */
	public static function loadDictionary($namespace, Locale $locale, $force_load=false) {
		$dictionary_key = $namespace.":".$locale;

		if(
			!isset(static::$dictionaries[$dictionary_key]) ||
			$force_load
		) {
			static::$dictionaries[$dictionary_key] = static::getBackendInstance()->loadDictionary($namespace, $locale);
		}

		return static::$dictionaries[$dictionary_key];
	}


	/**
	 * Export phrases
	 *
	 * @param string $namespace
	 * @param Locale $locale
	 *
	 * @return string
	 */
	public static function exportDictionary($namespace, Locale $locale ){

		$dictionary = static::loadDictionary( $namespace, $locale );
		return $dictionary->export();
	}

	/**
	 * Import dictionary
	 *
	 * @param string $data
	 *
	 * @throws Translator_Exception
	 *
	 */
	public static function importDictionary($data){

		list($namespace, $locale) = Translator_Dictionary::getImportDataNamespaceAndLocale($data);

		if(!$namespace || !$locale) {
			throw new Translator_Exception(
				"Incorrect file format. Header is missing.",
				Translator_Exception::CODE_IMPORT_INCORRECT_DICTIONARY_EXPORT_FILE_FORMAT
			);
		}

		$dictionary = static::loadDictionary( $namespace, $locale );
		$dictionary->import($data);
		static::getBackendInstance()->saveDictionary($dictionary);
	}

	/**
	 * Creates backend after installation
	 *
	 */
	public static function helper_create() {
		static::getBackendInstance()->helper_create();
	}

}