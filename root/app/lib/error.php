<?

//=============================================================================================
// Production Error Logging
//=============================================================================================

//---------------------------------------------------------------------------------------------
// A reimplementation of var_dump with better formatting and 3-depth limit
function dumpVar ($var, $currentDepth = 0) {
    if (is_array($var)) {
        if ($currentDepth >= 3) {
            return '...';
        } else {
            $dump = 'array(' . count($var) . ") {\n";
            foreach ($var as $key => $value) {
                $dump .= str_repeat('   ', $currentDepth + 1) . '["' . $key . '"] => ' . dumpVar($value, $currentDepth + 1) . "\n";
            }
            $dump .= str_repeat('   ', $currentDepth) . "}";
            return $dump;
        }
    } else {
        ob_start();
        var_dump($var);
        return chop(ob_get_clean());
    }
}

//---------------------------------------------------------------------------------------------
/**
 * Mechanism for handling internal and/or unexpected errors.
 * Logs an error with the specified string. The error is actually handled as a PHP warning
 * because script execution should continue. An error would cause it to halt.
 * In debug mode, this will be output to the browser and to error.log.
 * In production mode, this will be logged with full debug info.
 * Data contains any extra data the erring function might want to pass on.
 */
function LogAppError ($errorString, $data = false) {
    // Manually invoke production error handler if production is on, else trigger normally
    if (defined('DISABLE_DEFAULT_ERROR_HANDLING'))
        HandleProductionError(E_USER_WARNING, $errorString, false, false, get_defined_vars(), $data);
    else
        trigger_error($errorString . ' Data: ' . print_r($data, true), E_USER_WARNING);
}

//---------------------------------------------------------------------------------------------
// App exception class for throwing user errors, meant for the user to see.
class AppException extends Exception {
    public function __construct ($message) {
        parent::__construct($message);
    }

    public function getData () { return false; }
}

//---------------------------------------------------------------------------------------------
// Exception class for throwing user errors, meant for the user to see.
class UserException extends AppException {
    public function __construct ($message) {
        parent::__construct($message);
    }
}

//---------------------------------------------------------------------------------------------
// Exception class for internal errors, meant to be logged.
class InternalException extends AppException {
    private $data = false;

    public function __construct ($message, $data) {
        parent::__construct($message);
        $this->data = $data;
        // Log immediately.
        LogAppError($message, $data);
    }

    public function getData () {
        return $this->data;
    }
}

//---------------------------------------------------------------------------------------------
// Throw an exception to be reported to the user!
function UERR ($errorString) {
    throw new UserException($errorString);
}

//---------------------------------------------------------------------------------------------
// Throw an internal exception.
function IERR ($errorString, $data = false) {
    throw new InternalException($errorString, $data);
}

//---------------------------------------------------------------------------------------------
// Global exception handler, which converts it to an error.
function HandleException ($exception) {
    if (get_class($exception) == 'UserException')
        LogAppError('Uncaught user exception: ' . $exception->getMessage());
    else if (get_class($exception) == 'InternalException')
        LogAppError('Uncaught internal exception: ' . $exception->getMessage(), $exception->getData());
    else
        LogAppError('Uncaught exception: ' . $exception->getMessage());

    // Show really bad error page.
    echo file_get_contents('exception.html');
}

set_exception_handler('HandleException');

//---------------------------------------------------------------------------------------------
// Production error handler that catches errors in production and logs them with detailed reports
function HandleProductionError ($errno, $errorMsg, $errfile, $errline, $context, $data = false) {
    // Ignore suppressed errors.
    if (error_reporting() == 0) {
        return;
    }

    // What type of error
    $level = LOG_DEBUG;
    $levelString = 'debug';
    switch ($errno) {
        case E_PARSE:
        case E_ERROR:
        case E_CORE_ERROR:
        case E_COMPILE_ERROR:
        case E_USER_ERROR:
            $level = LOG_ERROR;
            $levelString = 'error';
        break;
        case E_WARNING:
        case E_USER_WARNING:
        case E_COMPILE_WARNING:
            $level = LOG_WARNING;
            $levelString = 'warning';
        break;
        case E_NOTICE:
        case E_USER_NOTICE:
            $level = LOG_NOTICE;
            $levelString = 'notice';
        break;
        default:
            return false;
        break;
    }

    // User id who experienced the error.
    $userId = null;
    if (isset($_COOKIE[USER_COOKIE_NAME])) {
        $cookieData = $_COOKIE[USER_COOKIE_NAME];
        $cookie = DecipherCookie($cookieData);
        if ($cookie !== false)
            $userId = $cookie[0];
    }
    if ($userId === null)
        $userId = 'Unknown';

    // Backtrace
    $stackTrace = '';
    $bt = debug_backtrace();

    $file = false;
    $line = false;

    $viewVars = false;
    for ($i = 0; $i < count($bt); $i++) {
        $frame = $bt[$i];

        $argDump = '';

        // Disregard frames from bootstrap.

        if (!isset($frame['file']))
            $frame['file'] = 'Unknown file';

        if (!isset($frame['line']))
            $frame['line'] = 'Unknown line';

        if (strpos($frame['file'], 'error') !== false) {
            continue;
        }

        // Disregard args if file is from cake, or if the function is this one.
        if (strpos($frame['file'], 'cake') === false && $frame['function'] != 'HandleProductionError') {
            foreach ($frame['args'] as $arg) {
                $argText = dumpVar($arg);
                $argDump .= chop($argText) . ", ";
            }
            $argDump = substr($argDump, 0, -2);
        } else if (strpos($frame['file'], 'view.php') !== false && $frame['function'] == '_render') {
            // Capture view vars
            $viewVars = $frame['args'][1];
        } else {
            $argDump = 'Omitted';
        }

        if ($file === false)
            $file = $frame['file'];
        if ($line === false)
            $line = $frame['line'];

        $stackTrace .= sprintf("%s - %s (%s): %s\n", $frame['file'], $frame['function'], $frame['line'], $argDump);
    }

    $referrer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'None';

    // Log

    // Setup MySQL error connection only once.
    static $logConnection = false;
    if ($logConnection === false) {
        config('database');
        $dbConfig = new DATABASE_CONFIG();
        $logConnection = mysql_connect($dbConfig->default['host'], $dbConfig->default['login'], $dbConfig->default['password']);
        mysql_select_db($dbConfig->default['database'], $logConnection);
    }

    if ($logConnection) {
        // Use SQL log.
        $query = sprintf("
            INSERT INTO
                `error_log`
            (
                `type`,
                `error`,
                `request`,
                `referrer`,
                `file`,
                `line`,
                `user_id`,
                `stack_trace`,
                `view_vars`,
                `get`,
                `post`,
                `cookie`,
                `session`,
                `server`,
                `data`,
                `context`,
                `time`
            ) VALUES (
                '%s', '%s', '%s', '%s', '%s',
                '%s', '%s', '%s', '%s', '%s',
                '%s', '%s', '%s', '%s', '%s',
                '%s', '%s'
            )",
            mysql_real_escape_string($levelString, $logConnection),
            mysql_real_escape_string($errorMsg, $logConnection),
            mysql_real_escape_string($_SERVER['REQUEST_URI'], $logConnection),
            mysql_real_escape_string($referrer, $logConnection),
            mysql_real_escape_string($file, $logConnection),
            mysql_real_escape_string($line, $logConnection),
            mysql_real_escape_string($userId, $logConnection),
            mysql_real_escape_string($stackTrace, $logConnection),
            mysql_real_escape_string(dumpVar($viewVars), $logConnection),
            mysql_real_escape_string(dumpVar($_GET), $logConnection),
            mysql_real_escape_string(dumpVar($_POST), $logConnection),
            mysql_real_escape_string(dumpVar($_COOKIE), $logConnection),
            mysql_real_escape_string(isset($_SESSION) ? dumpVar($_SESSION) : 'None', $logConnection),
            mysql_real_escape_string(dumpVar($_SERVER), $logConnection),
            mysql_real_escape_string(dumpVar($data), $logConnection),
            mysql_real_escape_string(dumpVar($context), $logConnection),
            date(DB_FORMAT)
        );

        $result = mysql_query($query, $logConnection);
        if ($result === false) {
            // Turn off SQL debugging if error occurs.
            $logConnection = null;
            LogError('Failed to log error.');
        }

    } else {
        // Fall back to text log.
        $errorString = "\n";
        $errorString .= sprintf("Error: %s\n", $errorMsg);
        $errorString .= sprintf("Request: %s\n", $_SERVER['REQUEST_URI']);
        $errorString .= sprintf("Referrer: %s\n", $referrer);
        $errorString .= sprintf("File: %s (%d)\n", $file, $line);
        $errorString .= sprintf("User id: %s\n", $userId);

        $errorString .= "Stack Trace:\n";
        $errorString .= $stackTrace;

        $errorString .= "View variables:\n";
        if ($viewVars)
            $errorString .= dumpVar($viewVars);
        else
            $errorString .= "None\n";

        // HTTP Data
        $errorString .= "\nGET:\n";
        $errorString .= dumpVar($_GET);

        $errorString .= "\nPOST:\n";
        $errorString .= dumpVar($_POST);

        $errorString .= "\nCOOKIE:\n";
        $errorString .= dumpVar($_COOKIE);

        $errorString .= "\nSESSION:\n";
        $errorString .= isset($_SESSION) ? dumpVar($_SESSION) : 'None';

        $errorString .= "\nSERVER:\n";
        $errorString .= dumpVar($_SERVER);

        CakeLog::write($level, $errorString);
    }

    // Die if fatal
    if ($level === LOG_ERROR) {
        die();
    }

    return true;
}

uses('cake_log');

if (Configure::read() === 0) {
    // Disable the default handling and include logger
    define('DISABLE_DEFAULT_ERROR_HANDLING', 1);

    $errorMask = E_ALL & ~E_DEPRECATED & ~E_STRICT;
    set_error_handler('HandleProductionError', $errorMask);
    error_reporting($errorMask);  // Doesn't do anything, but value can be read in error handler.
}

?>