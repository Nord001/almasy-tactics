<div class = 'PageDiv'>

    <div class = 'PageTitle'>
        <?= $html->link2($guild['Guild']['name'], array('controller' => 'guilds', 'action' => 'view', $guild['Guild']['id'])); ?> | Change Emblem
    </div>

    <div class = 'PageContent' style = 'padding-left: 10px;'>
        Choose an emblem to upload. Emblems must be 100x100 or smaller, 100 kB or smaller, and must be in .jpg or .png format.
        <form enctype = 'multipart/form-data' method = 'POST'>
            <?= $form->hidden('csrf_token', array('name' => 'data[csrf_token]', 'value' => $a_user['User']['csrf_token'])); ?>

            <input type = 'hidden' name = 'MAX_FILE_SIZE' value = '100000' />
            <input name = 'userfile' type = 'file' style = 'width: 400px' /> <br />
            <input type = 'submit' value = 'Change Emblem' />
        </form>
    </div>
</div>