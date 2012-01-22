<h2>FAQs</h2>

<?= $paginator->counter("%count% FAQs in database."); ?>

<table class = 'data'>
<tr>
    <th><?= $paginator->sort('question');?></th>
    <th><?= $paginator->sort('answer');?></th>
    <th><?= $paginator->sort('path');?></th>
    <th><?= $paginator->sort('category');?></th>
    <th><?= $paginator->sort('link');?></th>
    <th class = "actions">Actions</th>
</tr>
<?php
$i = 0;
foreach ($faqs as $faq):
    $class = null;
    if ($i++ % 2 == 0) {
        $class = ' class="altrow"';
    }
?>
    <tr<?= $class;?>>
        <td>
            <?= $faq['Faq']['question']; ?>
        </td>
        <td>
            <?= $faq['Faq']['answer']; ?>
        </td>
        <td>
            <?= $faq['Faq']['path']; ?>
        </td>
        <td>
            <?= $faq['Faq']['category']; ?>
        </td>
        <td>
            <?= $faq['Faq']['link']; ?>
        </td>
        <td class = "actions">
            <?= $html->link('View', array('action' => 'view', $faq['Faq']['id'])); ?>
            <?= $html->link('Edit', array('action' => 'edit', $faq['Faq']['id'])); ?>
            <?= $html->link('Delete', array('action' => 'delete', $faq['Faq']['id']), null, 'Are you sure you want to delete this faq?'); ?>
        </td>
    </tr>
<?php endforeach; ?>
</table>