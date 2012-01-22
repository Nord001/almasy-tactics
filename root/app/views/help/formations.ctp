<div class = 'PageDiv'>

    <div class = 'PageTitle'>
        <?= $html->link2('Help', array('controller' => 'help', 'action' => 'index')); ?> | Formations
    </div>

    <div class = 'PageContent' style = 'position: relative'>
        <? require '../views/help/navbar.ctp'; ?>

        <div class = 'HelpPageHeader'>Managing Formations</div>

        <div class = 'HelpIntro'>
            Formations are teams of up to seven characters that you can arrange into a 3x4 square. The positioning of your characters into formations is extremely important, because your arrangement affects how strong your characters are from class buffs and what order they get attacked in. Each formation's strength is estimated by <a href = '/help/powerandrep'>Reputation</a>.
        </div>

        <div class = 'StatHeader'>
            Active and Standby Formations
        </div>
        <div class = 'StatContent'>
            You can only have one active formation at any time. This formation is the one you will attack with when you battle, and the only one that will be attacked by other people. On the other hand, you can have any amount of standby formations. Standby formations can only be used to spar other formations or used later on.
        </div>

        <div class = 'StatHeader'>
            Editing Formations
        </div>
        <div class = 'StatContent'>
            On the Edit page for formations, you can select which characters you want to put in a formation. You can put up to seven characters into one formation, and there's no reason to put less! A character can be in multiple formations. You can also decide whether or not you want your formation to be available for sparring. When you don't want your secret formations to be unveiled, you can disable that.
        </div>

        <div class = 'StatHeader'>
            Organizing Formations
        </div>
        <div class = 'StatContent'>
            The Organize page allows you to arrange characters in the formation. To move a character around, click the character you want to move, then click on the desired spot. The stats of characters are shown next to the formation. These stats will probably be different than the stats that you will see on the View Character page because of the effects of class buffs. The View Character page shows the character's stats by itself, but the Organize page will show the character's stats when affected by the characters around it.
        </div>

        <div class = 'StatHeader'>
            Tips on Organizing Formations
        </div>
        <div class = 'StatContent'>
            Your formation arrangement is <b>really important</b> to your battle strength. Almasy is a game about teamwork between your characters! If you don't pay close attention to how your characters work with each other, your formation will be very weak, even if it has strong individual characters. If you have a Knight that increases the VIT of characters behind it, it'd be a good idea to put other characters behind it so they can benefit from the Knight's bonus. Also, you should put classes with higher HP or defense in the top row (the front row), so that they can absorb damage while your weaker classes like your archers and mages can stay safe.
        </div>

        <? require '../views/help/back_to_top.ctp'; ?>
    </div>
</div>
