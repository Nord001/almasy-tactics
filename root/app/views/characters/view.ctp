<?= $html->css('items'); ?>

<script type = 'text/javascript'>
    ActivateItemTooltips();
</script>

<style type = 'text/css'>

#BorderDiv {
    padding: 0px;
    width: 800px;
    padding-bottom: 207px;
    position: relative;
}

#CharacterDiv, #StatsDiv {
    position: absolute;
    margin: 2px;
}

#CharacterDivContent, #StatsDivContent {
    position: relative;
    height: 170px;
}

#CharacterDiv {
    top: 0px;
    left: 0px;
    width: 400px;
}

#CharacterInnerDiv, #StatsInnerDiv, #BuffInnerDiv {
    border: 1px solid;
}

#CharacterInnerDiv {
    background-color: hsl(42, 70%, 80%);
    <? GradientBackground(array(
        array(0, 'hsl(42, 70%, 80%)'),
        array(1, 'hsl(42, 70%, 65%)')
    )); ?>
}

#StatsInnerDiv {
    background-color: hsl(0, 70%, 80%);
    <? GradientBackground(array(
        array(0, 'hsl(0, 70%, 80%)'),
        array(1, 'hsl(0, 70%, 70%)')
    )); ?>
}

#BuffInnerDiv {
    background-color: hsl(221, 60%, 80%);
    <? GradientBackground(array(
        array(0, 'hsl(221, 60%, 80%)'),
        array(1, 'hsl(221, 60%, 65%)')
    )); ?>
}

#StatsDiv {
    top: 0px;
    left: 415px;
    width: 433px;
}

#StatsDivContent .ColumnTd1 {
    padding-left: 5px;
    width: 150px;
}

#StatsDivContent .ColumnTd2 {
    font-weight: bold;
    text-align: right;
    width: 130px;
    padding-right: 5px;
}

#BuffDiv {
    position: relative;
    top: 208px;
    left: 2px;
    width: 848px;
}

.Header {
    border-bottom: 1px dotted;
    font-size: 100%;
    margin: 0px;
    padding: 1px 5px 1px 5px;
    text-indent: 0cm;
    font-weight: bold;
    height: 20px;
}

.BlackTooltip {
    position: absolute;
    display: none;
    border: 1px solid #333;
    border-radius: 3px;
    -moz-border-radius: 3px;
    padding: 4px 8px 4px 8px;
    color: #fff;
    background-color: #000;
    opacity: 0.90;
    font-weight: bold;
}

.PinkTooltip {
    position: absolute;
    display: none;
    border: 1px solid #333;
    border-radius: 3px;
    -moz-border-radius: 3px;
    padding: 4px 8px 4px 8px;
    color: #000;
    background-color: rgb(245, 240, 220);
    opacity: 0.90;
    width: 250px;
    font-weight: bold;
}

</style>

<div class = 'PageDiv'>
    <div class = 'PageTitle'>
        <?= $html->link2('Army', array('controller' => 'army', 'action' => 'index')); ?> | <?= $character['Character']['name']; ?>
    </div>

    <div class = 'PageContent'>
        <div id = 'BorderDiv'>
            <!-- Character Info -->
            <div id = 'CharacterDiv' class = 'BorderDiv'>
                <div id = 'CharacterInnerDiv'>

                    <!-- Header -->
                    <div class = 'Header'>
                        <div style = 'position: relative;'>
                            <span style = 'position: absolute; top: 0px; left: 0px;'>
                                <?= $character['CClass']['name']; ?>
                            </span>
                            <span style = 'position: absolute; top: 0px; left: 325px; width: 60px; text-align: right;'>
                                Lv. <?= $character['Character']['level']; ?>
                            </span>
                            <? if ($character['Character']['level'] < 99): ?>
                                <span style = 'position: absolute; top: 0px; left: 300px;' id = 'ExpSpan'>
                                    <?= intval($character['Character']['exp'] / $character['Character']['total_exp_to_next_level'] * 100) ?> %
                                </span>
                                <div id = 'ExpTooltip' class = 'BlackTooltip' style = 'display: none'>
                                    <?
                                        printf('EXP. %s / %s',
                                            number_format($character['Character']['exp']),
                                            number_format($character['Character']['total_exp_to_next_level'])
                                        );
                                    ?>
                                </div>
                            <? endif; ?>
                        </div>
                    </div>

                    <div id = 'CharacterDivContent'>
                        <div style = 'position: absolute; top: 5px; left: 5px;'>
                            <?= $ui->displayFaceIcon($character['CClass']['face_icon']); ?>
                        </div>
                        <div style = 'position: absolute; top: 18px; left: 260px;'>
                            <? $ui->HelpIcon('GrowthHelpIcon'); ?>
                            <div class = 'Tooltip PinkTooltip'>
                                The numbers below represent <?= $character['Character']['name']; ?>'s growth. When he levels up, his stats will increase or decrease based on these numbers. Growth is
                                based on <?= $character['Character']['name']; ?>'s class. When he promotes, these growths will change depending on which class he promotes to. For example, swordsman classes tend to increase
                                STR and VIT growth, while spellcaster classes tend to increase INT growth. You should plan ahead to shape <?= $character['Character']['name']; ?> into the warrior you want him to be.
                            </div>
                        </div>
                        <div style = 'position: absolute; top: 30px; left: 120px; width: 180px;'>
                            <table style = 'width: 100%'>
                                <?
                                    $stats = array(
                                        array(
                                            'str',
                                            'Strength is a measure of ' . $character['Character']['name'] . '\'s physical fighting power. The higher the strength, the more physical damage he can deal. ' .
                                            $character['Character']['name'] . ' ' . ($character['Character']['growth_str'] > 0 ? 'gains' : 'loses') . ' ' . abs($character['Character']['growth_str']) . ' strength per level.',
                                        ),
                                        array(
                                            'vit',
                                            'Vitality is a measure of ' . $character['Character']['name'] . '\'s endurance. High vitality helps ' .
                                            'him stay alive longer and take less damage from physical attacks. ' .
                                            $character['Character']['name'] . ' ' . ($character['Character']['growth_vit'] > 0 ? 'gains' : 'loses') . ' ' . abs($character['Character']['growth_vit']) . ' vitality per level.',
                                        ),
                                        array(
                                            'int',
                                            'Intelligence is a measure of ' . $character['Character']['name'] . '\'s magical skills. High intelligence means higher magical damage ' .
                                            'and higher defense against magical attacks.' .
                                            $character['Character']['name'] . ' ' . ($character['Character']['growth_int'] > 0 ? 'gains' : 'loses') . ' ' . abs($character['Character']['growth_int']) . ' intelligence per level.',
                                        ),
                                        array(
                                            'luk',
                                            'Luck represents ' . $character['Character']['name'] . '\'s luckiness. High luck increases ' . $character['Character']['name'] . '\'s critical rate and dodge rate. ' .
                                            $character['Character']['name'] . ' ' . ($character['Character']['growth_luk'] > 0 ? 'gains' : 'loses') . ' ' . abs($character['Character']['growth_luk']) . ' luck per level.',
                                        ),
                                    );
                                ?>
                                <? foreach ($stats as $statData): ?>
                                    <? list($stat, $statNameTooltip) = $statData; ?>
                                    <? $change = $character['Character']['Stats'][$stat] - $character['Character'][$stat]; ?>
                                    <tr>
                                        <td class = 'StatNameTd' style = 'font-weight: bold; text-align: center; width: 33%;'>
                                            <?= strtoupper($stat); ?> <? $ui->HelpIcon(); ?>
                                        </td>
                                        <td class = 'StatValueTd' stat = '<?= $stat; ?>' style = 'text-align: center; width: 33%;'>
                                            <? $class = $change != 0 ? 'item-mod' : ''; ?>
                                            <span class = '<?= $class; ?>'>
                                                <?= $ui->displayStat($character['Character']['Stats'][$stat]); ?>
                                            </span>
                                        </td>
                                        <td style = 'text-align: center; width: 33%;'>
                                            <?= $ui->displayGrowth($character['Character']['growth_' . $stat]); ?>
                                            <div class = 'BlackTooltip StatValueTooltip'>
                                                <?
                                                    $statValue = intval($character['Character'][$stat]);
                                                    $change = intval($character['Character']['Stats'][$stat]) - $statValue;
                                                    if ($change >= 0)
                                                        $color = 'color: rgb(25, 125, 25)';
                                                    else
                                                        $color = 'color: rgb(125, 25, 25)';

                                                    printf('Base: %s (<span style = \'%s\'>%+d</span>)',
                                                        intval($character['Character'][$stat]),
                                                        $color,
                                                        $change
                                                    );
                                                ?>
                                            </div>
                                            <div class = 'PinkTooltip StatNameTooltip'>
                                                <?= $statNameTooltip; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <? endforeach; ?>
                            </table>
                        </div>

                        <div style = 'position: absolute; top: 5px; right: 0px;'>
                            <?= $ui->displayAffinitySprite($character['Character']['affinity']); ?>
                        </div>

                        <div style = 'position: absolute; top: 45px; right: 5px;'>
                            <table style = 'text-align: center; margin-left: auto; margin-right: auto;'>
                                <tr>
                                    <td style = 'width: 24px; height: 24px; border: 1px dashed;'>
                                        <? if (!empty($character['Character']['Weapon'])): ?>
                                            <?= $this->element('item_tooltip', array('display' => 'sprite', 'userItem' => $character['Character']['Weapon'])); ?>
                                        <? endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td style = 'width: 24px; height: 24px; border: 1px dashed;'>
                                        <? if (!empty($character['Character']['Armor'])): ?>
                                            <?= $this->element('item_tooltip', array('display' => 'sprite', 'userItem' => $character['Character']['Armor'])); ?>
                                        <? endif; ?>
                                    </td>
                                </tr>
                            </table>
                        </div>

                        <div style = 'position: absolute; top: 110px; left: 5px; width: 103px; text-align: center;'>
                            <span style = 'font-size: 80%'>
                                <?= date('M. j, Y', strtotime($character['Character']['date_created'])); ?>
                            </span>
                        </div>

                        <div style = 'position: absolute; bottom: 5px; left: 5px; width: 103px; text-align: center; <?= @$character['Character']['has_promotions'] ? '' : 'display: none'; ?>'>
                            <? $buttonName = @$character['Character']['can_promote'] ? 'Promote!' : 'Promotions'; ?>
                            <input
                                type = 'button'
                                style = 'width: 100px;'
                                value = '<?= $buttonName; ?>'
                                id = 'PromoteButton'
                                href = '<?= $html->url(array('controller' => 'characters', 'action' => 'change_class', $character['Character']['id'])); ?>'
                            />
                            <script type = 'text/javascript'>
                                $('#PromoteButton').linkButton();
                            </script>
                        </div>

                        <div style = 'position: absolute; bottom: 5px; left: 110px; width: 120px; text-align: center; <?= $character['Character']['has_custom_name'] ? 'display: none' : ''; ?>'>
                            <input
                                type = 'button'
                                style = 'width: 110px;'
                                value = 'Change Name'
                                id = 'ChangeNameButton'
                                href = '<?= $html->url(array('controller' => 'characters', 'action' => 'change_name', $character['Character']['id'])); ?>'
                            />
                            <script type = 'text/javascript'>
                                $('#ChangeNameButton').linkButton();
                            </script>
                        </div>

                        <? if (LEVEL_UP_CHEAT || $a_user['User']['admin'] == 1): ?>
                            <div style = 'position: absolute; right: 5px; bottom: 30px;'>
                                <a id = 'LevelUpLink' href = '#'>Level Up</a>
                            </div>
                        <? endif; ?>

                        <div style = 'position: absolute; bottom: 5px; right: 5px;'>
                            <input type = 'button' class = 'AlarmButton' value = 'Expel' id = 'ExpelButton' />
                            <form id = 'Form_Expel' method = 'POST' action = '<?= $html->url(array('controller' => 'characters', 'action' => 'delete')); ?>'>
                                <?= $form->hidden('csrf_token', array('name' => 'data[csrf_token]', 'value' => $a_user['User']['csrf_token'])); ?>
                                <input type = 'hidden' name = 'data[Character][character_id]' value = '<?= $character['Character']['id']; ?>' />
                            </form>

                            <script type = 'text/javascript'>
                                $('#ExpelButton').click(function(event) {
                                    event.preventDefault();

                                    if (confirm('Are you sure you want to expel this character? They will leave you forever!'))
                                        $('#Form_Expel').submit();
                                });
                            </script>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stats -->
            <div id = 'StatsDiv' class = 'BorderDiv'>
                <div id = 'StatsInnerDiv'>
                    <div class = 'Header'>
                        Stats
                    </div>

                    <div style = 'position: relative;' id = 'StatsDivContent'>
                        <div style = 'position: absolute; top: 50%; margin-top: -85px; width: 100%'>
                            <table style = 'height: 170px;'>
                                <tr>
                                    <td class = 'ColumnTd1'>
                                        HP <? $ui->HelpIcon(); ?>

                                        <? $hp = intval($character['Character']['Stats']['maxHp']); ?>

                                        <div class = 'PinkTooltip Tooltip'>
                                            <?= $character['Character']['name']; ?> has <?= $hp; ?> health points. When he loses all his health points, he dies. HP is increased by VIT.
                                        </div>
                                    </td>
                                    <td class = 'ColumnTd2'><?= $hp; ?></td>

                                    <td class = 'ColumnTd1'>
                                        <? $crit = intval($character['Character']['Stats']['crit']); ?>
                                        Critical <? $ui->HelpIcon(); ?>

                                        <div class = 'PinkTooltip Tooltip'>
                                            When <?= $character['Character']['name']; ?> attacks, he has a <?= $crit; ?>% chance to deal a critical hit, dealing extra damage! Critical is increased by LUK.
                                        </div>
                                    </td>
                                    <td class = 'ColumnTd2'><?= $crit; ?>%</td>
                                </tr>
                                <tr>
                                    <td class = 'ColumnTd1'>
                                        Damage <? $ui->HelpIcon(); ?>
                                        <?
                                            $damageString = '';
                                            $damageTooltip = '';
                                            if ($character['Character']['Stats']['meleeAtk'] != $character['Character']['Stats']['rangedAtk']) {
                                                $damageString = sprintf('%s/%s', intval($character['Character']['Stats']['meleeAtk']), intval($character['Character']['Stats']['rangedAtk']));
                                                $damageTooltip = sprintf(
                                                    '%s can deal up to %s damage to enemies close to him and up to %s damage to enemies far away from him. He ',
                                                    $character['Character']['name'],
                                                    intval($character['Character']['Stats']['meleeAtk']),
                                                    intval($character['Character']['Stats']['rangedAtk'])
                                                );
                                            } else {
                                                $damageString = intval($character['Character']['Stats']['meleeAtk']);
                                                $damageTooltip = sprintf(
                                                    '%s can deal up to %s damage and',
                                                    $character['Character']['name'],
                                                    $damageString
                                                );
                                            }

                                            $strikes = $character['Character']['Stats']['numStrikes'];
                                            if ($strikes < 1)
                                                $strikes = 1;
                                        ?>

                                        <div class = 'PinkTooltip Tooltip'>
                                            <?= $damageTooltip; ?> can attack <?= $strikes; ?> time<?= $strikes > 1 ? 's' : ''; ?> per round.
                                        </div>
                                    </td>
                                    <td class = 'ColumnTd2'>
                                        <? printf('%s x %s', $damageString, $strikes); ?>
                                    </td>

                                    <td class = 'ColumnTd1'>
                                        Dodge <? $ui->HelpIcon(); ?>
                                        <? $dodge = intval($character['Character']['Stats']['luckyDodge']); ?>
                                        <div class = 'PinkTooltip Tooltip'>
                                            <?= $character['Character']['name']; ?> has a <?= $dodge; ?>% chance to avoid an attack, taking no damage. Dodge is increased by LUK.
                                        </div>
                                    </td>
                                    <td class = 'ColumnTd2'><?= $dodge; ?>%</td>
                                </tr>
                                <tr>
                                    <td class = 'ColumnTd1'>
                                        Range <? $ui->HelpIcon(); ?>

                                        <?
                                            $range = '';
                                            $isMelee = false;
                                            if ($character['Character']['Stats']['minRange'] == $character['Character']['Stats']['maxRange']) {
                                                $range = $character['Character']['Stats']['minRange'];
                                                if ($range == 1)
                                                    $isMelee = true;
                                            } else {
                                                $range = sprintf('%s - %s', $character['Character']['Stats']['minRange'], $character['Character']['Stats']['maxRange']);
                                            }
                                        ?>

                                        <div class = 'PinkTooltip Tooltip'>
                                            <? if ($isMelee): ?>
                                                <?= $character['Character']['name']; ?> is a melee attacker. He can only attack enemies close to him. Range is based on <?= $character['Character']['name']; ?>'s class.
                                            <? else: ?>
                                                <?= $character['Character']['name']; ?> is a ranged attacker. He can attack enemies farther away. Range is based on <?= $character['Character']['name']; ?>'s class.
                                            <? endif; ?>
                                        </div>
                                    </td>
                                    <td class = 'ColumnTd2'>
                                        <?= $range; ?>
                                    </td>

                                    <td class = 'ColumnTd1'>
                                        HP Regen <? $ui->HelpIcon(); ?>

                                        <? $regen = intval($character['Character']['Stats']['hpRegen']); ?>

                                        <div class = 'PinkTooltip Tooltip'>
                                            <? if ($regen > 0): ?>
                                                <?= $character['Character']['name']; ?> regenerates <?= $regen; ?>% of his HP per round, which helps him stay alive longer. HP Regen is acquired from items and class abilities.
                                            <? elseif ($regen == 0): ?>
                                                <?= $character['Character']['name']; ?> does not gain or lose any HP per round. HP Regen is acquired from items and class abilities.
                                            <? else: ?>
                                                <?= $character['Character']['name']; ?> loses <?= abs($regen); ?>% of his HP per round, so he will die faster than usual. HP Regen is acquired from items and class abilities.
                                            <? endif; ?>
                                        </div>
                                    </td>
                                    <td class = 'ColumnTd2'><?= $regen; ?>% / Round</td>
                                </tr>
                                <tr>
                                    <td class = 'ColumnTd1'>
                                        Speed <? $ui->HelpIcon(); ?>

                                        <div class = 'PinkTooltip Tooltip'>
                                            Speed determines when your character attacks in a round - the higher the speed, the earlier the character gets to attack. Faster characters can get their
                                            damage in before slower characters, and certain classes are faster than others. Speed is based on <?= $character['Character']['name']; ?><?= "'"; ?>s class. The highest base speed is 40, but you can get higher speeds with buffs and items.
                                        </div>
                                    </td>
                                    <td class = 'ColumnTd2'><?= intval($character['Character']['Stats']['speed']); ?></td>

                                    <td class = 'ColumnTd1'>
                                        Atk. Ele <? $ui->HelpIcon(); ?>

                                        <? $atkElement = AffinityNameFromAffinity($character['Character']['Stats']['attackingAffinity']); ?>

                                        <div class = 'PinkTooltip Tooltip'>
                                            When <?= $character['Character']['name']; ?> attacks, his element is considered to be <?= $atkElement; ?>. Attacking element is affected by items.
                                        </div>
                                    </td>
                                    <td class = 'ColumnTd2'><?= $atkElement; ?></td>
                                </tr>
                                <tr>
                                    <td class = 'ColumnTd1'>
                                        Phys. Def <? $ui->HelpIcon(); ?>

                                        <?
                                            $physReduction = intval($character['Character']['Stats']['physReduction']);
                                            $physDefense = intval($character['Character']['Stats']['physDefense']);
                                            $result = 1000 * (1 - $physReduction / 100) - $physDefense;
                                            if ($result < 1)
                                                $result = 1;
                                            $result = intval($result);
                                        ?>

                                        <div class = 'PinkTooltip Tooltip'>
                                            <?= $character['Character']['name']; ?> reduces all incoming physical damage by <?= $physReduction; ?>%, and then subtracts <?= $physDefense; ?> from the result.
                                            For example, a 1000 damage physical attack would deal only 1000 * (1 - <?= $physReduction; ?>%) - <?= $physDefense; ?> = <?= $result; ?> damage. Physical defense is
                                            increased by items and VIT.
                                        </div>
                                    </td>
                                    <td class = 'ColumnTd2'>
                                        <?= $physReduction; ?>%
                                        <?= sprintf("%+d", $physDefense); ?>
                                    </td>

                                    <td class = 'ColumnTd1'>
                                        Def. Ele <? $ui->HelpIcon(); ?>

                                        <? $defElement = AffinityNameFromAffinity($character['Character']['Stats']['defendingAffinity']); ?>

                                        <div class = 'PinkTooltip Tooltip'>
                                            When <?= $character['Character']['name']; ?> takes damage, his element is considered to be <?= $defElement; ?>. Defending element is affected by items.
                                        </div>
                                    </td>
                                    <td class = 'ColumnTd2'><?= $defElement; ?></td>
                                </tr>
                                <tr>
                                    <td class = 'ColumnTd1'>
                                        Mag. Def <? $ui->HelpIcon(); ?>

                                        <?
                                            $magReduction = intval($character['Character']['Stats']['magReduction']);
                                            $magDefense = intval($character['Character']['Stats']['magDefense']);
                                            $result = 1000 * (1 - $magReduction / 100) - $magDefense;
                                            if ($result < 1)
                                                $result = 1;
                                            $result = intval($result);
                                        ?>

                                        <div class = 'PinkTooltip Tooltip'>
                                            <?= $character['Character']['name']; ?> reduces all incoming magical damage by <?= $magReduction; ?>%, and then subtracts <?= $magDefense; ?> from the result.
                                            For example, a 1000 damage magical attack would deal only 1000 * (1 - <?= $magReduction; ?>%) - <?= $magDefense; ?> = <?= $result; ?> damage. Magical defense is
                                            increased by items and INT.
                                        </div>
                                    </td>
                                    <td class = 'ColumnTd2'>
                                        <?= intval($character['Character']['Stats']['magReduction']); ?>%
                                        <?= sprintf("%+d", $character['Character']['Stats']['magDefense']); ?>
                                    </td>
                                </tr>
                            </table>

                            <div style = 'position: absolute; bottom: 3px; right: 3px'>
                                <table style = 'text-align: center; margin-left: auto; margin-right: auto;'>
                                    <tr>
                                        <? if (isset($character['CClass']['WeaponType'])): ?>
                                            <? foreach ($character['CClass']['WeaponType'] as $type): ?>
                                                <td style = 'width: 24px; height: 24px;'>
                                                    <?= $html->image('sprites/' . $type['sprite'] . '.png', array('title' => 'Uses ' . Inflector::pluralize($type['name']))); ?>
                                                </td>
                                            <? endforeach; ?>
                                        <? endif; ?>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <? $hasAbility = $character['CClass']['bonus_name'] != ''; ?>
            <? if ($hasAbility): ?>
                <div id = 'BuffDiv' class = 'BorderDiv'>
                    <div id = 'BuffInnerDiv'>
                        <!-- Buff -->

                        <div class = 'Header'>
                            <?= $character['CClass']['bonus_name']; ?>
                        </div>
                        <table style = 'margin: 2px'>
                            <tr>
                                <td style = 'vertical-align: middle'>
                                    <? $ui->displayBonusGrid ($character); ?>
                                </td>
                                <td style = 'vertical-align: top'>
                                    <?= htmlspecialchars_decode($ui->replaceBonusDesc($character['CClass']['bonus_description'], $character)); ?>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            <? endif; ?>
        </div>
    </div>
</div>

<script type = 'text/javascript'>
    $(document).ready(function() {
        var exp = $('#ExpSpan');
        if (exp.exists()) {
            AddTooltip(exp, $('#ExpTooltip'), false);
        }

        var growthHelpIcon = $('#GrowthHelpIcon');
        var tooltip = growthHelpIcon.siblings('.Tooltip');
        AddTooltip(growthHelpIcon, tooltip, false);

        var statValueTds = $('td[class=StatValueTd]');
        for (var i = 0; i < statValueTds.length; i++) {
            var td = $(statValueTds[i]);
            var tooltip = td.parent().find('.StatValueTooltip');
            AddTooltip(td, tooltip, false);
        }

        var statHelpIcons = $('#CharacterDiv .HelpIcon');
        for (var i = 0; i < statHelpIcons.length; i++) {
            var icon = $(statHelpIcons[i]);
            var tooltip = icon.parent().parent().find('.StatNameTooltip');
            AddTooltip(icon, tooltip, false);
        }

        var substatHelpIcons = $('#StatsDiv .HelpIcon');
        for (var i = 0; i < substatHelpIcons.length; i++) {
            var icon = $(substatHelpIcons[i]);
            var tooltip = icon.siblings('.Tooltip');

            if (tooltip.exists())
                AddTooltip(icon, tooltip, false);
        }

        $('#LevelUpLink').click(function(event) {
            event.preventDefault();

            var amount = prompt('How many levels?', '1');
            amount = parseInt(amount);
            if (amount != NaN && amount >= 1) {
                window.location = '<?= $html->url(array('controller' => 'characters', 'action' => 'levelup', $character['Character']['id'])); ?>' + '/' + amount;
            }
        });
    });
</script>
