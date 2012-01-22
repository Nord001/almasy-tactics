<?php echo $form->create('User', array('action' => 'email'));?>
    <fieldset>
        <legend>Email Users</legend>
    <?php
        echo $form->input('subject');

        echo $form->label('Content');
        echo $form->textarea('content', array('style' => 'width: 900px; height: 600px;'));
    ?>
    </fieldset>
    <input id = 'MassSendButton' type="submit" value="Mass Send" name = 'submit' />
    <input type="submit" value="Test Email" name = 'submit' />
</form>

<script type = 'text/javascript'>
    $(document).ready(function() {
        $('#MassSendButton').click(function(event) {
            if (!confirm('Are you sure?'))
                event.preventDefault();
        });
    });
</script>