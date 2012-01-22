<div style = 'padding: 5px'>
    Click on a character to select it. Then click on another character to swap positions or on an empty space to move the character to that location.
</div>

<div style = 'position: relative; height: 470px;'>
    <div style = 'width: 290px; text-align: center; font-weight: bold;'>
        Front
    </div>
    <table id = 'FormationTable' formationId = '<?= $formation['Formation']['id']; ?>'>
        <? for ($y = 0; $y < FORMATION_HEIGHT; $y++): ?>
            <tr>
                <? for ($x = 0; $x < FORMATION_WIDTH; $x++): ?>
                        <?
                            $position = $x + $y * FORMATION_WIDTH;
                            $characterIndex = $formation['CharacterFormation'][$position];
                            if ($characterIndex != -1)
                                $character = $formation['Characters'][$characterIndex];
                        ?>
                    <td class = 'FormationCell FormationRow<?= $y; ?>'
                        position = '<?= $position; ?>'
                        <?= $characterIndex != -1 ? sprintf("characterId = '%s'", $character['Character']['id']) : ''; ?>
                        style = 'border: 1px solid; width: 70px; height: 75px; text-align: center;'>

                        <? if ($characterIndex != -1): ?>
                            <div style = 'position: relative; width: 65px; text-align: center;'>
                                <div style = 'position: absolute; top: 56px; left: 3px; font-size: 50%;'>
                                    <?= $character['CClass']['face_icon'] == '' ? $character['CClass']['name'] : ''; ?>
                                </div>
                                <div style = 'position: absolute; top: 0px; right: 0px; font-size: 80%; font-weight: bold;'>
                                    <?= $character['Character']['level']; ?>
                                </div>
                                <?= $ui->displayFaceIcon($character['CClass']['face_icon'], 'width: 65px'); ?>
                                <div style = 'text-align: center; width: 70px; font-size: 70%; height: 15px; overflow: hidden;'>
                                    <?= $character['Character']['name']; ?>
                                </div>
                            </div>

                            <? if ($character['CClass']['bonus_name'] != ''): ?>
                                <div class = 'BonusTooltip'>
                                    <div style = 'font-weight: bold'>
                                        <?= $character['CClass']['bonus_name']; ?>
                                    </div>
                                    <table style = 'margin: 2px'>
                                        <tr>
                                            <td style = 'vertical-align: middle'>
                                                <div style = 'width: 100px'>
                                                    <? $ui->displayBonusGrid ($character); ?>
                                                </div>
                                            </td>
                                            <td style = 'vertical-align: top'>
                                                <?= htmlspecialchars_decode($ui->replaceBonusDesc($character['CClass']['bonus_description'], $character)); ?>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            <? endif; ?>
                        <? endif; ?>
                    </td>
                <? endfor; ?>
            </tr>
        <? endfor; ?>
    </table>
    <div style = 'width: 290px; text-align: center; font-weight: bold;'>
        Back
    </div>

    <div id = 'FormationStatsDiv'>
        <div style = 'width: 100%; text-align: center; font-size: 110%;'>
            <a href = '#' id = 'StatsLessLink' style = 'font-weight: bold'>Less</a> | <a href = '#' id = 'StatsMoreLink'>More</a>
        </div>
        <table>
            <? for ($i = 0; $i < count($formation['Characters']); $i++): ?>
                <? $character = $formation['Characters'][$i]; ?>
                <? if ($i % 2 == 0) echo '<tr>'; ?>

                <td class = '<?= (($i + 3) / 2) % 2 == 0 ? 'AltCell' : ''; ?>'>
                    <div class = 'CharacterStatDiv rounded-corners'>
                        <div class = 'CharacterStatViewMore' style = 'display: none'>
                            <div class = 'CharacterStatHeader'>
                                <div class = "CharacterStatName">
                                    <?= $ui->displayFaceIcon($character['CClass']['face_icon'], 'width: 15px'); ?>
                                    <?= $character['Character']['name']; ?>
                                </div>
                                <div class = "CharacterStats">
                                    <?
                                        printf('S: %s V: %s I: %s L: %s',
                                            intval($character['CharactersFormation']['Stats']['str']),
                                            intval($character['CharactersFormation']['Stats']['vit']),
                                            intval($character['CharactersFormation']['Stats']['int']),
                                            intval($character['CharactersFormation']['Stats']['luk'])
                                        );
                                    ?>
                                </div>
                            </div>

                            <?
                                $damageString = '';
                                if ($character['CharactersFormation']['Stats']['meleeAtk'] != $character['CharactersFormation']['Stats']['rangedAtk'])
                                    $damageString = sprintf('%s/%s', intval($character['CharactersFormation']['Stats']['meleeAtk']), intval($character['CharactersFormation']['Stats']['rangedAtk']));
                                else
                                    $damageString = intval($character['CharactersFormation']['Stats']['meleeAtk']);

                                $damageString .= 'x' . $character['CharactersFormation']['Stats']['numStrikes'];

                                $rangeString = '';
                                if ($character['CharactersFormation']['Stats']['minRange'] == $character['CharactersFormation']['Stats']['maxRange'])
                                    $rangeString = $character['CharactersFormation']['Stats']['minRange'] == 1 ? 'Melee' : $character['CharactersFormation']['Stats']['minRange'];
                                else
                                    $rangeString = sprintf('%s-%s', $character['CharactersFormation']['Stats']['minRange'], $character['CharactersFormation']['Stats']['maxRange']);

                                $pd = sprintf("%s%%%+d",
                                    intval($character['CharactersFormation']['Stats']['physReduction']),
                                    $character['CharactersFormation']['Stats']['physDefense']
                                );
                                $md = sprintf("%s%%%+d",
                                    intval($character['CharactersFormation']['Stats']['magReduction']),
                                    $character['CharactersFormation']['Stats']['magDefense']
                                );
                            ?>
                            <table>
                                <tr>
                                    <td>HP: <?= intval($character['CharactersFormation']['Stats']['maxHp']); ?></td>
                                    <td>RGN: <?= intval($character['CharactersFormation']['Stats']['hpRegen']); ?>%</td>
                                    <td>PD: <?= $pd; ?></td>
                                </tr>
                                <tr>
                                    <td>DMG: <?= $damageString; ?></td>
                                    <td>SPD: <?= $character['CharactersFormation']['Stats']['speed']; ?></td>
                                    <td>MD: <?= $md; ?></td>
                                </tr>
                                <tr>
                                    <td>RNG: <?= $rangeString; ?></td>
                                    <td>CRT: <?= intval($character['CharactersFormation']['Stats']['crit']); ?>%</td>
                                    <td>DGE: <?= intval($character['CharactersFormation']['Stats']['luckyDodge']); ?>%</td>
                                </tr>
                            </table>
                        </div>
                        <div class = 'CharacterStatViewLess'>
                            <div class = 'CharacterStatHeader'>
                                <div class = "CharacterStatName" style = 'font-size: 120%'>
                                    <?= $ui->displayFaceIcon($character['CClass']['face_icon'], 'width: 15px;'); ?>
                                    <?= $character['Character']['name']; ?>
                                </div>
                            </div>
                            <table style = 'font-size: 130%; margin-top: 3px;'>
                                <tr>
                                    <td style = 'width: 60%'><?= $html->image('Less - HP.png', array('title' => 'Hit Points', 'class' => 'LessIcon')); ?><?= intval($character['CharactersFormation']['Stats']['maxHp']); ?></td>
                                    <td><?= $html->image('Less - Range.png', array('title' => 'Range', 'class' => 'LessIcon')); ?><?= $rangeString; ?></td>
                                </tr>
                                <tr>
                                    <td><?= $html->image('Less - Damage.png', array('title' => 'Damage', 'class' => 'LessIcon')); ?><?= $damageString; ?></td>
                                    <td><?= $html->image('Less - Crit.png', array('title' => 'Critical', 'class' => 'LessIcon')); ?><?= intval($character['CharactersFormation']['Stats']['crit']); ?>%</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </td>
                <? if ($i % 2 == 1) echo '</tr>'; ?>
            <? endfor; ?>
        </table>
    </div>
</div>

<script type = 'text/javascript'>
    $(document).ready(function() {

        var lessMore = new LessMoreView({
            'name': 'FormationLessMore',
            'lessLink': '#StatsLessLink',
            'moreLink': '#StatsMoreLink',
            'lessView': '.CharacterStatViewLess',
            'moreView': '.CharacterStatViewMore',
        });

        var selectedCell = null;
        var changeTriggered = false;

        var tooltips = $('div[class=BonusTooltip]');

        for (var i = 0; i < tooltips.length; i++) {
            var td = $(tooltips[i]).closest('td').get(0);

            var div = tooltips[i];

            // Wire up tooltip so that hovering over the span shows the div
            AddTooltip(td, div, false);
        }

        // Setup formation dragging
        $('.FormationCell').click(function() {
            if (changeTriggered)
                return;

            if (selectedCell == null) {
                // Select
                if ($(this).attr('characterId') != null) {
                    selectedCell = this;
                    $(selectedCell).css('background-color', 'rgb(100, 100, 240)');
                }
            } else if (selectedCell == this) {
                // Deselect
                $(selectedCell).css('background-color', 'inherit');
                selectedCell = null;
            } else {
                // Trigger change
                changeTriggered = true;
                $(this).css('background-color', 'rgb(240, 100, 100)');

                var formationId = $('#FormationTable').attr('formationId');
                var characterId = $(selectedCell).attr('characterId');
                var endPosition = $(this).attr('position');
                ShowLoadAnim();
                $.post(
                    '/formations/move_character',
                    {
                        formationId: formationId,
                        characterId: characterId,
                        endPosition: endPosition
                    },
                    function (data) {
                        HideLoadAnim();
                        if (data == '<?= AJAX_ERROR_CODE; ?>') {
                            alert('An error has occurred.');
                            return;
                        }

                        $('#FormationViewPage').html(data);
                    }
                );


                $(this).animate({ opacity: 0 }, 500);
            }
        });
    });
</script>