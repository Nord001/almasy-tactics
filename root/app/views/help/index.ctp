<div class = 'PageDiv'>

    <div class = 'PageTitle'>
        Help
    </div>

    <div class = 'PageContent' style = 'position: relative'>
        <? require '../views/help/navbar.ctp'; ?>

        <? if (!isset($a_user) || strtotime($a_user['User']['date_created']) > strtotime('-1 week')): ?>
            <div style = 'font-size: 160%; padding: 5px;'>
                <?= $html->link2('New to Almasy? Learn how to get started!', '/help/getting_started'); ?>
            </div>
        <? endif; ?>

        <div class = 'StatContent'>
            Welcome to the help section of Almasy! Here you'll hopefully get all your questions answered about how to play Almasy. It can be complex at times, so we hope this manual helps you get acquainted with everything! In addition to this guide, there are also commonly asked questions at the bottom of every page. If you ever find yourself confused by what you see on a page, there probably will be a question that'll help you get it. Good luck!
        </div>

        <div style = 'position: absolute; top: 207px; right: 45px'>
            <?= $html->image('help/savant_107x140.png'); ?>
        </div>
    </div>
</div>