<div class = 'PageDiv'>

    <div class = 'PageTitle'>
        <?= $html->link2('Help', array('controller' => 'help', 'action' => 'index')); ?> | Imbuing Items
    </div>
    <div class = 'PageContent' style = 'position: relative'>
        <? require '../views/help/navbar.ctp'; ?>

        <div class = 'HelpPageHeader'>Imbuing Items</div>

        <div class = 'HelpIntro'>
            Another way to upgrade your items is to imbue them. It's highly recommended that you imbue your items, since imbued weapons are a lot stronger than regular weapons. There are a lot of imbues, so try them out to experiment with all the different ones! Check out the imbue list <a href = '/help/imbue_list'>here</a>.
        </div>

        <div class = 'StatHeader'>
            Effect
        </div>
        <div class = 'StatContent'>
            When a piece of equipment is imbued, its name will change and it will get several kinds of enchantments, depending on what imbue you choose. The effect of the enchantments vary, so if you have a lot of money, a good investment may be to imbue the same weapon several times until you get a very strong item.
        </div>

        <div class = 'StatHeader'>
            Cost
        </div>
        <div class = 'StatContent'>
            Imbuing costs more money than <a href = '/help/refining'>refining</a>, but there is no chance for the item to break. It costs 1000 + 3 * (Item Price) to imbue an item.
        </div>

        <div style = 'position: absolute; top: 220px; right: 10px;'>
            <?= $html->image('help/alchemist_190x306.png'); ?>
        </div>

        <? require '../views/help/back_to_top.ctp'; ?>
    </div>
</div>