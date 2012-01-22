<?php

class NewsController extends AppController {

    var $paginate = array(
        'limit' => 0,
        'order' => 'News.id DESC',
        'contain' => array(
            'User',
        ),
    );

    //---------------------------------------------------------------------------------------------
    function admin_index () {
        $this->set('news', $this->paginate());
    }

    //---------------------------------------------------------------------------------------------
    function admin_add () {
        if (!empty($this->data)) {
            $this->News->create();
            $this->data['News']['user_id'] = $this->GameAuth->GetLoggedInUserId();
            $this->data['News']['date_posted'] = date("Y-m-d H:i:s");
            if ($this->News->save($this->data)) {
                $this->Session->setFlash('News saved.');
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash('Could not save news.');
            }
        }
    }

    //---------------------------------------------------------------------------------------------
    function admin_edit ($id = null) {
        if (!$id && empty($this->data)) {
            $this->Session->setFlash('Invalid post.');
            $this->redirect(array('action' => 'index'));
        }
        if (!empty($this->data)) {
            if ($this->News->save($this->data)) {
                $this->Session->setFlash('Post saved.');
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash('Could not save post.');
            }
        }
        if (empty($this->data)) {
            $this->data = $this->News->read(null, $id);
        }
    }

    //---------------------------------------------------------------------------------------------
    function admin_delete ($id = null) {
        if (!$id) {
            $this->Session->setFlash('Invalid ID for post.');
            $this->redirect(array('action' => 'index'));
        }
        if ($this->News->del($id)) {
            $this->Session->setFlash('Post deleted.');
            $this->redirect(array('action' => 'index'));
        } else {
            $this->Session->setFlash('Could not delete post.');
            $this->redirect($this->referer());
        }
    }

}
?>