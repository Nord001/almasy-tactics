<div class = 'PageDiv'>

    <div class = 'PageTitle'>
        <?= $html->link2('Help', array('controller' => 'help', 'action' => 'index')); ?> | Reputation
    </div>

    <div class = 'PageContent' style = 'position: relative'>
        <? require '../views/help/navbar.ctp'; ?>

        <div class = 'HelpPageHeader'>Formation Reputation</div>

        <div class = 'HelpIntro'>
            Formations reputation is Almasy's way of estimating its fighting strength.
        </div>

        <div class = 'StatHeader'>
            Reputation Basics
        </div>
        <div class = 'StatContent'>
           Reputation is the battle rating of your formation, representing the actual fighting strength of your team. It's a lot like a rating system in chess.  You gain or lose reputation after every battle, based on the reputations of you and your opponent. You should try to have high reputation, because formations with high reputation will usually fight teams with high reputation, which will help your characters level more quickly.
        </div>

        <div style = 'position: absolute; top: 250px; right: 10px;'>
            <?= $html->image('help/gladiator_200x277.png'); ?>
        </div>

        <div class = 'StatHeader'>
            Reputation Uses
        </div>
        <div class = 'StatContent'>
           <ul>
           <li>When you battle, you will be paired up against teams with similar reputations.<br/>
<?= $html->image('help/reputation_battle.png'); ?>
           <li>The rewards that you get from battle depend on your opponent's reputation.  The higher the reputation, the larger the reward.
           </ul>
        </div>

        <? require '../views/help/back_to_top.ctp'; ?>
    </div>
</div>
