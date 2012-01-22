<div class = 'PageDiv'>

    <div class = 'PageTitle'>
        <?= $html->link2('Help', array('controller' => 'help', 'action' => 'index')); ?> | Promoting
    </div>

    <div class = 'PageContent' style = 'position: relative'>
        <? require '../views/help/navbar.ctp'; ?>

        <div class = 'HelpPageHeader'>Promotions</div>

        <div class = 'HelpIntro'>
            As your characters level up, you'll unlock the ability to change them to a different class. When this is available, the character will light up when you are in the Army Tab.  Two things change when the character changes classes: their ability and their stat growth.  You should take both of these into consideration when faced with a decision: Stat Growth will shape how your characters' stats will end up, while abilities show how they will affect their neighbors in battle. Take a look at the
               <?= $html->link2('list of classes', '/help/class_list'); ?>.

        </div>

        <div class = 'StatHeader'>
            Stat Growth Change
        </div>
        <div class = 'StatContent'>
            The stat growth of characters is also changed when they promote to a new class.  The stats themselves stay the same, but the growth changes; this is what affects how the class ultimately turns out ten or fifty levels down the line.<br/>

<img src="/img/help/promotionexample.png" width="750"/>
You should promote characters when you unlock your preferred class as soon as possible so you can get the increased stat growth as soon as possible!
        </div>

        <div class = 'StatHeader'>
            Class Ability Change
        </div>
        <div class = 'StatContent'>
            When you promote a character, their ability will usually change; this varies from class to class.  Usually, abilities will either affect more squares or bestow stronger enhancements.
        </div>
        <? require '../views/help/back_to_top.ctp'; ?>
    </div>
</div>
