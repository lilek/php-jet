<?php 
/**
 *
 *
 *
 *
 * specific errors:
 *  invalid_value
 *
 *
 * @copyright Copyright (c) 2011-2012 Miroslav Marek <mirek.marek.2m@gmail.com>
 * @license http://www.gnu.org/licenses/agpl-3.0.html AGPLv3
 * @author Miroslav Marek <mirek.marek.2m@gmail.com>
 * @version <%VERSION%>
 *
 * @category Jet
 * @package Form
 */
namespace Jet;

class Form_Field_RadioButton extends Form_Field_Abstract {
	/**
	 * @var string
	 */
	protected $_type = "RadioButton";

	/**
	 * @var array
	 */
	protected $error_messages = array(
				"input_missing" => "input_missing",
				"empty" => "empty",
				"invalid_format" => "invalid_format",
				"invalid_value" => "invalid_value"
			);


	/**
	 * catch value from input (input = most often $_POST)
	 *
	 * @param Data_Array $data
	 */
	public function catchValue( Data_Array $data ) {
		$this->_value = null;
		$this->_has_value = true;

		if($data->exists($this->_name)) {
			$this->_value_raw = $data->getRaw($this->_name);
			$this->_value = trim( $data->getString($this->_name) );
		} else {
			$this->_value_raw = null;
			$this->_value = null;
		}
	}

	/**
	 * @return bool
	 */
	public function validateValue() {
		if($this->_value===null && !$this->is_required) {
			return true;
		}

		$options = $this->select_options;
		
		if(!isset($options[$this->_value])) {
			$this->setValueError("invalid_value");
			return false;
		}
		
		$this->_setValueIsValid();
		
		return true;
	}

	/**
	 * @param Form_Parser_TagData $tag_data
	 * @return string
	 */
	protected function _getReplacement_field_option_label( Form_Parser_TagData $tag_data ) {
		$key = $tag_data->getProperty("key");

		if(!isset($this->select_options[$key])) {
			return "";
		}

		$tag_data->unsetProperty( "key" );
		$tag_data->setProperty( "for", $this->getID()."_{$key}" );

		$label = $this->getTranslation( $this->select_options[ $key ] );

		return "<label {$this->_getTagPropertiesAsString( $tag_data )}>{$label}</label>";

	}

	/**
	 * @param Form_Parser_TagData $tag_data
	 *
	 * @return string
	 */
	protected function _getReplacement_field_option( Form_Parser_TagData $tag_data ) {
		$key = $tag_data->getProperty("key");

		if(!isset($this->select_options[$key])) {
			return "";
		}

		$tag_data->unsetProperty( "key" );
		$tag_data->setProperty( "name", $this->getName() );
		$tag_data->setProperty( "id", $this->getID()."_{$key}" );
		$tag_data->setProperty( "type", "radio" );
		$tag_data->setProperty( "value", $key );

		if(!$tag_data->getPropertyIsSet("class")){
			$tag_data->setProperty("class", "radio");
			$properties["class"] = "radio";
		}


		if($this->_value==$key) {
			$tag_data->setProperty("checked", "checked");
		} else {
			$tag_data->unsetProperty("checked");
		}

		return "<input {$this->_getTagPropertiesAsString($tag_data)}/>";
	}

	/**
	 * @param null|string $template
	 *
	 * @return string
	 */
	public function helper_getBasicHTML($template=null) {
		if(!$template) {
			$template = $this->__form->getTemplate_field();
		}


		$label = "<jet_form_field_label name=\"{$this->_name}\"/>";
		$field = "<jet_form_field_error_msg name=\"{$this->_name}\" class=\"error\"/>";

		foreach($this->select_options as $key=>$val) {
			$field .= "\t\t\t<jet_form_field_option name=\"{$this->_name}\" key=\"{$key}\"/>\n";
			$field .= "\t\t\t<jet_form_field_option_label name=\"{$this->_name}\" key=\"{$key}\"/><br/>\n";

		}

		return Data_Text::replaceData($template, array(
			"LABEL" => $label,
			"FIELD" => $field
		));

	}	
	
}