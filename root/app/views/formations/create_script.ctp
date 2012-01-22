<div class = 'PageDiv'>

    <div class = 'PageTitle'>
        <?= $html->link2('Formations', array('controller' => 'formations', 'action' => 'index')); ?> |
        <?= $formation['Formation']['name']; ?> |
        <?= $html->link2('Strategy', array('controller' => 'formations', 'action' => 'strategy', $formation['Formation']['id'])); ?> |
        New AI Script
    </div>

    <div class = 'PageContent' style = 'padding-left: 10px;'>

        <form id = '/formations/createScriptForm' method = 'post' action = '/formations/create_script'>
            <?= $form->hidden('csrf_token', array('name' => 'data[csrf_token]', 'value' => $a_user['User']['csrf_token'])); ?>

            <label for = 'Script Name'>Script Name</label>
            <input name = 'data[AiScript][name]' maxlength = 30 type = 'text' value = '' id = 'name' />

            <label for = 'Script'>Script</label>
            <textarea name = 'data[AiScript][contents]' id = 'contents' style = 'font-family: courier; width: 550px; height: 400px'></textarea>

            <input type = 'hidden' name = 'data[formation_id]' value = '<?= $formation['Formation']['id']; ?>' />

        <?= $form->end('Save!'); ?>
    </div>
</div>
