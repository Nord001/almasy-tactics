<?

App::import('Controller', '{{shortControllerName}}');

// Insert model import code here
App::import('Model', '{{modelName}}');
Mock::generate('{{modelName}}');

class {{controllerName}}Test extends ControllerUnitTestCase {

    var $c;

    //---------------------------------------------------------------------------------------------
    function setUp () {
        $this->c = new {{controllerName}};
	$this->c->constructClasses();
	$this->c->Component->initialize($c);
	$this->c->GameAuth = $this->SetupMockAuth();
	$this->c->stopFof = true;

	$this->c->{{modelName}} = new Mock{{modelName}};
    }

    //---------------------------------------------------------------------------------------------
    function tearDown () {
        unset($this->c);
    }
{{testCases}}
}

?>