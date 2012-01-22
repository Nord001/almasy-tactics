<div class = 'PageDiv'>

    <div class = 'PageTitle'>
        <?= $html->link2('Help', array('controller' => 'help', 'action' => 'index')); ?> | Battlegrounds
    </div>
    <div class = 'PageContent' style = 'position: relative'>
        <? require '../views/help/navbar.ctp'; ?>

        <div class = 'HelpPageHeader'>Battlegrounds</div>

        <div class = 'HelpIntro'>
            Players who decide to participate in Battlegrounds have the benefit of choosing and analyzing formations that they attack.  However, by doing so, they also put a portion of their own money on the line.
        </div>

        <div class = 'StatHeader'>
            Basic Rules
        </div>
        <div class = 'StatContent'>
            <ul>
            <li>To enter, you must first pay an entry fee (the cost of equal to the power of the entered formation).  This fee is non-refundable, but becomes your starting bounty.
            <li>After you have entered, you will be able to attack any formation that has a bounty equal or greater than yours.  Battling costs one battle point.
            <li>The winner of a battle takes the bounty of the loser.  50% of the loser's bounty is added to the winner's bounty; the other 50% goes to the winner's pockets.
            <li>Losing a battle means your bounty is forfeited.  You will have to pay the entry fee to be re-entered into battlegrounds.  You do not lose anything other than the entry fee.
            </ul>
        </div>

        <div class = 'StatHeader'>
            Bounty Perks
        </div>
        <div class = 'StatContent'>
            <ul>
            <li>Every day, you get 5% of your bounty as yuanbao.
            </ul>
        </div>

        <? require '../views/help/back_to_top.ctp'; ?>
    </div>
</div>