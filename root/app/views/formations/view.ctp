<?= $html->css('pages/formation_view'); ?>

<div class = 'PageDiv'>

    <div class = 'PageTitle'>
        <?= $html->link2('Formations', array('controller' => 'formations', 'action' => 'index')); ?> |
        <?= $formation['Formation']['name']; ?>
    </div>

    <div class = 'PageContent' id = 'FormationViewPage'>
        <?= $this->element('ajax_formations_view_page'); ?>
    </div>
</div>
