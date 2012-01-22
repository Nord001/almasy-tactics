<style type = 'text/css'>
    li img {
        vertical-align: middle;
    }

    .DataTable {
        padding: 5px;
    }

    .DataTable th {
        text-align: center;
        padding: 3px;
    }

    .DataTable td {
        text-align: center;
        padding: 3px;
        width: 200px;
        font-weight: bold;
    }
    .PrimaryStrength {
        color: rgb(70, 220, 70);
    }
    .SecondaryStrength {
        color: rgb(0, 100, 0);
    }
    .PrimaryWeakness {
        color: rgb(220, 70, 70);
    }
    .SecondaryWeakness {
        color: rgb(100, 20, 20);
    }
</style>

<div class = 'PageDiv'>

    <div class = 'PageTitle'>
        <?= $html->link2('Help', array('controller' => 'help', 'action' => 'index')); ?> | Elements
    </div>
    <div class = 'PageContent' style = 'position: relative'>
        <? require '../views/help/navbar.ctp'; ?>

        <div class = 'HelpPageHeader'>Elements and Affinity</div>

        <div class = 'HelpIntro'>
            Almasy Tactics has five different elements: Fire, Water, Earth, Wood, and Steel.

        </div>

        <div class = 'StatHeader'>
            Innate Affinity Bonuses
        </div>
        <div class = 'StatContent'>
            Characters receive a small bonus depending on their affinity. These never change, no matter what equipment the character wears.
            <ul>
            <li><?= $html->image('sprites/affinity_fire.png'); ?>Fire affinity characters receive +5% damage and +1 speed.
            <li><?= $html->image('sprites/affinity_steel.png'); ?>Steel affinity characters receive +5% HP and +5% Physical and Magical Reduction.
            <li><?= $html->image('sprites/affinity_wood.png'); ?>Wood affinity characters receive +5% HP and +5% critical rate.
            <li><?= $html->image('sprites/affinity_earth.png'); ?>Earth affinity characters receive +5% attack and +5% critical rate.
            <li><?= $html->image('sprites/affinity_water.png'); ?>Water affinity characters receive +1 Speed and +5% Physical and Magical Reduction.
            </ul>
            An elemental attack will deal 20% less damage to a character that has an affinity for that element.
        </div>

        <div class = 'StatHeader'>
            Attacking and Defending Element
        </div>
        <div class = 'StatContent'>
            Each element has elements that it's strong against and weak against. By default, characters will attack according to their affinity's element and defend with neutral element. These are shown on the View Character Page as "Atk. Element" and "Def. Element."  This means that characters' attacks are aligned with a particular element, so it will deal more damage to people who are weak to that element.
        </div>

        <div class = 'StatHeader'>
            Element Strengths and Weaknesses
        </div>
        <div class = 'StatContent'>
            Each element has two strengths and weaknesses: one primary and one secondary. An element will deal significantly more damage to its primary strength, more damage to its secondary strength, regular damage to itself, less damage to its secondary weakness, and significantly less damage to its primary weakness. However, a neutral element defense takes regular damage from every element. See the table below for precise numbers.

            <br />
            <br />

            Primary Strengths: Fire > Steel > Wood > Earth > Water > Fire <br />
            Secondary Strengths: Fire > Wood > Water > Steel > Earth > Fire <br />

            <br />

            <div style = 'float: left; width: 125px;'>
                <?= $html->image('help/primarystr edit.png'); ?>
            </div>
            <div style = 'float: right; width: 600px; margin-top: 40px;'>
                In this picture, the path around the circle clockwise represents the circle of primary strengths, while the star inside represents secondary strengths.
            </div>
            <div style = 'clear: both;'></div>
        </div>

        <div class = 'StatHeader'>
            Attack Multiplier Table
        </div>
        <div class = 'StatContent'>
            <table class = 'DataTable'>
                <tr>
                    <th></th><th></th><th></th><th></th><th colspan = 3>Defending Element</th><th></th><th></th><th></th>
                </tr>
                <tr>
                    <th></th><th></th><th></th><th><?= $html->image('sprites/affinity_fire.png'); ?></th><th><?= $html->image('sprites/affinity_steel.png'); ?></b></th><th><?= $html->image('sprites/affinity_wood.png'); ?></th><th><?= $html->image('sprites/affinity_earth.png'); ?></th><th><?= $html->image('sprites/affinity_water.png'); ?></th>
                </tr>
                <tr>
                    <th></th><th></th><th></th><th>Fire</th><th>Steel</b></th><th>Wood</th><th>Earth</th><th>Water</th>
                </tr>
                <tr>
                    <td></td><th><?= $html->image('sprites/affinity_fire.png'); ?></th><th>Fire</td><td>100%</td><td class = 'PrimaryStrength'>140%</td><td class = 'SecondaryStrength'>120%</td><td class = 'PrimaryWeakness'>80%</td><td class = 'SecondaryWeakness'>60%</td>
                </tr>
                <tr>
                    <th>Attacking</th><th><?= $html->image('sprites/affinity_steel.png'); ?></th><td><b>Steel</th><td class = 'SecondaryWeakness'>60%</td><td>100%</td><td class = 'PrimaryStrength'>140%</td><td class = 'SecondaryStrength'>120%</td><td class = 'PrimaryWeakness'>80%</td>
                </tr>
                <tr>
                    <th>Element</th><th><?= $html->image('sprites/affinity_wood.png'); ?></th><th>Wood</th><td class = 'PrimaryWeakness'>80%</td><td class = 'SecondaryWeakness'>60%</td><td>100%</td><td class = 'PrimaryStrength'>140%</td><td class = 'SecondaryStrength'>120%</td>
                </tr>
                <tr>
                    <td></td><th><?= $html->image('sprites/affinity_earth.png'); ?></th><th>Earth</th><td class = 'SecondaryStrength'>120%</td><td class = 'PrimaryWeakness'>80%</td><td class = 'SecondaryWeakness'>60%</td><td>100%</td><td class = 'PrimaryStrength'>140%</td>
                </tr>
                <tr>
                    <td></td><th><?= $html->image('sprites/affinity_water.png'); ?></th><th>Water</th><td class = 'PrimaryStrength'>140%</td><td class = 'SecondaryStrength'>120%</td><td class = 'PrimaryWeakness'>80%</td><td class = 'SecondaryWeakness'>60%</td><td>100%</td>
                </tr>
            </table>
        </div>
        <div class = 'StatHeader'>
            Example 1
        </div>
        <div class = 'StatContent'>
            <ul>
                <li>Crysello has Fire affinity with no special equipment. This means that his attacking element is Fire and his defending element is Neutral.
                <li>Erik has Water affinity with no special equipment. This means that his attacking element is Water and his defending element is Neutral.
                <li>Since both characters' defending element are neutral, they do 100% (regular) damage on each other.
            </ul>
        </div>

        <div class = 'StatHeader'>
            Example 2
        </div>
        <div class = 'StatContent'>
            <ul>
                <li>Crysello (Fire affinity) equips an armor with a "Spirit of Earth" bonus. This changes his defending element to Earth type.
                <li>Erik (Water affinity) remains the same.
                <li>Crysello will still do 100% damage on Erik, but Erik now will only deal 60% (a 40% decrease) of his normal damage to Crysello, since his Water's primary weakness is Earth (conversely, Earth's primary strength is Water).
            </ul>
        </div>

        <div class = 'StatHeader'>
            Example 3
        </div>
        <div class = 'StatContent'>
            <ul>
                <li>Crysello (Fire affinity, with Earth armor) remains the same.
                <li>Erik (Water affinity) equips a weapon with the bonus "Spirit of Steel." This changes his attacking element to Steel.
                <li>Crysello still does 100% damage on Erik because Fire deals 100% (regular) damage to Neutral. However, Erik now will deal 120% of his normal damage to Crysello (a 20% increase) because Steel's secondary strength is against Earth (Conversely, Earth's secondary weakness is against Steel).
            </ul>
        </div>

        <? require '../views/help/back_to_top.ctp'; ?>
    </div>
</div>