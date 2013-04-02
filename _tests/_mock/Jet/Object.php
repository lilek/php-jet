<?php
namespace JetTest;

class Object {

	protected static $__factory_class_name = null;
	protected static $__factory_class_method_name = null;
	protected static $__factory_must_be_instance_of_class_name = null;

	public function __get( $property_name ) {
		if(isset($this->$property_name)) {
			//workaround for tests
			return $this->$property_name;
		}

		throw new \Exception("Undefined class property ".get_class($this)."::{$property_name}");
	}

	/**
	 * Get factory class name
	 *
	 * @return string
	 */
	public static function getFactoryClassName() {
		return static::$__factory_class_name;
	}

	/**
	 * Get factory class method name
	 *
	 *
	 * @return string
	 */
	public static function getFactoryClassMethod() {
		return static::$__factory_class_method;
	}


	/**
	 * Get class name which the object generated by factory must be instance of
	 *
	 * @return string
	 */
	public static function getFactoryMustBeInstanceOfClassName() {
		return static::$__factory_must_be_instance_of_class_name;
	}


	/**
	 *
	 * @param array $data
	 *
	 */
	public function __test_set_state(array $data) {

		foreach($data as $key=>$val) {
			$this->{$key} = $val;
		}
	}


	/**
	 *
	 * @param array $data
	 *
	 * @return Object
	 */
	public static function __test_create_instance(array $data) {
		$called_class = get_called_class();
		$_this = new $called_class();

		foreach($data as $key=>$val) {
			$_this->{$key} = $val;
		}
		return $_this;
	}


	/**
	 * Example:
	 *
	 * class name: MyNamespace\MyClass
	 *
	 * returns MyClass
	 *
	 * @return string
	 */
	public function getClassNameWithoutNamespace() {
		$class_name = explode("\\", get_class($this));
		return end($class_name);
	}

	/**
	 * @param $property_name
	 *
	 * @return bool
	 */
	public function getHasProperty( $property_name ) {
		if(
			$property_name[0]=="_" ||
			!property_exists($this, $property_name)
		) {
			return false;
		}
		return true;
	}

}
class_alias("JetTest\Object", "Jet\Object");