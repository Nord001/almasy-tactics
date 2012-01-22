<div class = 'PageDiv'>

    <div class = 'PageTitle'>
        <?= $html->link2('Help', array('controller' => 'help', 'action' => 'index')); ?> | Bounty
    </div>
    <div class = 'PageContent' style = 'position: relative'>
        <? require '../views/help/navbar.ctp'; ?>

        <div class = 'HelpPageHeader'>Bounty</div>

        <div class = 'HelpIntro'>
            When battling, you may notice that each formation has a "bounty" attached to them.  Bounty represents the winning streak of the formation.
        </div>

        <div class = 'StatHeader'>
            Basic Rules
        </div>
        <div class = 'StatContent'>
            <ul>
            <li>Whenever you win a battle, you gain 4 bounty + 50% of the bounty of the formation you defeated.
            <li>Whenever you lose a battle, you lose all your bounty.
            </ul>
        </div>

        <div style = 'position: absolute; top: 270px; right: 10px;'>
            <?= $html->image('help/marauder_200x237.png'); ?>
        </div>

        <div class = 'StatHeader'>
            Bounty Perks
        </div>
        <div class = 'StatContent'>
            <ul>
            <li>Battle Reward Bonuses - You get a bonus in yuanbao and exp whenever you win.  The bonus formula is 6 (Bounty) / (Bounty + 500) %. So, if you manage to get 100 bounty, every battle you win will give you double the reward!
            <li>Bragging rights! You'll be displayed on the front page as one of "Almasy's Most Wanted"!
            </ul>
        </div>

        <? require '../views/help/back_to_top.ctp'; ?>
    </div>
</div>