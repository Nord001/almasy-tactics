<?php
/* SVN FILE: $Id: form.ctp 8283 2009-08-03 20:49:17Z gwoo $ */
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
<?php echo "<?php echo \$form->create('{$modelClass}');?>\n";?>
    <fieldset>
        <legend><?php echo "" . Inflector::humanize($action) . " " . Inflector::humanize(Inflector::underscore($singularHumanName));?></legend>
<?php
        echo "\t<?php\n";
        foreach ($fields as $field) {
            if ($action == 'add' && $field == $primaryKey) {
                continue;
            } elseif (!in_array($field, array('created', 'modified', 'updated'))) {
                echo "\t\techo \$form->input('{$field}');\n";
            }
        }
        if (!empty($associations['hasAndBelongsToMany'])) {
            foreach ($associations['hasAndBelongsToMany'] as $assocName => $assocData) {
                echo "\t\techo \$form->input('{$assocName}');\n";
            }
        }
        echo "\t?>\n";
?>
    </fieldset>
<?php
    echo "<?php echo \$form->end('Submit');?>\n";
?>
