<?

define('GUILD_MESSAGES_CACHE', 'guild_messages');

class GuildMessage extends AppModel {

    var $belongsTo = array(
        'Guild',
        'User',
    );

    //--------------------------------------------------------------------------------------------
    function AddMessage ($guildId, $userId, $message) {
        CheckNumeric($guildId);
        CheckNumeric($userId);

        if ($message == '')
            UERR('You must enter a message.');

        $guild = G($this->Guild->GetGuild($guildId));

        $user = $this->User->GetUser($userId);
        if (empty($user['GuildMembership']) || $user['GuildMembership']['guild_id'] != $guildId)
            UERR('You do not belong to that guild.');

        $this->create();
        $success = $this->save(array(
            'guild_id' => $guildId,
            'user_id' => $userId,
            'message' => $message,
            'time' => date(DB_FORMAT)
        ));
        if ($success === false)
            IERR('Failed to add message.');

        $this->ClearGuildMessagesCache($guildId);
    }

    //--------------------------------------------------------------------------------------------
    function GetGuildMessages ($guildId) {
        CheckNumeric($guildId);

        $cacheKey = GenerateCacheKey(GUILD_MESSAGES_CACHE, $guildId);
        $guildMessages = CacheRead($cacheKey);
        if ($guildMessages === false) {
            $guildMessages = $this->find('all', array(
                'conditions' => array(
                    'GuildMessage.guild_id' => $guildId,
                ),
                'order' => 'GuildMessage.id DESC',
                'limit' => 50
            ));
            if ($guildMessages === false)
                return false;

            $guildMessages = array_reverse($guildMessages);

            CacheWrite($cacheKey, $guildMessages);
        }

        foreach ($guildMessages as &$message) {
            $user = $this->User->GetUser($message['GuildMessage']['user_id']);
            $message['GuildMessage']['User'] = $user['User'];
        }

        return $guildMessages;
    }

    //--------------------------------------------------------------------------------------------
    function ClearGuildMessagesCache ($guildId) {
        CheckNumeric($guildId);

        $cacheKey = GenerateCacheKey(GUILD_MESSAGES_CACHE, $guildId);
        CacheDelete($cacheKey);
    }
};

?>