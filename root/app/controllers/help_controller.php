<?php

class HelpController extends AppController {

    var $uses = array('CClass');
    //var $uses = array('CClass', 'HelpComment');

    var $helpers = array('HelpView');

    var $authList = array(
        'index' => AUTH_ALL,
        'class_list' => AUTH_ALL,
        'class_tree' => AUTH_ALL,
        'view_class' => AUTH_ALL
    );

    var $dir = '../views/help/';

    //---------------------------------------------------------------------------------------------
    function beforeFilter () {
        parent::beforeFilter();

        if (strpos($this->action, Configure::read('Routing.admin')) !== false)
            return;

        /*
        $comments = $this->HelpComment->GetCommentsByHelpPage($_SERVER['REQUEST_URI'], 'date');
        for ($i = 0; $i < 11; $i++)
            $comments[]  = array('HelpComment' => array('user_id' => 2, 'User' => array('username' => 'f'), 'time' => '2008-03-04 12:34:' . $i, 'comment' => 'f' . $i, 'rating' => $i - 5));
        $this->set('comments', $comments);
        */
    }

    //---------------------------------------------------------------------------------------------
    function index ($page = null) {
        if ($page != null)
            $this->render($page);
    }

    //---------------------------------------------------------------------------------------------
    function class_list () {
        $classListing = $this->CClass->GetClassListing();
        $this->set('classListing', $classListing);
    }

    //---------------------------------------------------------------------------------------------
    function class_tree ($branch = null) {
        if ($branch == null) {
            $this->fof();
            return;
        }

        if (!in_array($branch, array('swordsman', 'spellcaster', 'trainee'), true)) {
            $this->fof();
            return;
        }

        $file = $branch . '_tree';

        $classTree = file_get_contents('../webroot/files/' . $file . '.js');
        $classTree = preg_replace('/\/\/.*\n/', '', $classTree);

        $classTree = json_decode($classTree, true);
        foreach ($classTree as &$subTree) {
            $ids = Set::classicExtract($subTree, '{n}.id');
            $indexesByIds = array_flip($ids);


            foreach ($subTree as &$class) {
                $classData = $this->CClass->GetClass($class['id']);
                $class['CClass'] = $classData['CClass'];
                foreach ($class['promotions'] as &$promotionClass) {
                    $index = $indexesByIds[$promotionClass];
                    $promotionClass = array('index' => $index);
                }
            }
        }

        $this->set('classTree', $classTree, false);

        $weaponTypes = $this->CClass->WeaponType->GetWeaponTypes();
        $this->set('weaponTypes', $weaponTypes);

        $file = 'class_tree_' . $branch;        $this->render($file);
    }

    //---------------------------------------------------------------------------------------------
    function view_class ($str = null) {
        if (is_numeric($str)) {
            $class = $this->CClass->GetClass($str);
        } else {
            $class = $this->CClass->GetClassByName($str);
        }

        if ($class === false) {
            $this->fof();
            return;
        }

        $this->set('class', $class);

        $promotionClasses = $this->CClass->GetPromotionClasses($class['CClass']['id']);
        $this->set('promotionClasses', $promotionClasses);
    }


    //---------------------------------------------------------------------------------------------
    function admi_index () {
        if (!HELP_EDITING) {
            $this->fof();
        }

        if ($handle = opendir($this->dir)) {

            $files = array();

            while (false !== ($file = readdir($handle))) {
                if (strpos($file, '.ctp') !== false && strpos($file, Configure::read('Routing.admin')) === false)
                    $files[] = $file;
            }

            natcasesort($files);

            $this->set('files', $files);

            return;
        }
        $this->fof();
    }

    //---------------------------------------------------------------------------------------------
    function admi_add () {
        if (!HELP_EDITING) {
            $this->fof();
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
    function admi_edit ($file = null) {
        if (!HELP_EDITING) {
            $this->fof();
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
