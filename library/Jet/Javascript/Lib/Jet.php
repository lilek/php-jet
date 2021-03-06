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
 * @package Javascript
 * @subpackage Javascript_Lib
 */
namespace Jet;

class Javascript_Lib_Jet extends Javascript_Lib_Abstract {
	
	/**
	 * Framework config
	 *
	 * @var Javascript_Lib_Jet_Config
	 */
	protected $config = null;


	/**
	 * @var string
	 */
	protected $base_URI;

	/**
	 *
	 * @param Mvc_Layout $layout
	 *
	 * @throws Javascript_Exception
	 */
	public function __construct( Mvc_Layout $layout ) {
		$this->config = new Javascript_Lib_Jet_Config();

		$this->layout = $layout;

		$this->layout->requireJavascriptLib("Dojo");

	}
		
	
	/**
	 * Returns HTML snippet that initialize Java Script and is included into layout
	 *
	 * @return string
	 */
	public function getHTMLSnippet(){
		$router = $this->layout->getRouter();

		$Jet_config = array(
			"base_request_URI" => $this->getBaseRequestURI(),
			"base_URI" => $this->getBaseURI(),
			"modules_URI" => $this->getModulesURI(),
			"service_type_path_fragments_map" => Mvc_Router::getCurrentRouterInstance()->getServiceTypesPathFragmentsMap(),
			"auto_initialize" => true,
			"current_locale" => $router->getLocale(),
			"UI_manager_module_name" => $router->getUIManagerModuleName()
		);
		
		
		$result = '';
		
		$data = $this->_getDataForReplacement();
		if($this->required_components_CSS){
			foreach($this->required_components_CSS as $css){
				$css = Data_Text::replaceData($css, $data);
				$result .= '<link rel="stylesheet" type="text/css" href="'.$css.'">'."\n";
			}
		}
		
		$result .= '<script type="text/javascript">'."\n";
		$result .= '  var Jet_config = '.json_encode($Jet_config).';'."\n";
		$result .= '</script>'."\n";
		
		$result .= '<script type="text/javascript" src="'.$this->getComponentURI("Jet").'" charset="utf-8"></script>'."\n";

		if($this->required_components){
			$result .= '<script type="text/javascript" charset="utf-8">' . "\n";
			foreach( $this->required_components as $rc ) {
				if($rc == "Jet"){
					continue;
				}
				$result .= "Jet.require(\"{$rc}\");\n";
			}
			$result .= "</script>\n";
		}

		return $result;
	}

	/**
	 * @return array
	 */
	protected function _getDataForReplacement(){
		$data = array(
			"JETJS_URI" => $this->getBaseURI(),
		);
		return $data;
	}

	/**
	 * @return string
	 */
	public function getBaseRequestURI() {
		return $this->layout->getRouter()->getPage()->getURI();
	}

	/**
	 * Gets URI to /JetJS/
	 *
	 * In fact: _JetJS_/
	 *
	 *
	 * @return string
	 */
	public function getBaseURI(){
		if(!$this->base_URI) {
			$sm = Mvc_Router::getCurrentRouterInstance()->getServiceTypesPathFragmentsMap();
			$this->base_URI = $this->getBaseRequestURI().$sm[Mvc_Router::SERVICE_TYPE__JETJS_]."/";
		}

		return $this->base_URI;
	}


	/**
	 * Get URI to modules JS
	 *
	 * In fact _JetJS_/modules/
	 *
	 * @return string
	 */
	public function getModulesURI() {
		return $this->getBaseURI()."modules/";
	}

	/**
	 * Gets proper JS file URI by component and selected framework
	 *
	 * @param string $component
	 * @return string
	 */
	public function getComponentURI( $component ){
		$parts = explode(".", $component);
		return $this->getBaseURI() . implode("/", $parts) . ".js";
	}
	
	/**
	 * Equivalent to Jet.require().
	 * If $parameters["css"] is set (string or array or strings), additional CSS for given component is written into output
	 *
	 * @param string $component - JetJS module
	 * @param array $parameters(optional)
	 */
	public function requireComponent( $component, $parameters=array() ) {
		if( in_array( $component, $this->required_components ) ) {
			return;
		}

		$this->required_components[] = $component;
		if(isset($parameters["css"]) && $parameters["css"]){
			if(!is_array($parameters["css"])){
				$parameters["css"] = array($parameters["css"]);
			} 
			foreach($parameters["css"] as $css){
				if(in_array($css, $this->required_components_CSS)){
					continue;
				}
				$this->required_components_CSS[] = $css;
			}
		}
	}

	/**
	 * Returns Java Script toolkit version number
	 *
	 * @return string
	 */
	public function getVersionNumber() {
		return Version::getVersionNumber();
	}

	/**
	 * This method is called when processing is completed and the content is placed in its positions
	 *
	 * @param string &$result
	 * @param Mvc_Layout $layout
	 */
	public function finalPostProcess( &$result, Mvc_Layout $layout ) {
	}

}