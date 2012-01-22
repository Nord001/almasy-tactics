<?= $html->css('pages/class_tree'); ?>
<?= $javascript->link('pages/class_tree'); ?>

<div class = 'PageDiv'>

    <div class = 'PageTitle'>
        <?= $html->link2('Help', array('controller' => 'help', 'action' => 'index')); ?> |
        <?= $html->link2('Class Tree', array('controller' => 'help', 'action' => 'class_list')); ?> | Trainee
    </div>

    <div class = 'PageContent' style = 'position: relative'>
        <? require '../views/help/navbar.ctp'; ?>
        <div class = 'HelpPageHeader'>Trainee Tree</div>

        <div style = 'text-align: center; margin-bottom: 10px;'>
            <div class = 'TreeBar'>
                <?= $html->link2('Swordsman', array('controller' => 'help', 'action' => 'class_tree', 'swordsman')); ?> |
                <?= $html->link2('Spellcaster', array('controller' => 'help', 'action' => 'class_tree', 'spellcaster')); ?> |
                Trainee
            </div>

            <div style = 'text-align: center'>
                <a href = '#trainee'>Trainee</a> |
                <a href = '#archer'>Archer</a> |
                <a href = '#artificer'>Artificer</a> |
                <a href = '#pupil'>Pupil</a>
            </div>

            <? foreach ($weaponTypes as $type): ?>
                <?= $html->image('sprites/' . $type['WeaponType']['sprite'] . '.png', array('title' => Inflector::pluralize($type['WeaponType']['name']), 'style' => 'vertical-align: middle')); ?>
                <?= $type['WeaponType']['name']; ?>
            <? endforeach; ?>
        </div>

        <div class = 'TreeHeader'>
            <a name = 'trainee' class = 'NamedAnchor'>Trainee Branch</a>
        </div>
        <? $helpView->RenderSubtree($classTree['trainee'], 300); ?>
        <div class = 'BackToTop'>
            <? require 'back_to_top.ctp'; ?>
        </div>

        <div class = 'TreeHeader'>
            <a name = 'archer' class = 'NamedAnchor'>Archer Branch</a>
        </div>
        <? $helpView->RenderSubtree($classTree['archer'], 500); ?>
        <div class = 'BackToTop'>
            <? require 'back_to_top.ctp'; ?>
        </div>

        <div class = 'TreeHeader'>
            <a name = 'artificer' class = 'NamedAnchor'>Artificer Branch</a>
        </div>
        <? $helpView->RenderSubtree($classTree['artificer'], 900); ?>
        <div class = 'BackToTop'>
            <? require 'back_to_top.ctp'; ?>
        </div>

        <div class = 'TreeHeader'>
            <a name = 'pupil' class = 'NamedAnchor'>Pupil Branch</a>
        </div>
        <? $helpView->RenderSubtree($classTree['pupil'], 700); ?>
        <div class = 'BackToTop'>
            <? require 'back_to_top.ctp'; ?>
        </div>
    </div>
</div>
