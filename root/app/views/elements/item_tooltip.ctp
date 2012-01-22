<? // Because characters have items under "Weapon" and "Armor", we accept just the data array here
   // without the Item wrapper, because it might be "Weapon".

   // To make a tooltip, do something like this:
   // $this->element('item_tooltip', array('display' => 'TYPE', 'userItem' => USER_ITEM_DATA));
   // TYPE can be name or sprite.
?>

<? $userItemId = $userItem['id']; ?>

<?
    //$damageMod = 1;
    //$armor
    //foreach($userItem['ItemMod'] as $mod) {
    //    if ($mod['id'] == ENHANCED_DAMAGE_BONUS_TYPE_ID)
    //        $damageMu
    //}
?>


<div class = 'ItemTooltip' style = 'display: inline; position: relative;'>
    <!--span style = 'background-color: rgb(200, 0, 0); position: absolute;' class = 'ImbueShade rounded-corners'>
    </span-->

    <? if (!empty($userItem['CharacterEquipped']['id'])): ?>
        <span style = 'font-size: 6pt; position: absolute; top: -12px; right: 5px;'>E</span>
    <? endif; ?>

    <?
        $color = '';
        if ($userItem['rarity'] == 'imbued')
            $color = 'rgb(255, 215, 0)';
        else if ($userItem['rarity'] == 'unique')
            $color = 'rgb(200, 0, 240)';
    ?>

    <?
        switch ($display) {
            case 'name':
                printf("<span class = 'ItemName' style = 'color: %s; %s'>", $color, isset($displayStyle) ? $displayStyle : '');
                if ($userItem['refine'] > 0)
                    echo $userItem['refine_prefix'];

                echo $userItem['name'];
                echo '</span>';
            break;
            case 'sprite':
                echo $ui->displayItemIcon($userItem['Item']['sprite']);
            break;
        }
    ?>

    <div style = 'display: none'>
        <table>
            <tr>
                <td style = 'font-weight: bold'>
                    <span style = 'color: <?= $color; ?>'>
                        <?= $userItem['refine'] > 0 ? $userItem['refine_prefix'] : ''; ?>
                        <?= $userItem['name']; ?>
                        (<?
                            if (!empty($userItem['Item']['WeaponType']) && $userItem['Item']['WeaponType']['id'] != '')
                                echo $userItem['Item']['WeaponType']['name'];
                            else if (!empty($userItem['Item']['ArmorType']) && $userItem['Item']['ArmorType']['id'] != '')
                                echo $userItem['Item']['ArmorType']['name'] . ' Armor';
                        ?>)
                    </span>
                </td>
            </tr>

            <? if (!empty($userItem['CharacterEquipped']['id'])): ?>
                <tr>
                    <td>
                        Equipped to <b><?= $userItem['CharacterEquipped']['name']; ?></b>
                    </td>
                </tr>
            <? endif; ?>
            <tr>
                <td>Requires Lv. <b><?= $userItem['Item']['req_lvl']; ?></b></td>
            </tr>

            <!-- Weapon Data -->
            <? if (!empty($userItem['Item']['WeaponType']) && $userItem['Item']['WeaponType']['id'] != ''): ?>
                <tr>
                    <td>
                        <?
                            $class = '';
                            if ($userItem['refine'] > 0)
                                $class = 'item-mod';
                        ?>
                        <span style = 'font-weight: bold;' class = '<?= $class; ?>'>
                            <?= $userItem['Item']['attack']; ?>
                            <?
                                if ($userItem['refine'] > 0)
                                    echo ' + ' . $userItem['refine_bonus'];
                            ?>
                        </span> Attack
                    </td>
                </tr>
                <tr>
                    <td>Strikes <b><?= $userItem['Item']['strikes']; ?></b> Time<?= $userItem['Item']['strikes'] == 1 ? '' : 's'; ?></td>
                </tr>
                <? if ($userItem['Item']['critical'] != 0): ?>
                    <tr>
                        <td><b><? printf("%+d", $userItem['Item']['critical']); ?></b> Critical</td>
                    </tr>
                <? endif; ?>
            <? endif; ?>

            <!-- Armor Data -->
            <? if (!empty($userItem['Item']['ArmorType']) && $userItem['Item']['ArmorType']['id'] != ''): ?>
                <?
                    $class = '';
                    if ($userItem['refine'] > 0)
                        $class = 'item-mod';
                ?>
                <tr>
                    <td>Defense:
                        <b>
                            <span class = '<?= $class; ?>'><?= $userItem['Item']['phys_reduction'] + $userItem['refine_bonus']; ?></span>%
                            <?= sprintf("%+d", $userItem['Item']['phys_defense']); ?>
                        </b>
                    </td>
                </tr>
                <tr>
                    <td>Magic Defense:
                        <b>
                            <span class = '<?= $class; ?>'>
                                <?= $userItem['Item']['mag_reduction'] + $userItem['refine_bonus']; ?></span>%
                            <?= sprintf("%+d", $userItem['Item']['mag_defense']); ?>
                        </b>
                    </td>
                </tr>
                <? $speedMod = $userItem['Item']['ArmorType']['speed_mod']; ?>
                <? if ($speedMod != 0): ?>
                    <tr>
                        <td>

                                Speed
                                <?
                                    if ($speedMod > 0)
                                        echo 'Boost';
                                    elseif ($speedMod < 0)
                                        echo 'Penalty';
                                ?>: <b><? printf("%+d", $userItem['Item']['ArmorType']['speed_mod']); ?> Speed</b>
                        </td>
                    </tr>
                <? endif; ?>
            <? endif; ?>

            <!-- Mods -->
            <? foreach($userItem['ItemMod'] as $mod): ?>
                <tr>
                    <td style = 'font-weight: bold'>
                    <?
                        $color = $mod['amount'] >= 0 ? 'rgb(75, 225, 75)' : 'rgb(225, 75, 75)';

                        $amountStr = '';
                        if ($mod['amount'] != '0.0') {
                            if ($mod['amount'] == intval($mod['amount']))
                                $amountStr = sprintf('%+d', $mod['amount']);
                            else
                                $amountStr = sprintf('%+.1f', $mod['amount']);
                        }

                        $durationStr = $mod['duration'] != '' ? sprintf(' for %s round%s', $mod['duration'], $mod['duration'] == 1 ? '' : 's') : '';

                        // If mod name isn't a percent, like Dodge, add a space so it's +5 Dodge, for example.
                        $modName = $mod['BonusType']['name'];
                        if ($modName{0} != '%')
                            $modName = ' ' . $modName;

                        printf("<span style = 'color: %s'>%+s%s%s</span>",
                            $color,
                            $amountStr,
                            $modName,
                            $durationStr
                        );
                    ?>
                    </td>
                </tr>
            <? endforeach; ?>
        </table>
    </div>
</div>