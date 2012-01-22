<?

define('GUILD_CACHE', 'guild');

//--------------------------------------------------------------------------------------------
function GuildLevelUpCost ($level) {
    return $level * $level * 1000000;
}

//--------------------------------------------------------------------------------------------
function GuildSizeUpgradeCost ($level) {
    $square = $level * $level;
    $cost = $square * 8 * 1000000;
    return $cost;
}

//--------------------------------------------------------------------------------------------
function GuildUpkeep ($numPlayers) {
    return $numPlayers * $numPlayers * 10000;
}

//--------------------------------------------------------------------------------------------
class Guild extends AppModel {

    var $hasMany = array(
        'GuildMembership',
        'GuildInvite',
        'GuildMessage',
        'GuildTransaction',
    );

    var $knows = array('User', 'Message');

    //--------------------------------------------------------------------------------------------
    function GetGuild ($guildId) {
        CheckNumeric($guildId);

        $cacheKey = GenerateCacheKey(GUILD_CACHE, $guildId);
        $guild = CacheRead($cacheKey);
        if ($guild === false) {
            $guild = $this->find('first', array(
                'conditions' => array(
                    'Guild.id' => $guildId,
                    'Guild.dissolved' => 0,
                ),
            ));
            if ($guild === false)
                return false;

            CacheWrite($cacheKey, $guild);
        }

        $guild['Guild']['GuildMembership'] = $this->GuildMembership->GetGuildMembership($guildId);
        $guild['Guild']['level_up_cost'] = GuildLevelUpCost($guild['Guild']['level']);
        $guild['Guild']['size_upgrade_cost'] = GuildSizeUpgradeCost($guild['Guild']['size_level']);
        $guild['Guild']['upkeep_cost'] = GuildUpkeep(count($guild['Guild']['GuildMembership']));

        $leader = $this->User->GetUser($guild['Guild']['leader_id']);
        $guild['Guild']['Leader'] = $leader['User'];

        return $guild;
    }

    //--------------------------------------------------------------------------------------------
    function ClearGuildCache ($guildId) {
        CheckNumeric($guildId);

        $cacheKey = GenerateCacheKey(GUILD_CACHE, $guildId);
        CacheDelete($cacheKey);
    }

    //--------------------------------------------------------------------------------------------
    function CreateGuild ($name, $leaderId) {
        CheckNumeric($leaderId);

        if (!IsValidFormationName($name))  // TODO: Put function in better place.
            UERR('Invalid name for guild.');
        if (strlen($name) > GUILD_NAME_MAX_CHARS)
            UERR('That name is too long.');
        if ($this->findByName($name) !== false)
            UERR('That guild name already exists.');

        $leader = G($this->User->GetUser($leaderId));

        if (!empty($leader['GuildMembership']))
            UERR('You are already in a guild!');

        $this->begin();

        try {
            if (!$this->User->DeductMoney($leaderId, GUILD_CREATION_COST))
                UERR('You don\'t have enough money!');

            $this->create();
            $success = $this->save(array(
                'name' => $name,
                'max_size' => GUILD_STARTING_SIZE,
                'created' => date(DB_FORMAT),
                'last_upkeep' => date(DB_FORMAT),
                'leader_id' => $leaderId,
            ));
            if ($success === false)
                IERR('Failed to save guild.');

            $this->GuildMembership->UpdateGuildMembership($leaderId, $this->id, 'Leader', true, true, true);

            $this->commit();

            return $this->id;
        } catch (AppException $e) {
            $this->rollback();
            throw $e;
        }
    }

    //--------------------------------------------------------------------------------------------
    function DepositMoney ($guildId, $userId, $amount) {
        CheckNumeric($guildId);
        CheckNumeric($userId);
        CheckNumeric($amount);

        $guild = G($this->GetGuild($guildId));

        $this->begin();

        try {
            if (!$this->User->DeductMoney($userId, $amount))
                UERR('You don\'t have enough money.');

            $success = $this->query("
                UPDATE
                    `guilds`
                SET
                    `money` = `money` + {$amount}
                WHERE
                    `id` = {$guildId}"
            );

            if ($success === false)
                IERR('Could not deposit money to guild.');

            $this->GuildTransaction->LogTransaction($guildId, 'deposit', $amount, $userId);

            $this->commit();

            $this->ClearGuildCache($guildId);
        } catch (AppException $e) {
            $this->rollback();
            throw $e;
        }
    }

    //--------------------------------------------------------------------------------------------
    function DeductMoney ($guildId, $amount) {
        CheckNumeric($guildId);
        CheckNumeric($amount);

        $guild = G($this->GetGuild($guildId));

        if ($guild['Guild']['money'] < $amount)
            return false;

        $success = $this->query("
            UPDATE
                `guilds`
            SET
                `money` = `money` - {$amount}
            WHERE
                `id` = {$guildId}"
        );

        if ($success === false)
            IERR('Could not deduct money from guild.');

        return true;
    }

    //--------------------------------------------------------------------------------------------
    function WithdrawMoney ($guildId, $authUserId, $targetUserId, $amount) {
        CheckNumeric($guildId);
        CheckNumeric($authUserId);
        CheckNumeric($targetUserId);
        CheckNumeric($amount);

        $guild = G($this->GetGuild($guildId));

        $authUser = $this->User->GetUser($authUserId);
        if (@$authUser['GuildMembership']['guild_id'] != $guildId)
            UERR('You do not belong to that guild.');

        $targetUser = $this->User->GetUser($targetUserId);
        if (@$targetUser['GuildMembership']['guild_id'] != $guildId)
            UERR('Recipient does not belong to that guild.');

        if (!$authUser['GuildMembership']['can_transfer_money'])
            UERR('You do not have the privileges to transfer money.');

        $this->begin();

        try {
            if (!$this->User->GiveMoney($targetUserId, $amount))
                IERR('Failed to give money.');

            if (!$this->DeductMoney($guildId, $amount))
                UERR('The guild does not have enough money to do that.');

            $this->GuildTransaction->LogTransaction($guildId, 'withdrawal', $amount,
                                                    $authUserId, $targetUserId);

            $this->commit();

            $this->ClearGuildCache($guildId);
        } catch (AppException $e) {
            $this->rollback();
            throw $e;
        }
    }

    //--------------------------------------------------------------------------------------------
    function ChangeAnnouncement ($guildId, $authUserId, $message) {
        CheckNumeric($guildId);
        CheckNumeric($authUserId);

        $guild = G($this->GetGuild($guildId));
        if ($guild['Guild']['leader_id'] != $authUserId)
            UERR('You are not authorized to do that.');

        $this->id = $guildId;
        $this->fastSave('announcement', $message);
        $this->fastSave('last_announcement_time', date(DB_FORMAT));
        $this->ClearGuildCache($guildId);
    }

    //--------------------------------------------------------------------------------------------
    function InviteUser ($guildId, $inviterUserId, $inviteeUserId, $message) {
        CheckNumeric($guildId);
        CheckNumeric($inviterUserId);
        CheckNumeric($inviteeUserId);

        $authUser = G($this->User->GetUser($inviterUserId));
        if (empty($authUser['GuildMembership']) || $authUser['GuildMembership']['guild_id'] != $guildId)
            IERR('Auth user does not belong to given guild.');

        if (!$authUser['GuildMembership']['can_invite'])
            UERR('You do not have invite privilege.');

        $inviteeUser = G($this->User->GetUser($inviteeUserId));
        if (!empty($inviteeUser['GuildMembership']))
            UERR('The person to invite is already in a guild.');

        $guild = G($this->GetGuild($guildId));
        if (count($guild['Guild']['GuildMembership']) >= $guild['Guild']['max_size'])
            UERR('Sorry, the guild is full.');

        $this->begin();

        try {
            $this->GuildInvite->create();
            $success = $this->GuildInvite->save(array(
                'guild_id' => $guildId,
                'inviter_id' => $inviterUserId,
                'invitee_id' => $inviteeUserId,
                'time' => date(DB_FORMAT),
                'message' => $message,
            ));
            if ($success === false)
                IERR('Failed to save guild invite.');

            $this->Message->SendMessage($inviterUserId, $inviteeUserId,
                'Guild Invitation',
                "You have been invited to join the guild {$guild['Guild']['name']}. Click " .
                "<a href = '/guilds/view_invite/{$this->GuildInvite->id}'>here</a> to view the invitation.",
                false
            );

            $this->commit();
        } catch (AppException $e) {
            $this->rollback();
            throw $e;
        }
    }

    //--------------------------------------------------------------------------------------------
    function BuyEmblem ($guildId, $userId) {
        CheckNumeric($guildId);
        CheckNumeric($userId);

        $guild = G($this->GetGuild($guildId));
        if ($guild['Guild']['leader_id'] != $userId)
            UERR('You are not allowed to do that.');

        if ($guild['Guild']['can_have_emblem'])
            UERR('Your guild already can have an emblem.');

        $this->begin();

        try {
            if (!$this->DeductMoney($guildId, GUILD_EMBLEM_COST))
                UERR('The guild does not have enough money.');


            $this->id = $guild['Guild']['id'];
            if (!$this->fastSave('can_have_emblem', 1))
                IERR('Could not enable emblem for guild.');

            $this->GuildTransaction->LogTransaction($guildId, 'payment', GUILD_EMBLEM_COST, $userId);

            $this->commit();

            $this->ClearGuildCache($guildId);
        } catch (AppException $e) {
            $this->rollback();
            throw $e;
        }
    }

    //--------------------------------------------------------------------------------------------
    function ChangeEmblem ($guildId, $userId, $emblem) {
        CheckNumeric($guildId);
        CheckNumeric($userId);

        $guild = G($this->GetGuild($guildId));
        if ($guild['Guild']['leader_id'] != $userId)
            UERR('You are not allowed to do that.');

        if (!$guild['Guild']['can_have_emblem'])
            UERR('Your guild can\'t have an emblem yet.');

        $this->id = $guildId;
        G($this->fastSave('emblem', $emblem));
        $this->ClearGuildCache($guildId);
    }

    //--------------------------------------------------------------------------------------------
    function DissolveGuild ($guildId, $userId) {
        CheckNumeric($guildId);
        CheckNumeric($userId);

        $guild = G($this->GetGuild($guildId));
        if ($guild['Guild']['leader_id'] != $userId)
            UERR('You are not allowed to do that.');

        $this->begin();

        try {
            $this->id = $guildId;
            G($this->fastSave('dissolved', 1));
            G($this->fastSave('leader_id', 0));
            G($this->fastSave('name', $guild['Guild']['name'] . '_dissolved'));

            $this->GuildMembership->RemoveAllMembers($guildId);

            $this->ClearGuildCache($guildId);

            $this->commit();
        } catch (AppException $e) {
            $this->rollback();
            throw $e;
        }
    }

    //--------------------------------------------------------------------------------------------
    function LevelUp ($guildId, $userId) {
        CheckNumeric($guildId);
        CheckNumeric($userId);

        $guild = G($this->GetGuild($guildId));
        if ($guild['Guild']['leader_id'] != $userId)
            UERR('You are not allowed to do that.');

        $amount = $guild['Guild']['level_up_cost'];

        $this->begin();

        try {
            if (!$this->DeductMoney($guildId, $amount))
                UERR('The guild does not have enough money.');

            $this->id = $guildId;

            if (!$this->fastSave('level', $guild['Guild']['level'] + 1))
                IERR('Could not level up guild.');

            $this->GuildTransaction->LogTransaction($guildId, 'payment', $amount, $userId);

            $this->commit();

            $this->ClearGuildCache($guildId);
        } catch (AppException $e) {
            $this->rollback();
            throw $e;
        }
    }

    //--------------------------------------------------------------------------------------------
    function UpgradeSize ($guildId, $userId) {
        CheckNumeric($guildId);
        CheckNumeric($userId);

        $guild = G($this->GetGuild($guildId));
        if ($guild['Guild']['leader_id'] != $userId)
            UERR('You are not allowed to do that.');

        if ($guild['Guild']['size_level'] >= GUILD_SIZE_MAX_LEVEL)
            UERR('Your guild size can\'t be any bigger.');

        $this->begin();

        try {
            if (!$this->DeductMoney($guildId, $guild['Guild']['size_upgrade_cost']))
                UERR('The guild does not have enough money.');

            $this->id = $guildId;

            if (!$this->fastSave('max_size', $guild['Guild']['max_size'] + GUILD_SIZE_INCREASE))
                IERR('Could not level up guild.');

            if (!$this->fastSave('size_level', $guild['Guild']['size_level'] + 1))
                IERR('Could not level up guild.');

            $this->GuildTransaction->LogTransaction($guildId, 'payment', $guild['Guild']['size_upgrade_cost'], $userId);

            $this->commit();

            $this->ClearGuildCache($guildId);
        } catch (AppException $e) {
            $this->rollback();
            throw $e;
        }
    }

    //--------------------------------------------------------------------------------------------
    function RespondToInvitation ($inviteId, $inviteeId, $accept) {
        CheckNumeric($inviteId);
        CheckNumeric($inviteeId);

        $invite = G($this->GuildInvite->findById($inviteId));
        $guildId = $invite['GuildInvite']['guild_id'];
        if ($invite['GuildInvite']['invitee_id'] != $inviteeId)
            IERR('Invite does not belong to user.');

        $guild = G($this->GetGuild($guildId));
        $user = G($this->User->GetUser($inviteeId));

        $this->begin();
        try {
            if ($accept) {
                if (count($guild['Guild']['GuildMembership']) >= $guild['Guild']['max_size'])
                    UERR('Sorry, the guild is full.');

                $this->GuildMembership->UpdateGuildMembership($inviteeId, $guildId);

                $this->Message->SendMessage($inviteeId, $invite['GuildInvite']['inviter_id'],
                    'Guild Invitation Accepted',
                    "{$user['User']['username']} has accepted your invitation to join your guild."
                );
            } else {
                $this->Message->SendMessage($inviteeId, $invite['GuildInvite']['inviter_id'],
                    'Guild Invitation Rejected',
                    "{$user['User']['username']} has rejected your invitation to join your guild."
                );
            }

            G($this->GuildInvite->del($invite['GuildInvite']['id']));

            $this->commit();
        } catch (AppException $e) {
            $this->rollback();
            throw $e;
        }
    }

    //--------------------------------------------------------------------------------------------
    function Ping ($guildId) {
        // Upkeep stuff.
    }

    //--------------------------------------------------------------------------------------------
    function OnCronJob () {
        // Ping all guilds.
    }
};

?>