<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <?= $html->charset(); ?>

    <title>
        <?= Configure::read('debug') ? 'Debug ' : ''; ?>Almasy Tactics <?= $title_for_layout != '' ? '| ' . $title_for_layout : ''; ?>
    </title>

    <meta name = "Keywords" content = "Almasy, Tactics, Strategy game, play free, online game, role playing game, rpg, browser game, online game, game"/>

    <script type = 'text/javascript'>
        <? // Shortcuts on only if user is logged in and does not have disable_shortcuts turned on. ?>
        <? $shortcutsOn = isset($a_user) && !(isset($a_user['User']['disable_shortcuts']) && $a_user['User']['disable_shortcuts']); ?>
        var shortcutsOn = <?= $shortcutsOn ? 1 : 0; ?>;

        var currentUrl = '<?= h($_SERVER['REQUEST_URI']); ?>';
    </script>

    <?php
        echo $html->meta('icon', '/img/icon3.png', array('type' => 'icon'));
        echo $html->css('pack');
        echo $javascript->link('pack');
        echo $javascript->link('pages/default');
        echo $scripts_for_layout;
    ?>

    <!--[if IE 8]>
      <?= $html->css('ie8'); ?>
    <![endif]-->
</head>

<body>
    <?php if ($session->check('Message.flash')): ?>
        <?
            $msg = $session->read('Message.flash');
            $msg = htmlspecialchars($msg['message'], ENT_QUOTES);
            $session->del('Message.flash');
        ?>
        <div id = 'SessionDialog' style = 'display: none' title = 'Alert'>
            <div style = 'text-align: center; margin-bottom: 10px; font-weight: bold;'>
                <?= $msg; ?>
            </div>
            <input type = 'button' value = 'Okay' style = 'display: block; margin: 0 auto; width: 100px; height: 50px' class = 'ConfirmButton' />
        </div>
    <? endif; ?>

    <div style = 'width: 100%; position: relative;'>
        <div id = 'MainDiv' class = 'rounded-corners' style = '<?= Configure::read('debug') > 0 ? 'background-color: rgb(0, 50, 0); background-image: none' : ''; ?>'>
            <div style = 'position: relative;'>

                <div style = 'height: 125px'> </div>

                <div id = 'FeedbackLinkDiv'>
                    <a style = 'color: rgb(255, 255, 255)' href = '#' id = 'FeedbackLink'>
                        <?
                            $phrases = array('Tell us what you think about Almasy!', 'Comments about this page?', 'Anything wrong with this page?');
                            $phrase = $phrases[array_rand($phrases)];
                            echo $phrase;
                        ?>
                        <?= $html->image('cycle.gif', array('style' => 'margin-right: 2px; vertical-align: middle; display: none;', 'id' => 'Img_FormLoading')); ?>
                    </a>
                </div>

                <noscript>
                    <div id = 'Div_NoScript' class = 'rounded-corners'>
                        Warning: This site relies heavily on Javascript. Because you have Javascript disabled, many parts of the site will be nonfunctional. Please enable Javascript to play Almasy.
                    </div>
                </noscript>

                <? if (isset($faqsForPage) && (isset($a_user) && !$a_user['User']['hide_help_bar'])): ?>
                    <div id = 'HelpBar' class = 'rounded-corners'>
                        <b>Help Bar</b>

                        <? for ($i = 0; $i < count($faqsForPage); $i++) {
                                $link = '';
                                if ($faqsForPage[$i]['Faq']['link'] != '')
                                    $link = $faqsForPage[$i]['Faq']['link'];
                                else
                                    $link = '/faqs/#' . $faqsForPage[$i]['Faq']['id'];

                                echo $html->link2($faqsForPage[$i]['Faq']['question'], $link);
                                if ($i != count($faqsForPage) - 1)
                                    echo ' | ';
                            }
                        ?>
                    </div>
                <? endif; ?>

                <div id = 'ContentDiv' style = 'position: relative' class = 'rounded-corners'>
                    <?= $content_for_layout; ?>
                </div>

                <div id = 'NavigationDiv' class = 'rounded-corners'>
                    <table style = 'width: 100%'>
                        <tr>
                            <td>
                                <a href = "<?= $this->base; ?>/army" id = "ArmyLink">Army
                                    <? if ($shortcutsOn): ?>
                                        <span class = "NavigationLinkShortcut">(a)</span>
                                    <? endif; ?>
                                </a>
                            </td>
                            <td>
                                <a href = "<?= $this->base; ?>/battles" id = "BattleLink">War Room
                                    <? if ($shortcutsOn): ?>
                                        <span class = "NavigationLinkShortcut">(w)</span>
                                    <? endif; ?>
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <a href = "<?= $this->base; ?>/formations" id = "FormationsLink">Formations
                                    <? if ($shortcutsOn): ?>
                                        <span class = "NavigationLinkShortcut">(f)</span>
                                    <? endif; ?>
                                </a>
                            </td>
                            <td>

                                <a href = "<?= $this->base; ?>/items" id = "ArmoryLink">Armory
                                    <? if ($shortcutsOn): ?>
                                        <span class = "NavigationLinkShortcut">(y)</span>
                                    <? endif; ?>
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <a href = "<?= $this->base; ?>/forums/" id = "ForumsLink">Forums
                                    <? if ($shortcutsOn): ?>
                                        <span class = "NavigationLinkShortcut">(r)</span>
                                    <? endif; ?>
                                </a>
                            </td>
                            <td>
                                <a href = "<?= $this->base; ?>/help" id = "HelpLink">Help
                                    <? if ($shortcutsOn): ?>
                                        <span class = "NavigationLinkShortcut">(h)</span>
                                    <? endif; ?>
                                </a>
                            </td>
                        </tr>
                    </table>
                </div>

                <div id = 'LogoDiv'>
                    <?= $html->link2($html->image('new_logo2.png'), '/'); ?>
                </div>

                <div style = 'position: absolute; top: 105px; left: 50%; margin-left: -24px; display: none; border: 1px solid; padding: 2px; background-color: rgb(255, 255, 255); z-index: 2' id = 'Image_LoadAnim' class = 'rounded-corners'>
                    <?= $html->image('loader2.gif'); ?>
                </div>

                <? if (MAINTENANCE_MODE): ?>
                    <div style = 'position: absolute; top: 25px; left: 300px; font-size: 200%; color: rgb(255, 0, 0)'>
                        MAINTENANCE ON
                    </div>
                <? endif; ?>

                <? if ($this->name != 'CakeError'): // Don't show login dialog so people don't try to login. ?>
                    <div id = 'UserPane' class = 'rounded-corners'>
                        <? if (isset($a_user)): ?>
                            <div id = 'UserPaneContent'>
                                <?=
                                    $html->link2(
                                        $html->image('sprites/' . $a_user['User']['portrait'] . '.png', array('class' => 'face-icon', 'style' => 'height: 70px')),
                                        array('controller' => 'users', 'action' => 'change_portrait')
                                    );
                                ?>

                                <div style = 'position: absolute; top: 2px; left: 85px;'>
                                    <span style = 'border-bottom: 1px dotted;' class = 'ShrinkText' desiredWidth = '140px'>
                                        <?= $html->link2($a_user['User']['username'], array('controller' => 'users', 'action' => 'profile')); ?>
                                    </span>

                                    <div style = 'position: absolute; left: 0px; top: 30px;'>
                                        <?= $html->image('coins.png'); ?>
                                    </div>

                                    <div style = 'font-size: 85%; position: absolute; left: 30px; top: 31px;'>
                                        <span id = 'MoneyDisplay' money = '<?= $a_user['User']['money']; ?>'>
                                            <?= number_format($a_user['User']['money']); ?>
                                        </span>
                                        <div id = 'MoneyTimeTooltip' style = 'display: none'>
                                            Income: <?= $a_user['User']['income']; ?> yb <br />
                                            <?= $time->GetHourMinuteSecondString($a_user['User']['seconds_to_next_income_award']); ?> to next income
                                        </div>
                                    </div>

                                    <div style = 'position: absolute; top: 56px; left: 0px'>
                                        <?=
                                            $html->link(
                                                $html->image('S_Sword06.png'),
                                                array('controller' => 'battles', 'action' => 'matchmake'),
                                                array('id' => 'MatchmakeLink'),
                                                false
                                            );
                                        ?>
                                    </div>

                                    <div style = 'position: absolute; top: 56px; left: 30px; font-size: 90%;'>
                                        <div style = 'position: relative'>
                                            <span id = 'BattleSpan'>
                                                <?= $a_user['User']['num_battles']; ?>
                                            </span>
                                            <div id = 'BattleTimeTooltip' class = 'TimeTooltip'>
                                                <?= $time->GetHourMinuteSecondString($a_user['User']['seconds_to_next_battle_award']); ?> to next battle
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div style = 'position: absolute; top: 5px; right: 5px; font-size: 75%; text-align: right;'>
                                    <input type = 'button' value = 'Quit' id = 'LogoutButton' style = 'font-size: 8pt; width: 40px;' />
                                </div>

                                <? if (@$a_user['GuildMembership']): ?>
                                    <div style = 'position: absolute; bottom: 7px; right: 45px; font-size: 70%;'>
                                        <? if ($a_guild['Guild']['emblem'] !== ''): ?>
                                            <a href = '<?= $html->url(array('controller' => 'guilds', 'action' => 'view')); ?>'>
                                                <img src = '<?= $html->url(array('controller' => 'guilds', 'action' => 'emblem', $a_guild['Guild']['id'])); ?>' class = 'GuildEmblem' style = 'width: 25px' />
                                            </a>
                                        <? else: ?>
                                            <a href = '<?= $html->url(array('controller' => 'guilds', 'action' => 'view')); ?>'>
                                                Guild
                                            </a>
                                        <? endif; ?>
                                    </div>
                                <? endif; ?>

                                <div style = 'position: absolute; bottom: 4px; right: 8px;'>
                                    <?= $html->image('notifications.png', array('id' => 'Notifications')); ?>
                                    <?= $html->image('notifications_hover.png', array('id' => 'NotificationHover', 'style' => 'display: none')); ?>
                                </div>

                                <? $alertRed = $a_user['User']['num_unread_messages'] == 0 ? 'color: rgb(0, 0, 0); ' : 'color: rgb(200, 0, 0);'; ?>
                                <div id = 'MessageNotification' style = '<?= $alertRed; ?>'>
                                    <?= $html->link2($a_user['User']['num_unread_messages'], array('controller' => 'messages', 'action' => 'index')); ?>
                                </div>
                            </div>
                        <? else: ?>
                            <div id = 'UserPaneLoginContent'>
                                <form id = "UserLoginForm" method = "post" action = "<?= $this->base; ?>/users/login">

                                    <div style = 'position: absolute; top: 6px; left: 10px;'>
                                        <label for = "UserUsername">Username</label>
                                    </div>
                                    <div style = 'position: absolute; top: 10px; left: 80px;'>
                                        <input name = "data[User][username]" type = "text" maxlength = "45" id = "UserUsername" tabindex = '1' />
                                    </div>

                                    <div style = 'position: absolute; top: 41px; left: 10px;'>
                                        <label for = "UserPassword">Password</label>
                                    </div>
                                    <div style = 'position: absolute; top: 45px; left: 80px;'>
                                        <input type = "password" name = "data[User][password]" value = "" id = "UserPassword"  tabindex = '2' />
                                    </div>

                                    <div style = 'position: absolute; top: -5px; right: 4px; font-size: 80%;'>
                                        <?= $html->link2('Forgot?', array('controller' => 'users', 'action' => 'forgot')); ?>
                                    </div>

                                    <div style = 'position: absolute; top: 6px; left: 190px;'>
                                        <label for = "UserRememberMe">Remember</label>
                                    </div>

                                    <div style = 'position: absolute; top: 11px; left: 165px;'>
                                        <input type = "hidden" name = "data[User][remember_me]" id = "UserRememberMe_" value = "0" />
                                        <input type = "checkbox" name = "data[User][remember_me]" style = 'width: auto;' value = "1" id = "UserRememberMe" tabindex = '3' />
                                    </div>

                                    <div style = 'position: absolute; top: 30px; left: 170px;'>
                                        <input type = "submit" id = 'LoginButton' value = "Login" style = 'width: 90px; height: 35px' tabindex = '4' />
                                    </div>
                                </form>
                            </div>
                        <? endif; ?>
                    </div>
                <? endif; ?>
            </div>

            <div id = 'Footer'>
                Copyright, Almasy Tactics 2009-2011. Most updated browser recommended. IE6 is <u>not</u> an updated browser. <br />
                <?= $html->link2('About Us', '/help/who_we_are'); ?> |
                <?= $html->link2('Terms of Service', '/help/tos'); ?>
            </div>
        </div>
    </div>

    <script type="text/javascript">
    var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
    document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
    </script>
    <script type="text/javascript">
    try {
    var pageTracker = _gat._getTracker("UA-15746942-1");
    pageTracker._trackPageview();
    } catch(err) {}</script>

    <div id = 'FeedbackDialog'>
    </div>
</body>
</html>
