<?php ///r3v engine \Error.php
namespace {
///Error class
class Error extends ErrorException {

	/**
	 * Return trimmed dirs in string
	 * @param $str
	 * @return trimmed ROOT
	 */
	public static function pathdiff($str) {
		return str_replace(ROOT, '', $str);
	}

	public static function friendlyErrorType($type) {
		switch($type) {
			case E_ERROR: // 1 //
				return 'E_ERROR';
			case E_WARNING: // 2 //
				return 'E_WARNING';
			case E_PARSE: // 4 //
				return 'E_PARSE';
			case E_NOTICE: // 8 //
				return 'E_NOTICE';
			case E_CORE_ERROR: // 16 //
				return 'E_CORE_ERROR';
			case E_CORE_WARNING: // 32 //
				return 'E_CORE_WARNING';
			case E_CORE_ERROR: // 64 //
				return 'E_COMPILE_ERROR';
			case E_CORE_WARNING: // 128 //
				return 'E_COMPILE_WARNING';
			case E_USER_ERROR: // 256 //
				return 'E_USER_ERROR';
			case E_USER_WARNING: // 512 //
				return 'E_USER_WARNING';
			case E_USER_NOTICE: // 1024 //
				return 'E_USER_NOTICE';
			case E_STRICT: // 2048 //
				return 'E_STRICT';
			case E_RECOVERABLE_ERROR: // 4096 //
				return 'E_RECOVERABLE_ERROR';
			case E_DEPRECATED: // 8192 //
				return 'E_DEPRECATED';
			case E_USER_DEPRECATED: // 16384 //
				return 'E_USER_DEPRECATED';
		}
		return "";
	}

	/**
	 * Prettify trace
	 * @param $trace
	 * @param $separator string
	 * @return string
	 */
	public static function prettyTrace($trace, $separator = '<br>') {
		$result = [];
		foreach ($trace as $i => $v) {
			$result[] = $i.'# ';

			if (isset($v['file']) && $v['file'])
				$result[] = self::pathdiff($v['file']).':'.$v['line'].' - ';
			else
				$result[] = '[internal call] ';

			if (isset($v['class']))
				$result[] = $v['class'].$v['type'];

			$result[] = $v['function'];

			if (isset($v['args']) && $v['args'])
				$result[] = htmlspecialchars(self::pathdiff(json_encode($v['args'], JSON_UNESCAPED_SLASHES|JSON_NUMERIC_CHECK)), ENT_COMPAT|ENT_HTML5);
			else
				$result[] = '()';

			$result[] = $separator;
		}
		return implode($result);
	}

	/**
	 * Custom all-error handler
	 * @param [standard params]
	 * @return varies
	 */
	public static function h($eno = NULL, $estr = NULL, $efile = NULL, $eline = NULL, $econtext = NULL) {
		$trace = debug_backtrace();
		$result = array('<br><br>');

		if ((isset($eno, $estr, $efile)) || (!isset($eno) && (($e_last = error_get_last()) !== NULL))) { //error
			$eName = '?';

			if (isset($e_last['type']))
				$eno = $e_last['type'];

			if ($eName = self::friendlyErrorType($eno))
					array_shift($trace);

			if (isset($e_last['message'])) {
				$eName = '<b>FATAL</b> '.$eName;
				$efile = $e_last['file'];
				$eline = $e_last['line'];
				$estr = nl2br(self::pathdiff($e_last['message']));
			}

			$result[] = '<big><b>Error</b></big>  ['.$eName.']  '.$estr;
			$result[] = '<br><i>@</i>'.self::pathdiff($efile).':'.$eline;
		} elseif (isset($eno)) { //exception handler
			$result[] = '<big><b>'.get_class($eno).'</b></big>  ';

			$result[] = (method_exists($eno, 'getExtMessage') ? $eno->getExtMessage() : $eno->getMessage());
			$result[] = '<br><i>@</i>'.self::pathdiff($eno->getFile()).':'.$eno->getLine();

			$trace = $eno->getTrace();
		}

		if ((isset($eno) && !($eno instanceof ErrorHTTP)) || isset($e_last))
			http_response_code(500);

		if ($trace)
			$result[] = '<br>'.Error::prettyTrace($trace);

		echo implode($result);
		return true;
	}
}

class ErrorCMS extends Error {}

class ErrorHTTP extends Error {
	public $httpcode;
	public $inmessage;
	public function __construct($msg = NULL, $code = NULL, $add = NULL) {
		$this->httpcode = $code;
		$this->inmessage = $msg;

		if ($code)
			http_response_code($code);

		$msg = 'HTTP '.(int)$code.': '.$msg;

		parent::__construct($msg);
	}

	public function getHttpCode() {
		return $this->httpcode;
	}
}

class Error404 extends ErrorHTTP {
	public function __construct($m = NULL, $add = NULL) {
		if (!$m) $m = 'Page not found';
		parent::__construct($m, 404, $add);
	}
}

class Error403 extends ErrorHTTP {
	public function __construct($m = NULL, $add = NULL) {
		if (!$m) $m = 'Forbidden';
		parent::__construct($m, 403, $add);
	}
}

class Error400 extends ErrorHTTP {
	public function __construct($m = NULL) {
		if (!$m) $m = 'Bad Request';
		parent::__construct($m, 400);
	}
}

class Error418 extends ErrorHTTP {
	public function __construct($m = NULL) {
		if (!$m) $m = "I\'m a teapot :D<br />(Some funny error occured, please return to <a href=\"/\">index</a> page)";
		parent::__construct($m, 400);
	}
}

class Error500 extends ErrorHTTP {
	public function __construct($m = NULL) {
		if (!$m) $m = 'Internal Server Error';
		parent::__construct($m, 500);
	}
}

class Error501 extends ErrorHTTP {
	public function __construct($m = NULL) {
		if (!$m) $m = 'Not (Yet...) Implemented';
		parent::__construct($m, 500);
	}
}

class Error503 extends ErrorHTTP {
	public function __construct($m = NULL) {
		if (!$m) $m = 'Site Overlo[ar]d';
		parent::__construct($m, 503);
	}
}
}
namespace r3v {
class Error extends \Error {}
}
