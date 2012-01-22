<style type = 'text/css'>

.bordered img {
   padding:1px;
   border:2px solid #021a40;
   margin-top:10px;
   margin-bottom:10px;
}

</style>

<div class = 'PageDiv'>

    <div class = 'PageTitle'>
        <?= $html->link2('Help', array('controller' => 'help', 'action' => 'index')); ?> | Item Basics
    </div>

    <div class = 'PageContent' style = 'position: relative'>

        <? require '../views/help/navbar.ctp'; ?>

        <div class = 'HelpPageHeader'>Item Basics</div>

        <div class = 'HelpIntro'>
            Your warriors will suck in battle if they don't have weapons or armor, so it's a good idea to give them equipment (duh). Managing equipment can be done in the Armory page.
        </div>

        <div class = 'StatHeader'>
            Basics
        </div>
        <div class = 'StatContent'>
            You can equip items to characters by clicking the item, then clicking on the empty space near a character. To unequip, click the equipped item, then click on Armory. Mousing over an item will bring up a tooltip containing the stats of the equipment.  Equipment that is currently equipped to a character will have an "E" at the top right hand corner of the icon (You also can mouse over the item to see which character it actually equipped to).
<div class = 'bordered'><?= $html->image('help/ex_item_equipped.png'); ?></div>
        </div>

        <div class = 'StatHeader'>
            Getting Better Equipment
        </div>
        <div class = 'StatContent'>
            When you level up and gain more money, you should buy new equipment to make your characters stronger. This is done by clicking the Store link. Be careful with buying new equipment; make sure your characters are able to equip the item before spending your hard earned cash.
<div class = 'bordered'><?= $html->image('help/ex_item_store.png'); ?></div>
        </div>

        <div class = 'StatHeader'>
            Limitations
        </div>
        <div class = 'StatContent'>
            Classes only can equip certain types of weapons, but can equip any type of armor. The types of weapons available can be seen on the View Character page. The weight of the armor affects the speed of the character (the speed effect can be found by mousing over the equipment).
<div class = 'bordered'><?= $html->image('help/ex_item_limit.png'); ?></div>
        </div>

        <div class = 'StatHeader'>
            Upgrading Items
        </div>
        <div class = 'StatContent'>
            If you have a bit of extra money, you may want to try <a href = '/help/refining'>Refining</a> or <a href = '/help/imbuing'>Imbuing</a> your equipment.
<div class = 'bordered'><?= $html->image('help/ex_item_imbue.png'); ?></div>
        </div>

        <div style = 'position: absolute; top: 340px; right: 0px;'>
            <?= $html->image('help/mercenary_190x243.png'); ?>
        </div>
        <? require '../views/help/back_to_top.ctp'; ?>
    </div>
</div>