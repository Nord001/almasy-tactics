<div class = 'PageDiv'>

    <div class = 'PageTitle'>
        <?= $html->link2('Help', array('controller' => 'help', 'action' => 'index')); ?> | Organize Formation
    </div>
    <div class = 'PageContent' style = 'position: relative'>
        <? require '../views/help/navbar.ctp'; ?>

        <div class = 'HelpPageHeader'>Organizing your Formation</div>

        <div class = 'HelpIntro'>

        </div>

        <div class = 'StatHeader'>
            Basics
        </div>
        <div class = 'StatContent'>
            The positioning of characters in formations is important:
                    <ul>
                    <li>Bonuses: Depending on how your characters are placed, the effects of your other characters' abilities may provide a significant boost to their fighting power.
                    <li>Range: Characters have a range, which shows how far an enemy can be and still be able to attack him.  A character with 1 range can only attack enemies 1 row away, a character with 1-2 range can attack enemies 1 or 2 rows away, and a character with range 2 can only attack enemies 2 rows away.
                    </ul>
        </div>

        <div class = 'StatHeader'>
            Usage
        </div>
        <div class = 'StatContent'>
                    <ul>
                    <li>To move a character around, click on the character, then the desired location.
                    <li>If you want to know how a character affects his neighbors, mouse over the character's portrait.
                    </ul>
        </div>

        <div class = 'StatHeader'>
            Example
        </div>
        <div class = 'StatContent'>
                <div style = 'float: left; width: 300px;'>
                <?= $html->image('help/start_formation2.png'); ?>
            </div>
            <div style = 'float: right; width: 450px; margin-top: 1px;'>
                <ul><li>I have put all my swordsmen-type in the front row with spellcasters behind them. This is a common way of arranging a formation, since you'll want all your characters to be able to attack.
                    <li>Characters that are higher up are closer to the front lines. If you have any ranged classes, you should probably put them in the middle or bottom row to protect them.
                    <li>When changing classes, you will probably need to make modifications to your formations if their abilities change.
                </ul>
            </div>
            <div style = 'clear: both;'></div>
        </div>

        <? require '../views/help/back_to_top.ctp'; ?>
    </div>
</div>