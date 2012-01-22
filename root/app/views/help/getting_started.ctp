<div class = 'PageDiv'>

    <div class = 'PageTitle'>
        <?= $html->link2('Help', array('controller' => 'help', 'action' => 'index')); ?> | What is Almasy Tactics?
    </div>

    <div class = 'PageContent' style = 'position: relative'>

        <? require '../views/help/navbar.ctp'; ?>

        <div class = 'HelpPageHeader'>Welcome to Almasy Tactics!</div>

        <div class = 'HelpIntro'>
            Almasy Tactics is a creative strategy game with both RPG and strategy game elements. Players manage an army by raising characters, upgrading items, and defeating other players.
        </div>

        <div class = 'StatHeader'>
            What is Almasy Tactics?
        </div>
        <div class = 'StatContent'>
              In Almasy Tactics, you establish and manage a powerful army by creating formations of seven characters and battling other players. Fighting other players gets experience for your warriors,  which allows them to become more powerful and wield better weapons .<br/><br/>

<a href = '/help/firststeps'><img src ="http://www.almasytactics.com/img/help/firststepbutton.png"/></a><br/><br/>
<a href = '/help/organize'><img src ="http://www.almasytactics.com/img/formationbutton.png"/></a>

        </div>

        <div class = 'StatHeader'>
            The Classes of Almasy Tactics
        </div>
        <div class = 'StatContent'>
            Characters in Almasy Tactics level up and promote individually from other characters.  Promoting to a new class not only powers up the character's fighting abilities, but also grants the character new abilities to affect their surrounding allies.  There are over <b>one hundred</b> distinct classes with unique abilities for you to mix and match in formations.  There are limitless possibilities; finding the right mix of characters is key to success! <br/><br/>
<a href = '/help/class_tree/swordsman'><img src ="http://www.almasytactics.com/img/treebutton.png"/></a><br/><br/>
<a href = '/help/char_basics'><img src ="http://www.almasytactics.com/img/characterbutton.png"/></a><br/>
        </div>

        <div class = 'StatHeader'>
            Items and Imbues
        </div>
        <div class = 'StatContent'>
            Each character in your army can be outfitted with two pieces of equipment: one weapon and one armor.  Each piece of equipment can be endowed with an imbue, which gives powerful effects to its holder.   Buying, refining(flat upgrades), and imbuing equipment costs yuanbao (the currency of Almasy), which is obtained through battling.<br/><br/>
<a href = '/help/items'><img src ="http://www.almasytactics.com/img/itembutton.png"/></a><br/><br/>
<a href = '/help/imbuing'><img src ="http://www.almasytactics.com/img/imbuebutton.png"/></a><br/>

        </div>


        <? require '../views/help/back_to_top.ctp'; ?>
</div>
</div>