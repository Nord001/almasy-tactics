<?

define('USER_CACHE', 'user');
define('USER_CACHE_DURATION', 'short');

define('USER_NUM_ONLINE_CACHE', 'user_num_online');
define('USER_NUM_ONLINE_CACHE_DURATION', 'fifteen_min');

define('PLAYED_THREE_DAYS_NOTE', 'playedThreeDays');
define('CAN_PROMOTE_NOTE', 'canPromote');

define('TOP_USERS_BY_WINS_CACHE', 'users_top_wins');
define('TOP_USERS_BY_WINS_CACHE_DURATION', 'fifteen_min');

define('TOP_USERS_BY_EARNINGS_CACHE', 'users_top_earnings');
define('TOP_USERS_BY_EARNINGS_CACHE_DURATION', 'fifteen_min');

class User extends AppModel {

    var $hasMany = array(
        'UserItem',
        'News',
        'Character',
        'AiScript',
        'Message' => array(
            'foreignKey' => 'receiver_id',
        )
    );

    var $hasOne = array(
        'GuildMembership',
    );

    //--------------------------------------------------------------------------------------------
    function GetUserByUsername ($username) {
        $user = $this->findByUsername($username);
        if ($user === false)
            return false;

        return $this->GetUser($user['User']['id']);
    }

    //--------------------------------------------------------------------------------------------
    function GetUser ($userId) {
        CheckNumeric($userId);

        $cacheKey = GenerateCacheKey(USER_CACHE, $userId);
        $user = Cache::read($cacheKey, USER_CACHE_DURATION);
        if ($user === false) {
            $user = $this->find('first', array(
                'conditions' => array(
                    'User.id' => $userId
                ),
                'contain' => array(
                    'GuildMembership',
                ),
            ));
            if ($user == false)
                return false;

            // Get total exp
            App::import('Model', 'Experience');
            $Experience = new Experience;
            $user['User']['total_exp_to_next_level'] = $Experience->GetExpForNextLevel($user['User']['level']);

            $notifications = $this->query("
                SELECT
                    `Notification`.`notification`
                FROM
                    `user_notifications` AS `Notification`
                WHERE
                    `Notification`.`user_id` = {$userId}", false);
            $notifications = Set::classicExtract($notifications, '{n}.Notification.notification');

            $user['User']['csrf_token'] = md5($user['User']['username'] . CSRF_SALT);

            $user['User']['SystemNotifications'] = $notifications;

            if (!is_numeric($user['GuildMembership']['id']))
                unset($user['GuildMembership']);

            Cache::write($cacheKey, $user, USER_CACHE_DURATION);
        }

        $user['User']['num_unread_messages'] = $this->Message->GetNumUnreadMessagesByReceiverId($userId);

        $timeToWait = $this->SecondsToGetBattle($user['User']['zeal'], $user['User']['num_battles']);
        $timeWaited = time() - strtotime($user['User']['last_battle_awarded']);
        $user['User']['seconds_to_next_battle_award'] = ceil($timeToWait - $timeWaited);

        $timeToWait = 60 * 60 * 24;
        $timeWaited = time() - strtotime($user['User']['last_income_awarded']);
        $user['User']['seconds_to_next_income_award'] = ceil($timeToWait - $timeWaited);

        $user['User']['income'] = $this->GetIncome($user['User']['greed']);

        return $user;
    }

    //---------------------------------------------------------------------------------------------
    function GetUsers ($userIds) {
        $data = array();
        foreach ($userIds as $userId)
            $data[] = $this->GetUser($userId);

        return $data;
    }

    //--------------------------------------------------------------------------------------------
    function ClearUserCache ($userId) {
        CheckNumeric($userId);

        $cacheKey = GenerateCacheKey(USER_CACHE, $userId);
        Cache::delete($cacheKey, USER_CACHE_DURATION);
    }

    //--------------------------------------------------------------------------------------------
    function GiveMoney ($userId, $amount) {
        CheckNumeric($userId);
        CheckNumeric($amount);

        $success = $this->query("
            UPDATE
                `users` as `User`
            SET
                `User`.`total_money_earned` = `User`.`total_money_earned` + {$amount},
                `User`.`money` = `User`.`money` + {$amount}
            WHERE
                `User`.`id` = {$userId}
        ");
        $this->ClearUserCache($userId);
        return $success !== false;
    }

    //--------------------------------------------------------------------------------------------
    function DeductMoney ($userId, $amount) {
        CheckNumeric($userId);
        CheckNumeric($amount);

        $this->id = $userId;
        $user = G($this->GetUser($userId));

        $money = $user['User']['money'];

        if ($money < $amount)
            return false;

        $this->fastSave('money', $money - $amount);
        $this->ClearUserCache($userId);

        return true;
    }

    //--------------------------------------------------------------------------------------------
    function ChangePassword ($userId, $newPassword) {
        CheckNumeric($userId);

        $this->id = $userId;
        $success = $this->saveField('password', Security::hash($newPassword, 'md5', true));
        if (!$success)
            return false;

        $user = $this->GetUser($userId);

        // Change password on forum
        if ($user['User']['forum_user_id']) {
            $result = file_get_contents(sprintf(
                'http://%s%s?key=%s&mode=change_password&forumUserId=%s&password=%s',
                $_SERVER['SERVER_NAME'],
                FORUM_HANDLER,
                FORUM_HANDLER_KEY,
                $user['User']['forum_user_id'],
                $newPassword
            ));

            if (strlen($result) == 0) {
                IERR('Could not change user\'s password on forum.');
                return;
            }

            $cookie = json_decode($result, true);
            setcookie($cookie['name'], $cookie['content'], $cookie['expire'], '/');
        }

        $this->ClearUserCache($userId);
        return true;
    }

    //--------------------------------------------------------------------------------------------
    function LevelUp ($userId, $levels = 1) {
        CheckNumeric($userId);
        CheckNumeric($levels);

        if ($levels == 0) return;

        $user = $this->GetUser($userId);
        $user['User']['level'] += $levels;
        $user['User']['stat_points'] += USER_STAT_POINTS_PER_LEVEL * $levels;

        $this->save($user);
        $this->ClearUserCache($userId);
    }

    //--------------------------------------------------------------------------------------------
    function GainExp ($userId, $exp) {
        CheckNumeric($userId);
        CheckNumeric($exp);

        App::import('Model', 'Experience');
        $Experience = new Experience;

        // Load fields
        $this->id = $userId;

        $user = $this->GetUser($userId);
        $startLevel = $user['User']['level'];
        $level = $startLevel;
        $currentExp = $user['User']['exp'];
        $currentExp += $exp;

        // Simulate leveling up until you can't level up anymore
        $expToNextLevel = $Experience->GetExpForNextLevel($level);
        while($currentExp >= $expToNextLevel) {
            if ($level >= CHARACTER_MAX_LEVEL)
                break;
            $currentExp -= $expToNextLevel;
            $level++;
            $expToNextLevel = $Experience->GetExpForNextLevel($level);
        }

        $levelChange = $level - $startLevel;
        if ($levelChange > MAX_LEVEL_GAIN) {
            $levelChange = MAX_LEVEL_GAIN;
            $level = $startLevel + $levelChange;
            $currentExp = 0;
        }

        $this->LevelUp($userId, $levelChange);

        if ($level != $startLevel)
            $this->fastSave('level', $level);

        $this->fastSave('exp', $currentExp);

        $this->ClearUserCache($userId);
    }

    //--------------------------------------------------------------------------------------------
    function ResetUser ($userId) {
        CheckNumeric($userId);

        // Delete characters, items, and formations
        $success = true;

        $this->id = $userId;

        $success = $this->Character->Formation->deleteAll(array('Formation.user_id' => $userId));
        if (!$success) {
            IERR('Error when resetting user: Could not remove formations.');
            return false;
        }

        $success = $this->UserItem->deleteAll(array('UserItem.user_id' => $userId));
        if (!$success) {
            IERR('Error when resetting user: Could not remove items.');
            return false;
        }

        $success = $this->Character->deleteAll(array('Character.user_id' => $userId));
        if (!$success) {
            IERR('Error when resetting user: Could not remove characters.');
            return false;
        }

        $success = $this->SetupNewUser($userId);
        if (!$success) {
            IERR('Error when resetting user: Could not give new data.');
            return false;
        }

        $this->UserItem->ClearUserItemCacheByUser($userId);
        $this->Character->Formation->ClearFormationsCacheByUser($userId);
        $this->Character->ClearCharacterIdsCacheByUser($userId);
        $this->ClearUserCache($userId);

        return true;
    }

    //--------------------------------------------------------------------------------------------
    function SetupNewUser ($userId) {
        CheckNumeric($userId);

        $success = true;

        $this->begin();

        do {
            $this->id = $userId;

            // Reset fields
            $success = $this->save(array(
                'money' => STARTING_MONEY,
                'num_battles' => STARTING_BATTLES,
                'last_battle_awarded' => date(DB_FORMAT),
                'last_income_awarded' => date(DB_FORMAT),
                'battles_won' => 0,
                'battles_lost' => 0,
                'total_money_earned' => 0,
                'total_bounty_earned' => 0,
                'level' => 1,
                'exp' => 0,
                'zeal' => 1,
                'greed' => 1,
                'ambition' => 1,
                'stat_points' => 0,
            ));
            if ($success === false) {
                IERR('Failed to save user.');
                break;
            }

            $firstCharacterName = $this->field('first_character_name');

            // Save new characters for user
            $newParty = NEW_CHARACTER_PARTY();
            $newParty[0][0] = $firstCharacterName;
            $characterIds = array();
            $isFirstCharacter = true;
            foreach ($newParty as $character) {
                $newData = array(
                    'name'         => $character[0],
                    'affinity'     => mt_rand(0, NUM_AFFINITIES - 1),
                    'str'          => mt_rand(5, 15),
                    'vit'          => mt_rand(5, 15),
                    'int'          => mt_rand(5, 15),
                    'luk'          => mt_rand(5, 15),
                    'growth_str'   => $character[1] + mt_rand(-500, 500) / 500 * NEW_CHARACTER_STAT_VARIATION,
                    'growth_vit'   => $character[2] + mt_rand(-500, 500) / 500 * NEW_CHARACTER_STAT_VARIATION,
                    'growth_int'   => $character[3] + mt_rand(-500, 500) / 500 * NEW_CHARACTER_STAT_VARIATION,
                    'growth_luk'   => $character[4] + mt_rand(-500, 500) / 500 * NEW_CHARACTER_STAT_VARIATION,
                    'date_created' => date(DB_FORMAT),
                    'class_id'     => 1, // Novice
                    'user_id'      => $userId,
                    'has_custom_name' => $isFirstCharacter ? 1 : 0,
                );

                $this->Character->create();
                $success = $this->Character->save($newData);
                if ($success === false) {
                    IERR('Failed to save character.');
                    break;
                }

                $characterIds[] = $this->Character->id;

                // If starting class isn't blank, change to appropriate class
                $this->Character->LevelUp($this->Character->id, 3);
                if ($character[7] != 0) {
                    $this->Character->ChangeClass($this->Character->id, $character[7]);
                }

                // Give items to character
                $weapId = $this->UserItem->GiveUserItemToUser($character[5], $userId);
                $armorId = $this->UserItem->GiveUserItemToUser($character[6], $userId);

                $this->Character->EquipItem($this->Character->id, $weapId);
                $this->Character->EquipItem($this->Character->id, $armorId);

                $isFirstCharacter = false;
            }

            // Save new formations for user

            $formationName = $firstCharacterName . "'s Raiders";
            $tempName = $formationName;
            $i = 1;
            while ($this->Character->Formation->GetFormationByName($tempName) !== false) {
                $tempName = $formationName . ' ' . $i++;
            }

            $newFormationData = array(
                'user_id' => $userId,
                'name' => $tempName,
                'date_created' => date(DB_FORMAT),
            );

            $success = $this->Character->Formation->save($newFormationData);
            if ($success === false) {
                IERR('Could not save formation.');
                break;
            }

            $formationId = $this->Character->Formation->id;

            // Save characters into formation
            $n = 0;
            foreach ($characterIds as $characterId) {
                $success = $this->query("
                    INSERT INTO
                        characters_formations
                    (
                        character_id,
                        formation_id,
                        position
                    ) VALUES (
                        {$characterId},
                        {$formationId},
                        {$n}
                    )");
                if ($success === false)
                    break;
                $n++;
            }
            if ($success === false) {
                IERR('Could not save character into formation.');
                break;
            }

            // Setup formation reputation.
            $success = $this->Character->Formation->ResetReputation($formationId);
            if ($success == false)
                break;

            $this->commit();

            return true;
        } while (false);

        $this->rollback();
        return false;
    }

    //--------------------------------------------------------------------------------------------
    function CreateNewUser ($username, $password, $email, $firstCharacterName, $referringUserId) {

        $success = true;

        $password_hash = Security::hash($password, 'md5', true);
        // Save user
        $user = array(
            'username' => $username,
            'password' => $password_hash,
            'email' => $email,
            'date_created' => date(DB_FORMAT),
            'last_action' => date(DB_FORMAT),
            'first_character_name' => $firstCharacterName,
            'num_battles' => 30,
            'last_battle_awarded' => date(DB_FORMAT),
            'last_income_awarded' => date(DB_FORMAT),
        );

        if ($referringUserId !== false)
            $user['referring_id'] = $referringUserId;

        $success = $this->save($user);
        if (!$success) {
            IERR('Error when creating new user: could not save user.');
            return false;
        }

        $userId = $this->id;

        $success = $this->SetupNewUser($userId);
        if ($success === false) {
            IERR('Error when creating new user: could not setup new user');
            return false;
        }

        $success = $this->AiScript->GiveStartingScripts($userId);
        if ($success === false) {
            IERR('Could not give starting scripts.');
            return false;
        }

        // Create forum account
        if (Configure::read('test') != 1) {
            $result = file_get_contents(sprintf(
                'http://%s%s?key=%s&mode=register&username=%s&password=%s&email=%s&ip=%s',
                $_SERVER['SERVER_NAME'],
                FORUM_HANDLER,
                FORUM_HANDLER_KEY,
                $username,
                $password,
                $email,
                $_SERVER['REMOTE_ADDR']
            ));

            if (strlen($result) == 0) {
                IERR('Failed to create forum account.');
                return false;
            }

            $data = json_decode($result, true);

            if ($data === null) {
                IERR('Failed to decode forum JSON.');
                return false;
            }

            setcookie($data['name'], $data['content'], $data['expire'], '/');

            $forumUserId = $data['id'];
            $success = $this->saveField('forum_user_id', $forumUserId);
            if (!$success)
                return false;

        }

        // Send notification to referring user
        if ($referringUserId !== false) {
            $link = sprintf('http://%s/users/referrals', $_SERVER['SERVER_NAME']);
            $message = sprintf(
                "Thanks to your referral, <b>%s</b> has started playing Almasy! " .
                "Thanks for helping to grow our community and bring more awesome players " .
                "to the game.\n\n" .
                "To show our thanks, you'll receive a couple of bonuses if %s continues to play Almasy. " .
                "You can read about them here:\n\n" .
                "<a href = '%s'>%s</a>\n\n" .
                "Thanks for playing Almasy!\n" .
                "The Almasy Team",
                $username,
                $username,
                $link,
                $link
            );
            $this->Message->SendNotification($referringUserId, 'Congratulations!', $message);
        }

        // Send welcome notifications
        $message = sprintf(
            "Welcome to Almasy, %s! Thanks for being interested enough to play our game! We hope you have a great time! " .
            "We know getting started can be pretty complicated, so we want to help you as much as possible while you're " .
            "getting the hang of things. Here are some resources at your disposal:\n" .
            "<ul><li>The <b><a href = '/help/getting_started'>Getting Started</a></b> guide is a great way to get started, obviously. Very recommended reading!</li>" .
            "<li>At the top of every page is a <b>Help Bar</b> which has many FAQs for each page that you're looking at. " .
            "If you ever feel overwhelmed by something on a page or don't know what something means, check there first to " .
            "see if we've answered your question.</li>" .
            "<li>The general <b><a href = '/help'>Help</a></b> section can probably answer any other questions you might have.</li></ul>" .
            "Well, we hope you have a great time! Let us know if you have any comments or questions!\n\n" .
            "Thanks for playing Almasy!\n" .
            "The Almasy Team",
            $username
        );
        $this->Message->SendNotification($userId, 'Welcome to Almasy!', $message);

        return true;
    }

    //--------------------------------------------------------------------------------------------
    function Ping ($userId) {
        CheckNumeric($userId);

        $user = $this->GetUser($userId);
        $timeSinceLastPing = time() - strtotime($user['User']['last_action']);
        // If time since last ping was within 15 minutes, consider it as staying
        // on the site.
        if ($timeSinceLastPing < 60 * 15 && $user['User']['admin'] == 0) {
            $this->query(sprintf("
                INSERT INTO
                    `user_activity`
                (
                    user_id,
                    duration,
                    time,
                    page
                ) VALUES (
                    {$userId},
                    {$timeSinceLastPing},
                    '%s',
                    '%s'
                )",
                date(DB_FORMAT),
                mysql_escape_string($_SERVER['REQUEST_URI'])
            ));
        }

        $this->query(sprintf("
            UPDATE
                `users` as `User`
            SET
                `User`.`last_action` = '%s',
                `User`.`last_ip` = '%s'
            WHERE
                `User`.`id` = {$userId}",
            date(DB_FORMAT),
            $_SERVER['REMOTE_ADDR']
        ));
        $this->ClearUserCache($userId);
    }

    //--------------------------------------------------------------------------------------------
    function SecondsToGetBattle ($zeal, $numBattles) {
        $seconds = $numBattles * 30 + pow($numBattles, 2.6) * 1.1 + 100;
        $seconds *= BATTLE_AWARD_RATE;
        return $seconds;
    }

    //--------------------------------------------------------------------------------------------
    function GetIncome ($greed) {
        return intval(STARTING_INCOME * (pow(1.05, $greed) + $greed / 2));
    }

    //--------------------------------------------------------------------------------------------
    function AwardBattles ($userId) {
        CheckNumeric($userId);

        $user = $this->GetUser($userId);

        $lastBattleAwarded = strtotime($user['User']['last_battle_awarded']);

        $secondsElapsed = time() - $lastBattleAwarded;
        $currentBattles = $user['User']['num_battles'];
        while ($secondsElapsed > $this->SecondsToGetBattle($user['User']['zeal'], $currentBattles)) {
            $secondsElapsed -= $this->SecondsToGetBattle($user['User']['zeal'], $currentBattles);
            $currentBattles++;
        }

        if ($currentBattles != $user['User']['num_battles']) {
            $this->id = $userId;
            $this->fastSave('num_battles', $currentBattles);
            $this->fastSave('last_battle_awarded', date(DB_FORMAT, time() - $secondsElapsed));
            $this->ClearUserCache($userId);
        }
    }

    //--------------------------------------------------------------------------------------------
    function AwardDailyIncome ($userId) {
        CheckNumeric($userId);

        $user = $this->GetUser($userId);

        $lastIncomeAward = strtotime($user['User']['last_income_awarded']);

        $secondsElapsed = time() - $lastIncomeAward;

        $days = intval($secondsElapsed / (60 * 60 * 24));
        $secondsElapsed -= $days * 60 * 60 * 24;

        if ($days > 0) {
            $income = $this->GetIncome($user['User']['greed']);
            $amount = $days * $income;

            $this->GiveMoney($userId, $amount);
            $this->id = $userId;
            $this->fastSave('last_income_awarded', date(DB_FORMAT,  time() - $secondsElapsed));
        }
    }

    //--------------------------------------------------------------------------------------------
    function GiveVictory ($userId) {
        CheckNumeric($userId);

        $this->query("
            UPDATE
                `users` as `User`
            SET
                `User`.`battles_won` = `User`.`battles_won` + 1
            WHERE
                `User`.`id` = {$userId}
        ");
        $this->ClearUserCache($userId);
    }

    //--------------------------------------------------------------------------------------------
    function GiveLoss ($userId) {
        CheckNumeric($userId);

        $this->query("
            UPDATE
                `users` as `User`
            SET
                `User`.`battles_lost` = `User`.`battles_lost` + 1
            WHERE
                `User`.`id` = {$userId}
        ");
        $this->ClearUserCache($userId);
    }

    //--------------------------------------------------------------------------------------------
    function GiveLevel1Prize ($userId) {
        CheckNumeric($userId);

        $user = $this->GetUser($userId);
        $money = $user['User']['money'];
        $reward = intval(max($money + 5000, $money * 1.2));
        $change = $reward - $money;
        $change = min($change, 100000);

        $this->GiveMoney($user['User']['id'], $change);

        return $change;
    }

    //--------------------------------------------------------------------------------------------
    function GiveLevel2Prize ($userId) {
        CheckNumeric($userId);

        $user = $this->GetUser($userId);
        $money = $user['User']['money'];
        $reward = intval(max($money + 5000, $money * 1.2));
        $change = $reward - $money;
        $change = min($change, 100000);

        $this->GiveMoney($user['User']['id'], $change);

        $this->UserItem->GiveUserItemToUser(REFERRAL_SYSTEM_LEVEL_2_USER_ITEM_ID, $userId);

        return $change;
    }

    //--------------------------------------------------------------------------------------------
    function GiveLevel3Prize ($userId) {
        CheckNumeric($userId);

        $user = $this->GetUser($userId);
        $money = $user['User']['money'];
        $reward = intval(max($money + 5000, $money * 1.2));
        $change = $reward - $money;
        $change = min($change, 100000);

        $this->GiveMoney($user['User']['id'], $change);

        $item1Choices = REFERRAL_SYSTEM_LEVEL_3_1_USER_ITEM_ID_LIST();
        $item2Choices = REFERRAL_SYSTEM_LEVEL_3_2_USER_ITEM_ID_LIST();

        $choice = mt_rand(0, count($item1Choices) - 1);
        $this->UserItem->GiveUserItemToUser($item1Choices[$choice], $userId);
        $choice = mt_rand(0, count($item2Choices) - 1);
        $this->UserItem->GiveUserItemToUser($item2Choices[$choice], $userId);

        return $change;
    }

    //--------------------------------------------------------------------------------------------
    function CheckReferrals ($userId) {
        CheckNumeric($userId);

        $user = $this->GetUser($userId);

        if ($user['User']['referring_id'] != null) {
            if ($user['User']['referring_bonuses_given'] == 0) {
                if ($user['User']['battles_won'] >= REFERRAL_SYSTEM_LEVEL_1_REQ_BATTLES) {
                    $referringUser = $this->GetUser($user['User']['referring_id']);

                    // Give prize to both referrer and referree
                    $referrerAwardedAmount = $this->GiveLevel1Prize($user['User']['referring_id']);
                    $referreeAwardedAmount = $this->GiveLevel1Prize($userId);

                    // Record that level 1 prize given for referred person
                    $this->id = $userId;
                    $this->fastSave('referring_bonuses_given', 1);
                    $this->ClearUserCache($userId);

                    // Message referring player
                    $link = sprintf('http://%s/users/referrals', $_SERVER['SERVER_NAME']);
                    $username = $user['User']['username'];
                    $message = sprintf(
                        "%s has won 70 battles! For referring %s to Almasy, we've given you <b>%s yb</b>. You'll get " .
                        " an even better reward if %s wins 150 battles! Thanks for growing our community! \n\n" .
                        "You can check out your referrals here: \n\n" .
                        "<a href = '%s'>%s</a>\n\n" .
                        "The Almasy Team",
                        $username,
                        $username,
                        number_format($referrerAwardedAmount),
                        $username,
                        $link,
                        $link
                    );
                    $this->Message->SendNotification($user['User']['referring_id'], 'Congratulations!', $message);

                    // Message referred player
                    $username = $referringUser['User']['username'];
                    $message = sprintf(
                        "You've won 70 battles! Since you were referred to Almasy by <b>%s</b>, you get a prize of <b>%s yb</b>! " .
                        "You'll get an even better reward if you win 150 battles!\n\n" .
                        "The Almasy Team",
                        $username,
                        number_format($referreeAwardedAmount)
                    );
                    $this->Message->SendNotification($userId, 'Congratulations!', $message);
                }
            } else if ($user['User']['referring_bonuses_given'] == 1) {
                if ($user['User']['battles_won'] >= REFERRAL_SYSTEM_LEVEL_2_REQ_BATTLES) {
                    $referringUser = $this->GetUser($user['User']['referring_id']);

                    // Give prize to both referrer and referree
                    $referrerAwardedAmount = $this->GiveLevel2Prize($user['User']['referring_id']);
                    $referreeAwardedAmount = $this->GiveLevel2Prize($userId);

                    // Record that level 1 prize given for referred person
                    $this->id = $userId;
                    $this->fastSave('referring_bonuses_given', 2);
                    $this->ClearUserCache($userId);

                    // Message referring player
                    $link = sprintf('http://%s/users/referrals', $_SERVER['SERVER_NAME']);
                    $username = $user['User']['username'];
                    $message = sprintf(
                        "%s has won 150 battles! For referring %s to Almasy, we've given you <b>%s yb</b>. " .
                        "In addition, you also get the unique armor " .
                        "<span style = 'font-weight: bold; color: rgb(200, 0, 240)'>Friendship's Bond</span>! " .
                        "You'll get an even better reward if %s wins 250 battles! " .
                        "Thanks so much for contributing to the community!\n\n" .
                        "You can check out your referrals here: \n\n" .
                        "<a href = '%s'>%s</a>\n\n" .
                        "The Almasy Team",
                        $username,
                        $username,
                        number_format($referrerAwardedAmount),
                        $username,
                        $link,
                        $link
                    );
                    $this->Message->SendNotification($user['User']['referring_id'], 'Congratulations!', $message);

                    // Message referred player
                    $username = $referringUser['User']['username'];
                    $message = sprintf(
                        "You've won 150 battles! Since you were referred to Almasy by <b>%s</b>, you get a prize of <b>%s yb</b>! " .
                        "In addition, you also get the unique armor " .
                        "<span style = 'font-weight: bold; color: rgb(200, 0, 240)'>Friendship's Bond</span>! " .
                        "You'll get an even better reward if you win 250 battles!\n\n" .
                        "The Almasy Team",
                        $username,
                        number_format($referreeAwardedAmount)
                    );
                    $this->Message->SendNotification($userId, 'Congratulations!', $message);
                }
            } else if ($user['User']['referring_bonuses_given'] == 2) {
                if ($user['User']['battles_won'] >= REFERRAL_SYSTEM_LEVEL_3_REQ_BATTLES) {
                    $referringUser = $this->GetUser($user['User']['referring_id']);

                    // Give prize to both referrer and referree
                    $referrerAwardedAmount = $this->GiveLevel3Prize($user['User']['referring_id']);
                    $referreeAwardedAmount = $this->GiveLevel3Prize($userId);

                    // Record that level 1 prize given for referred person
                    $this->id = $userId;
                    $this->fastSave('referring_bonuses_given', 3);
                    $this->ClearUserCache($userId);

                    // Message referring player
                    $link = sprintf('http://%s/users/referrals', $_SERVER['SERVER_NAME']);
                    $username = $user['User']['username'];
                    $message = sprintf(
                        "%s has won 250 battles! For referring %s to Almasy, we've given you %s yb. " .
                        "In addition, you also get the unique weapons " .
                        "<span style = 'font-weight: bold; color: rgb(200, 0, 240)'>Blade of Loyalty</span> " .
                        " and <span style = 'font-weight: bold; color: rgb(200, 0, 240)'>Jade of Devotion</span>! " .
                        "Thanks so much for contributing to the community!\n\n" .
                        "The Almasy Team",
                        $username,
                        $username,
                        number_format($referrerAwardedAmount)
                    );
                    $this->Message->SendNotification($user['User']['referring_id'], 'Congratulations!', $message);

                    // Message referred player
                    $username = $referringUser['User']['username'];
                    $message = sprintf(
                        "You've won 250 battles! Since you were referred to Almasy by <b>%s</b>, you get a prize of <b>%s yb</b>! " .
                        "In addition, you also get the unique weapons " .
                        "<span style = 'font-weight: bold; color: rgb(200, 0, 240)'>Blade of Loyalty</span> " .
                        " and <span style = 'font-weight: bold; color: rgb(200, 0, 240)'>Jade of Devotion</span>!\n\n" .
                        "The Almasy Team",
                        $username,
                        number_format($referreeAwardedAmount)
                    );
                    $this->Message->SendNotification($userId, 'Congratulations!', $message);
                }
            }
        }
    }

    //--------------------------------------------------------------------------------------------
    function OnUserPage ($userId) {
        CheckNumeric($userId);

        $this->AwardBattles($userId);
        $this->CheckReferrals($userId);
        //$this->AwardDailyIncome($userId);

        $user = $this->GetUser($userId);
        if (in_array(PLAYED_THREE_DAYS_NOTE, $user['User']['SystemNotifications'], true) === false) {
            if (strtotime($user['User']['date_created']) < time() - 60 * 60 * 24 * 3) {

                $this->RecordSystemNotification($userId, PLAYED_THREE_DAYS_NOTE);
                $message = sprintf(
                    "You've been playing Almasy for three days! That's awesome! We'd like to give " .
                    "you a small prize of five battles to help your leveling :) Enjoy!\n\n" .
                    "The Almasy Team"
                );
                $this->Message->SendNotification($userId, 'Hey!', $message);

                $numBattles = $user['User']['num_battles'] + 5;
                $this->id = $user['User']['id'];
                $this->fastSave('num_battles', $numBattles);
                $this->ClearUserCache($userId);
            }
        }

        // New user log - Log every action of users less than a day old.
        if (strtotime($user['User']['date_created']) > time() - 60 * 60 * 24) {
            $this->query(sprintf("
                INSERT INTO
                    `new_user_activity`
                (
                    user_id,
                    time,
                    page
                ) VALUES (
                    {$userId},
                    '%s',
                    '%s'
                )",
                date(DB_FORMAT),
                mysql_escape_string($_SERVER['REQUEST_URI'])
            ));
        }
    }

    //--------------------------------------------------------------------------------------------
    function GetNumOnlineUsers () {
        $numOnline = Cache::read(USER_NUM_ONLINE_CACHE, USER_NUM_ONLINE_CACHE_DURATION);
        if ($numOnline !== false)
            return $numOnline;

        $numOnline = $this->find('count', array(
            'conditions' => array(
                'last_action >' => date(DB_FORMAT, strtotime('-30 minutes')),
                'admin' => 0,
            ),
        ));

        if ($numOnline === false)
            $numOnline = 0;

        Cache::write(USER_NUM_ONLINE_CACHE, $numOnline, USER_NUM_ONLINE_CACHE_DURATION);
        return $numOnline;
    }

    //--------------------------------------------------------------------------------------------
    function GetNumNewAccountsToday () {

        $numAccounts = $this->find('count', array(
            'conditions' => array(
                'date_created >' => date(DB_FORMAT, strtotime('-1 day')),
            ),
        ));

        return $numAccounts;
    }

    //--------------------------------------------------------------------------------------------
    function GetNumNewReferredAccountsToday () {

        $numAccounts = $this->find('count', array(
            'conditions' => array(
                'date_created >' => date(DB_FORMAT, strtotime('-1 day')),
                'referring_id IS NOT NULL'
            ),
        ));

        return $numAccounts;
    }

    //--------------------------------------------------------------------------------------------
    function SendResetKey ($userId) {
        CheckNumeric($userId);

        $this->id = $userId;
        $resetKey = sha1(microtime() . USER_RESET_KEY_SALT);
        $success = $this->saveField('reset_key', $resetKey);
        $this->ClearUserCache($userId);

        if (!$success) {
            IERR('Could not save reset key for user.');
            return false;
        }

        $to = $this->field('email');

        // subject
        $subject = 'Almasy Tactics Reset Password';

        $link = sprintf('http://%s/users/reset_password/%s/%s', $_SERVER['SERVER_NAME'], $this->field('username'), $resetKey);

        // message
        $message = '
        <html>
        <body>
          <p>
            You are receiving this email because a password reset was requested for your account on Almasy.
            If this is not the case, please ignore the email.
          </p>

          <p>To reset your password, please click on the following link:</p>

          <p><a href = \'' . $link . '\'>' . $link . '</a></p>

          <p>Thanks!</p>

          <p>The Almasy Team</p>
        </body>
        </html>
        ';

        // To send HTML mail, the Content-type header must be set
        $headers  = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

        // Additional headers
        $headers .= 'From: Almasy Tactics <dontreply@almasytactics.com>' . "\r\n";

        // Mail it
        $success = @mail($to, $subject, $message, $headers);

        if (!$success) {
            IERR('Could not send reset mail.');
            return false;
        }

        return true;
    }

    //--------------------------------------------------------------------------------------------
    function IncreaseStat ($userId, $stat) {
        $this->id = $userId;
        $user = $this->GetUser($userId);
        if ($user === false)
            return false;

        $points = $user['User']['stat_points'];

        if ($points <= 0)
            return false;

        $this->fastSave('stat_points', $points - 1);

        $statAmount = $user['User'][$stat];
        $this->fastSave($stat, $statAmount + 1);

        $this->ClearUserCache($userId);

        return true;
    }

    //--------------------------------------------------------------------------------------------
    function RecordSystemNotification ($userId, $name) {
        $time = date(DB_FORMAT);
        $this->query(
            "INSERT INTO
                `user_notifications`
            (
                `user_id`,
                `notification`,
                `time`
            ) VALUES (
                {$userId},
                '{$name}',
                '{$time}'
            );");
        $this->ClearUserCache($userId);
    }

    //---------------------------------------------------------------------------------------------
    function GetTopUserIdsByWins () {
        $userIds = Cache::read(TOP_USERS_BY_WINS_CACHE, TOP_USERS_BY_WINS_CACHE_DURATION);
        if ($userIds !== false)
            return $userIds;

        $userIds = $this->find('all', array(
            'conditions' => array(
                'User.admin' => 0,
                'User.state' => USER_STATE_NORMAL,
            ),
            'order' => 'User.battles_won DESC',
            'limit' => 5,
        ));
        $userIds = Set::classicExtract($userIds, '{n}.User.id');
        Cache::write(TOP_USERS_BY_WINS_CACHE, $userIds, TOP_USERS_BY_WINS_CACHE_DURATION);

        return $userIds;
    }

    //---------------------------------------------------------------------------------------------
    function GetTopUserIdsByEarnings () {
        $userIds = Cache::read(TOP_USERS_BY_EARNINGS_CACHE, TOP_USERS_BY_EARNINGS_CACHE_DURATION);
        if ($userIds !== false)
            return $userIds;

        $userIds = $this->find('all', array(
            'conditions' => array(
                'User.admin' => 0,
                'User.state' => USER_STATE_NORMAL,
            ),
            'order' => 'User.total_money_earned DESC',
            'limit' => 5,
        ));
        $userIds = Set::classicExtract($userIds, '{n}.User.id');
        Cache::write(TOP_USERS_BY_EARNINGS_CACHE, $userIds, TOP_USERS_BY_EARNINGS_CACHE_DURATION);

        return $userIds;
    }

    //---------------------------------------------------------------------------------------------
    function DeductBattle ($userId) {
        CheckNumeric($userId);

        $user = $this->GetUser($userId);
        if ($user['User']['num_battles'] <= 0)
            return false;

        $this->query("
            UPDATE
                `users` as `User`
            SET
                `User`.`num_battles` = `User`.`num_battles` - 1
            WHERE
                `User`.`id` = {$userId}
        ");
        $this->ClearUserCache($userId);
        return true;
    }

    //--------------------------------------------------------------------------------------------
    function Email ($subject, $content, $userId = null) {
        // To send HTML mail, the Content-type header must be set
        $headers  = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

        // Additional headers
        $headers .= 'From: Almasy Tactics <dontreply@almasytactics.com>' . "\r\n";

        // Mail it
        $success = true;
        if ($userId != null) {
            $user = $this->GetUser($userId);
            $success = mail($user['User']['email'], $subject, $content, $headers);
        } else {
            $emails = $this->query("SELECT DISTINCT `email` from `users` WHERE `email` <> ''");
            $emails = Set::classicExtract($emails, '{n}.users.email');

            foreach ($emails as $email)
                $success &= mail($email, $subject, $content, $headers);
        }

        return $success;
    }

    //--------------------------------------------------------------------------------------------
    function GetNumUsersPlayedToday () {
        $numUsers = $this->find('count', array(
            'conditions' => array(
                'last_action >' => date(DB_FORMAT, strtotime('-1 day')),
            ),
        ));

        return $numUsers;
    }

    //--------------------------------------------------------------------------------------------
    function GetTimeSpentOnSiteToday () {
        $time = $this->query(sprintf("
            SELECT
                SUM(`duration`) as `time`
            FROM
                `user_activity`
            WHERE
                `time` > '%s'",
            date(DB_FORMAT, strtotime('-1 day'))
        ));

        return $time[0][0]['time'];
    }

    //--------------------------------------------------------------------------------------------
    function GetTimeSpentOnSite ($range) {
        $time = $this->query(sprintf("
            SELECT
                SUM(`duration`) / 60 / 60 AS `timeSpent`,
                UNIX_TIMESTAMP(DATE_FORMAT(DATE_SUB(`time`, INTERVAL 2 HOUR), '%%Y-%%m-%%d')) AS `day`
            FROM
                `user_activity`
            WHERE
                DATE_SUB(`time`, INTERVAL 2 HOUR) > '%s'
            GROUP BY
                `day`;",
            date('Y-m-d', $range)
        ));

        $times = Set::combine($time, '{n}.0.day', '{n}.0.timeSpent');
        return $times;
    }

    //--------------------------------------------------------------------------------------------
    function GetPlayers ($range) {
        $time = $this->query(sprintf("
            SELECT
                COUNT(DISTINCT `user_id`) AS `players`,
                UNIX_TIMESTAMP(DATE_FORMAT(DATE_SUB(`time`, INTERVAL 2 HOUR), '%%Y-%%m-%%d')) AS `day`
            FROM
                `user_activity`
            WHERE
                DATE_SUB(`time`, INTERVAL 2 HOUR) > '%s'
            GROUP BY
                `day`;",
            date('Y-m-d', $range)
        ));

        $players = Set::combine($time, '{n}.0.day', '{n}.0.players');
        unset($players[0]);
        return $players;
    }

    //--------------------------------------------------------------------------------------------
    function GetPersonalRankings ($userId) {
        CheckNumeric($userId);

        $userFormationIds = $this->Character->Formation->GetFormationIdsByUserId($userId);
        $userFormations = $this->Character->Formation->GetFormations($userFormationIds);

        $rankings = $this->Character->Formation->GetFormationRankings();

        $personalRankings = range(1, 5);

        // Add the 10 formations around the rank of each formation of the user.
        foreach ($userFormations as $formation) {
            if (!isset($rankings['RankByFormation'][$formation['Formation']['id']]))
                continue;

            $rank = $rankings['RankByFormation'][$formation['Formation']['id']];

            for ($i = $rank - 5; $i < $rank + 5; $i++)
                $personalRankings[] = $i;
        }

        $personalRankings = array_unique($personalRankings);
        sort($personalRankings);

        $formationRankData = array();
        foreach ($personalRankings as $ranking) {
            if (!isset($rankings['FormationRankings'][$ranking]))
                continue;

            $formationRankData[] = array('FormationId' => $rankings['FormationRankings'][$ranking], 'Rank' => $ranking);
        }

        return $formationRankData;
    }

    //--------------------------------------------------------------------------------------------
    function RegisterLogin ($userId) {
        CheckNumeric($userId);

        $time = date(DB_FORMAT);
        $this->id = $userId;
        $this->fastSave('last_login', $time);
        $this->fastSave('user_agent', isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '');
        $this->ClearUserCache($userId);
    }
}

?>
