$(document).ready(function() {
    var selectedMessage = null;

    $('#ReplyLink').click(function(event) {
        event.preventDefault();

        if (selectedMessage == null) {
            alert('No message is selected.');
        }

        window.location = '<?= $html->url(array('controller' => 'messages', 'action' => 'send')); ?>' + '/' + selectedMessage.attr('messageId');
    });

    $('#DeleteMessageLink').click(function(event) {
        event.preventDefault();

        if (selectedMessage == null) {
            alert('No message is selected.');
        }

        selectedMessage.find('input').attr('checked', true);

        $('#MessageForm').submit();
    });

    $('.MessageSubject').click(function(event) {
        var messageTr = $(this).closest('.Message');
        var content = messageTr.children('.MessageContent').eq(0);

        $('#MessageDiv').html(content.html());

        if (selectedMessage != null) {
            selectedMessage.removeClass('Selected');
            selectedMessage.addClass('Unselected');
        }

        selectedMessage = messageTr;

        selectedMessage.addClass('Selected');
        selectedMessage.removeClass('Unselected');

        if (messageTr.hasClass('Unread')) {
            $.post(
                '/messages/mark_read?',
                { messageId: messageTr.attr('messageId') },
                function(data) {
                    messageTr.removeClass('Unread');
                }
            );
        }
    });

    $('.MessageSubject').hover(
        function() {
            $('body').css('cursor', 'pointer');
        },
        function() {
            $('body').css('cursor', 'auto');
        }
    );

    $('#DeleteLink').click(function(event) {
        event.preventDefault();

        var numChecked = $('.MessageCheckbox:checked').length;

        if (numChecked == 0) {
            alert('No messages are checked.');
            return;
        }

        $('#MessageForm').submit();
    });

    $('#SelectAllLink').click(function(event) {
        event.preventDefault();

        $('.MessageCheckbox').attr('checked', true);
    });

    $('#SelectNoneLink').click(function(event) {
        event.preventDefault();

        $('.MessageCheckbox').attr('checked', false);
    });

    $('#SelectReadLink').click(function(event) {
        event.preventDefault();

        $('.MessageCheckbox').each(function() {
            $(this).attr('checked', !$(this).closest('.Message').hasClass('Unread'));
        });
    });

    $('#Link_MarkAllRead').click(function(event) {
        event.preventDefault();

        $('#Form_MarkAllRead').submit();
    });

    $('#Link_Clean').click(function(event) {
        event.preventDefault();

        if (confirm('This will delete all read messages from your inbox. Continue?'))
            $('#Form_Clean').submit();
    });

    var createUrl = '<?= $html->url(array('controller' => 'messages', 'action' => 'send')); ?>';
    $('#SendButton').click(function(event) {
        event.preventDefault();

        window.location = createUrl;
    });
});