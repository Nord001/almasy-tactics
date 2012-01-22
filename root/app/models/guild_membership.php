<?

define('GUILD_MEMBERSHIP_CACHE', 'guild_membership');

class GuildMembership extends AppModel {
    var $useTable = 'guild_membership';

    var $belongsTo = array(
        'Guild',
        'User',
    );

    var $knows = array('Message');

    //--------------------------------------------------------------------------------------------
    function GetGuildMembership ($guildId) {
        CheckNumeric($guildId);

        $cacheKey = GenerateCacheKey(GUILD_MEMBERSHIP_CACHE, $guildId);
        $guildMembership = CacheRead($cacheKey);
        if ($guildMembership === false) {
            $guildMembership = $this->find('all', array(
                'conditions' => array(
                    'GuildMembership.guild_id' => $guildId,
                ),
            ));
            if ($guildMembership === false)
                return false;

            CacheWrite($cacheKey, $guildMembership);
        }

        foreach ($guildMembership as &$membership) {
            $user = $this->User->GetUser($membership['GuildMembership']['user_id']);
            $membership['GuildMembership']['User'] = $user['User'];
        }

        return $guildMembership;
    }

    //--------------------------------------------------------------------------------------------
    function ClearGuildMembershipCache ($guildId) {
        CheckNumeric($guildId);

        $cacheKey = GenerateCacheKey(GUILD_MEMBERSHIP_CACHE, $guildId);
        CacheDelete($cacheKey);
    }

    //--------------------------------------------------------------------------------------------
    function ExpelUserFromGuild ($userId, $guildId, $notify = true) {
        CheckNumeric($userId);
        CheckNumeric($guildId);

        $user = G($this->User->GetUser($userId));

        if (empty($user['GuildMembership']) || $user['GuildMembership']['guild_id'] != $guildId)
            UERR('That person isn\'t in the guild!');

        $guild = G($this->Guild->GetGuild($guildId));
        if ($guild['Guild']['leader_id'] == $userId)
            UERR('You can\'t expel the leader from guild.');

        $success = $this->deleteAll(array(
            'GuildMembership.guild_id' => $guildId,
            'GuildMembership.user_id' => $userId,
        ));
        if ($success === false)
            IERR('Failed to expel user from guild.');

        $this->User->ClearUserCache($userId);
        $this->ClearGuildMembershipCache($guildId);

        if ($notify) {
            $this->Message->SendNotification(
                $userId,
                'Expel Notice',
                "You have been expelled from the guild {$guild['Guild']['name']}."
            );
        }
    }

    //--------------------------------------------------------------------------------------------
    function UpdateGuildMembership ($userId, $guildId, $title = 'Member', $canInvite = false,
                             $canExpel = false, $canTransferMoney = false) {
        CheckNumeric($userId);
        CheckNumeric($guildId);

        $user = G($this->User->GetUser($userId));
        $guild = G($this->Guild->GetGuild($guildId));

        $data = array(
            'guild_id' => $guildId,
            'user_id' => $userId,
            'position' => $title,
            'can_invite' => $canInvite ? 1 : 0,
            'can_expel' => $canExpel ? 1 : 0,
            'can_transfer_money' => $canTransferMoney ? 1 : 0,
        );

        if (!empty($user['GuildMembership'])) {
            if ($user['GuildMembership']['guild_id'] != $guildId)
                UERR('User is already in a different guild.');
            $data['id'] = $existingMembership['GuildMembership']['id'];
        } else {
            $data['join_date'] = date(DB_FORMAT);
            $this->create();
        }

        G($this->save($data), 'Failed to save membership.');

        $this->User->ClearUserCache($userId);
        $this->ClearGuildMembershipCache($guildId);
    }

    //--------------------------------------------------------------------------------------------
    function RemoveAllMembers ($guildId) {
        CheckNumeric($guildId);

        $memberships = $this->GetGuildMembership($guildId);
        G($this->deleteAll(array(
            'guild_id' => $guildId
        )));

        foreach ($memberships as $membership)
            $this->User->ClearUserCache($membership['GuildMembership']['user_id']);
        $this->ClearGuildMembershipCache($guildId);
    }
};

?>