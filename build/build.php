<?

define('DEBUG', 1);
define('TEST', 2);
define('PROD', 3);

define('BASE_DIR', '../root/');

$mode = 0;

error_reporting(E_ALL);

function PrintStr ($str) {
    echo $str . "\n";
}

$build = array(
    array(
        'path' => '.',
        'filter' => '/(index.php|.htaccess|VERSION)/',
        'recursive' => false,
    ),
    array(
        'path' => 'cake',
        'recursive' => true,
    ),
    array(
        'path' => 'app',
        'recursive' => false,
        'filter' => '/.*(.php|htaccess)$/',
        'special_files' => array(
            'prod' => array(
                'server_constants.php',
            ),
        ),
    ),
    array(
        'path' => 'app/config',
        'recursive' => true,
        'special_files' => array(
            'test' => array(
                'debug_mode.php',
            ),
            'prod' => array(
                'debug_mode.php',
                'database.php',
            ),
        ),
    ),
    array(
        'path' => 'app/controllers',
        'recursive' => true,
    ),
    array(
        'path' => 'app/models',
        'recursive' => true,
    ),
    array(
        'path' => 'app/lib',
        'recursive' => true,
        'filter' => '/.*.(ctp|php)$/',
    ),
    array(
        'path' => 'app/views',
        'recursive' => true,
        'filter' => '/.*.(ctp|php)$/',
    ),
    array(
        'path' => 'app/webroot',
        'recursive' => true,
    ),
    array(
        'path' => 'app/vendors',
        'recursive' => true,
    ),
    'app/tmp',
    'app/tmp/cache',
    'app/tmp/logs',
    'app/tmp/cache/models',
    'app/tmp/cache/persistent',
);

function RecursiveAdd (&$zip, $path, $specialFiles, $filter, $recurse) {
    global $mode;
    $prodFiles = isset($specialFiles['prod']) ? $specialFiles['prod'] : array();
    $testFiles = isset($specialFiles['test']) ? $specialFiles['test'] : array();

    if ($handle = opendir($path)) {
        while (($file = readdir($handle)) !== false) {
            if ($file == '.' || $file == '..')
                continue;

            if ($path != '.')
                $fullPath = $path . '/'. $file;
            else
                $fullPath = $file;

            if (is_dir($fullPath) && $recurse) {
                $dirName = basename($fullPath);
                if (strpos($dirName, '.') !== 0)
                    RecursiveAdd($zip, $fullPath, $specialFiles, $filter, true);
            } else if (is_file($fullPath)) {
                if ($filter === false || preg_match($filter, $file) > 0) {
                    $isProdFile = stripos($file, 'prod_') !== false;
                    $isProdReplacedFile = in_array($file, $prodFiles);
                    $isTestFile = stripos($file, 'test_') !== false;
                    $isTestReplacedFile = in_array($file, $testFiles);

                    // Cut off base dir for zip path
                    $zipPath = substr($fullPath, strlen(BASE_DIR));
                    if (strpos($zipPath, './') === 0) {
                        $zipPath = substr($zipPath, 2);
                    }

                    switch ($mode) {
                        case DEBUG:
                            // Never add prod or test files
                            if (!$isProdFile && !$isTestFile) {
                                PrintStr('Adding ' . $zipPath . '...');
                                $zip->addFile($fullPath, $zipPath);
                            }
                        break;
                        case TEST:
                            if (!$isProdFile) { // Never add production files
                                if (!$isTestReplacedFile) { // Don't add a file that will be replaced
                                    $trueName = str_replace('test_', '', $file);
                                    $truePath = dirname($zipPath) . '/' . $trueName;

                                    // Make sure renamed really was meant to replace something
                                    if ($isTestFile && in_array($trueName, $testFiles)) {
                                        PrintStr('Adding ' . $fullPath . ' as ' . $truePath . '...');
                                        $zip->addFile($fullPath, $truePath);
                                    } else {
                                        // Add normally, regular file
                                        PrintStr('Adding ' . $zipPath . '...');
                                        $zip->addFile($fullPath, $zipPath);
                                    }
                                }
                            }
                        break;
                        case PROD:
                            if (!$isTestFile) { // Never add test files
                                if (!$isProdReplacedFile) { // Don't add a file that will be replaced
                                    $trueName = str_replace('prod_', '', $file);
                                    $truePath = dirname($zipPath) . '/' . $trueName;

                                    // Make sure renamed really was meant to replace something
                                    if ($isProdFile && in_array($trueName, $prodFiles)) {
                                        PrintStr('Adding ' . $fullPath . ' as ' . $truePath . '...');
                                        $zip->addFile($fullPath, $truePath);
                                    } else {
                                        // Add normally, regular file
                                        PrintStr('Adding ' . $fullPath . '...');
                                        $zip->addFile($fullPath, $zipPath);
                                    }
                                }
                            }
                        break;
                    }
                }
            }
        }
        closedir($handle);
    } else {
        PrintStr('Could not open directory ' . $path);
    }
}

function AddFilesInDir (&$zip, $path) {
    RecursiveAdd($zip, $path, false);
}

if (!extension_loaded('zip')) {
    PrintStr('Zip module must be installed.');
    return;
}

PrintStr('Almasy Build Script');

if (count($argv) == 1) {
    while (true) {
        echo 'Mode? 1) Debug 2) Test 3) Prod: ';
        $mode = trim(fgets(STDIN));
        if (!is_numeric($mode) || $mode <= 0 || $mode >= 4)
            PrintStr('Enter a valid mode.');
        else
            break;
    }
} else {
    switch ($argv[1]) {
        case 'd': $mode = DEBUG; break;
        case 't': $mode = TEST; break;
        case 'p': $mode = PROD; break;
        default: $mode = PROD; break;
    }
}

$suffix = '';
switch ($mode) {
    case DEBUG: $suffix = '_d'; break;
    case TEST: $suffix = '_t'; break;
    case PROD: $suffix = '_p'; break;
}

// Get revision number
$exec = "C:\cygwin\bin\svnversion " . BASE_DIR;
$revNumber = `$exec`;

// Strip beginning stuff up to colon.
$colonPos = strpos($revNumber, ':');
if ($colonPos !== false) {
    $revNumber = substr($revNumber, $colonPos + 1);
}
$revNumber = intval($revNumber);

file_put_contents(BASE_DIR . 'VERSION', $revNumber);

// Build zip
$zip = new ZipArchive();
$filename = 'build_' . date('Y-m-d') . '_b' . $revNumber . $suffix . '.zip';
@unlink($filename);
if ($zip->open($filename, ZIPARCHIVE::CREATE) != true) {
    PrintStr('Could not open zip file.');
    return;
}

PrintStr('Building archive ' . $filename . '...');

foreach ($build as $buildRule) {
    if (!is_array($buildRule)) {
        // Just naming directory structure
        $zip->addEmptyDir($buildRule);
        PrintStr('Constructing ' . $buildRule . '...');
    } else {
        // Read build rule
        if (!isset($buildRule['path'])) {
            PrintStr('Error in build rule: path missing.');
            break;
        }

        if (!isset($buildRule['recursive'])) {
            PrintStr('Error in build rule: recursive field missing.');
            break;
        }

        $specialFiles = isset($buildRule['special_files']) ? $buildRule['special_files'] : array();
        $filter = isset($buildRule['filter']) ? $buildRule['filter'] : false;
        RecursiveAdd($zip, BASE_DIR . $buildRule['path'], $specialFiles, $filter, $buildRule['recursive']);
    }
}

PrintStr('Files: ' . $zip->numFiles);


PrintStr($zip->close() ? 'Wrote zip successfully!' : 'Failed to write zip..');

?>