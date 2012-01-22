ActivateItemTooltips();

$(document).ready(function() {
    $('tr[userItemId] input[type=button]').click(function(event) {
        event.preventDefault();

        var item = $(this).parent().parent();
        var userItemId = item.attr('userItemId');
        var itemName = item.attr('itemName');

        if (confirm('Are you sure you want to buy a ' + itemName + '?')) {
        var dialog = $('#BuyDialog');
        dialog.dialog(
            {
            modal: true,
            draggable: false,
            }
        );
        dialog.text('Please Wait...');
        dialog.dialog('option', 'title', 'Processing...');
        dialog.append('<?= $html->image('cycle.gif', array('style' => 'margin-right: 2px; vertical-align: middle')); ?>');
        dialog.dialog('open');

        $.post(
            '<?= $html->url(array('controller' => 'items', 'action' => 'buy_item')); ?>',
            {
                userItemId: userItemId
            },
            function(data) {
                if (data == '<?= AJAX_SUCCESS; ?>') {
                    dialog.dialog('option', 'title', 'Success!');
                    dialog.text('Your item has been purchased!');
                    dialog.append("<input type = 'button' value = 'Awesome!' style = 'width: 100%; height: 75px' class = 'ConfirmButton' />");

                    $('.ConfirmButton').click(function() {
                        dialog.dialog('close');
                    });
                    $('.ConfirmButton').focus();

                    ShowItemView(itemType, itemTypeId);
                    UpdateMoneyDisplay();

                } else if (data == '<?= AJAX_INSUFFICIENT_FUNDS; ?>') {
                    dialog.dialog('option', 'title', 'Oops!');
                    dialog.text('You don\'t have enough money.');
                    dialog.append("<input type = 'button' value = 'Okay..' style = 'width: 100%; height: 75px' class = 'ConfirmButton' />");

                    $('.ConfirmButton').click(function() {
                        dialog.dialog('close');
                    });
                } else {
                    dialog.dialog('option', 'title', 'Error');
                    dialog.text('An error has occurred.');
                    dialog.append("<input type = 'button' value = 'Okay..' style = 'width: 100%; height: 75px' class = 'ConfirmButton' />");

                    $('.ConfirmButton').click(function() {
                        dialog.dialog('close');
                    });
                    $('.ConfirmButton').focus();
                }
            }
        );
        }
    });
});
