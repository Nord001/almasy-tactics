<div class = 'PageDiv'>

    <div class = 'PageTitle'>
        <?= $html->link2('Help', array('controller' => 'help', 'action' => 'index')); ?> | Character Stats
    </div>
    <div class = 'PageContent' style = 'position: relative'>
        <? require '../views/help/navbar.ctp'; ?>

        <div class = 'HelpPageHeader'>Character Stats</div>

        <div class = 'HelpIntro'>
            All characters have 4 stats: STR, VIT, INT, and LUK. Stats are a good predictor of how your character will do in battle, but looking at your character's <a href = '/help/substats'>substats</a> will usually be more accurate.
        </div>

        <div class = 'StatHeader'>
            Strength (STR)
        </div>
        <div class = 'StatContent'>
            Strength improves the attack of characters that use physical attacks.
        </div>

        <div class = 'StatHeader'>
            Vitality (VIT)
        </div>
        <div class = 'StatContent'>
            Vitality affects the survivability of the character by increasing HP and defense against physical attacks.
        </div>

        <div class = 'StatHeader'>
            Intelligence (INT)
        </div>
        <div class = 'StatContent'>
            Intelligence improves the attack of characters that use magical attacks and increases defense against incoming magical attacks.
        </div>

        <div class = 'StatHeader'>
            Luck (LUK)
        </div>
        <div class = 'StatContent'>
            Luck improves a character's chance of getting a critical strike on an opponent and decreases the chance of getting hit by an incoming attack.
        </div>

        <div style = 'position: absolute; top: 340px; right: 0px;'>
            <?= $html->image('help/pupil_190x231.png'); ?>
        </div>

        <? require '../views/help/back_to_top.ctp'; ?>
    </div>
</div>