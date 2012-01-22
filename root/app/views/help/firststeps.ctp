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
        <?= $html->link2('Help', array('controller' => 'help', 'action' => 'index')); ?> | First Steps
    </div>

    <div class = 'PageContent' style = 'position: relative'>

        <? require '../views/help/navbar.ctp'; ?>

        <div class = 'HelpPageHeader'>Quickstart Guide</div>

        <div class = 'HelpIntro'>
            This page lists the recommended first steps you take to help you become accustomed to Almasy's interface. If you have any questions afterwards, check the Help Bar located at the top of each page, or post on the <a href = "www.almasytactics.com/forums">forums</a>.
        </div>

        <div class = 'StatHeader'>
            Manage Characters / Army
        </div>
        <div class = 'StatContent'>
            Your first seven characters (all with pretty good stats) will be pre-created for you. To see them, click on the Army link in the upper left hand corner. You'll be directed to a page that similar to this:
            <br />
            <img src = '/img/help/start_army.png' style = 'width: 750px' />
            <br />
            This page provides a general overview of all your characters. You can see the level, class, and exp bar for your characters. One of your characters (the one you named) can be promoted straightaway, so click on his box to view his stats and choose a promotion for him!
        </div>

        <div class = 'StatHeader'>
            View Characters / Promote Your First Character
        </div>
        <div class = 'StatContent'>
            When clicking on a character on the Army screen, you'll be directed to a page with detailed information about that character. Click on the "Promote!" button to choose a class for him to promote to.
            <br />
            <img src = '/img/help/start_char.png' style = 'width: 750px' />
            <br />
            When characters reach a certain level, they can be promoted to stronger classes.  People typically choose promotions based on what growth the character has. Since your first character has good stats all around, he'll do well as any class.
        </div>

        <div class = 'StatHeader'>
            Equip Items
        </div>
        <div class = 'StatContent'>
            After promoting your main character, you'll want to head over to the Armory page to manage the items of your formation. You start off with equipment already attached to your army, but you might be interested in buying more by clicking the "Store" button on the upper right hand corner.
            <br />
            <img src = '/img/help/start_items.png' style = 'width: 750px' />
            <br />

            On the Armory page, you can do a couple of things after selecting an item (by clicking):
            <ul>
                <li>Equip the item to a character by clicking in the space next to the character.
                <li>Unequip the item by clicking in the Armory box.
                <li><a href = '/help/refining'>Refine</a> or <a href = '/help/imbuing'>imbue</a> your item.
            </ul>
            Imbuing is probably too expensive to do right away, but you might be interested in refining your items to make them stronger.
        </div>

        <div class = 'StatHeader'>
            Manage Formations
        </div>
        <div class = 'StatContent'>
              Once you finish upgrading your weapons, click on the Formations link to arrange how your characters enter combat. An overview of your formations is provided on the formation main page.
              <ul>
                  <li>Your formations' <a href = '/help/powerandrep'>reputation</a> is shown, which is an estimator of their fighting strengths.
                  <li>The <u>Edit</u> page is for selecting characters for a formation or renaming a formation.
                  <li>The most page, <u>Organize</u> Formation, lets you position your characters within a formation.   Positioning your characters to receive the maximum bonus from each of their abilities while making sure that your weaker characters are protected (put them toward the back) will ensure you have a great formation. <a href = 'organize'>More information on Organizing.</a>
              </ul>
        </div>
        </div>

        <div class = 'StatHeader'>
            Fight Enemies
        </div>
        <div class = 'StatContent'>
            After you're satisfied with how your characters and their arrangements, head off to the War Room and click "To War" to fight other players! You and your characters gain experience by fighting other players' formations. I recommend using Battle as much as possible, so you gain experience points.  Try to find formations that you can beat to build up <a href = '/help/bounty'>bounty</a>.  Careful of attacking people with high bounty: though the reward is higher, formations with high bounty usually are strong!
            <br />
            <img src = '/img/help/start_battle.png' />
        </div>

        <div class = 'StatHeader'>
            Tactician Bar
        </div>
        <div class = 'StatContent'>
            You may have noticed that there is a tactician bar at the top right hand corner. It contains a information irrelevant to your characters:
            <br />
            <?= $html->image('help/start_tact.png'); ?>
            <br />

            <ul>
                <li>You can see how much <a href = '/help/income'>yuanbao</a> (the currency of Almasy) you have.
                <li>You can also see the number of remaining <a href = '/help/battles'>battles</a>. You can see when you will get more by mousing over the number. Hint: Use up all your battles as soon as you can because you get battles faster when you have fewer battles.
                <li>The number of unread messages is shown in the small speech box. You will receive automatic messages whenever you are attacked or an event happens.  Clicking the speech box will bring you to your message inbox.
            </ul>
        </div>


        <div class = 'StatHeader'>
            Future Steps
        </div>
        <div class = 'StatContent'>
            After you've gotten the hang of Almasy and have battled some, here are some things you might want to do:
              <ul>
                  <li><a href = '/help/imbuing'>Imbue</a> or <a href = '/help/refining'>refine</a> your items.
                  <li><a href = '/help/promoting'>Promote</a> your characters to become more powerful. There are some suggestions for your starting characters below.
                  <li><a href = '/help/recruiting'>Recruit</a> more characters for your army.
              </ul>
            If you have any questions, you may be able to find the answer at the bottom of the page (Help Bar) or a part of the manual.
        </div>

        <div class = 'StatHeader'>
            Promote Characters
        </div>
        <div class = 'StatContent'>
             By fighting battles, your characters will gain experience points to level up. At certain levels, characters may be given the opportunity to <a href = '/help/promoting'>promote</a> to a different class, which increases their strength and abilities in battle. Here are some recommendations on how to promote your starting characters (their first promotions with you will be available at level 11):
            <ul>
                <li><u>Your starting character</u> (which you name), has exceptional stats everywhere. Rounded characters do well in any promotion tree, so feel free to experiment with him.
                <li><u>Crysello</u> has high STR and VIT, so it's best to promote him to a Knight, Savage, or Fighter. Most swordsman classes take the role of tanking (shielding damage) and/or dealing damage
                <li><u>Erik</u> starts with high INT and LUK. High INT and LUK means high damage and lots of critical hits, so it's recommended that you promote him to a Mage.
                <li><u>Leo</u> begins with high STR, but his other stats aren't so great. Since he'll be good at dealing damage, but not taking it, you could make him an Archer so that he can be protected by other people while dealing ranged damage.
                <li><u>Joshua</u> has high VIT and INT, so he'll become a more resilient spellcaster. He'll benefit your team greatly if you promote him to an Empath, which gives your team healing.
                <li><u>Allos</u> is similar to your starting character. All his stats are pretty good, but he has slightly lower stats. Any path for Allos would be fine, but trying out any of the Trainee branches is always an adventure.
                <li><u>Matthew</u> has exceptional STR and LUK. The promotion paths Savage and Thief are both oriented towards dealing lots of damage, so one of these would be best for Matthew.
            </ul>
        <? require '../views/help/back_to_top.ctp'; ?>
        </div>


</div>