<?php
/**
 *
 *
 *
 * Sites Data handle exception
 *
 *
 * @copyright Copyright (c) 2011-2012 Miroslav Marek <mirek.marek.2m@gmail.com>
 * @license http://www.gnu.org/licenses/agpl-3.0.html AGPLv3
 * @author Miroslav Marek <mirek.marek.2m@gmail.com>
 * @version <%VERSION%>
 *
 * @category Jet
 * @package Mvc
 * @subpackage Mvc_Sites
 */
namespace Jet;

class Mvc_Sites_Site_Exception extends Exception {

        const CODE_DATA_CHECK_FATAL_ERROR = 20;

	const CODE_URL_NOT_DEFINED = 100;
	const CODE_URL_INVALID_FORMAT = 101;
	const CODE_URL_ALREADY_ADDED = 102;

}