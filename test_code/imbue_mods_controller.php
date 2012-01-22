<?php

class ImbueModsController extends AppController {


    var $paginate = array(
        'conditions' => array(
            'ImbueMod.imbue_id IS NULL',
        ),
        'contain' => array(
            'BonusType',
        ),
        'limit' => 0,
    );

    //---------------------------------------------------------------------------------------------
    function admin_index () {
        $imbueMods = $this->paginate();

        $this->set('imbueMods', $imbueMods);
    }

    //---------------------------------------------------------------------------------------------
    function admin_add ($imbueId = null) {
        if (!empty($this->data)) {
            $this->ImbueMod->create();

            // Clear item type if it's associated to an imbue
            if (isset($this->data['ImbueMod']['imbue_id']))
                $this->data['ImbueMod']['item_type'] = '';

            if ($this->ImbueMod->save($this->data)) {
                $this->Session->setFlash('Imbue mod saved.');

                // If there is an associated imbue id, go to the imbue, else go to the imbue mod list
                if (isset($this->data['ImbueMod']['imbue_id']))
                    $this->redirect(array('controller' => 'imbues', 'action' => 'view', $this->data['ImbueMod']['imbue_id']));
                else
                    $this->redirect(array('controller' => 'imbue_mods', 'action' => 'index'));
            } else {
                $this->Session->setFlash('Could not save imbue mod.');
            }
        }

        if ($imbueId != null)
            $this->set('imbue_id', $imbueId);

        $itemTypes = $this->ImbueMod->getEnumValues('item_type');
        $this->set('itemTypes', $itemTypes);
        $bonusTypes = $this->ImbueMod->BonusType->find('list');
        $this->set(compact('itemTypes', 'bonusTypes'));
    }

    //---------------------------------------------------------------------------------------------
    function admin_edit ($id = null) {
        if (!$id && empty($this->data)) {
            $this->Session->setFlash('Invalid imbue mod.');
            $this->redirect($this->referer());
        }
        if (!empty($this->data)) {
            if ($this->ImbueMod->save($this->data)) {
                $this->Session->setFlash('Imbue mod saved.');
                $this->redirect(array('controller' => 'imbues', 'action' => 'view', $this->data['ImbueMod']['imbue_id']));
            } else {
                $this->Session->setFlash('Could not save imbue mod.');
            }
        }
        if (empty($this->data)) {
            $this->data = $this->ImbueMod->read(null, $id);
        }

        $itemTypes = $this->ImbueMod->getEnumValues('item_type');
        $this->set('itemTypes', $itemTypes);
        $bonusTypes = $this->ImbueMod->BonusType->find('list');
        $this->set(compact('itemTypes', 'bonusTypes'));
    }

    //---------------------------------------------------------------------------------------------
    function admin_delete ($id = null) {
        if (!$id) {
            $this->Session->setFlash('Invalid ID for imbue mod.');
            $this->redirect($this->referer());
        }
        if ($this->ImbueMod->del($id)) {
            $this->Session->setFlash('Imbue mod deleted.');
            $this->redirect($this->referer());
        } else {
            $this->Session->setFlash('Could not delete imbue mod.');
            $this->redirect($this->referer());
        }
    }

}
?>