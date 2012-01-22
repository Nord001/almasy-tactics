<div class = 'PageDiv'>

    <div class = 'PageTitle'>
        <?= $html->link2($a_user['User']['username'], array('controller' => 'users', 'action' => 'profile')); ?> |
        <?= $html->link2('Inbox', array('controller' => 'messages', 'action' => 'index')); ?> |
        Send Message
    </div>

    <div class = 'PageContent'>

        <div style = 'padding-left: 10px'>
            <?= $form->create('Message', array('action' => 'send')); ?>
            <?= $form->hidden('csrf_token', array('name' => 'data[csrf_token]', 'value' => $a_user['User']['csrf_token'])); ?>

            <?
                $username = '';
                if (isset($replyMessage))
                    $username = $replyMessage['Message']['Sender']['username'];
                else if (isset($this->data['Message']['username']))
                    $username = $this->data['Message']['username'];

                $subject = '';
                if (isset($replyMessage))
                    $subject = 'Re: ' .$replyMessage['Message']['subject'];
                else if (isset($this->data['Message']['subject']))
                    $subject = $this->data['Message']['subject'];

                $message = '';
                if (isset($replyMessage)) {
                    $message .= "\n\n\n";
                    $message .= "----------------------------------------------------\n";
                    $message .= sprintf('%s said on %s:',
                        $replyMessage['Message']['Sender']['username'],
                        date('M. j', strtotime($replyMessage['Message']['time_sent']))
                    );
                    $message .= "\n\n";
                    $message .= $replyMessage['Message']['message'];
                } else if (isset($this->data['Message']['message'])) {
                    $message = $this->data['Message']['message'];
                }
            ?>
            <?= $form->input('username', array('value' => $username)); ?>
            <?= $form->input('subject', array('style' => 'width: 300px', 'value' => $subject)); ?>
            <?= $form->input('message', array('id' => 'MessageInput', 'value' => $message, 'style' => 'width: 600px; height: 300px')); ?>

            <?= $form->end('Send Message'); ?>
        </div>
    </div>
</div>

<? if (isset($replyMessage)): ?>
    <script type = 'text/javascript'>
        $(document).ready(function() {
            SetCaretPosition('MessageInput', 0);
        });
    </script>
<? endif; ?>
