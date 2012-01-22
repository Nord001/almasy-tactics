<?php

class ItemCatalogEntriesController extends AppController {


    var $paginate = array(
        'limit' => 0,
        'contain' => array(
            'UserItem' => array(
                'fields' => array(
                    'UserItem.refine',
                    'UserItem.name',
                ),
                'Item' => array(
                    'fields' => array(
                        'Item.id',
                        'Item.name',
                    ),
                ),
            ),
        ),
    );

    //---------------------------------------------------------------------------------------------
    function admi_index () {
        $this->set('itemCatalogEntries', $this->paginate());
    }

    //---------------------------------------------------------------------------------------------
    function admi_view ($id = null) {
        if (!$id) {
            $this->Session->setFlash('Invalid item catalog entry.');
            $this->redirect(array('action' => 'index'));
        }
        $itemCatalogEntry = $this->ItemCatalogEntry->find('first', array(
            'conditions' => array(
                'ItemCatalogEntry.id' => $id,
            ),
            'contain' => array(
                'UserItem' => array(
                    'Item',
                ),
            ),
        ));
        $this->set('itemCatalogEntry', $itemCatalogEntry);
    }

    //---------------------------------------------------------------------------------------------
    function admi_add () {
        if (!empty($this->data)) {
            $this->ItemCatalogEntry->create();
            if ($this->ItemCatalogEntry->save($this->data)) {
                $this->Session->setFlash('Item catalog entry saved.');
                $this->redirect(array('action' => 'view', $this->ItemCatalogEntry->id));
            } else {
                $this->Session->setFlash('Could not save item catalog entry.');
            }
        }

        // Find items from Iggly
        $userItemIds = $this->ItemCatalogEntry->GetUnsoldUserItemIds();
        $items = $this->ItemCatalogEntry->UserItem->find('all', array(
            'conditions' => array(
                'UserItem.id' => $userItemIds,
            ),
            'contain' => array(
                'Item' => array(
                    'fields' => array(
                        'Item.name',
                    ),
                ),
            )
        ));
        $items = Set::combine($items, '{n}.UserItem.id', '{n}.Item.name');
        $this->set('userItems', $items);
    }

    //---------------------------------------------------------------------------------------------
    function admi_edit ($id = null) {
        if (!$id && empty($this->data)) {
            $this->Session->setFlash('Invalid item catalog entry.');
            $this->redirect(array('action' => 'index'));
        }
        if (!empty($this->data)) {
            if ($this->ItemCatalogEntry->save($this->data)) {
                $this->Session->setFlash('Item catalog entry saved.');
                $this->redirect(array('action' => 'view', $this->ItemCatalogEntry->id));
            } else {
                $this->Session->setFlash('Could not save item catalog entry.');
            }
        }
        if (empty($this->data)) {
            $this->data = $this->ItemCatalogEntry->read(null, $id);
        }
    }

    //---------------------------------------------------------------------------------------------
    function admi_delete ($id = null) {
        if (!$id) {
            $this->Session->setFlash('Invalid ID for item catalog entry.');
            $this->redirect(array('action' => 'index'));
        }
        if ($this->ItemCatalogEntry->del($id)) {
            $this->Session->setFlash('Item catalog entry deleted.');
            $this->redirect(array('action' => 'index'));
        } else {
            $this->Session->setFlash('Could not delete item catalog entry.');
            $this->redirect($this->referer());
        }
    }

}
?>