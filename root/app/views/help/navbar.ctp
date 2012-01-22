<? $sat = 70; $light = 74; $light2 = intval($light * 0.85); ?>
<style type = 'text/css'>
    .BasicsDiv {
        left: 0px;
    }

    .BasicsDivContent {
        background-color: hsl(0, <?= $sat; ?>%, <?= $light; ?>%);
        <? GradientBackground(array(
            array(0, 'hsl(0, ' . $sat . '%, ' . $light . '%)'),
            array(0.8, 'hsl(10, ' . $sat . '%, ' . $light2 . '%)')
        )); ?>
    }

    .CharactersDiv {
        left: 195px;
    }

    .CharactersDivContent {
        background-color: hsl(40, <?= $sat; ?>%, <?= $light; ?>%);
        <? GradientBackground(array(
            array(0, 'hsl(50, ' . $sat . '%, ' . $light . '%)'),
            array(0.8, 'hsl(70, ' . $sat . '%, ' . $light2 . '%)')
        )); ?>
    }

    .ItemsDiv {
        left: 390px;
    }

    .ItemsDivContent {
        background-color: hsl(<?= $light; ?>, <?= $sat; ?>%, <?= $light; ?>%);
        <? GradientBackground(array(
            array(0, 'hsl(100, ' . $sat . '%, ' . $light . '%)'),
            array(0.8, 'hsl(120, ' . $sat . '%, ' . $light2 . '%)')
        )); ?>
    }

    .FormationsDiv {
        left: 585px;
    }

    .FormationsDivContent {
        background-color: hsl(120, <?= $sat; ?>%, <?= $light; ?>%);
        <? GradientBackground(array(
            array(0, 'hsl(200, ' . $sat . '%, ' . $light . '%)'),
            array(0.8, 'hsl(220, ' . $sat . '%, ' . $light2 . '%)')
        )); ?>
    }

    .BattleDiv {
        left: 780px;
    }

    .BattleDivContent {
        background-color: hsl(160, <?= $sat; ?>%, <?= $light; ?>%);
        <? GradientBackground(array(
            array(0, 'hsl(240, ' . $sat . '%, ' . $light . '%)'),
            array(0.8, 'hsl(260, ' . $sat . '%, ' . $light2 . '%)')
        )); ?>
    }
</style>

<div class = 'HelpDiv BorderDiv BasicsDiv'>
    <div class = 'HelpDivContent BasicsDivContent'>
        <div class = 'HelpHeader'>
            Basics
        </div>
        <ul>
            <li><?= $html->link2('What is Almasy?', '/help/getting_started'); ?></li>
            <li><?= $html->link2('Starting Out', '/help/firststeps'); ?></li>
            <li><?= $html->link2('Income', '/help/income'); ?></li>
            <li><?= $html->link2('Elements', '/help/elements'); ?></li>
            <li><?= $html->link2('Hotkeys', '/help/hotkeys'); ?></li>
            <li><?= $html->link2('FAQs', '/faqs'); ?></li>
        </ul>
    </div>
</div>
<div class = 'HelpDiv BorderDiv CharactersDiv'>
    <div class = 'HelpDivContent CharactersDivContent'>
        <div class = 'HelpHeader'>
            Characters
        </div>
        <ul>
            <li><?= $html->link2('Character Basics', '/help/char_basics'); ?></li>
            <li><?= $html->link2('Stats', '/help/stats'); ?></li>
            <li><?= $html->link2('Substats', '/help/substats'); ?></li>
            <li><?= $html->link2('Promoting', '/help/promoting'); ?></li>
            <li><?= $html->link2('Recruiting', '/help/recruiting'); ?></li>
            <li><?= $html->link2('Class List', '/help/class_list'); ?></li>
        </ul>
    </div>
</div>
<div class = 'HelpDiv BorderDiv ItemsDiv'>
    <div class = 'HelpDivContent ItemsDivContent'>
        <div class = 'HelpHeader'>
            Items
        </div>
        <ul>
            <li><?= $html->link2('Item Basics', '/help/items'); ?></li>
            <li><?= $html->link2('Refining', '/help/refining'); ?></li>
            <li><?= $html->link2('Imbuing', '/help/imbuing'); ?></li>
            <li><?= $html->link2('Imbue List', '/help/imbue_list'); ?></li>
        </ul>
    </div>
</div>
<div class = 'HelpDiv BorderDiv FormationsDiv'>
    <div class = 'HelpDivContent FormationsDivContent'>
        <div class = 'HelpHeader'>
            Formations
        </div>
        <ul>
            <li><?= $html->link2('Formation Basics', '/help/formations'); ?></li>
            <li><?= $html->link2('Organizing', '/help/organize'); ?></li>
            <li><?= $html->link2('Reputation', '/help/powerandrep'); ?></li>
            <li><?= $html->link2('AI Strategy', '/help/ai'); ?></li>
        </ul>
    </div>
</div>
<div class = 'HelpDiv BorderDiv BattleDiv'>
    <div class = 'HelpDivContent BattleDivContent'>
        <div class = 'HelpHeader'>
            Battle
        </div>
        <ul>
            <li><?= $html->link2('Battle Basics', '/help/battle_basics'); ?></li>
            <li><?= $html->link2('Bounty', '/help/bounty'); ?></li>
            <li><?= $html->link2('Battle Mechanics', '/help/battle_mechanics'); ?></li>
        </ul>
    </div>
</div>

<div style = 'height: 205px'></div>
