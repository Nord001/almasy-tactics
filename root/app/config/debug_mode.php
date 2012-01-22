<?
    // Debug 0 => Production. Debug 2 => Debug Mode.
    Configure::write('debug', 2);

    // Test 0 => Non-test. Test 1 => Test.
    // In test mode, forum accounts are ignored.
    Configure::write('test', 1);
?>
