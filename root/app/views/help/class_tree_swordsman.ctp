<?= $html->css('pages/class_tree'); ?>
<?= $javascript->link('pages/class_tree'); ?>

<div class = 'PageDiv'>

    <div class = 'PageTitle'>
        <?= $html->link2('Help', array('controller' => 'help', 'action' => 'index')); ?> |
        <?= $html->link2('Class Tree', array('controller' => 'help', 'action' => 'class_list')); ?> | Swordsman
    </div>

    <div class = 'PageContent' style = 'position: relative'>

        <? require '../views/help/navbar.ctp'; ?>
        <div class = 'HelpPageHeader'>Swordsman Tree</div>

        <div style = 'text-align: center; margin-bottom: 10px;'>
            <div class = 'TreeBar'>
                Swordsman |
                <?= $html->link2('Spellcaster', array('controller' => 'help', 'action' => 'class_tree', 'spellcaster')); ?> |
                <?= $html->link2('Trainee', array('controller' => 'help', 'action' => 'class_tree', 'trainee')); ?>
            </div>

            <div style = 'text-align: center'>
                <a href = '#swordsman'>Swordsman</a> |
                <a href = '#knight'>Knight</a> |
                <a href = '#savage'>Savage</a> |
                <a href = '#fighter'>Fighter</a> |
                <a href = '#thief'>Thief</a>
            </div>

            <? foreach ($weaponTypes as $type): ?>
                <?= $html->image('sprites/' . $type['WeaponType']['sprite'] . '.png', array('title' => Inflector::pluralize($type['WeaponType']['name']), 'style' => 'vertical-align: middle')); ?>
                <?= $type['WeaponType']['name']; ?>
            <? endforeach; ?>
        </div>

        <div class = 'TreeHeader'>
            <a name = 'swordsman' class = 'NamedAnchor'>Swordsman Branch</a>
        </div>
        <? $helpView->RenderSubtree($classTree['swordsman'], 300); ?>
        <div class = 'BackToTop'>
            <? require 'back_to_top.ctp'; ?>
        </div>

        <div class = 'TreeHeader'>
            <a name = 'knight' class = 'NamedAnchor'>Knight Branch</a>
        </div>
        <? $helpView->RenderSubtree($classTree['knight'], 800); ?>
        <div class = 'BackToTop'>
            <? require 'back_to_top.ctp'; ?>
        </div>

        <div class = 'TreeHeader'>
            <a name = 'savage' class = 'NamedAnchor'>Savage Branch</a>
        </div>
        <? $helpView->RenderSubtree($classTree['savage'], 800); ?>
        <div class = 'BackToTop'>
            <? require 'back_to_top.ctp'; ?>
        </div>

        <div class = 'TreeHeader'>
            <a name = 'fighter' class = 'NamedAnchor'>Fighter Branch</a>
        </div>
        <? $helpView->RenderSubtree($classTree['fighter'], 700); ?>
        <div class = 'BackToTop'>
            <? require 'back_to_top.ctp'; ?>
        </div>

        <div class = 'TreeHeader'>
            <a name = 'thief' class = 'NamedAnchor'>Thief Branch</a>
        </div>
        <? $helpView->RenderSubtree($classTree['thief'], 800); ?>
        <div class = 'BackToTop'>
            <? require 'back_to_top.ctp'; ?>
        </div>
    </div>
</div>
