<div class = 'PageDiv'>

    <div class = 'PageTitle'>
        <?= $html->link2('Help', array('controller' => 'help', 'action' => 'index')); ?> | Tactician Traits
    </div>

    <div class = 'PageContent' style = 'position: relative'>
        <? require '../views/help/navbar.ctp'; ?>

        <div class = 'HelpPageHeader'>Tactician Traits</div>

        <div class = 'HelpIntro'>
            Along with your characters, you, the tactician, can level up too. Leveling up your tactician level gives you trait points to distribute into the three tactician traits: zeal, greed, and ambition.
        </div>

        <div class = 'StatHeader'>
            Zeal
        </div>
        <div class = 'StatContent'>
            Zeal increases how fast you get battle points. If you're the battling type, this trait will give you battles faster so you can battle and level more.
        </div>

        <div class = 'StatHeader'>
            Greed
        </div>
        <div class = 'StatContent'>
            Greed increases the daily <a href = '/help/income'>income</a> that you receive. It's an easier way to make money that battling, but it doesn't give as much money.
        </div>

        <div class = 'StatHeader'>
            Ambition
        </div>
        <div class = 'StatContent'>
            Ambition increases the benefits of participating in battlegrounds. Increasing this trait will give you more bounty for winning and more income from having bounty.
        </div>

        <div style = 'position: absolute; top: 240px; right: 0px;'>
            <?= $html->image('help/tinker_190x323.png'); ?>
        </div>

        <? require '../views/help/back_to_top.ctp'; ?>
    </div>
</div>
