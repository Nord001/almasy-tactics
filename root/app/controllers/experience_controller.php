<?php

class ExperienceController extends AppController {

    var $pageTitle = 'Experience';
    var $name = 'Experience';

    var $paginate = array(
        'limit' => 0,
    );

    //---------------------------------------------------------------------------------------------
    function admi_index () {
        $this->set('experience', $this->paginate());
    }

    //---------------------------------------------------------------------------------------------
    function admi_edit ($id = null) {
        if (!$id && empty($this->data)) {
            $this->Session->setFlash('Invalid experience level.');
            $this->redirect(array('action' => 'index'));
        }
        if (!empty($this->data)) {
            if ($this->Experience->save($this->data)) {
                $this->Session->setFlash('Experience level saved.');
                $this->redirect(array('action' => 'view', $this->Experience->id));
            } else {
                $this->Session->setFlash('Could not save experience level.');
            }
        }
        if (empty($this->data)) {
            $this->data = $this->Experience->read(null, $id);
        }
    }

}
?>