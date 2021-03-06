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

class Debug_Tools_Formatter {
	/**
	 * Get current URL
	 *
	 * @return string
	 */
	protected static function getCurrentURL(){
		if( php_sapi_name() == "cli" ) {
			return isset($_SERVER["SCRIPT_FILENAME"]) ? $_SERVER["SCRIPT_FILENAME"] : "CLI";
		} else {
			return $_SERVER["HTTP_HOST"] .$_SERVER["REQUEST_URI"];
		}
	}

	/**
	 * Format variable value to smart string output
	 *
	 * @param mixed $var
	 * @return string
	 */
	public static function formatVariable($var){
		$result = print_r($var, true);
		if(strlen($result)>2048) {
			$result = substr($result, 0, 2048)." ...";
		}
		return $result;
	}

	/**
	 * Format debug backtrace to printable format
	 *
	 * @param array $debug_backtrace
	 * @param bool $expand_call_arguments(optional - show arguments at function call)
	 * @return array
	 *
	 */
	public static function formatBacktrace(array $debug_backtrace, $expand_call_arguments=true){
		$output = array();
		foreach ($debug_backtrace as $d) {
			$file = isset($d["file"]) ? $d["file"] : "?";
			$args = isset($d["args"]) ? $d["args"] : array();
			$class = isset($d["class"]) ? $d["class"] : "";
			$type = isset($d["type"]) ? $d["type"] : "";
			$function = isset($d["function"]) ? $d["function"] : "";
			$line = isset($d["line"]) ? $d["line"] : 0;

			$content = "";
			if($class){
				$content .= "{$class}{$type}";
			}
			$args_array = array();
			if($function){
				$content .= $function . "(";
				if($args){
					if($expand_call_arguments){
						foreach($args as $arg){
							$args_array[] = self::formatVariable($arg);
						}
						$content .= implode(", ", $args_array);
					} else {
						$content .= "[arguments]";
					}
				}
				$content .= ")";
			}

			$output[] = array(
				"file" => $file,
				"line" => $line,
				"class" => $class,
				"type" => $type,
				"function" => $function,
				"args" => $args_array,
				"call" => $content
			);
		}
		return $output;
	}


	/**
	 * Format error context (variables) to printable format
	 *
	 * @param array $error_context
	 * @return string[]
	 */
	public static function formatErrorContext(array $error_context){
		foreach($error_context as $k => $v){
			$error_context[$k] = self::formatVariable($v);
		}
		return $error_context;
	}



	/**
	 * nl2br and htmlspecialchars
	 *
	 * @param string $html
	 * @return string
	 */
	public static function encodeForHTML($html){
		return nl2br(htmlspecialchars($html));
	}

	/**
	 * Formats error message
	 *
	 * @param  Debug_ErrorHandler_Error $e
	 * @param string $bg_color
	 *
	 * @return string
	 */
	public static function formatErrorMessage_HTML( Debug_ErrorHandler_Error $e, $bg_color ){
		$report = array();

		$report[] = "<br /><div style=\"background-color: {$bg_color};overflow:auto;padding:10px;border: 1px solid black; font-family: 'Arial CE', Arial, sans-serif;\">";
		$report[] = "<h2 style='padding:0;margin:0;'>".static::encodeForHTML($e->txt)."</h2><br/>";
		$report[] = "<strong>".static::encodeForHTML($e->message)."</strong><br/>";
		$report[] = "<hr/>";
		$report[] = "<table cellspacing='0' cellpadding='2' border='1' style='border-collapse:collapse;collapse;background-color: #c9c9c9;'>";
		$report[] = "<tr><td>script:</td><td>{$e->file}</td></tr>";
		$report[] = "<tr><td>line:</td><td>{$e->line}</td></tr>";
		$report[] = "<tr><td>time:</td><td>{$e->date} {$e->time}</td></tr>";


		$url = static::getCurrentURL();

		$url = static::encodeForHTML($url);

		$report[] = "<tr><td>URL:</td><td>{$url}</td></tr>";

		$report[] = "</table><br />";



		if($e->context){
			$report[] = static::formatErrorContext_HTML($e->context);
		}


		if($e->backtrace){
			$report[] = static::formatBacktrace_HTML($e->backtrace);
		}

		$report[] = "</div><br />";

		return implode("\n", $report)."\n";
	}

	/**
	 * Format error context (variables)
	 *
	 * @param array $error_context
	 * @return string
	 */
	public static function formatErrorContext_HTML(array $error_context){
		$output = array();
		$output[] = "<br /><strong>Error context:</strong><br />";
		$output[] = "<table border='1' cellspacing='0' cellpadding='2' style='border-collapse:collapse;background-color: #999999;'>";
		$output[] = "<tr><th align='left'>Variable</th><th align='left'>Value</th></tr>";
		$i = 0;
		foreach(self::formatErrorContext($error_context) as $var_name => $var_value){
			$row_style = "background-color:" . ( ($i % 2 ? "#f0f0f0" : "#c9c9c9") );
			$i++;
			$output[] = "<tr style=\"{$row_style}\"><td valign=\"top\"> \${$var_name}</td><td>" . static::encodeForHTML($var_value) . "</td></tr>";
		}
		$output[] = "</table>";
		return implode("\n", $output) . "\n";
	}

	/**
	 * Format debug backtrace
	 *
	 * @param array $debug_backtrace
	 *
	 * @return string
	 */
	public static function formatBacktrace_HTML(array $debug_backtrace){
		$output = array();
		$output[] = "<br /><strong>Debug backtrace:</strong><br />";
		$output[] = "<table border='1' cellspacing='0' cellpadding='2' style='border-collapse:collapse;background-color: #999999;'>";
		//$output[] = "<tr><th align='left'>File</th><th align='left'>Line</th><th align='left'>Call</th><th>Source</th></tr>";
		$output[] = "<tr><th align='left'>File</th><th align='left'>Line</th><th align='left'>Call</th></tr>";

		$i = 0;
		foreach( self::formatBacktrace($debug_backtrace) as $d ) {
			$row_style = "background-color:" . ( ($i % 2 ? "#f0f0f0" : "#c9c9c9") );
			$i++;

			$row = "<tr style='{$row_style}'><td valign='top'>{$d["file"]}</td><td valign='top'>{$d["line"]}</td>";

			$td_highlighted = "";

			if($d["call"]){
				$row .= "<td valign=\"top\">".self::encodeForHTML($d["call"])."</td>{$td_highlighted}</tr>";
			} else {
				$row .= "<td></td>{$td_highlighted}</tr>";
			}
			$output[] = $row;
		}
		$output[] = "</table>";
		return implode("\n", $output);
	}


	/**
	 * Formats error message
	 *
	 * @param  Debug_ErrorHandler_Error $e
	 * @return string
	 */
	public static function formatErrorMessage_TXT( Debug_ErrorHandler_Error $e ){
		$report = array();

		$url = static::getCurrentURL();

		$report[] = $e->txt;
		$report[] = $e->message;
		$report[] = "";
		$report[] = "script: {$e->file}";
		$report[] = "line: {$e->line}";
		$report[] = "time: {$e->date} {$e->time}";
		$report[] = "URL: {$url}";
		$report[] = "";


		if($e->context){
			$report[] = static::formatErrorContext_TXT($e->context);
		}


		if($e->backtrace){
			$report[] = static::formatBacktrace_TXT($e->backtrace);
		}

		$report[] = "";

		return implode(PHP_EOL, $report).PHP_EOL;
	}

	/**
	 * Format error context (variables)
	 *
	 * @param array $error_context
	 * @return string
	 */
	public static function formatErrorContext_TXT(array $error_context){
		$output = array();
		$output[] = "Error context:";
		$output[] = "";

		foreach(self::formatErrorContext($error_context) as $var_name => $var_value){
			$output[] = "\t\${$var_name} = " . $var_value;
		}
		$output[] = "";
		return implode(PHP_EOL, $output) . PHP_EOL;
	}

	/**
	 * Format debug backtrace
	 *
	 * @param array $debug_backtrace
	 *
	 * @return string
	 */
	public static function formatBacktrace_TXT(array $debug_backtrace){
		$output = array();
		$output[] = "Debug backtrace:";
		$output[] = "";


		foreach( self::formatBacktrace($debug_backtrace) as $d ) {
			$output[] = "{$d["file"]}";
			$output[] = "\tLine: {$d["line"]}";
			$output[] = "\tCall: {$d["call"]}";
			$output[] = "";
		}
		$output[] = "";
		return implode(PHP_EOL, $output).PHP_EOL;
	}

}