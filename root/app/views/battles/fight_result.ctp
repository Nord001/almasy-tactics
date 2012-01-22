<?
    define('CELL_HEIGHT', 140);
    define('CELL_WIDTH_SPACING', 135); // Distance from left edge of one cell to next
    define('CELL_HEIGHT_SPACING', 150);
?>

<style type = 'text/css'>

#AttackingFormation {
    position: absolute;
    width: 400px;
    top: 35px;
    left: 10px;
}

#DefendingFormation {
    position: absolute;
    top: 35px;
    right: 10px;
    width: 400px;
}

.FormationTable {
    width: 100%;
}

.CharacterCell {
    font-weight: bold;
}

.FormationCell, .HpBar {
    position: absolute;
    width: 120px;
    height: <?= CELL_HEIGHT; ?>px;
}

.FormationCell {
    border: 1px solid rgb(0, 0, 0);
    text-align: center;
    font-size: 80%;
}

.HpBar {
    width: 121px;
    height: <?= CELL_HEIGHT + 1; ?>px;
    border-top: 1px solid;
}

#MessageArea {
    margin-left: 10px;
    margin-top: 620px;
    background-color: rgb(0, 0, 0);
    color: rgb(255, 255, 255);
    width: 700px;
    height: 130px;
    font-family: 'tahoma';
    padding: 3px;
    padding-left: 10px;
    border: 1px dotted rgb(255, 255, 255);
    overflow: auto;
}

#MessageTitle {
    font-size: 150%;
}

#Messages {
}

#ButtonArea {
    position: absolute;
    top: 10px;
    left: 50%;
    margin-left: -175px;
    width: 350px;
    text-align: center;
}

#ResultsDiv {
    padding: 10px;
    margin-top: 10px;
}

</style>

<div class = 'PageDiv'>

    <div class = 'PageTitle'>
        <?= $html->link2('War Room', array('controller' => 'battles', 'action' => 'index')); ?> |
        <?= $battle['Battle']['attacker_formation_name']; ?> vs. <?= $battle['Battle']['defender_formation_name']; ?>
    </div>

    <div class = 'PageContent'>
        <div id = 'ButtonArea'>
            <input type = 'button' value = '|<<' id = 'FirstButton' />
            <input type = 'button' value = '<<<' id = 'FastPrevButton' />
            <input type = 'button' value = '<<' id = 'PrevButton' />
            <input type = 'button' value = 'Play' id = 'PlayButton' />
            <input type = 'button' value = '>>' id = 'NextButton' />
            <input type = 'button' value = '>>>' id = 'FastNextButton' />
            <input type = 'button' value = '>>|' id = 'LastButton' />
        </div>

        <div id = 'MessageArea' class = 'rounded-corners'>
            <div id = 'MessageTitle'>
            </div>

            <div id = 'Messages'>
            </div>
        </div>

        <div id = 'AttackingFormation'>
            <div style = 'position: relative'>
                <? for ($x = 0; $x < FORMATION_WIDTH; $x++): ?>
                    <? for ($y = 0; $y < FORMATION_HEIGHT; $y++): ?>
                            <?
                                $position = $x + $y * FORMATION_WIDTH;
                                if (isset($battleInfo['attacker'][$position]))
                                    $character = $battleInfo['attacker'][$position];
                                else
                                    $character = false;

                                $top = $x * CELL_HEIGHT_SPACING;
                                $left = (FORMATION_HEIGHT - $y - 1) * CELL_WIDTH_SPACING;
                            ?>

                        <div
                            class = 'CharacterCell'
                            <?= $character ? sprintf("characterId = '%s'", $character['id']) : ''; ?>
                        >
                            <div style = 'position: absolute; left: <?= $left; ?>px; top: <?= $top; ?>px;'>
                                <div style = 'position: relative'>
                                    <div class = 'HpBar rounded-corners'></div>
                                </div>
                            </div>

                            <div class = 'FormationCell rounded-corners' style = 'left: <?= $left; ?>px; top: <?= $top; ?>px;'>

                                <? if ($character): ?>
                                    <div style = 'margin-top: 2px; width: 100px; margin-left: auto; margin-right: auto; position: relative;'>
                                        <div style = 'position: absolute; bottom: 0px; left: 3px; font-size: 80%;'>
                                            <?= $character['icon'] == '' ? $character['class'] : ''; ?>
                                        </div>
                                        <div style = 'position: absolute; top: 0px; right: 0px; font-weight: bold;'>
                                            <?= $character['level']; ?>
                                        </div>
                                        <?= $ui->displayFaceIcon($character['icon']); ?>
                                    </div>
                                    <span class = 'CharacterName'><?= $character['name']; ?></span> <br />
                                    <span class = 'CharacterHp'></span> /
                                    <span class = 'CharacterMaxHp'></span>
                                <? endif; ?>
                            </div>
                        </div>
                    <? endfor; ?>
                <? endfor; ?>
            </div>
        </div>

        <div id = 'DefendingFormation'>
            <div style = 'position: relative'>
                <? for ($x = 0; $x < FORMATION_WIDTH; $x++): ?>
                    <? for ($y = 0; $y < FORMATION_HEIGHT; $y++): ?>
                            <?
                                $position = $x + $y * FORMATION_WIDTH;
                                if (isset($battleInfo['defender'][$position]))
                                    $character = $battleInfo['defender'][$position];
                                else
                                    $character = false;

                                $top = $x * CELL_HEIGHT_SPACING;
                                $left = $y * CELL_WIDTH_SPACING;
                            ?>

                        <div
                            class = 'CharacterCell'
                            <?= $character ? sprintf("characterId = '%s'", $character['id']) : ''; ?>
                        >
                            <div style = 'position: absolute; left: <?= $left; ?>px; top: <?= $top; ?>px;'>
                                <div style = 'position: relative'>
                                    <div class = 'HpBar rounded-corners'></div>
                                </div>
                            </div>

                            <div class = 'FormationCell rounded-corners' style = 'left: <?= $left; ?>px; top: <?= $top; ?>px;'>

                                <? if ($character): ?>
                                    <div style = 'margin-top: 2px; width: 100px; margin-left: auto; margin-right: auto; position: relative;'>
                                        <div style = 'position: absolute; bottom: 0px; left: 3px; font-size: 80%;'>
                                            <?= $character['icon'] == '' ? $character['class'] : ''; ?>
                                        </div>
                                        <div style = 'position: absolute; top: 0px; right: 0px; font-weight: bold;'>
                                            <?= $character['level']; ?>
                                        </div>
                                        <?= $ui->displayFaceIcon($character['icon']); ?>
                                    </div>
                                    <span class = 'CharacterName'><?= $character['name']; ?></span> <br />
                                    <span class = 'CharacterHp'></span> /
                                    <span class = 'CharacterMaxHp'></span>
                                <? endif; ?>
                            </div>
                        </div>
                    <? endfor; ?>
                <? endfor; ?>
            </div>
        </div>

        <? if (isset($a_user)): ?>
            <? if ($a_user['User']['id'] == $battle['Battle']['attacker_user_id'] || $a_user['User']['id'] == $battle['Battle']['defender_user_id']): ?>
                <div id = 'ResultsDiv'>
                    <div style = 'font-size: 140%'>Results</div>

                    <?
                        if ($a_user['User']['id'] == $battle['Battle']['attacker_user_id'])
                            echo nl2br($battle['Battle']['attacker_result']);
                        else if ($a_user['User']['id'] == $battle['Battle']['defender_user_id'])
                            echo nl2br($battle['Battle']['defender_result']);
                    ?>
                </div>
            <? endif; ?>
        <? endif; ?>
    </div>
</div>

<script type = 'text/javascript'>
    var cellHeight = <?= CELL_HEIGHT; ?>;

    var messages = [<?= implode(',', $messages); ?>];

    var curMessage = 0;

    function SetHp (hpBar, hp, maxHp, tries) {
        if (tries == null)
            tries = 1;

        var percent = parseFloat(hp) / parseFloat(maxHp);
        if (percent < 0)
            percent = 0;

        var oldPercent = parseFloat(hpBar.attr('curPercent'));
        if (oldPercent) {
            var delta = percent - oldPercent;
            if (delta > 0.02)
                delta = 0.02;
            if (delta < -0.02)
                delta = -0.02;
            if (Math.abs(delta) > 0.015 && tries < 50) {
                percent = oldPercent + delta;
                setTimeout(function() { SetHp(hpBar, hp, maxHp, tries + 1); }, 25);
            }
        }

        hpBar.attr('curPercent', percent);

        var height = cellHeight * percent;

        hpBar.css('height', height + 'px');
        hpBar.css('top', (cellHeight - height) + 'px');

        var characterCell = hpBar.closest('.CharacterCell');
        var formationCell = characterCell.children('.FormationCell');
        var characterName = formationCell.children('.CharacterName');
        var hpSpan = formationCell.children('.CharacterHp');
        var maxHpSpan = formationCell.children('.CharacterMaxHp');
        characterName.css('color', 'rgb(0, 0, 0)');
        characterName.css('text-decoration', 'inherit');
        hpSpan.text(parseInt(hp));
        maxHpSpan.text(parseInt(maxHp));

        if (percent > .6) {
            hpBar.css('background-color', 'rgb(140, 240, 140)');
        } else if (percent > .3) {
            hpBar.css('background-color', 'rgb(240, 240, 130)');
        } else if (percent > 0) {
            hpBar.css('background-color', 'rgb(240, 140, 140)');
        } else {
            /*
            hpBar.css('height', cellHeight + 'px');
            hpBar.css('top', '0px');
            hpBar.css('background-color', 'rgb(75, 0, 0)');
            characterName.css('color', 'rgb(255, 255, 255)');
            */
            characterName.css('text-decoration', 'line-through');
        }
    }

    function Update () {
        var message = messages[curMessage];

        $('#MessageTitle').html(message.title);

        $('#Messages').html('');
        var subMessages = message.messages;
        for (subMessageIndex in subMessages)
            $('#Messages').append(subMessages[subMessageIndex] + '<br />');

        var hps = message.hps;
        for (characterId in hps) {
            var hpStr = hps[characterId];
            var characterCell = $('.CharacterCell[characterId=' + characterId + ']');
            if (characterCell.exists()) {
                var hpBar = characterCell.find('.HpBar');

                var hpSplit = hpStr.split('/');
                if (hpSplit.length == 2) {
                    var hp = hpSplit[0];
                    var maxHp = hpSplit[1];
                    SetHp(hpBar, hp, maxHp);
                }
            }
        }
    }

    var isPlaying = false;
    var playTimeout = null;

    function Play () {
        Update();
        curMessage++;
        if (curMessage < messages.length)
            playTimeout = setTimeout(Play, 600);
    }

    function TogglePlaying () {
        isPlaying = !isPlaying;
        if (isPlaying) {
            Play();
        } else {
            if (playTimeout != null)
                clearTimeout(playTimeout);
        }
    }

    $(document).ready(function() {
        $('#FirstButton').click(function() {
            curMessage = 0;
            Update();
        });
        $('#LastButton').click(function() {
            curMessage = messages.length - 1;
            Update();
        });
        $('#PlayButton').click(function() {
            TogglePlaying();
            if (isPlaying)
                $(this).attr('value', 'Pause');
            else
                $(this).attr('value', 'Play');
        });

        $('#PrevButton').click(function() {
            curMessage--;
            if (curMessage < 0)
                curMessage = 0;
            Update();
        });

        $('#NextButton').click(function() {
            curMessage++;
            if (curMessage >= messages.length)
                curMessage = messages.length - 1;

            Update();
        });

        $('#FastNextButton').click(function() {
            curMessage += 20;
            if (curMessage >= messages.length)
                curMessage = messages.length - 1;

            Update();
        });
        $('#FastPrevButton').click(function() {
            curMessage -= 20;
            if (curMessage < 0)
                curMessage = 0;
            Update();
        });

        Update();
    });
</script>