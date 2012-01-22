<?php echo $form->create('User', array('action' => 'find'));?>
    <fieldset>
        <legend>Find User</legend>
    <?php
        echo $form->input('username');
    ?>
    </fieldset>
<?php echo $form->end('Submit');?>

<script type="text/javascript">
    $(document).ready(function() {
        $('#UserUsername').autocomplete({
            minLength: 2,
            source: function(request, response) {
                $.ajax({
                    url: '<?= $html->url(array('controller' => 'users', 'action' => 'find_lookup')); ?>',
                    dataType: "json",
                    data: request,
                    success: function(data) {
                        response(data);
                    }
                });
            }
        });
    });
</script>

