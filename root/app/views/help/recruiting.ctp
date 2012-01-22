<div class = 'PageDiv'>

    <div class = 'PageTitle'>
        <?= $html->link2('Help', array('controller' => 'help', 'action' => 'index')); ?> | Recruiting
    </div>

    <div class = 'PageContent' style = 'position: relative'>

        <? require '../views/help/navbar.ctp'; ?>

        <div class = 'HelpPageHeader'>Recruiting New Characters</div>

        <div class = 'HelpIntro'>
            There will be a time where you will want to make new characters. To do this, click the Recruit! button on the Army page.
        </div>

        <div class = 'StatHeader'>
            Rolling Characters
        </div>
        <div class = 'StatContent'>
            <div>
               Clicking the Recruit New Character button will randomize the base stats and stat growth of the character. Base stats are the initial values of a character’s stats. stat growth is how much of a stat a character gains per level.  Stat growth is much more important than base stat because it influences how the character will turn out later on.
            </div>
            <div>
               You'll also get to choose the name of your new character. Choose wisely, because you won't get to change it afterwards!
            </div>
        </div>
<img src="/img/help/rolling.png" width="750"/>
        <div class = 'StatHeader'>
            Some Guidelines/Tips on Rolling New Characters
        </div>
        <div class = 'StatContent'>
            <ul>
                <li>A stat growth of 5.5+ is usually satisfactory. Multiple stats with 5.5+ growth is excellent.
                <li>A stat growth of 6.3+ in either STR or INT is generally amazing for characters you plan to make attackers.
                <li>High VIT and LUK is recommended for characters for tanking (so they stay alive and enemies don't get critical hits them too much)
            </ul>
        </div>

        <div style = 'position: absolute; top: 340px; right: 0px;'>
            <?= $html->image('help/novice_190x261.png'); ?>
        </div>

    </div>
</div>