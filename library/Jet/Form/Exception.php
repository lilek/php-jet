<?php
/**
 *
 *
 *
 * Form handle exception
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

class Form_Exception extends Exception {
	const CODE_INVALID_FIELD_CLASS = 1;

	const CODE_VIEW_PARSE_ERROR = 10;

	const CODE_UNKNOWN_FIELD = 20;
	const CODE_UNKNOWN_FIELD_TYPE = 21;


}