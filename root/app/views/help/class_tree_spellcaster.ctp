<?= $html->css('pages/class_tree'); ?>
<?= $javascript->link('pages/class_tree'); ?>

<div class = 'PageDiv'>

    <div class = 'PageTitle'>
        <?= $html->link2('Help', array('controller' => 'help', 'action' => 'index')); ?> |
        <?= $html->link2('Class Tree', array('controller' => 'help', 'action' => 'class_list')); ?> | Spellcaster
    </div>

    <div class = 'PageContent' style = 'position: relative'>
        <? require '../views/help/navbar.ctp'; ?>

        <div class = 'HelpPageHeader'>Spellcaster Tree</div>

        <div style = 'text-align: center; margin-bottom: 10px;'>

            <div class = 'TreeBar'>
                <?= $html->link2('Swordsman', array('controller' => 'help', 'action' => 'class_tree', 'swordsman')); ?> |
                Spellcaster |
                <?= $html->link2('Trainee', array('controller' => 'help', 'action' => 'class_tree', 'trainee')); ?>
            </div>

            <div style = 'text-align: center'>
                <a href = '#spellcaster'>Spellcaster</a> |
                <a href = '#mage'>Mage</a> |
                <a href = '#neophyte'>Neophyte</a> |
                <a href = '#shadowmage'>Shadowmage</a>
            </div>

            <? foreach ($weaponTypes as $type): ?>
                <?= $html->image('sprites/' . $type['WeaponType']['sprite'] . '.png', array('title' => Inflector::pluralize($type['WeaponType']['name']), 'style' => 'vertical-align: middle')); ?>
                <?= $type['WeaponType']['name']; ?>
            <? endforeach; ?>
        </div>

        <div class = 'TreeHeader'>
            <a name = 'spellcaster' class = 'NamedAnchor'>Spellcaster Branch</a>
        </div>
        <? $helpView->RenderSubtree($classTree['spellcaster'], 300); ?>
        <div class = 'BackToTop'>
            <? require 'back_to_top.ctp'; ?>
        </div>

        <div class = 'TreeHeader'>
            <a name = 'mage' class = 'NamedAnchor'>Mage Branch</a>
        </div>
        <? $helpView->RenderSubtree($classTree['mage'], 700); ?>
        <div class = 'BackToTop'>
            <? require 'back_to_top.ctp'; ?>
        </div>

        <div class = 'TreeHeader'>
            <a name = 'neophyte' class = 'NamedAnchor'>Neophyte Branch</a>
        </div>
        <? $helpView->RenderSubtree($classTree['neophyte'], 900); ?>
        <div class = 'BackToTop'>
            <? require 'back_to_top.ctp'; ?>
        </div>

        <div class = 'TreeHeader'>
            <a name = 'shadowmage' class = 'NamedAnchor'>Shadowmage Branch</a>
        </div>
        <? $helpView->RenderSubtree($classTree['shadowmage'], 800); ?>
        <div class = 'BackToTop'>
            <? require 'back_to_top.ctp'; ?>
        </div>
    </div>
</div>
