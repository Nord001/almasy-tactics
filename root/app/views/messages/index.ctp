<?= $javascript->link('pages/messages.js'); ?>

<style type = 'text/css'>
    .SubjectDiv {
        border-bottom: 1px dotted rgb(128, 128, 128);
        font-size: 125%;
        margin-bottom: 10px;
    }

    .Selected td {
        background-color: rgb(240, 225, 175);
    }

    .Unselected td {
        background-color: rgb(240, 240, 240);
    }

    .Unread {
        font-weight: bold;
    }

    .PageContent td {
        padding-left: 5px;
        padding-right: 5px;
        border-bottom: 1px solid rgb(128, 128, 128);
    }

    #MessagesList {
        border: 1px solid;
        width: 900px;
        height: 278px;
        margin-bottom: 5px;
    }

    #MessageDiv {
        padding: 10px;
        padding-top: 3px;
        margin-top: 10px;
        width: 880px;
        height: 200px;
        overflow: auto;
        border: 1px solid;
        background-color: rgb(235, 230, 200);
    }

    #ButtonList {
        width: 900px;
    }

    .MessageContent {
        display: none;
    }

    #ButtonList {
        margin-bottom: 5px;
    }

    #MessageButtonList {
    }

    .FromSpan {
        font-size: 80%;
    }

</style>

<form id = 'Form_MarkAllRead' action = '<?= $html->url(array('controller' => 'messages', 'action' => 'mark_all_read')); ?>' method = 'POST'>
    <?= $form->hidden('csrf_token', array('name' => 'data[csrf_token]', 'value' => $a_user['User']['csrf_token'])); ?>
</form>
<form id = 'Form_Clean' action = '<?= $html->url(array('controller' => 'messages', 'action' => 'clean')); ?>' method = 'POST'>
    <?= $form->hidden('csrf_token', array('name' => 'data[csrf_token]', 'value' => $a_user['User']['csrf_token'])); ?>
</form>

<div class = 'PageDiv'>

    <div class = 'PageTitle'>
        <?= $html->link2($a_user['User']['username'], array('controller' => 'users', 'action' => 'profile')); ?> |
        Inbox

        <div style = 'position: absolute; top: 8px; right: 10px; font-size: 70%'>
            <input type = 'button' value = 'Send Message' id = 'SendButton' />
        </div>
    </div>

    <div class = 'PageContent'>
        <div style = 'position: relative; left: 50%; margin-left: -450px;'>
            <? if ($numMessages >= MAX_MESSAGES_PER_USER): ?>
                <span style = 'font-size: 120%; color: rgb(200, 0, 0)'>Your inbox is full! Clean out some messages!</span>
            <? endif; ?>
            <div id = 'ButtonList' style = 'position: relative'>
                <a href = '#' id = 'DeleteLink'>Delete</a> |
                <span style = 'color: rgb(0, 50, 0)'>Select</span>:
                <a href = '#' id = 'SelectAllLink'>All</a>,
                <a href = '#' id = 'SelectNoneLink'>None</a>,
                <a href = '#' id = 'SelectReadLink'>Read</a> |
                <a href = '#' id = 'Link_MarkAllRead'>Mark All Read</a> |
                <a href = '#' id = 'Link_Clean'>Clean</a>

                <div style = 'position: absolute; right: 0px; top: 0px;'>
                    <?
                        if ($startMessage != 0) {
                            echo $html->link('First', array('controller' => 'messages', 'action' => 'index'));
                            echo ' ';
                            echo $html->link('Prev', array('controller' => 'messages', 'action' => 'index', $page - 1));
                        }
                    ?>
                    <? printf('%d - %d of %d', min($numMessages, $startMessage + 1), min($numMessages, $startMessage + MESSAGES_PER_PAGE), $numMessages); ?>
                    <?
                        if ($page != $lastPage && $numMessages != 0) {
                            echo $html->link('Next', array('controller' => 'messages', 'action' => 'index', $page + 1));
                            echo ' ';
                            echo $html->link('Last', array('controller' => 'messages', 'action' => 'index', $lastPage));
                        }
                    ?>
                </div>
            </div>

            <div id = 'MessagesList'>
                <form id = 'MessageForm' method = 'POST' action = '/messages/delete'>
                    <?= $form->hidden('csrf_token', array('name' => 'data[csrf_token]', 'value' => $a_user['User']['csrf_token'])); ?>
                    <? if (!empty($messages)): ?>
                        <table style = 'width: 900px;'>
                            <? $i = 0; ?>
                            <? foreach ($messages as $message): ?>
                                <tr class = 'Message Unselected <?= $message['Message']['read'] ? '' : 'Unread'; ?>' messageId = '<?= $message['Message']['id']; ?>'>
                                    <td class = 'MessageContent'>
                                        <div class = 'SubjectDiv'>
                                            <?= $message['Message']['subject']; ?>
                                            <span class = 'FromSpan'>
                                                from
                                                <?=
                                                    $message['Message']['Sender']['id'] != 1 ?
                                                    $html->link2($message['Message']['Sender']['username'], array('controller' => 'users', 'action' => 'profile', $message['Message']['Sender']['username'])) :
                                                    $message['Message']['Sender']['username'];
                                                ?>
                                            </span>
                                        </div>

                                        <?= nl2br($message['Message']['message']); ?>
                                    </td>

                                    <td style = 'width: 25px' class = 'MessageCheckboxTd'>
                                        <input name = 'data[Message][id][]' value = '<?= $message['Message']['id']; ?>' type = 'checkbox' class = 'MessageCheckbox' style = 'width: auto'/>
                                    </td>

                                    <td style = 'width: 150px'>
                                        <?= $html->image('sprites/' . $message['Message']['Sender']['portrait'] . '.png', array('style' => 'position: relative; top: 3px; border: 1px solid rgb(128, 128, 128); height: 25px')); ?>
                                        <span style = 'position: relative; top: -5px'>
                                            <?=
                                                $message['Message']['Sender']['id'] != 1 ?
                                                $html->link2($message['Message']['Sender']['username'], array('controller' => 'users', 'action' => 'profile', $message['Message']['Sender']['username'])) :
                                                $message['Message']['Sender']['username'];
                                            ?>
                                        </span>
                                     </td>
                                    <td class = 'MessageSubject'>
                                        <?
                                            $str = $message['Message']['subject'] . ' - ' . $message['Message']['message'];
                                            if (strlen($str) < 50)
                                                echo $str;
                                            else
                                                echo substr($str, 0, 47) . ' ...';
                                        ?>
                                    </td>
                                    <td style = 'width: 100px; text-align: right;'><?= $time->GetTimeAgoString(strtotime($message['Message']['time_sent'])); ?></td>
                                </tr>
                            <? endforeach; ?>
                        </table>
                    <? else: ?>
                        <div style = 'margin: 15px'>
                            No messages.
                        </div>
                    <? endif; ?>
                </form>
            </div>

            <div id = 'MessageButtonList' style = 'position: relative'>
                <a href = '#' id = 'ReplyLink'>Reply to Message</a> |
                <a href = '#' id = 'DeleteMessageLink'>Delete Message</a>
                </div>

            <div id = 'MessageDiv' class = 'rounded-corners'>
            </div>
        </div>
    </div>
</div>