<?= $form->create('Faq');?>
    <fieldset>
        <legend>Add Question</legend>
    <?php
        echo $form->input('question');
        echo $form->input('answer');
        echo $form->input('path');
        echo $form->input('category');
        echo $form->input('link');
    ?>
    </fieldset>
<?= $form->end('Submit');?>
