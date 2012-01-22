<h2><?= $html->link('Almasy Admin', array('controller' => 'admin_home', 'action' => 'index')); ?></h2>

<?
    $tags = array();
    $tags[] = 'build ' . GetVersionNumber();
    if (Configure::read('debug'))
        $tags[] = 'debug';
    if (Configure::read('test'))
        $tags[] = 'test';
?>

<div style = 'font-style: italic'><?= implode(', ', $tags); ?></div>

<div>
    Game Server:
    <span style = 'font-weight: bold'>
        <?
            $status = 'N/A';
            $res = @fsockopen(GAME_SERVER, GAME_SERVER_PORT, $no, $str, 0.5);
            if ($res !== false) {
                $status = "<span style = 'color: hsl(120, 100%, 40%)'>UP</span>";
                fclose($res);
            } else {
                $status = "<span style = 'color: hsl(0, 100%, 40%)'>DOWN</span>";
            }
            echo $status;
        ?>
    </span>
</div>

<div>
    CPU:
    <span style = 'font-weight: bold'>
        <?
            if (function_exists('sys_getloadavg'))
                $load = implode(', ', sys_getloadavg());
            else
                $load = 'N/A';
            echo $load;
        ?>
    </span>
</div>

<h3>Game</h3>
<ul>
    <li><?= $html->link('Clear Cache', array('controller' => 'admin_home', 'action' => 'clear_cache'), null, 'ARE YOU SURE? THIS CAN FUCK UP SHIT HARD.'); ?></li>
    <li>
        <?= $html->link('FAQs', array('controller' => 'faqs', 'action' => 'index')); ?>
        <ul>
            <li><?= $html->link('Add Question', array('controller' => 'faqs', 'action' => 'add')); ?></li>
        </ul>
    </li>
    <li>
        <?= $html->link('News', array('controller' => 'news', 'action' => 'index')); ?>
        <ul>
            <li><?= $html->link('Make A Post', array('controller' => 'news', 'action' => 'add')); ?></li>
        </ul>
    </li>
    <li>
        <?= $html->link('Store', array('controller' => 'item_catalog_entries', 'action' => 'index')); ?>
        <ul>
            <li><?= $html->link('Sell Item', array('controller' => 'item_catalog_entries', 'action' => 'add')); ?></li>
        </ul>
    </li>
    <li>
        <?= $html->link('Users', array('controller' => 'users', 'action' => 'index')); ?>
        <ul>
            <li><?= $html->link('Find User', array('controller' => 'users', 'action' => 'find')); ?></li>
            <li><?= $html->link('Email Users', array('controller' => 'users', 'action' => 'email')); ?></li>
        </ul>
    </li>
    <? if (HELP_EDITING): ?>
        <li>
            <?= $html->link('Help', array('controller' => 'help', 'action' => 'index')); ?>
            <ul>
                <li><?= $html->link('New Page', array('controller' => 'help', 'action' => 'add')); ?></li>
            </ul>
        </li>
    <? endif; ?>
    <li><?= $html->link('Battle Simulator', array('controller' => 'battles', 'action' => 'simulator')); ?></li>
</ul>

<h3>Debug</h3>
<ul>
    <li><?= $html->link('Error Log', array('controller' => 'error_logs', 'action' => 'index')); ?></li>
</ul>

<h3>Stats</h3>
<ul>
    <li><?= $html->link('New Account Funnel', array('controller' => 'stats', 'action' => 'funnel')); ?></li>
    <li><?= $html->link('New Account Activity', array('controller' => 'stats', 'action' => 'new_account_activity')); ?></li>
</ul>

<h3>Data</h3>
<ul>
    <li>
        <?= $html->link('Armor Types', array('controller' => 'armor_types', 'action' => 'index')); ?>
        <ul>
            <li><?= $html->link('New A. Type', array('controller' => 'armor_types', 'action' => 'add')); ?></li>
        </ul>
    </li>
    <li>
        <?= $html->link('Bonus Types', array('controller' => 'bonus_types', 'action' => 'index')); ?>
        <ul>
            <li><?= $html->link('New B. Type', array('controller' => 'bonus_types', 'action' => 'add')); ?></li>
        </ul>
    </li>
    <li>
        <?= $html->link('Classes', array('controller' => 'c_classes', 'action' => 'index')); ?>
        <ul>
            <li><?= $html->link('New Class', array('controller' => 'c_classes', 'action' => 'add')); ?></li>
            <li><?= $html->link('Growth Calc', array('controller' => 'c_classes', 'action' => 'growth')); ?></li>
        </ul>
    </li>
    <li><?= $html->link('Elements', array('controller' => 'c_elements', 'action' => 'index')); ?></li>
    <li><?= $html->link('Experience', array('controller' => 'experience', 'action' => 'index')); ?></li>
    <li>
        <?= $html->link('Imbues', array('controller' => 'imbues', 'action' => 'index')); ?>
        <ul>
            <li><?= $html->link('Mod Pool', array('controller' => 'imbue_mods', 'action' => 'index')); ?></li>
            <ul>
                <li><?= $html->link('New Mod', array('controller' => 'imbue_mods', 'action' => 'add')); ?></li>
            </ul>

            <li><?= $html->link('New Imbue', array('controller' => 'imbues', 'action' => 'add')); ?></li>
        </ul>
    </li>
    <li>
        <?= $html->link('Items', array('controller' => 'items', 'action' => 'index')); ?>
        <ul>
            <li><?= $html->link('New Item', array('controller' => 'items', 'action' => 'add')); ?></li>
        </ul>
    </li>
    <li>
        <?= $html->link('Misc Item Types', array('controller' => 'misc_item_types', 'action' => 'index')); ?>
        <ul>
            <li><?= $html->link('New MI Type', array('controller' => 'misc_item_types', 'action' => 'add')); ?></li>
        </ul>
    </li>
    <li>
        <?= $html->link('Missions', array('controller' => 'missions', 'action' => 'index')); ?>
        <ul>
            <li><?= $html->link('New Mission', array('controller' => 'missions', 'action' => 'add')); ?></li>
            <li><?= $html->link('New Mission Group', array('controller' => 'mission_groups', 'action' => 'add')); ?></li>
        </ul>
    </li>
    <li>
        <?= $html->link('Monsters', array('controller' => 'monsters', 'action' => 'index')); ?>
        <ul>
            <li><?= $html->link('New Monster', array('controller' => 'monsters', 'action' => 'add')); ?></li>
        </ul>
    </li>
    <li>
        <?= $html->link('Sprites', array('controller' => 'sprites', 'action' => 'index')); ?>
        <ul>
            <li><?= $html->link('Upload', array('controller' => 'sprites', 'action' => 'add')); ?></li>
        </ul>
    </li>
    <li>
        <?= $html->link('Weapon Types', array('controller' => 'weapon_types', 'action' => 'index')); ?>
        <ul>
            <li><?= $html->link('New W. Type', array('controller' => 'weapon_types', 'action' => 'add')); ?></li>
        </ul>
    </li>
</ul>
