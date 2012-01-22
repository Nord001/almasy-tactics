<div class = 'PageDiv'>

    <div class = 'PageTitle'>
        <?= $html->link2('Help', array('controller' => 'help', 'action' => 'index')); ?> | Income
    </div>
    <div class = 'PageContent' style = 'position: relative'>
        <? require '../views/help/navbar.ctp'; ?>

        <div class = 'HelpPageHeader'>Income</div>

        <div class = 'HelpIntro'>
            Players receive two things regularly: Yuanbao and Battle Points.
        </div>

        <div class = 'StatHeader'>
            Yuanbao
        </div>
        <div class = 'StatContent'>
            Yuanbao, the currency of Almasy, is used primarily for <a href = '/help/items'>buying</a>, <a href = '/help/refining'>refining</a>, and <a href = '/help/imbuing'>imbuing</a> equipment. It is also used for recruiting new characters. Yuanbao is obtained by battling other players.
        </div>

        <div class = 'StatHeader'>
            Battle Points
        </div>
        <div class = 'StatContent'>
            Battle Points are spent when players attack. Battle Points are automatically obtained in intervals. The fewer battle points you have, the faster you gain them, so spend them as soon as possible!
        </div>

        <div style = 'position: absolute; top: 240px; right: 0px;'>
            <?= $html->image('help/professor_188x229.png'); ?>
        </div>

        <? require '../views/help/back_to_top.ctp'; ?>
    </div>
</div>