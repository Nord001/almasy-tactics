<h1>Classes</h1>

<form>
    <input type = 'text' id = 'ClassSearchInput' />
    <input type = 'submit' style = 'width: auto; height: auto; font-size: 10pt;' value = 'Quick Find' id = 'ClassSearchButton' />
</form>

<script type = 'text/javascript'>
    var url = '<?= $html->url(array('controller' => 'c_classes', 'action' => 'view')); ?>';
    $(document).ready(function() {
        $('#ClassSearchButton').click(function(event) {
            event.preventDefault();
            window.location = url + '/' + $('#ClassSearchInput').val();
        });

        $('#ClassSearchInput').autocomplete({
            source: <?= json_encode($classNames); ?>,
            minLength: 2
        });
    });
</script>

<?= $paginator->counter("%count% classes in database."); ?>

<table class = 'data'>
<tr class = 'header'>
    <th><?= $paginator->sort('name'); ?></th>
    <th><?= $paginator->sort('+STR', 'growth_str'); ?></th>
    <th><?= $paginator->sort('+INT', 'growth_int'); ?></th>
    <th><?= $paginator->sort('+VIT', 'growth_vit'); ?></th>
    <th><?= $paginator->sort('+LUK', 'growth_luk'); ?></th>
    <th><?= $paginator->sort('Range', 'max_range'); ?></th>
    <th><?= $paginator->sort('Speed', 'speed'); ?></th>
    <th><?= $paginator->sort('Bonus', 'bonus_name'); ?></th>
    <th>Promote Class 1</th>
    <th>Promote Class 2</th>
    <th>Promote Class 3</th>
    <th>Promote Class 4</th>
    <th class = 'actions'>Actions</th>
</tr>
<?php
$i = 0;
foreach ($classes as $class):
    $css = null;
    if ($i++ % 2 == 0) {
        $css = ' class = "altrow"';
    }
?>
    <tr<?= $css;?>>
        <td><?= $html->link($class['CClass']['name'], array('controller' => 'c_classes', 'action' => 'view', $class['CClass']['id'])); ?></td>
        <td><?= $class['CClass']['growth_str']; ?></td>
        <td><?= $class['CClass']['growth_int']; ?></td>
        <td><?= $class['CClass']['growth_vit']; ?></td>
        <td><?= $class['CClass']['growth_luk']; ?></td>
        <td>
            <?= $class['CClass']['min_range']; ?>-<?= $class['CClass']['max_range']; ?>
        </td>
        <td><?= $class['CClass']['speed']; ?></td>
        <td><?= $class['CClass']['bonus_name'] ? $class['CClass']['bonus_name'] : ''; ?></td>
        <td>
            <?
                if ($class['CClass']['promote_class_1_id']) {
                    echo $html->link($class['CClass1']['name'], array('controller' => 'c_classes', 'action' => 'view', $class['CClass1']['id']));
                    echo sprintf(' (Level %s)', $class['CClass']['promote_class_1_level']);
                } else {
                    echo 'None';
                }
            ?>
        </td>
        <td>
            <?
                if ($class['CClass']['promote_class_2_id']) {
                    echo $html->link($class['CClass2']['name'], array('controller' => 'c_classes', 'action' => 'view', $class['CClass2']['id']));
                    echo sprintf(' (Level %s)', $class['CClass']['promote_class_2_level']);
                } else {
                    echo 'None';
                }
            ?>
        </td>
        <td>
            <?
                if ($class['CClass']['promote_class_3_id']) {
                    echo $html->link($class['CClass3']['name'], array('controller' => 'c_classes', 'action' => 'view', $class['CClass3']['id']));
                    echo sprintf(' (Level %s)', $class['CClass']['promote_class_3_level']);
                } else {
                    echo 'None';
                }
            ?>
        </td>
        <td>
            <?
                if ($class['CClass']['promote_class_4_id']) {
                    echo $html->link($class['CClass4']['name'], array('controller' => 'c_classes', 'action' => 'view', $class['CClass4']['id']));
                    echo sprintf(' (Level %s)', $class['CClass']['promote_class_4_level']);
                } else {
                    echo 'None';
                }
            ?>
        </td>
        <td class = "actions">
            <?= $html->link('View', array('action' => 'view', $class['CClass']['id'])); ?>
            <?= $html->link('Edit', array('action' => 'edit', $class['CClass']['id'])); ?>
            <? $html->link('Delete', array('action' => 'delete', $class['CClass']['id']), null, 'Are you sure you want to delete this class?'); ?>
        </td>
    </tr>
<?php endforeach; ?>
</table>
