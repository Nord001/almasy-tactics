<?php

define('SIMPLETEST_DIR', '../vendors/simpletest/');
define('TEST_DIR', dirname(__FILE__));
define('CONTROLLER_TEST_DIR', dirname(__FILE__) . '/controllers/');

define('SOME_NUM', 4082);

require_once 'almasy_reporter.php';
require_once SIMPLETEST_DIR . 'simpletest.php';
require_once SIMPLETEST_DIR . 'autorun.php';

require_once 'controller_unit_test_case.php';

App::import('Component', 'GameAuth');
Mock::generate('GameAuthComponent');

SimpleTest::prefer(new AlmasyReporter());

class AlmasyTests extends TestSuite {

    //---------------------------------------------------------------------------------------------
    function __construct () {
        parent::__construct('Almasy Tests');
        $this->addControllerTests();
    }

    //---------------------------------------------------------------------------------------------
    function addControllerTests () {
        if ($handle = opendir(CONTROLLER_TEST_DIR)) {

            $controllerTests = array();

            while (false !== ($test = readdir($handle))) {
                if (strpos($test, '_test.php') !== false) {
                    $this->addFile(CONTROLLER_TEST_DIR . $test);
                }
            }
        }
    }
}

?>
