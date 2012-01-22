<?php
/* SVN FILE: $Id: index.ctp 8283 2009-08-03 20:49:17Z gwoo $ */
/**
 *
 * PHP versions 4 and 5
 *
 * CakePHP(tm) :  Rapid Development Framework (http://www.cakephp.org)
 * Copyright 2005-2008, Cake Software Foundation, Inc. (http://www.cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @filesource
 * @copyright     Copyright 2005-2008, Cake Software Foundation, Inc. (http://www.cakefoundation.org)
 * @link          http://www.cakefoundation.org/projects/info/cakephp CakePHP(tm) Project
 * @package       cake
 * @subpackage    cake.cake.console.libs.templates.views
 * @since         CakePHP(tm) v 1.2.0.5234
 * @version       $Revision: 8283 $
 * @modifiedby    $LastChangedBy: gwoo $
 * @lastmodified  $Date: 2009-08-03 13:49:17 -0700 (Mon, 03 Aug 2009) $
 * @license       http://www.opensource.org/licenses/mit-license.php The MIT License
 */
?>
<h2><?= Inflector::humanize(Inflector::underscore($pluralHumanName));?></h2>
<p>
<?= "<?php
echo \$paginator->counter(array(
'format' => 'Page %page% of %pages%, showing %current% records out of %count% total, starting on record %start%, ending on %end%'
));
?>";?>
</p>
<table class = 'data'>
<tr>
<?php  foreach ($fields as $field):?>
    <th><?= "<?= \$paginator->sort('{$field}');?>";?></th>
<?php endforeach;?>
    <th class = "actions">Actions</th>
</tr>
<?php
echo "<?php
\$i = 0;
foreach (\${$pluralVar} as \${$singularVar}):
    \$class = null;
    if (\$i++ % 2 == 0) {
        \$class = ' class=\"altrow\"';
    }
?>\n";
    echo "\t<tr<?= \$class;?>>\n";
        foreach ($fields as $field) {
            $isKey = false;
            if (!empty($associations['belongsTo'])) {
                foreach ($associations['belongsTo'] as $alias => $details) {
                    if ($field === $details['foreignKey']) {
                        $isKey = true;
                        echo "\t\t<td>\n\t\t\t<?= \$html->link(\${$singularVar}['{$alias}']['{$details['displayField']}'], array('controller' => '{$details['controller']}', 'action' => 'view', \${$singularVar}['{$alias}']['{$details['primaryKey']}'])); ?>\n\t\t</td>\n";
                        break;
                    }
                }
            }
            if ($isKey !== true) {
                echo "\t\t<td>\n\t\t\t<?= \${$singularVar}['{$modelClass}']['{$field}']; ?>\n\t\t</td>\n";
            }
        }

        echo "\t\t<td class = \"actions\">\n";
        echo "\t\t\t<?= \$html->link('View', array('action' => 'view', \${$singularVar}['{$modelClass}']['{$primaryKey}'])); ?>\n";
        echo "\t\t\t<?= \$html->link('Edit', array('action' => 'edit', \${$singularVar}['{$modelClass}']['{$primaryKey}'])); ?>\n";
        echo "\t\t\t<?= \$html->link('Delete', array('action' => 'delete', \${$singularVar}['{$modelClass}']['{$primaryKey}']), null, 'Are you sure you want to delete this " . low(Inflector::humanize(Inflector::underscore($singularVar))) . "?'); ?>\n";
        echo "\t\t</td>\n";
    echo "\t</tr>\n";

echo "<?php endforeach; ?>\n";
?>
</table>

<div class = "pagination">
<?= "\t<?= \$paginator->prev('<< '.'previous', array(), null, array('class'=>'disabled'));?>\n";?>
 | <?= "\t<?= \$paginator->numbers();?>\n"?>
<?= "\t<?= \$paginator->next('next'.' >>', array(), null, array('class' => 'disabled'));?>\n";?>
</div>
<div class = "actions">
    <ul>
        <li><?= "<?= \$html->link('New " . Inflector::humanize(Inflector::underscore($singularHumanName)) . "', array('action' => 'add')); ?>";?></li>
    </ul>
</div>
