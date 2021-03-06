<?php
/**
 *
 * @copyright Copyright (c) 2011-2012 Miroslav Marek <mirek.marek.2m@gmail.com>
 * @license http://www.gnu.org/licenses/agpl-3.0.html AGPLv3
 * @author Miroslav Marek <mirek.marek.2m@gmail.com>
 * @version <%VERSION%>
 *
 * @category Jet
 * @package Debug
 * @subpackage Debug_ErrorHandler
 */
namespace Jet;

require_once JET_LIBRARY_PATH."Jet/Debug/Tools/Formatter.php";

class Debug_ErrorHandler_Handler_Display extends Debug_ErrorHandler_Handler_Abstract {


	/**
	 * Internal elements ID counter
	 *
	 * @var int
	 */
	protected static $ID_counter = 0;

	/**
	 * @param Debug_ErrorHandler_Error $error
	 */
	public function handle( Debug_ErrorHandler_Error $error ) {
		if( Debug_ErrorHandler::getHTMLErrorsEnabled() ) {
			echo Debug_Tools_Formatter::formatErrorMessage_HTML( $error, $this->HTML_bg_color );
		} else {
			echo Debug_Tools_Formatter::formatErrorMessage_TXT( $error );
		}
	}

	/**
	 * @return bool
	 */
	public function errorDisplayed() {
		return true;
	}
}