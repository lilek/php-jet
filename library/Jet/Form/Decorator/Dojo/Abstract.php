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
 * @abstract
 *
 * @category Jet
 * @package Form
 */
namespace Jet;

abstract class Form_Decorator_Dojo_Abstract extends Form_Decorator_Abstract {
	const DOJO_TYPE_PROPERTY = "data-dojo-type";
	const DOJO_PROPS_PROPERTY = "data-dojo-props";

	/**
	 * @var array
	 */
	protected $decoratable_tags = array(
		"field" => array(
			"dojo_type" => "dijit.form.TextBox",
			"get_dojo_type_method_name" => "",
			"get_dojo_props_method_name" => "",

		)
	);

	/**
	 * @var array
	 */
	protected $_dojo_properties = array();

	/**
	 *
	 * @param Form_Parser_TagData $tag_data
	 *
	 */
	public function decorate( Form_Parser_TagData $tag_data ) {
		$tag = $tag_data->getTag();

		if(!isset($this->decoratable_tags[$tag])) {
			return;
		}

		$decorate_data = $this->decoratable_tags[$tag];

		if(!empty($decorate_data["get_dojo_type_method_name"])) {
			$dojo_type = $this->{$decorate_data["get_dojo_type_method_name"]}( $tag_data );
		} else {
			$dojo_type = $decorate_data["dojo_type"];
		}

		if(!empty($decorate_data["get_dojo_props_method_name"])) {
			$get_dojo_properties_method_name = $decorate_data["get_dojo_props_method_name"];
		} else {
			$get_dojo_properties_method_name = "getDojoProperties";
		}

		$this->$get_dojo_properties_method_name($tag_data);

		$Dojo = $this->form->getLayout()->requireJavascriptLib("Dojo");
		$Dojo->requireComponent( $dojo_type );


		$_dojo_props = array();
		foreach( $this->_dojo_properties as $k=>$val) {
			$_dojo_props[] = "{$k}:".json_encode($val);
		}

		$tag_data->setProperty( static::DOJO_TYPE_PROPERTY, $dojo_type );
		$tag_data->setProperty( static::DOJO_PROPS_PROPERTY, implode(",", $_dojo_props));
	}

	/**
	 * @param Form_Parser_TagData $tag_data
	 */
	protected function getDojoProperties( Form_Parser_TagData $tag_data ) {
		if($this->field->getIsRequired()) {
			$this->_dojo_properties["required"] = "true";


			$this->_dojo_properties["missingMessage"] = $tag_data->getProperty(
								"missingMessage",
								$this->field->getErrorMessage("empty")
			);
			$tag_data->unsetProperty("missingMessage");
		}

		$validation_regexp = $this->field->getValidationRegexp();

		if($validation_regexp){
			$this->_dojo_properties["regExp"] = $validation_regexp;

			$this->_dojo_properties["invalidMessage"] = $tag_data->getProperty(
				"invalidMessage",
				$this->field->getErrorMessage("empty")
			);
			$tag_data->unsetProperty("invalidMessage");
		}

	}
}