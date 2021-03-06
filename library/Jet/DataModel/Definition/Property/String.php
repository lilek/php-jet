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
 * @category Jet
 * @package DataModel
 * @subpackage DataModel_Definition
 */
namespace Jet;

class DataModel_Definition_Property_String extends DataModel_Definition_Property_Abstract {
	/**
	 * @var string
	 */
	protected $_type = DataModel::TYPE_STRING;

	/**
	 * @var null|string
	 */
	protected $validation_regexp = null;

	/**
	 * @var int
	 */
	protected $max_len = 255;

	/**
	 * @var string
	 */
	protected $default_value = "";

	/**
	 * @var string
	 */
	protected $form_field_type = "Input";

	/**
	 * @param $definition_data
	 */
	public function setUp( $definition_data ) {
		if($definition_data) {
			parent::setUp($definition_data);

			$this->max_len = (int)$this->max_len;
		}

	}

	/**
	 * @param mixed &$value
	 *
	 */
	public function checkValueType( &$value ) {
		$value = (string)$value;
	}

	/**
	 * @return string|null
	 */
	public function getValidationRegexp() {
		return $this->validation_regexp;
	}

	/**
	 * @return int|null
	 */
	public function getMaxLen() {
		return $this->max_len;
	}


	/**
	 * Column value test - checks format
	 *
	 * @param mixed &$value
	 * @param DataModel_ValidationError &$errors
	 * @param string $locale_str
	 *
	 * @return bool
	 */
	public function _validateData_test_value( &$value, &$errors, $locale_str=NULL ) {

		if(!$this->validation_regexp) {
			return true;
		}
		
		if(!preg_match($this->validation_regexp, $value)) {
			$errors[] = new DataModel_ValidationError(
					DataModel_ValidationError::CODE_INVALID_FORMAT,
					$this, $value, $locale_str
				);

			return false;
		}

		return true;
	}

	/**
	 * @return string
	 */
	public function getFormFieldType() {
		if($this->form_field_type!="Input") {
			return $this->form_field_type;
		}

		if($this->max_len<=255) {
			return "Input";
		} else {
			return "Textarea";
		}
	}

	/**
	 * @return array|Form_Field_Abstract
	 */
	public function getFormField() {
		$field = parent::getFormField();
		if($this->validation_regexp) {
			$field->setValidationRegexp($this->validation_regexp);
		}
		return $field;
	}


	/**
	 * @return string
	 */
	public function getTechnicalDescription() {
		$res = "Type: ".$this->getType().", max length: {$this->max_len}";

		$res .= ", required: ".($this->is_required ? "yes":"no");

		if($this->is_ID) {
			$res .= ", is ID";
		}

		if($this->default_value) {
			$res .= ", default value: {$this->default_value}";
		}

		if($this->validation_regexp) {

			$res .= ", validation regexp: {$this->validation_regexp}";
		}

		if($this->description) {
			$res .= "\n\n{$this->description}";
		}

		return $res;
	}


}