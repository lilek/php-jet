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
 * @subpackage Http_Request
 */
namespace Jet;

class Http_Request extends Object {


	/**
	 * PHP's super global $_SERVER original value
	 *
	 * @var array
	 */
	protected static $_SERVER;
	
	
	/**
	 * PHP's super global $_POST original value
	 *
	 * @var array
	 */
	protected static $_POST;
	
	
	/**
	 * PHP's super global $_GET original value
	 *
	 * @var array
	 */
	protected static $_GET;


	/**
	 * @var bool
	 */
	protected static $is_initialized = false;


	/**
	 *
	 * @var Data_Array
	 */
	protected static $GET = null;
	
	/**
	 *
	 * @var Data_Array
	 */
	protected static $POST = null;
	
	/**
	 *
	 * @var Data_Array
	 */
	protected static $SERVER = null;


	/**
	 * @var string
	 */
	protected static $raw_post_data = null;

	/**
	 * @var array
	 */
	protected static $headers = null;


	/**
	 * Initialize HTTP request
	 *
	 * @param bool|null $hide_PHP_request_data (optional, default: true)
	 *
	 */
	public static function initialize( $hide_PHP_request_data=true ){

		if( self::$is_initialized ){
			return;
		}
		self::$_SERVER = $_SERVER;
		self::$_POST = $_POST;
		self::$_GET = $_GET;
		self::$is_initialized = true;

		if( $hide_PHP_request_data ){
			Http_Request::hidePHPRequestData();
		}
	}
	
	/**
	 *
	 */
	public static function hidePHPRequestData(){
		$_GET = new Http_Request_Data_Hoax();
		$_POST = new Http_Request_Data_Hoax();
		$_REQUEST = new Http_Request_Data_Hoax();
	}


	/**
	 * Is request initialized?
	 *
	 * @return bool
	 */
	public static function getIsInitialized(){
		return self::$is_initialized;
	}


	/**
	 * Get $_POST replacement instance
	 *
	 * @return Data_Array
	 */
	public static function POST(){
		if(!static::$POST){
			static::$POST = new Data_Array(self::$_POST);
		}
		return static::$POST;
	}
	
	/**
	 * Get $_GET replacement instance
	 *
	 * @return Data_Array
	 */
	public static function GET(){
		if(!static::$GET){
			static::$GET = new Data_Array(self::$_GET);
		}
		return static::$GET;
	}

	/**
	 * Get $_SERVER replacement instance
	 *
	 * @return Data_Array
	 */
	public static function SERVER(){
		if(!static::$SERVER){
			static::$SERVER = new Data_Array(self::$_SERVER);
		}
		return static::$SERVER;
	}


	/**
	 * @see http://php.net/manual/en/wrappers.php  php://input
	 *
	 *
	 *
	 * @return string
	 */
	public static function getRawPostData() {
		if(static::$raw_post_data===null) {
			static::$raw_post_data=file_get_contents("php://input");
		}

		return static::$raw_post_data;
	}
	
	

	/**
	 * Get request method (one of Http_Request::REQUEST_METHOD_*)
	 *
	 * @return string 
	 */
	public static function getRequestMethod(){
		return isset( $_SERVER["REQUEST_METHOD"] ) ? strtoupper($_SERVER["REQUEST_METHOD"]) : "GET";
	}
	

	/**
	 * Is HTTP?
	 *
	 * @return bool
	 */
	public static function getRequestIsHttp(){
		return !static::getRequestIsHttps();
	}
	
	/**
	 * Is HTTPS?
	 *
	 * @return bool
	 */
	public static function getRequestIsHttps(){
		return isset( $_SERVER["HTTPS"] ) && strtolower($_SERVER["HTTPS"])!=="off";
	}
	
	/**
	 * Get client's IP address
	 *
	 * @return string 
	 */
	public static function getClientIP(){
		return isset( $_SERVER["REMOTE_ADDR"] ) ? $_SERVER["REMOTE_ADDR"] : "unknown";
	}

	/**
	 * Get client's user agent
	 *
	 * @return string
	 */
	public static function getClientUserAgent(){
		return isset( $_SERVER["HTTP_USER_AGENT"] ) ? $_SERVER["HTTP_USER_AGENT"] :  "unknown";
	}

	/**
	 * @static
	 *
	 * @param $include_query_string (optional, default: true)
	 *
	 * @return string
	 */
	public static function getURL( $include_query_string=true ) {
		$scheme = "http";
		$host = $_SERVER["HTTP_HOST"];
		$port = "";
		$request_URI = $_SERVER["REQUEST_URI"];

		if(!$include_query_string) {
			list($request_URI) = explode("?", $request_URI);
		}

		if(
			static::getRequestIsHttps()
		) {
			$scheme = "https";
			if( $_SERVER["SERVER_PORT"]!="443" ) {
				$port = ":{$_SERVER["SERVER_PORT"]}";
			}
		} else {
			if( $_SERVER["SERVER_PORT"]!="80" ) {
				$port = ":{$_SERVER["SERVER_PORT"]}";
			}
		}

		return "{$scheme}://{$host}{$port}{$request_URI}";
	}

	/**
	 * Returns request HTTP headers
	 *
	 * @static
	 * @return array
	 */
	public static function getHeaders() {
		if( static::$headers!==null ) {
			return static::$headers;
		}

		if(function_exists("apache_request_headers")) {
			$headers = apache_request_headers();
		} else {

			$headers = array();
			foreach($_SERVER as $key => $value) {
				if(substr($key, 0, 5) != "HTTP_") {
					continue;
				}

				$header = str_replace(" ", "-",
					ucwords(
						str_replace("_", " ",
							strtolower( substr($key, 5) )
						)
					)
				);

				$headers[$header] = $value;
			}
		}

		static::$headers = $headers;

		return static::$headers;
	}
}