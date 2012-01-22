<h2><?= $imbue['Imbue']['name']; ?> - <?= Inflector::humanize($imbue['Imbue']['item_type']); ?> Imbue</h2>

<h3>Mods</h3>
<ul>
    <? foreach($imbue['ImbueMod'] as $mod): ?>
        <li>
            <?
                $amountStr = $mod['min_amount'] != '' ? sprintf('%+.1f to %+.1f', $mod['min_amount'], $mod['max_amount']) : '';
                $durationStr = $mod['min_duration'] != '0.0' ? sprintf(' for %s to %s rounds', $mod['min_duration'], $mod['max_duration']) : '';
                $modName = $mod['BonusType']['name'];
                if ($modName{0} != '%' && $amountStr != '')
                    $modName = ' ' . $modName;

                printf("%s%s%s (%s) (%s)",
                    $amountStr,
                    $modName,
                    $durationStr,
                    $html->link('Edit', array('controller' => 'imbue_mods', 'action' => 'edit', $mod['id'])),
                    $html->link('Delete', array('controller' => 'imbue_mods', 'action' => 'delete', $mod['id']), null, 'Are you sure you want to delete this mod?')
                );
            ?>
        </li>
    <? endforeach; ?>
    <li><?= $html->link('New Mod', array('controller' => 'imbue_mods', 'action' => 'add', $imbue['Imbue']['id'])); ?></li>
</ul>

<div class = "actions">
    <ul>
        <li><?= $html->link('Edit Imbue', array('action' => 'edit', $imbue['Imbue']['id'])); ?> </li>
        <li><?= $html->link('Delete Imbue', array('action' => 'delete', $imbue['Imbue']['id']), null, 'Are you sure you want to delete this imbue?'); ?> </li>
    </ul>
</div>