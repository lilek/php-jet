<?php
/**
 *
 * @copyright Copyright (c) 2011-2012 Miroslav Marek <mirek.marek.2m@gmail.com>
 * @license http://www.gnu.org/licenses/agpl-3.0.html AGPLv3
 * @author Miroslav Marek <mirek.marek.2m@gmail.com>
 * @version <%VERSION%>
 *
 * @category Jet
 * @package IO
 * @subpackage IO_File
 */
namespace Jet;

class IO_File_Exception extends Exception {

	const CODE_READ_FAILED = 1;
	const CODE_WRITE_FAILED = 2;
	const CODE_CHMOD_FAILED = 3;
	const CODE_DELETE_FAILED = 4;
	const CODE_COPY_FAILED = 5;

	const CODE_IS_NOT_UPLOADED_FILE = 100;

	const CODE_GET_FILE_SIZE_FAILED = 200;

}