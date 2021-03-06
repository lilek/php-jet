<?php
/**
 *
 *
 *
 * Implementation of signal (message).
 *
 *
 * @copyright Copyright (c) 2011-2012 Miroslav Marek <mirek.marek.2m@gmail.com>
 * @license http://www.gnu.org/licenses/agpl-3.0.html AGPLv3
 * @author Miroslav Marek <mirek.marek.2m@gmail.com>,
 * @version <%VERSION%>
 *
 * @category Jet
 * @package Application
 * @subpackage Application_Signals
 */

namespace Jet;

class Application_Signals_Signal extends Object {

	/**
	 * Instance of the sender
	 *
	 * @var Object
	 */
	protected $sender;

	/**
	 * Name of the signal
	 *
	 * @var string
	 */
	protected $name;

	/**
	 * Signal data
	 *
	 * @var mixed
	 */
	protected $data = array();

	/**
	 *
	 * @param \Jet\Object $sender
	 * @param string $name
	 * @param array $data (optional)
	 */
	public function __construct( Object $sender, $name, array $data=array() ) {
		$this->sender = $sender;
		$this->name = $name;
		$this->data = $data;
	}

	/**
	 *
	 * @return Object
	 */
	public function getSender(){
		return $this->sender;
	}

	/**
	 *
	 * @return string
	 */
	public function getName(){
		return $this->name;
	}

	/**
	 *
	 * @return mixed
	 */
	public function getData(){
		return $this->data;
	}
}