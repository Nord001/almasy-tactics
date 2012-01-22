<?

require_once '../vendors/simpletest/reporter.php';

class AlmasyReporter extends HtmlReporter {

    var $passNum = 1;
    var $failNum = 1;

    //---------------------------------------------------------------------------------------------
    function paintHeader ($unused) {
        $this->sendNoCacheHeaders();
        print "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\" \"http://www.w3.org/TR/html4/loose.dtd\">";
        print "<html>\n<head>\n<title>Almasy Unit Testing</title>\n";
        print "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=" .
                $this->_character_set . "\">\n";
        print "<style type=\"text/css\">\n";
        print $this->_getCss() . "\n";
        print "</style>\n";
        print "</head>\n<body>\n";
        print "<div class = 'Header'>Almasy Unit Testing</div>\n";
        flush();
    }
    //---------------------------------------------------------------------------------------------
    function paintPass ($message) {
        print "<span class=\"pass\">$this->passNum. PASS</span>: ";
        $this->passNum++;
        $testChain = $this->getTestList();

        foreach ($testChain as $k => $v)
            if (strpos($testChain[$k], '.php') !== false)
                unset($testChain[$k]);

        print implode(' -&gt; ', $testChain);
        print "<br />\n";
    }

    //---------------------------------------------------------------------------------------------
    function paintFail($message) {
        print "<span class=\"fail\">$this->failNum. FAIL</span>: ";
        $this->failNum++;
        $breadcrumb = $this->getTestList();
        array_shift($breadcrumb);
        print implode(" -&gt; ", $breadcrumb);
        print " -&gt; " . $this->_htmlEntities($message) . "<br />\n";
    }

    //---------------------------------------------------------------------------------------------
    function _getCss() {
        return file_get_contents(dirname(__FILE__) . '/tests2.css');
    }
}

?>