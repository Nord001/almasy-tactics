<?php

class SpritesController extends AppController {

    var $uses = array('User', 'User'); // One user makes it complain about sprite..

    var $layout = 'admin';

    // Directory for sprites
    var $imgDir = 'sprites/';
    var $dir = '../webroot/img/sprites/';

    //---------------------------------------------------------------------------------------------
    function admi_index () {
        if ($handle = opendir($this->dir)) {

            $sprites = array();

            while (false !== ($sprite = readdir($handle))) {
                if (strpos($sprite, '.png') !== false)
                    $sprites[] = $sprite;
            }

            natcasesort($sprites);

            $this->set('sprites', $sprites);
            $this->set('imgDir', $this->imgDir);

            return;
        }
        $this->fof();
    }

    //---------------------------------------------------------------------------------------------
    function admi_add () {
        if (!empty($this->data)) {
            $tmpName = $this->data['File']['file']['tmp_name'];
            if (is_uploaded_file($tmpName)) {
                move_uploaded_file($tmpName, $this->dir . $this->data['File']['file']['name']);
                $this->Session->setFlash('Uploaded!');
            } else {
                $this->Session->setFlash('Failed to upload file.');
            }
        }
    }

    //---------------------------------------------------------------------------------------------
    function admi_delete ($filename = null) {
        if (!$filename) {
            $this->Session->setFlash('Invalid filename.');
            $this->redirect(array('action' => 'index'));
        }
        if (unlink($this->dir . $filename)) {
            $this->Session->setFlash('Sprite deleted.');
            $this->redirect(array('action' => 'index'));
        } else {
            $this->Session->setFlash('Could not delete sprite.');
            $this->redirect($this->referer());
        }
    }
}
?>