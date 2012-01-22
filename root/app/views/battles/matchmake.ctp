<div class = 'PageDiv'>
    <div class = 'PageTitle'>
        <?= $html->link2('War Room', array('controller' => 'battles', 'action' => 'index')); ?> |
        Matchmake
    </div>

    <form id = 'FightForm' method = 'POST' action = '<?= $html->url(array('controller' => 'battles', 'action' => 'fight')); ?>'>
        <?= $form->hidden('csrf_token', array('name' => 'data[csrf_token]', 'value' => $a_user['User']['csrf_token'])); ?>
        <input type = 'hidden' name = 'data[Formation][target_id]' value = '' />
        <input type = 'hidden' name = 'data[Formation][formation_id]' value = '<?= $activeFormation['Formation']['id']; ?>' />
        <input type = 'hidden' name = 'data[Battle][battle_type]' value = 'battle' />
    </form>

    <div class = 'PageContent'>
        <div>
            Entering battle with <b><?= $activeFormation['Formation']['name']; ?></b>,
            reputation <b><?= $activeFormation['Formation']['reputation']; ?></b>,
            bounty <b><?= $activeFormation['Formation']['bounty']; ?></b>.
        </div>

        <? if (count($targetFormations) > 0): ?>
            The following formations have been selected for you to attack. Choose a target.
            <table>
                <? for ($row = 0; $row < count($targetFormations) / 2; $row++): ?>
                    <tr>
                        <? for ($col = 0; $col < 2; $col++): ?>
                            <?
                                $index = $row * 2 + $col;
                                if ($index >= count($targetFormations))
                                    break;
                                $formation = $targetFormations[$index];
                            ?>
                            <td>
                                <div
                                    class = 'MatchmakingTarget rounded-corners'
                                    style = 'border: 1px solid rgb(75, 75, 75); margin-bottom: 10px; margin-right: 10px; padding: 2px; background-color: rgb(255, 220, 180); width: 400px;'
                                    formationId = '<?= $formation['Formation']['id']; ?>'
                                >
                                    <div style = 'font-size: 140%; border-bottom: 1px dotted;'>
                                        <?= $formation['Formation']['name']; ?>
                                    </div>
                                    <div style = 'font-size: 120%'>
                                        Reputation: <?= $formation['Formation']['reputation']; ?>
                                        Bounty: <?= $formation['Formation']['bounty']; ?>
                                    </div>
                                    <div>
                                        <table style = 'border: 1px solid;'>
                                            <? for ($y = 0; $y < FORMATION_HEIGHT; $y++): ?>
                                                <tr>
                                                    <? for ($x = 0; $x < FORMATION_WIDTH; $x++): ?>
                                                        <td style = 'width: 40px; height: 40px; border: 1px solid;'>
                                                            <?
                                                                $position = $x + $y * FORMATION_WIDTH;
                                                                $characterIndex = $formation['CharacterFormation'][$position];
                                                                if ($characterIndex != -1)
                                                                    $character = $formation['Characters'][$characterIndex];
                                                            ?>
                                                            <? if ($characterIndex != -1): ?>
                                                                <? if ($characterIndex < MATCHMAKE_NUM_VISIBLE_CHARACTERS): ?>
                                                                    <?
                                                                        $icon = $ui->getFaceIcon($character['CClass']['face_icon']);
                                                                        echo $html->image($icon, array('style' => 'width: 40px; border: 1px solid'));
                                                                    ?>
                                                                <? else: ?>
                                                                    <?= $html->image('question.png', array('style' => 'border: 1px solid')); ?>
                                                                <? endif; ?>
                                                            <? endif; ?>
                                                        </td>
                                                    <? endfor; ?>
                                                </tr>
                                            <? endfor; ?>
                                        </table>
                                    </div>
                                </div>
                            </td>
                        <? endfor; ?>
                    </tr>
                <? endfor; ?>
            </table>
        <? else: ?>
            Sorry, no formations could be found. Please try again later.
        <? endif; ?>
    </div>
</div>

<script type = 'text/javascript'>
    $(document).ready(function() {

        var form = $('#FightForm');

        $('.MatchmakingTarget').matchmakingTarget({
            'onClick': function(obj) {
                form.find(':eq(1)').attr('value', obj.attr('formationId'));
                form.submit();
            }
        });
    });
</script>