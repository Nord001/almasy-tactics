<div class = 'PageDiv'>

    <div class = 'PageTitle'>
        <?= $html->link2('Help', array('controller' => 'help', 'action' => 'index')); ?> | Battle Mechanics
    </div>
    <div class = 'PageContent' style = 'position: relative'>
        <? require '../views/help/navbar.ctp'; ?>

        <div class = 'HelpPageHeader'>How Battle Works</div>

        <div class = 'HelpIntro'>
            Almasy's battle system is similar to a turn based RPG game, only everything is automated for simplicity and speed.
        </div>

        <div class = 'StatHeader'>
            Battle Progression
        </div>
        <div class = 'StatContent'>
            <ul>
            <li>Battles are split up into rounds, which are split up into turns.
            <li>Each round, characters attack as many times as they have strikes.
            <li>Characters only attack once per turn. This means the number of turns in a round depends on maximum amount of strikes a character has.
            <li>The character with the highest speed attacks first in a turn.
            <li>Characters will always attack the enemy that they can deal the most damage to (provided they are in range).  All characters in a row are considered in the same range.
            <li>Upon death, characters' affects on others will disappear.
            <li>When there are no more characters left in the front row, the next line of characters becomes the front line (putting them in range of melee attacks).
            <li>At the end of a round, characters receive HP regeneration and/or have the effects of class abilities or items wear off.
            </ul>
        </div>



        <? require '../views/help/back_to_top.ctp'; ?>
    </div>
</div>