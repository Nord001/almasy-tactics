<style type = 'text/css'>

.PageDiv img {
   padding:1px;
   border:2px solid #021a40;
   margin-top:10px;
   margin-bottom:10px;
}
</style>

<div class = 'PageDiv'>

    <div class = 'PageTitle'>
        <?= $html->link2('Help', array('controller' => 'help', 'action' => 'index')); ?> | Character Basics
    </div>

    <div class = 'PageContent' style = 'position: relative'>
        <? require '../views/help/navbar.ctp'; ?>

        <div class = 'HelpPageHeader'>Character Basics</div>

        <div class = 'HelpIntro'>
            When you first create your account, you'll automatically have seven characters ready for you. Clicking on the Army link will show you an overview of all your characters; clicking any of them will bring up specific information on that character.
        </div>
        <?= $html->image('help/charpage.png', array('width' => '750')); ?>
        <div class = 'StatHeader'>
            Character Info
        </div>
        <div class = 'StatContent'>
            <ul>
                <li>Characters have 4 main <a href = '/help/stats'>stats</a> (Strength, Vitality, Intelligence, and Luck), which affect <a href = '/help/substats'>substats</a>, which directly affect how a character does in battle.</li>
                <li>Each stat has a growth rate, which determines how much a stat increases per level.</li>
                <li>The ability of the class, which can give special effects to the character and its neighbors.</li>
            </ul>
        </div>

        <div class = 'StatHeader'>
            Leveling Up
        </div>
        <div class = 'StatContent'>
            As your characters fight in more battles, they will gain exp and level up! Characters level independently from each other, but generally your characters will level up around the same speed as each other. When leveling up, two things happen: characters' stats grow a certain amount (stat growth) and may unlock new classes to promotions.
        </div>

        <div class = 'StatHeader'>
            Stat Growth
        </div>
        <div class = 'StatContent'>
            Each Stat of a character has a certain growth. The growth determines how much of that stat is gained every time the character levels up. So, although Stat Growth does not directly influence your character's fighting ability, it influences how quickly your character will develop. For this reason, when <a href ='/help/recruiting'>recruiting</a> characters, stat growth is much more important than base stats.<br/>

Stat growth usually is improved when a character changes classes, so it's recommended to change classes as soon as possible to take full advantage the bonuses.

        </div>

        <div class = 'StatHeader'>
            Stat Growth Example
        </div>
        <div class = 'StatContent'>
            Let's look at an example. <br/>
<?= $html->image('help/ex_statgrowth.png'); ?><br/>
The character's current stats are: 57 STR, 56 VIT, 80 INT, and 60 LUK, while his Stat Growths are +7.2 STR, +5.9 VIT, +9 INT, and +4.5 LUK. When the character levels up, his stats will change to 64 STR, 62 VIT, 89 INT, and 64 LUK. It's important to note that although stats are whole numbers while Stat Growth are in decimals, the decimals are actually hidden from view and still have an effect.

        </div>


        <div class = 'StatHeader'>
            Effects on Neighbors
        </div>
        <div class = 'StatContent'>
            Each class has an ability that affects nearby allies in combat.  Only certain spots are affected, so be sure to read the description for the ability.  One thing to remember is that in battle, when a character dies, his effect on neighbors wears off immediately.

        </div>

        <? require '../views/help/back_to_top.ctp'; ?>
    </div>
</div>
