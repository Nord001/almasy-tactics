<div class = 'PageDiv'>

    <div class = 'PageTitle'>
        <?= $html->link2('Help', array('controller' => 'help', 'action' => 'index')); ?> | Character Substats
    </div>

    <div class = 'PageContent' style = 'position: relative'>
        <? require '../views/help/navbar.ctp'; ?>

        <div class = 'HelpPageHeader'>Character Substats</div>

        <div class = 'HelpIntro'>
            The substats of a character are other stats besides the <a href = '/help/stats'>main four</a> (STR, VIT, INT, LUK) that affect how well a character does in battle. Some of these are affected by the main stats, and others are granted via class bonuses or items.
        </div>

        <div class = 'StatHeader'>
            HP
        </div>
        <div class = 'StatContent'>
            The number of health points a character has. In a battle, characters always start with full health. Once a character loses all their health points, they die.
        </div>

        <div class = 'StatHeader'>
            Damage
        </div>
        <div class = 'StatContent'>
            The first part of this is Damage/Strike, the second part is the number of strikes per round the character has. Damage is calculated by the formula:

            <br />
            <br />

            Damage = (Character Base Attack + Weapon Attack) * (1 + strikes * .2) / strikes
        </div>

        <div class = 'StatHeader'>
            Strikes
        </div>
        <div class = 'StatContent'>
            The number of times your character attacks per round. The number of strikes a character has is mostly based on how many strikes their weapon has.
        </div>

        <div style = 'position: absolute; top: 340px; right: 10px;'>
            <?= $html->image('help/entertainer_190x269.png'); ?>
        </div>

        <div style = 'margin-left: 200px; margin-top: 40px; margin-bottom: 40px;'>
            <div class = 'StatHeader'>
                Range
            </div>
            <div class = 'StatContent'>
                How far a character can attack in battle. A character with range 1 in the front row of battle can attack any enemy that is in the opposing front row (1 row away).  If the character had 1-2 range, then he would be able to attack opponents in the front and middle row.  Careful though, some characters have a minimum range and cannot attack enemies directly in front of them (an example would be the Sentinel class, which has 2-3 range).
            </div>

            <div class = 'StatHeader'>
                Physical Defense
            </div>
            <div class = 'StatContent'>
                How much the character can reduce incoming physical attacks by. There are two parts to physical defense:
                The first % is reduction and the other number is defense. Reduction reduces attacks by a percentage, while defense reduces attacks by a set amount. For example, Crysello attacks Erik. Crysello has 150 attack, and Erik has 12 + 20 defense.
                The final damage will be 150 * (1 - .12) - 20 = 112 damage.
            </div>

            <div class = 'StatHeader'>
                Magical Defense
            </div>
            <div class = 'StatContent'>
                The magical counterpart to Physical Defense, which reduces the damage of incoming magical attacks.
            </div>
        </div>


        <div style = 'position: absolute; top: 675px; left: 5px;'>
            <?= $html->image('help/archer_190x243.png'); ?>
        </div>

        <div class = 'StatHeader'>
            Speed
        </div>
        <div class = 'StatContent'>
            How early a character attacks in a turn. The higher the character's speed, the earlier the character will attack, which allows them to get in hits before slower enemies.
        </div>

        <div class = 'StatHeader'>
            Critical
        </div>
        <div class = 'StatContent'>
            The % chance a character will score a critical hit on an opponent. A critical strike will ignore the defense and reduction of a character. Base Critical = LUK / 40. Characters also reduce the chance of a critical hit happening to them by LUK / 70 (also known as "Crit Shield").
        </div>

        <div class = 'StatHeader'>
            Dodge
        </div>
        <div class = 'StatContent'>
            The % chance a character will completely dodge an attack of an opponent. Base Dodge = LUK / 250
        </div>

        <div style = 'position: absolute; top: 1020px; right: 10px;'>
            <?= $html->image('help/bountyhunter_190x262.png'); ?>
        </div>

        <div style = 'margin-left: 200px; margin-top: 40px; margin-bottom: 40px;'>
            <div class = 'StatHeader'>
                HP Regen
            </div>
            <div class = 'StatContent'>
                The percent of HP a character gains per round in battle. For example, if a character has 5% regen and 160 max hp, he will gain 8 HP a round. Characters will not gain HP over their max HP.
            </div>

            <div class = 'StatHeader'>
                Affinity
            </div>
            <div class = 'StatContent'>
                Each character has an affinity for a particular element, which affects the element of their attacks as well as some innate characteristics a character receives.
            </div>

            <div class = 'StatHeader'>
                Attacking/Defending Element
            </div>
            <div class = 'StatContent'>
                The element of a character's attacks and armor. Depending on these stats, characters may deal more or less damage to their enemies. This will be elaborated on in the Affinity section.
            </div>
        </div>

        <div style = 'position: absolute; top: 1300px; left: 5px;'>
            <?= $html->image('help/lord_190x247.png'); ?>
        </div>


        <? require '../views/help/back_to_top.ctp'; ?>
    </div>
</div>