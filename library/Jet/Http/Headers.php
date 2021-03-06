<?php
/**
 *
 * @copyright Copyright (c) 2011-2012 Miroslav Marek <mirek.marek.2m@gmail.com>
 * @license http://www.gnu.org/licenses/agpl-3.0.html AGPLv3
 * @author Miroslav Marek <mirek.marek.2m@gmail.com>
 * @version <%VERSION%>
 *
 * @category Jet
 * @package Http
 * @subpackage Http_Headers
 */
namespace Jet;

class Http_Headers {

	const CODE_200_OK = 200;
	const CODE_201_CREATED = 201;
	const CODE_202_ACCEPTED = 202;
	const CODE_204_NO_CONTENT = 204;
	const CODE_205_RESET_CONTENT = 205;
	const CODE_206_PARTIAL_CONTENT = 206;

	const CODE_301_MOVED_PERMANENTLY = 301;
	const CODE_302_FOUND = 302;
	const CODE_302_MOVED_TEMPORARY = 302;
	const CODE_303_SEE_OTHER = 303;
	const CODE_304_NOT_MODIFIED = 304;
	const CODE_307_TEMPORARY_REDIRECT = 307;
	const CODE_308_PERMANENT_REDIRECT = 308;

	const CODE_400_BAD_REQUEST = 400;
	const CODE_401_UNAUTHORIZED = 401;
	const CODE_402_PAYMENT_REQUIRED = 402;
	const CODE_403_FORBIDDEN = 403;
	const CODE_404_NOT_FOUND = 404;
	const CODE_405_METHOD_NOT_ALLOWED = 405;
	const CODE_406_NOT_ACCEPTABLE = 406;
	const CODE_407_PROXY_AUTHENTICATION_REQUIRED = 407;
	const CODE_408_REQUEST_TIMEOUT = 408;
	const CODE_409_CONFLICT = 409;
	const CODE_410_GONE = 410;
	const CODE_411_LENGTH_REQUIRED = 411;
	const CODE_412_PRECONDITION_FAILED = 412;
	const CODE_413_REQUEST_ENTITY_TOO_LARGE = 413;
	const CODE_414_REQUEST_URI_TOO_LONG = 414;
	const CODE_415_UNSUPPORTED_MEDIA_TYPE = 415;
	const CODE_416_REQUESTED_RANGE_NOT_SATISFIABLE = 416;
	const CODE_417_EXPECTATION_FAILED = 417;
	const CODE_425_UNORDERED_COLLECTION = 425;
	const CODE_426_UPGRADE_REQUIRED = 426;
	const CODE_428_PRECONDITION_REQUIRED = 428;
	const CODE_429_TOO_MANY_REQUESTS = 429;
	const CODE_431_REQUEST_HEADER_FIELDS_TOO_LARGE = 431;
	const CODE_444_NO_RESPONSE = 444;
	const CODE_451_UNAVAILABLE_FOR_LEGAL_REASONS = 451;

	const CODE_500_INTERNAL_SERVER_ERROR = 500;
	const CODE_501_NOT_IMPLEMENTED = 501;
	const CODE_502_BAD_GATEWAY = 502;
	const CODE_503_SERVICE_UNAVAILABLE = 503;
	const CODE_504_GATEWAY_TIMEOUT = 504;
	const CODE_505_HTTP_VERSION_NOT_SUPPORTED = 505;
	const CODE_506_VARIANT_ALSO_NEGOTIATES = 506;
	const CODE_509_BANDWIDTH_LIMIT_EXCEEDED = 509;
	const CODE_510_NOT_EXTENDED = 510;
	const CODE_511_NETWORK_AUTHENTICATION_REQUIRED = 511;
	const CODE_598_NETWORK_READ_TIMEOUT_ERROR = 598;
	const CODE_599_NETWORK_CONNECT_TIMEOUT_ERROR = 599;

	/**
	 *
	 * @var array
	 */
	protected static $response_messages = array(
		200 => "OK",
		201 => "Created",
		202 => "Accepted",
		204 => "No Content",
		205 => "Reset Content",
		206 => "Partial Content",

		301 => "Moved Permanently",
		302 => "Found",
		303 => "See Other",
		304 => "Not Modified",
		307 => "Temporary Redirect",
		308 => "Permanent Redirect",

		400 => "Bad Request",
		401 => "Unauthorized",
		402 => "Payment Required",
		403 => "Forbidden",
		404 => "Not Found",
		405 => "Method Not Allowed",
		406 => "Not Acceptable",
		407 => "Proxy Authentication Required",
		408 => "Request Timeout",
		409 => "Conflict",
		410 => "Gone",
		411 => "Length Required",
		412 => "Precondition Failed",
		413 => "Request Entity Too Large",
		414 => "Request-URI Too Long",
		415 => "Unsupported Media Type",
		416 => "Requested Range Not Satisfiable",
		417 => "Expectation Failed",
		425 => "Unordered Collection",
		426 => "Upgrade Required",
		428 => "Precondition Required",
		429 => "Too Many Requests",
		431 => "Request Header Fields Too Large",
		444 => "No Response",
		451 => "Unavailable For Legal Reasons",

		500 => "Internal Server Error",
		501 => "Not Implemented",
		502 => "Bad Gateway",
		503 => "Service Unavailable",
		504 => "Gateway Timeout",
		505 => "HTTP Version Not Supported",
		506 => "Variant Also Negotiates",
		509 => "Bandwidth Limit Exceeded",
		510 => "Not Extended",
		511 => "Network Authentication Required",
		598 => "Network read timeout error",
		599 => "Network connect timeout error",
	);


	/**
	 *
	 * @var string
	 */
	protected static $http_version = "1.1";


	/**
	 * @return string
	 */
	public static function getHttpVersion() {
		return static::$http_version;
	}

	/**
	 * @param string $http_version
	 */
	public static function setHttpVersion($http_version) {
		static::$http_version = $http_version;
	}

	/**
	 *
	 * @static
	 *
	 * @return array
	 */
	public static function getResponseCodes(){
		return static::$response_messages;
	}


	/**
	 * Returns HTTP text message (or false if unknown HTTP code)
	 *
	 * @static
	 *
	 * @param int $http_code
	 *
	 * @return string|bool
	 */
	public static function getResponseMessage( $http_code ){
		if(!isset(static::$response_messages[$http_code])) {
			return false;
		}

		return static::$response_messages[$http_code];
	}

	/**
	 * Get response status header
	 *
	 * @static
	 *
	 * @param int $http_code
	 *
	 * @return string|bool
	 */
	public static function getResponseHeader( $http_code ){
		$message = static::getResponseMessage($http_code);
		if(!$message) {
			return false;
		}

		return "HTTP/".static::$http_version." {$http_code} {$message}";
	}

	/**
	 * Send status header
	 *
	 * @static
	 *
	 * @param int $code
	 * @param array $headers (optional)
	 *
	 * @return bool
	 */
	public static function response($code, array $headers = array()){


		$header = static::getResponseHeader($code);
		if(!$header){
			return false;
		}


		static::sendHeader( $header, true, $code );

		foreach($headers as $header => $value){

			if( is_int($header) ){
				static::sendHeader($value);
			} else {
				if(is_array($value)){
					$value = implode("; ", $value);
				}

				static::sendHeader("{$header}: {$value}");
			}

		}

		return true;
	}

	/**
	 * PHP header function replacement
	 * CLI Unit test support
	 *
	 * @param $header
	 * @param bool $replace
	 * @param int $http_response_code
	 *
	 * @return bool
	 */
	protected static function sendHeader( $header, $replace=true, $http_response_code=0 ) {
		if( PHP_SAPI==="cli" && isset($GLOBALS["_test_Http_Headers_sent_headers"]) ){
			$GLOBALS["_test_Http_Headers_sent_headers"][] = $header;
		} else {
			header($header, $replace, $http_response_code);
		}
	}


	/**
	 *
	 * @param array $headers (optional)
	 * @param array $headers (optional, default: none)
	 */
	public static function responseOK( array $headers = array() ){
		static::response(static::CODE_200_OK, $headers);
	}


	/**
	 * Permanent redirection - 301
	 *
	 * @param string $target_URL target URL
	 * @param bool $application_end (optional, default: true)
	 * @param array $headers (optional, default: none)
	 *
	 */
	public static function movedPermanently($target_URL, $application_end = true,  array $headers = array()){
		static::response(
			static::CODE_301_MOVED_PERMANENTLY,
			array_merge(
				array(
					"Location" => $target_URL
				),
				$headers
			)
		);

		if($application_end) {
			Application::end();
		}
	}

	/**
	 * Temporary redirection - 302
	 *
	 * @param string $target_URL target URL
	 * @param bool $application_end (optional, default: true)
	 * @param array $headers (optional, default: none)
	 */
	public static function movedTemporary($target_URL, $application_end = true,  array $headers = array()){
		static::response(
			static::CODE_302_MOVED_TEMPORARY,
			array_merge(
				array(
					"Location" => $target_URL
				),
				$headers
			)
		);

		if($application_end) {
			Application::end();
		}
	}

	/**
	 * See other - 303
	 *
	 * @param string $target_URL target URL
	 * @param bool $application_end (optional, default: true)
	 * @param array $headers (optional, default: none)
	 */
	public static function seeOther( $target_URL, $application_end = true,  array $headers = array() ){
		static::response(
			static::CODE_303_SEE_OTHER,
			array_merge(
				array(
					"Location" => $target_URL
				),
				$headers
			)
		);
		if($application_end) {
			Application::end();
		}
	}


	/**
	 * Page not found - 404
	 *
	 * @param array $headers (optional, default: none)
	 */
	public static function notFound( array $headers = array() ){
		static::response( static::CODE_404_NOT_FOUND, $headers );
	}

	/**
	 * Page not found - 304
	 *
	 * @param array $headers (optional, default: none)
	 */
	public static function notModified( array $headers = array() ){
		static::response( static::CODE_304_NOT_MODIFIED, $headers);
	}


	/**
	 * Authorization required - 401
	 *
	 * @param array $headers (optional, default: none)
	 */
	public static function authorizationRequired( array $headers = array() ){
		static::response( static::CODE_401_UNAUTHORIZED, $headers );
	}

	/**
	 * Bad request - 400
	 *
	 * @param array $headers (optional, default: none)
	 */
	public static function badRequest( array $headers = array() ){
		static::response( static::CODE_400_BAD_REQUEST, $headers );
	}

	/**
	 * Forbidden - 403
	 *
	 * @param array $headers (optional, default: none)
	 */
	public static function forbidden( array $headers = array() ){
		static::response( static::CODE_403_FORBIDDEN, $headers );
	}

	/**
	 * Internal server error - 500
	 *
	 */
	public static function internalServerError( array $headers = array() ){
		static::response( static::CODE_500_INTERNAL_SERVER_ERROR, $headers  );
	}


	/**
	 *
	 * @param Form $form
	 *
	 * @param bool $application_end (optional, default: true)
	 */
	public static function formSent( Form $form, $application_end = true ) {
		static::sendHeader("Location: #".$form->getName());
		if($application_end){
			Application::end();
		}
	}

	/**
	 * Reload current page
	 *
	 * @param bool $application_end (optional, default: true)
	 */
	public static function reload( $application_end = true ) {
		static::sendHeader("Location: ?#");
		if($application_end){
			Application::end();
		}
	}


	/**
	 * Send headers for file download
	 *
	 * @param string $file_name
	 * @param string $file_mime
	 * @param int $file_size
	 * @param bool $force_download (optional, force download header, default: false)
	 *
	 */
	public static function sendDownloadFileHeaders(
					$file_name,
					$file_mime,
					$file_size,
					$force_download = false
				) {

		$date = gmdate("D, d M Y H:i:s");

		if($force_download) {
			static::sendHeader("Content-Description: File Transfer");
			static::sendHeader("Cache-Control: public, must-revalidate, max-age=0");
			static::sendHeader("Pragma: public");
			static::sendHeader("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
			static::sendHeader("Last-Modified: {$date} GMT");
			static::sendHeader("Content-Type: application/force-download");
			static::sendHeader("Content-Type: application/octet-stream");
			static::sendHeader("Content-Type: application/download");
			static::sendHeader("Content-Type: {$file_mime}");
			static::sendHeader("Content-Disposition: attachment; filename='{$file_name}';");
			static::sendHeader("Content-Transfer-Encoding: binary");
			static::sendHeader("Content-Length: {$file_size}");
		} else {
			static::sendHeader("Content-Type: {$file_mime}");
			static::sendHeader("Cache-Control: public, must-revalidate, max-age=0");
			static::sendHeader("Pragma: public");
			static::sendHeader("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
			static::sendHeader("Last-Modified: {$date} GMT");
			static::sendHeader("Content-Length: {$file_size}");
			static::sendHeader("Content-Disposition: inline; filename='{$file_name}';");
		}
	}

}