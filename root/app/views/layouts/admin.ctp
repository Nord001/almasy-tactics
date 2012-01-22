<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<!-- asdf -->
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <?= $html->charset(); ?>
    <title>
        Almasy Admin -
        <?= $title_for_layout; ?>
    </title>

    <?php
        echo $html->meta('icon');
        echo $html->css('admin');
        echo $html->css('ui-lightness/jquery-ui-1.8.2.custom');
        echo $javascript->link('third_party/jquery-1.4.2.min');
        echo $javascript->link('third_party/jquery-ui-1.8.2.custom.min');
        echo $javascript->link('jquery-custom');
        echo $javascript->link('admin');
        echo $scripts_for_layout;
    ?>
</head>
<body>
    <div id="container">
        <div id = "header">

        </div>

        <div id = 'dashboard'>
            <?= $this->element('admin_dashboard'); ?>
        </div>

        <div id = 'main-container'>
            <?php if ($session->check('Message.flash')): ?>
                    <?php $session->flash(); ?>
            <? endif; ?>

            <div style = 'font-size: 200%; color: rgb(200, 0, 0);'>
                <?= !is_writable(TMP) ? 'Warning: tmp dir not writable!': ''; ?>
            </div>

            <?= $content_for_layout; ?>
        </div>

        <div id = "footer">

        </div>

    </div>

    <?= $cakeDebug; ?>
</body>
</html>
