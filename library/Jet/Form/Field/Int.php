<?php 
/**
 *
 *
 *
 * class representing single form field - type int
 *
 *
 *
 *
 * specific options:
 * 	min_value: min. value
 *  max_value: max. value
 *
 * specific errors:
 *  out_of_range
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

class Form_Field_Int extends Form_Field_Abstract {
	/**
	 * @var string
	 */
	protected $_type = "Int";

	/**
	 * @var array
	 */
	protected $error_messages = array(
				"input_missing" => "input_missing",
				"empty" => "empty",
				"invalid_format" => "invalid_format",
				"out_of_range" => "out_of_range",
			);

	/**
	 * @var null|int
	 */
	protected $min_value = null;
	/**
	 * @var null|int
	 */
	protected $max_value = null;


	/**
	 * @return int|null
	 */
	public function getMinValue() {
		return $this->min_value;
	}

	/**
	 * @param int $min
	 */
	public function setMinValue($min) {
		$this->min_value = (int)$min;
	}

	/**
	 * @return int|null
	 */
	public function getMaxValue() {
		return $this->max_value;
	}

	/**
	 * @param int $max
	 */
	public function setMaxValue($max) {
		$this->max_value = (int)$max;
	}

	/**
	 * @return bool
	 */
	public function validateValue() {
		
		if(!$this->is_required && $this->_value_raw === ""){
			$this->_setValueIsValid();
			return true;
		}
		
		
		$this->_value = (int)$this->_value_raw;
		
		$min = $this->min_value;
		$max = $this->max_value;
		
		if(
			$min!==null &&
			$this->_value < $min
		) {
			$this->setValueError("out_of_range");
			return false;
		}
		
		if(
			$max!==null &&
			$this->_value > $max
		) {
			$this->setValueError("out_of_range");
			return false;
		}
		
		$this->_setValueIsValid();
		
		return true;
		
	}	
}