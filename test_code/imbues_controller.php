<?php
class ImbuesController extends AppController {

    var $name = 'Imbues';
    var $layout = 'admin';

    var $paginate = array(
        'limit' => 0,
    );

    //---------------------------------------------------------------------------------------------
    function admin_index () {
        $this->set('imbues', $this->paginate());
    }

    //---------------------------------------------------------------------------------------------
    function admin_view ($id = null) {
        if (!$id) {
            $this->Session->setFlash('Invalid imbue.');
            $this->redirect(array('action' => 'index'));
        }

        $imbue = $this->Imbue->find('first', array(
            'conditions' => array(
                'Imbue.id' => $id,
            ),
            'contain' => array(
                'ImbueMod' => array(
                    'BonusType',
                ),
            ),
        ));
        $this->set('imbue', $imbue);
    }

    //---------------------------------------------------------------------------------------------
    function admin_add () {
        if (!empty($this->data)) {
            $this->Imbue->create();
            if ($this->Imbue->save($this->data)) {
                $this->Session->setFlash('Imbue saved.');
                $this->redirect(array('action' => 'view', $this->Imbue->id));
            } else {
                $this->Session->setFlash('Could not save imbue.');
            }
        }

        $itemTypes = $this->Imbue->getEnumValues('item_type');
        $this->set('itemTypes', $itemTypes);
    }

    //---------------------------------------------------------------------------------------------
    function admin_edit ($id = null) {
        if (!$id && empty($this->data)) {
            $this->Session->setFlash('Invalid imbue.');
            $this->redirect(array('action' => 'index'));
        }
        if (!empty($this->data)) {
            if ($this->Imbue->save($this->data)) {
                $this->Session->setFlash('Imbue saved.');
                $this->redirect(array('action' => 'view', $this->Imbue->id));
            } else {
                $this->Session->setFlash('Could not save imbue.');
            }
        }
        if (empty($this->data)) {
            $this->data = $this->Imbue->read(null, $id);
        }

        $itemTypes = $this->Imbue->getEnumValues('item_type');
        $this->set('itemTypes', $itemTypes);
    }

    //---------------------------------------------------------------------------------------------
    function admin_delete ($id = null) {
        if (!$id) {
            $this->Session->setFlash('Invalid ID for imbue.');
            $this->redirect(array('action' => 'index'));
        }
        if ($this->Imbue->del($id)) {
            $this->Session->setFlash('Imbue deleted.');
            $this->redirect(array('action' => 'index'));
        }
    }

}
?>