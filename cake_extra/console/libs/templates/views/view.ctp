<?php
/* SVN FILE: $Id: view.ctp 8283 2009-08-03 20:49:17Z gwoo $ */
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
<h2><?= Inflector::humanize(Inflector::underscore($singularHumanName));?></h2>
<table class = 'view-table'>
    <tr>
        <td class = 'column-1' />
        <td class = 'column-2' />
    </tr>
<?php
foreach ($fields as $field) {
    echo "\t<tr>\n";
    $isKey = false;
    if (!empty($associations['belongsTo'])) {
        foreach ($associations['belongsTo'] as $alias => $details) {
            if ($field === $details['foreignKey']) {
                $isKey = true;
                echo "\t\t<td>" . Inflector::humanize(Inflector::underscore($alias)) . "</td>\n";
                echo "\t\t<td>\n\t\t\t<?= \$html->link(\${$singularVar}['{$alias}']['{$details['displayField']}'], array('controller' => '{$details['controller']}', 'action' => 'view', \${$singularVar}['{$alias}']['{$details['primaryKey']}'])); ?>\n\t\t</td>\n";
                break;
            }
        }
    }
    if ($isKey !== true && $field != 'id') {
        echo "\t\t<td>" . Inflector::humanize($field) . "</td>\n";
        echo "\t\t<td><?= \${$singularVar}['{$modelClass}']['{$field}']; ?></td>\n";
    }
    echo "\t</tr>\n";
}
?>
</table>

<div class = "actions">
    <ul>
<?php
    echo "\t\t<li><?= \$html->link('Edit " . Inflector::humanize(Inflector::underscore($singularHumanName)) . "', array('action' => 'edit', \${$singularVar}['{$modelClass}']['{$primaryKey}'])); ?> </li>\n";
    echo "\t\t<li><?= \$html->link('Delete " . Inflector::humanize(Inflector::underscore($singularHumanName)) . "', array('action' => 'delete', \${$singularVar}['{$modelClass}']['{$primaryKey}']), null, 'Are you sure you want to delete this " . low(Inflector::humanize(Inflector::underscore($singularVar))) . "?'); ?> </li>\n";
?>
    </ul>
</div>
