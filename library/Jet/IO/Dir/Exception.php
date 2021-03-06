<?php
/**
 *
 *
 *
 * Directory handle exception
 *
 *
 * @copyright Copyright (c) 2011-2012 Miroslav Marek <mirek.marek.2m@gmail.com>
 * @license http://www.gnu.org/licenses/agpl-3.0.html AGPLv3
 * @author Miroslav Marek <mirek.marek.2m@gmail.com>
 * @version <%VERSION%>
 *
 * @category Jet
 * @package IO
 * @subpackage IO_Dir
 */
namespace Jet;

class IO_Dir_Exception extends Exception {

	const CODE_CREATE_FAILED = 1;
	const CODE_OPEN_FAILED = 2;
	const CODE_REMOVE_FAILED = 3;
	const CODE_COPY_FAILED = 4;
	const CODE_MOVE_FAILED = 5;

}