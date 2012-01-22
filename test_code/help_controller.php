<?php

class HelpController extends AppController {

    var $uses = array('CClass');

    var $helpers = array('HelpView');

    var $authList = array(
        'index' => AUTH_ALL,
        'class_list' => AUTH_ALL,
        'view_class' => AUTH_ALL
    );

    var $dir = '../views/help/';

    //---------------------------------------------------------------------------------------------
    function index ($page = null) {
        if ($page != null)
            $this->render($page);
    }

    //---------------------------------------------------------------------------------------------
    function class_list () {
        //$classTree = $this->CClass->GetClassTree();
        //$this->set('classTree', $classTree);

        $classListing = $this->CClass->GetClassListing();
        $this->set('classListing', $classListing);
    }

    //---------------------------------------------------------------------------------------------
    function view_class ($str = null) {
        if (is_numeric($str)) {
            $class = $this->CClass->GetClass($str);
        } else {
            $class = $this->CClass->GetClassByName($str);
        }

        if ($class === false) {
            $this->Session->setFlash('That class does not exist.');
            $this->redirect($this->referer());
        }

        $this->set('class', $class);

        $promotionClasses = $this->CClass->GetPromotionClasses($class['CClass']['id']);
        $this->set('promotionClasses', $promotionClasses);
    }


    //---------------------------------------------------------------------------------------------
    function admin_index () {
        if (!HELP_EDITING) {
            $this->Session->setFlash('This is disabled.');
            $this->redirect($this->referer());
        }

        if ($handle = opendir($this->dir)) {

            $files = array();

            while (false !== ($file = readdir($handle))) {
                if (strpos($file, '.ctp') !== false && strpos($file, 'admin') === false)
                    $files[] = $file;
            }

            natcasesort($files);

            $this->set('files', $files);

            return;
        }
        $this->fof();
    }

    //---------------------------------------------------------------------------------------------
    function admin_add () {
        if (!HELP_EDITING) {
            $this->Session->setFlash('This is disabled.');
            $this->redirect($this->referer());
        }
        if (!empty($this->data)) {
            $path = $this->dir . $this->data['file'];
            if (file_exists($path)) {
                $this->Session->setFlash('That file exists!');
                return;
            }

            file_put_contents($path, 'This is a new file.');
            $this->Session->setFlash('File saved.');
            $this->redirect(array('controller' => 'help', 'action' => 'edit', $this->data['file']));
        }
    }

    //---------------------------------------------------------------------------------------------
    function admin_edit ($file = null) {
        if (!HELP_EDITING) {
            $this->Session->setFlash('This is disabled.');
            $this->redirect($this->referer());
        }
        if (!empty($this->data)) {
            $path = $this->dir . $this->data['file'];
            file_put_contents($path, $this->data['data']);
            $this->Session->setFlash('File saved.');
            $this->redirect(array('controller' => 'help', 'action' => 'index'));
        } else if ($file != null) {
            $contents = file_get_contents($this->dir . $file);
            $this->set('file', $file);
            $this->set('contents', $contents);
        } else {
            $this->fof();
        }
    }
}
?>