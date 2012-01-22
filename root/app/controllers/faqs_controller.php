<?php

class FaqsController extends AppController {

    var $paginate = array(
        'limit' => 0,
    );

    var $authList = array(
        'index' => AUTH_ALL
    );

    var $pageTitle = 'FAQs';

    //---------------------------------------------------------------------------------------------
    function index () {
        $this->set('faqs', $this->Faq->GetFaqsByCategory(), false);
    }

    //=============================================================================================
    // Admin functions
    //=============================================================================================

    //---------------------------------------------------------------------------------------------
    function admi_index () {
        $this->set('faqs', $this->paginate());
    }

    //---------------------------------------------------------------------------------------------
    function admi_add () {
        if (!empty($this->data)) {
            $this->Faq->create();
            if ($this->Faq->save($this->data)) {
                $this->Session->setFlash('Item type saved.');
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash('Could not save FAQ.');
            }
        }
    }

    //---------------------------------------------------------------------------------------------
    function admi_edit ($id = null) {
        if (!$id && empty($this->data)) {
            $this->Session->setFlash('Invalid FAQ.');
            $this->redirect(array('action' => 'index'));
        }
        if (!empty($this->data)) {
            if ($this->Faq->save($this->data)) {
                $this->Session->setFlash('FAQ saved.');
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash('Could not save FAQ.');
            }
        }
        if (empty($this->data)) {
            $this->data = $this->Faq->read(null, $id);
        }
    }

    //---------------------------------------------------------------------------------------------
    function admi_delete ($id = null) {
        if (!$id) {
            $this->Session->setFlash('Invalid FAQ.');
            $this->redirect(array('action' => 'index'));
        }
        if ($this->Faq->del($id)) {
            $this->Session->setFlash('FAQ deleted.');
            $this->redirect(array('action' => 'index'));
        } else {
            $this->Session->setFlash('Could not delete FAQ.');
            $this->redirect($this->referer());
        }
    }

}
?>