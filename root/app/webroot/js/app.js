AJAX_ERROR_CODE = 'err';
AJAX_SUCCESS = 'succ';
AJAX_INSUFFICIENT_FUNDS = 'if';

//---------------------------------------------------------------------------------------------
function AddCommas (nStr) {
    nStr += '';
    x = nStr.split('.');
    x1 = x[0];
    x2 = x.length > 1 ? '.' + x[1] : '';
    var rgx = /(\d+)(\d{3})/;
    while (rgx.test(x1)) {
        x1 = x1.replace(rgx, '$1' + ',' + '$2');
    }
    return x1 + x2;
}

//---------------------------------------------------------------------------------------------
function UpdateMoneyDisplay (completeCallback) {
    $.post(
        '/users/get_money', // Hardcoded :(
        null,
        function(data) {
            $('#MoneyDisplay').attr('money', data);
            $('#MoneyDisplay').text(AddCommas(data));
            if (completeCallback)
                completeCallback();
        }
    );
}

//---------------------------------------------------------------------------------------------
function GetMoney () {
    return parseInt($('#MoneyDisplay').attr('money'));
}

//---------------------------------------------------------------------------------------------
function ShowLoadAnim () {
    $('#Image_LoadAnim').fadeIn('slow');
}

//---------------------------------------------------------------------------------------------
function HideLoadAnim () {
    $('#Image_LoadAnim').fadeOut('slow');
}

//---------------------------------------------------------------------------------------------
function SetCaretPosition (elemId, caretPos) {
    var elem = document.getElementById(elemId);

    if(elem != null) {
        if(elem.createTextRange) {
            var range = elem.createTextRange();
            range.move('character', caretPos);
            range.select();
        }
        else {
            if(elem.selectionStart) {
                elem.focus();
                elem.setSelectionRange(caretPos, caretPos);
            }
            else
                elem.focus();
        }
    }
}

//---------------------------------------------------------------------------------------------
function ShowCaptcha (doneCallback) {
    $.post(
        '<?= $html->url(array('controller' => 'home', 'action' => 'captcha')); ?>',
        null,
        function (data) {
            dialog = $('<div id = "CaptchaDialog"></div>');
            dialog.show();
            dialog.html(data);
            dialog.appendTo('body');
            $('#Img_FormLoading').hide();
            $('#Div_CaptchaForm').show();
            $('#Div_CaptchaSuccess').hide();
            $('#Div_CaptchaFailure').hide();
            $('#CaptchaResponse').val('');
            $('#CaptchaResponse').focus();

            var success = false;

            $('#Input_CaptchaSubmit').click(function(event) {
                event.preventDefault();

                $('#Img_CaptchaFormLoading').show();

                $.post(
                    '<?= $html->url(array('controller' => 'home', 'action' => 'captcha')); ?>',
                    {
                        answer: $('#CaptchaResponse').val()
                    },
                    function (data) {
                        $('#Div_CaptchaForm').hide();
                        if (data == 1) {
                            success = true;
                            $('#Div_CaptchaSuccess').show();
                        } else if (data == 0) {
                            $('#Div_CaptchaFailure').show();
                        } else {
                            // Error
                        }
                        $('.Input_CloseDialog').focus();
                    }
                );
            });

            $('.Input_CloseDialog').click(function(event) {
                event.preventDefault();
                dialog.remove();
                if (success) {
                    doneCallback();
                }
            });
        }
    );
}

//---------------------------------------------------------------------------------------------
function RunCaptcha (doneCallback) {
    if ($('#CaptchaDialog').exists())
        return;

    $.post(
        '<?= $html->url(array('controller' => 'home', 'action' => 'captcha_required')); ?>',
        null,
        function (captchaRequired) {
            if (captchaRequired == 1) {
                ShowCaptcha(doneCallback);
            } else {
                doneCallback();
            }
        }
    );
}

//---------------------------------------------------------------------------------------------
function ToSignedStr (number) {
    return parseInt(number) > 0 ? '+' + number : number;
}