<div style = 'position: relative'>
    <div class = 'DialogShadow CaptchaShadow'></div>
    <div class = 'DialogContent CaptchaContent'>
    <div style = 'padding: 7px'>
        <div id = 'Div_CaptchaForm'>
        <div style = 'font-size: 140%'>Hold on..</div>

        Please answer the following question so we know you're not a bot programmed by someone to spam battles. If you're a human, we hope this question is easy for you.

        <form>
            <div style = 'padding: 5px; margin-left: 125px;'>
            <img src = 'data:image/png;base64,<?= base64_encode($captcha['image']); ?>' />
            </div>

            <div>
            Answer: <input type = 'text' id = 'CaptchaResponse' />
            </div>

            <div style = 'margin-top: 5px'>
            <input id = 'Input_CaptchaSubmit' type = 'submit' value = 'Submit' style = 'font-size: 100%; height: 30px;' />
            <?= $html->image('cycle.gif', array('style' => 'margin-right: 2px; vertical-align: middle; display: none;', 'id' => 'Img_CaptchaFormLoading')); ?>
            </div>
        </form>
        </div>
        <div id = 'Div_CaptchaSuccess' style = 'display: none'>
        Alright, you're a human. Enjoy your human activities, meatbag.
        <input type = 'button' value = 'Close' class = 'Input_CloseDialog' />
        </div>
        <div id = 'Div_CaptchaFailure' style = 'display: none'>
        Sorry, you got it wrong..
        <input type = 'button' value = 'Close' class = 'Input_CloseDialog' />
        </div>
    </div>
    </div>
</div>
