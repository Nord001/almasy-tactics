<?

define ('ANALYZE_PATH', '../app/controllers/');
define('USE_COLORS', 0);

error_reporting(E_ALL);

require_once 'colors.php';

$numWarnings = 0;
$numSuggestions = 0;
$numControllers = 0;
$numFunctions = 0;

//---------------------------------------------------------------------------------------------
function Warn ($str, $level = 0) {
    global $numWarnings;
    $numWarnings++;
    PrintStr($str, $level, 'RED');
}

//---------------------------------------------------------------------------------------------
function Suggest ($str, $level = 0) {
    global $numSuggestions;
    $numSuggestions++;
    PrintStr($str, $level, 'BROWN');
}

//---------------------------------------------------------------------------------------------
function PrintStr ($str, $level = 0, $color = false) {
    $str = str_repeat('   ', $level) . $str . "\n";
    if ($color && USE_COLORS)
        echo termcolored($str, $color);
    else
        echo $str;
}

//---------------------------------------------------------------------------------------------
function DetectSetsTitle ($function) {
    foreach ($function as $line)
        if (stripos($line, 'PageTitle') !== false)
            return true;
    return false;
}

//---------------------------------------------------------------------------------------------
function DetectManipData ($function) {
    foreach ($function as $line) {
        if (strpos($line, '$this->data') !== false)
            return true;

        if (strpos($line, '\'form\'') !== false)
            return true;

        if (strpos($line, 'setFlash') !== false)
            return true;
    }
    return false;
}

//---------------------------------------------------------------------------------------------
function DetectAjax ($function) {
    foreach ($function as $line)
        if (stripos($line, 'ajax') !== false)
            return true;
    return false;
}

//---------------------------------------------------------------------------------------------
function DetectCSRF ($function) {
    foreach ($function as $line)
        if (stripos($line, 'csrf') !== false)
            return true;
    return false;
}

//---------------------------------------------------------------------------------------------
function CheckDebugChange ($function, $beginFuncLine) {
    $i = 0;
    foreach ($function as $line) {
        if (stripos($line, '::\'debug\'') !== false)
            Warn('Function is changing debug config (line ' . ($beginFuncLine + $i + 1) . '), should be using ajaxMode.', 2);
        $i++;
    }
    return false;
}

//---------------------------------------------------------------------------------------------
function CheckUseOfLog ($function, $beginFuncLine) {
    $i = 0;
    foreach ($function as $line) {
        if (stripos($line, '$this->log') !== false)
            Warn('Function is using $this->log (line ' . ($beginFuncLine + $i + 1) . '), should use TriggerError.', 2);
        $i++;
    }
    return false;
}

//---------------------------------------------------------------------------------------------
function CheckBadFof ($function, $beginFuncLine) {
    $i = 0;
    foreach ($function as $line) {
        if (stripos($line, '$this->fof') !== false)
            Suggest('Function is using $this->fof (line ' . ($beginFuncLine + $i + 1) . '), should probably give more informative error.', 2);
        $i++;
    }
    return false;
}

//---------------------------------------------------------------------------------------------
function CheckUseOfEcho ($function, $beginFuncLine) {
    $i = 0;
    foreach ($function as $line) {
        if (stripos($line, 'echo ') !== false)
            Warn('Function is using echo (line ' . ($beginFuncLine + $i + 1) . '), should return value instead.', 2);
        $i++;
    }
    return false;
}

//---------------------------------------------------------------------------------------------
function CheckReturnAfterRedirectAndFof ($function, $beginFuncLine) {
    for ($i = 0; $i < count($function); $i++) {
        $startLine = $i;
        $hasRedirect = stripos($function[$i], 'redirect') !== false;
        $hasFof = stripos($function[$i], '->fof') !== false;
        if ($hasRedirect || $hasFof) {
            while (true) {
                $i++;
                if ($i >= count($function))
                    break;

                $nextLineHasReturn = stripos($function[$i], 'return');
                if ($nextLineHasReturn)
                    break;

                $nextLineHasBrace = stripos($function[$i], '}');
                if (!$nextLineHasBrace) {
                    PrintStr('Function missing return after ' . ($hasRedirect ? 'redirect' : 'fof') . ' on line ' . ($startLine + 1 + $beginFuncLine), 2, 'RED');
                    break;
                }
            }

        }
    }
}

//---------------------------------------------------------------------------------------------
function AnalyzeFunction ($function, $beginFuncLine) {
    preg_match('/function ([^ ]*) /', $function[0], $matches);
    $functionName = $matches[1];
    if (strpos($functionName, '_') === 0)
        return;

    if (strpos($functionName, 'admin_') !== false) {
        //PrintStr("Skipping admin function $functionName...", 1);
        return;
    }

    global $numFunctions;
    $numFunctions++;

    $manipsData = DetectManipData($function);
    $ajax = DetectAjax($function);
    $pageTitle = DetectSetsTitle($function);
    $csrf = DetectCSRF($function);

    $regularPage = !$manipsData && !$ajax;

    $properties = array();
    if ($manipsData)
        $properties[] = 'manipulates data';
    if ($ajax)
        $properties[] = 'is an ajax page';

    $propertyList = count($properties) > 0 ? '(' . implode(', ', $properties) . ')' : '';

    PrintStr("Analyzing function $functionName $propertyList...", 1);

    if ($regularPage && !$pageTitle)
        Warn('Function is missing page title.', 2);
    if ($manipsData && !$ajax && !$pageTitle)
        Suggest('Function may need page title.', 2);
    if ($manipsData && !$ajax && !$csrf)
        Warn('Function needs CSRF check.', 2);

    CheckReturnAfterRedirectAndFof($function, $beginFuncLine);
    CheckDebugChange($function, $beginFuncLine);
    CheckUseOfLog($function, $beginFuncLine);
    CheckUseOfEcho($function, $beginFuncLine);

    if ($manipsData)
        CheckBadFof($function, $beginFuncLine);
}

//---------------------------------------------------------------------------------------------
function Analyze ($file) {
    global $numControllers;
    $numControllers++;

    PrintStr("Analyzing controller " . basename($file) . "...");
    $contents = file_get_contents($file) or die("Could not open file $file.");
    $lines = explode("\n", $contents);

    $beginFuncLine = false;
    for ($i = 0; $i < count($lines); $i++) {
        if (strpos($lines[$i], 'function') !== false) {
            $beginFuncLine = $i;
        }

        if ($lines[$i] === '    }' && $beginFuncLine !== false) {
            $function = array_slice($lines, $beginFuncLine, $i - $beginFuncLine + 1);
            AnalyzeFunction($function, $beginFuncLine);
            $beginFuncLine = false;
        }
    }
}

PrintStr("===== Static Analysis of Controllers! =====\n");

$handle = opendir(ANALYZE_PATH) or die('Could not open dir');

while (false !== ($file = readdir($handle))) {
    if (preg_match('/controller\.php$/', $file) && strpos($file, '#') === false) {
        Analyze(ANALYZE_PATH . '/' . $file);
    }
}

echo "===== Result =====\n";
echo "Analyzed $numControllers controllers, $numFunctions functions.\n";
echo "Found $numWarnings warnings, $numSuggestions suggestions.\n";

?>
