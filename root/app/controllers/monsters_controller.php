<?php

class MonstersController extends AppController {

    var $pageTitle = 'Monsters';

    var $paginate = array(
        'limit' => 0,
        'conditions' => array(
            'monster' => 1,
        ),
    );

    //---------------------------------------------------------------------------------------------
    function admi_index () {
        $monsters = $this->paginate();
        $this->set('monsters', $monsters);
        $monsterNames = Set::classicExtract($monsters, '{n}.Monster.name');
        $this->set('monsterNames', $monsterNames);
    }

    //---------------------------------------------------------------------------------------------
    function admi_view ($id = null) {
        if (!$id) {
            $this->Session->setFlash('Invalid monster.');
            $this->redirect(array('action' => 'index'));
        }

        // Accept names too
        if (is_numeric($id))
            $conditions = array('Monster.id' => $id);
        else
            $conditions = array('Monster.name' => $id);

        $monster = $this->Monster->find('first', array(
            'conditions' => array(
                $conditions,
            ),
            'contain' => array(
                'Bonus' => array(
                    'BonusType',
                ),
            ),
        ));

        if (!$monster) {
            $this->Session->setFlash('Invalid monster.');
            $this->redirect(array('action' => 'index'));
        }

        // Build list of locations that the bonuses affect
        $locations = Set::classicExtract($monster, 'Bonus.{n}.locations');
        $locationSet = array();
        foreach ($locations as $location)
            $locationSet = array_merge($locationSet, $location);

        $monster['Monster']['bonus_locations'] = $locationSet;

        $this->set('monster', $monster, false);
    }

    //---------------------------------------------------------------------------------------------
    function admi_add () {
        if (!empty($this->data)) {
            $this->Monster->create();
            $this->data['Monster']['monster'] = 1;
            if ($this->Monster->save($this->data)) {
                $this->Session->setFlash('Monster saved.');
                $this->redirect(array('action' => 'view', $this->Monster->id));
            } else {
                $this->Session->setFlash('Could not save monster.');
            }
        }
        $monsters = $this->Monster->find('list');
        $this->set('monsters', $monsters);
    }

    //---------------------------------------------------------------------------------------------
    function admi_edit ($id = null) {
        if (!$id && empty($this->data)) {
            $this->Session->setFlash('Invalid monster.');
            $this->redirect(array('action' => 'index'));
        }
        if (!empty($this->data)) {
            $this->data['Monster']['monster'] = 1;
            if ($this->Monster->save($this->data)) {
                $this->Session->setFlash('Monster saved.');
                $this->redirect(array('action' => 'view', $this->Monster->id));
            } else {
                $this->Session->setFlash('Could not save monster.');
            }
        }
        if (empty($this->data)) {
            $this->data = $this->Monster->find('first', array(
                'conditions' => array(
                    'id' => $id,
                ),
            ));
        }
    }

    //---------------------------------------------------------------------------------------------
    function admi_delete ($id = null) {
        if (!$id) {
            $this->Session->setFlash('Invalid ID for monster.');
            $this->redirect(array('action' => 'index'));
        }
        if ($this->Monster->del($id)) {
            $this->Session->setFlash('Monster deleted.');
            $this->redirect(array('action' => 'index'));
        } else {
            $this->Session->setFlash('Could not delete monster.');
            $this->redirect($this->referer());
        }
    }
}
?>
