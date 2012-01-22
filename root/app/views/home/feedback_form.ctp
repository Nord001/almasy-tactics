<div style = 'position: relative'>
    <div class = 'DialogShadow'></div>
    <div class = 'DialogContent'>
        <div style = 'padding: 7px'>
            <div id = 'Div_FeedbackForm'>
                <div style = 'font-size: 140%'>Tell us what you think!</div>

                Thanks for giving feedback to Almasy! We try to make everyone happy, and your comments are extremely welcome!
                <form>
                    <div>
                    The current page: <input type = 'text' id = 'InputCurrentPage' value = '<?= $url; ?>' readonly = '1' />
                    </div>

                    <div>
                    What you want to say:
                    <textarea id = 'TextArea_InputFeedback' style = 'width: 560px; height: 200px'></textarea>
                    </div>

                    <div style = 'margin-top: 5px'>
                        <input id = 'Input_FeedbackSubmit' type = 'button' value = 'Send Feedback!' style = 'font-size: 100%; height: 30px;' />
                        <input class = 'Input_CloseFeedback' type = 'button' value = 'Never mind..' style = 'font-size: 100%; height: 30px;' />
                        <?= $html->image('cycle.gif', array('style' => 'margin-right: 2px; vertical-align: middle; display: none;', 'id' => 'Img_FeedbackFormLoading')); ?>
                    </div>
                </form>
            </div>
            <div id = 'Div_FeedbackSuccess' style = 'display: none'>
                Thanks for giving us feedback! :D
                <input type = 'button' value = 'Close' class = 'Input_CloseFeedback' />
            </div>
            <div id = 'Div_FeedbackFailure' style = 'display: none'>
                An error has occurred. Sorry!
                <input type = 'button' value = 'Close' class = 'Input_CloseFeedback' />
            </div>
        </div>
    </div>
</div>