$(document).ready(function() {

    //---------------------------------------------------------------------------------------------
    // Shortcuts

    if (shortcutsOn) {
        $(document).bind('keydown', {combi: 'a', disableInInput: true}, function(event) {
            window.location = $('#ArmyLink').attr('href');
        });
        $(document).bind('keydown', {combi: 'w', disableInInput: true}, function(event) {
            window.location = $('#BattleLink').attr('href');
        });
        $(document).bind('keydown', {combi: 'f', disableInInput: true}, function(event) {
            window.location = $('#FormationsLink').attr('href');
        });
        $(document).bind('keydown', {combi: 'y', disableInInput: true}, function(event) {
            window.location = $('#ArmoryLink').attr('href');
        });
        $(document).bind('keydown', {combi: 'r', disableInInput: true}, function(event) {
            window.location = $('#ForumsLink').attr('href');
        });
        $(document).bind('keydown', {combi: 'h', disableInInput: true}, function(event) {
            window.location = $('#HelpLink').attr('href');
        });
        $(document).bind('keydown', {combi: 'm', disableInInput: true}, function(event) {
            $('#MessageNotification').click();
        });
        $(document).bind('keydown', {combi: 'b', disableInInput: true}, function(event) {
            window.location = $('#MatchmakeLink').attr('href');
        });
    }

    //---------------------------------------------------------------------------------------------
    // Flash notification
    dialog = $('#SessionDialog');
    if (dialog.length > 0) {
        dialog.dialog(
            {
                width: 300,
                modal: true,
                draggable: false
            }
        );

        $('.ConfirmButton').click(function() {
            dialog.dialog('close');
        });
        $('.ConfirmButton').focus();
    }

    //---------------------------------------------------------------------------------------------
    // Message notifications

    $('#MessageNotification').click(function(event) {
        event.preventDefault();

        window.location = '<?= $html->url(array('controller' => 'messages', 'action' => 'index')); ?>';
    });

    $('#MessageNotification').hover(
        function () {
            $('#Notifications').css('display', 'none');
            $('#NotificationHover').css('display', 'inherit');
            $('body').css('cursor', 'pointer');
        },
        function () {
            $('#Notifications').css('display', 'inherit');
            $('#NotificationHover').css('display', 'none');
            $('body').css('cursor', 'auto');
        }
    );

    //---------------------------------------------------------------------------------------------
    // Tooltips

    var battleSpan = $('#BattleSpan');
    if (battleSpan.exists()) {
        AddTooltip(battleSpan, $('#BattleTimeTooltip'), 'TimeTooltip');
    }

    /*
    var moneySpan = $('#MoneyDisplay');
    if (moneySpan.exists()) {
        AddTooltip(moneySpan, $('#MoneyTimeTooltip'), 'TimeTooltip');
    }
    */

    //---------------------------------------------------------------------------------------------
    // Feedback

    $('#FeedbackLink').click(function(event) {
        $('#Img_FormLoading').show();
        event.preventDefault();
        $.post(
            '<?= $html->url(array('controller' => 'home', 'action' => 'feedback_form')); ?>',
            { url: currentUrl },
            function (data) {
                $('#Img_FormLoading').hide();

                $('#FeedbackDialog').show();
                $('#FeedbackDialog').html(data);
                $('#Img_FormLoading').hide();
                $('#Div_FeedbackForm').show();
                $('#Div_FeedbackSuccess').hide();
                $('#Div_FeedbackFailure').hide();
                $('#TextArea_InputFeedback').val('');

                $('#Input_FeedbackSubmit').click(function(event) {
                    event.preventDefault();

                    $('#Img_FeedbackFormLoading').show();

                    $.post(
                        '<?= $html->url(array('controller' => 'home', 'action' => 'feedback')); ?>',
                        {
                            current_page: $('#InputCurrentPage').val(),
                            feedback: $('#TextArea_InputFeedback').val()
                        },
                        function (data) {
                            $('#Div_FeedbackForm').hide();
                            if (data == 1) {
                                $('#Div_FeedbackSuccess').show();
                            } else {
                                $('#Div_FeedbackFailure').show();
                            }
                        }
                    );
                });

                $('.Input_CloseFeedback').click(function(event) {
                    event.preventDefault();
                    $('#FeedbackDialog').hide();
                });
            }
        );
    });

    //---------------------------------------------------------------------------------------------
    // Logout

    $('#LogoutButton').click(function(event) {
        event.preventDefault();

        window.location = '<?= $html->url(array('controller' => 'users', 'action' => 'logout')); ?>';
    });

    //---------------------------------------------------------------------------------------------
    // Put cake log at the bottom
    $('body > .cake-sql-div').prepend('<a href = \'#\' style = \'font-size: 14pt\'>Close</a>');
    $('body > .cake-sql-div > a').click(function(event) {
        event.preventDefault();
        $('body > .cake-sql-div').hide();
    });
    $('body > .cake-sql-div').hide();

    // Focus the first text input of every page.
    var firstInput = $('input[type=text][autoselect!=0]:first');
    var windowHeight = window.innerHeight;
    if (firstInput.offset().top < windowHeight && !$('#SessionDialog').exists())
        firstInput.focus();


    // Shrinkify items.
    $('.ShrinkText').shrinkText();

    $('.LinkButton').linkButton();
});