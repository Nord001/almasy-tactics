<style type = 'text/css'>
    .DivHeader {
        font-size: 140%;
        border-bottom: 1px dotted;
    }

    .DivContent {
        padding: 3px;
    }

</style>

<form id = 'Form_DeleteScript' action = '<?= $html->url(array('controller' => 'formations', 'action' => 'delete_script')); ?>' method = 'POST'>
    <?= $form->hidden('csrf_token', array('name' => 'data[csrf_token]', 'value' => $a_user['User']['csrf_token'])); ?>
    <input type = 'hidden' id = 'Input_ScriptId' name = 'data[script_id]' value = '' />
    <input type = 'hidden' id = 'Input_FormationId' name = 'data[formation_id]' value = '<?= $formation['Formation']['id']; ?>' />
</form>

<div class = 'PageDiv'>
    <div class = 'PageTitle'>
        <?= $html->link2('Formations', array('controller' => 'formations', 'action' => 'index')); ?> |
        <?= $formation['Formation']['name']; ?> |
        Strategy
    </div>

    <div class = 'PageContent'>
        <div style = 'float: left; width: 400px'>
            <div class = 'DivHeader'>
                AI Scripts
            </div>

            <div class = 'DivContent'>
                <? if (!empty($scripts)): ?>
                    <table style = 'width: 300px'>
                        <? foreach ($scripts as $script): ?>
                            <tr>
                                <td>
                                    <?= $html->link2('Edit', array('controller' => 'formations', 'action' => 'edit_script', $formation['Formation']['id'], $script['AiScript']['id'])); ?>
                                </td>
                                <td>
                                    <a class = 'Link_DeleteScript' href = '#' scriptId = '<?= $script['AiScript']['id']; ?>'>Delete</a>
                                </td>
                                <td><?= $script['AiScript']['name']; ?></td>
                            </tr>
                        <? endforeach; ?>
                    </table>
                <? else: ?>
                    No scripts.
                <? endif; ?>

                <div>
                    <?= $html->link2('New AI Script', array('controller' => 'formations', 'action' => 'create_script', $formation['Formation']['id'])); ?>
                </div>
            </div>
        </div>

        <div style = 'float: right; width: 400px'>
            <div class = 'DivHeader'>
                <?= $formation['Formation']['name']; ?>
            </div>

            <div class = 'DivContent'>
                <form id = 'FormationAIForm' method = 'post' action = '/formations/edit_ai'>
                    <?= $form->hidden('csrf_token', array('name' => 'data[csrf_token]', 'value' => $a_user['User']['csrf_token'])); ?>

                    <input type = 'hidden' value = '<?= $formation['Formation']['id']; ?>' name = 'data[formation_id]' />
                    <table style = 'width: 330px'>
                        <? $i = 0; ?>
                        <? foreach ($formation['Characters'] as $character): ?>
                            <input type = 'hidden' value = '<?= $character['Character']['id']; ?>' name = 'data[CharacterIds][]' />
                            <tr>
                                <td>
                                    <?= $character['Character']['name']; ?>
                                </td>
                                <td>
                                    AI
                                    <select style = 'width: 125px' name = 'data[CharacterSelect][]'>
                                        <? $selected = $character['CharactersFormation']['script_id'] == null; ?>
                                        <option value = '-1' <?= $selected ? "selected = 'selected'" : ''; ?>>
                                            None
                                        </option>
                                        <? foreach($scripts as $script): ?>
                                            <? $selected = $script['AiScript']['id'] == $character['CharactersFormation']['script_id']; ?>
                                            <option value = '<?= $script['AiScript']['id']; ?>' <?= $selected ? "selected = 'selected'" : ''; ?>>
                                                <?= $script['AiScript']['name']; ?>
                                            </option>
                                        <? endforeach; ?>
                                    </select>
                                </td>
                            </tr>
                        <? endforeach; ?>
                    </table>
                    <input type = 'submit' value = 'Save AI Config' />
                </form>
            </div>
        </div>

        <div style = 'clear: both'></div>
    </div>
</div>

<script type = 'text/javascript'>
    $(document).ready(function() {
        $('.Link_DeleteScript').click(function(event) {
            if (confirm('Are you sure you want to delete this script?')) {
                $('#Input_ScriptId').attr('value', $(this).attr('scriptId'));
                $('#Form_DeleteScript').submit();
            }
        });
    });
</script>
