<?

define('GUILD_TRANSACTIONS_CACHE', 'guild_transactions');

class GuildTransaction extends AppModel {

    var $belongsTo = array('Guild', 'User');
    var $knows = array('GuildMembership');

    //--------------------------------------------------------------------------------------------
    function GetGuildTransactions ($guildId) {
        CheckNumeric($guildId);

        $cacheKey = GenerateCacheKey(GUILD_TRANSACTIONS_CACHE, $guildId);
        $transactions = CacheRead($cacheKey);
        if ($transactions === false) {
            $transactions = $this->find('all', array(
                'conditions' => array(
                    'GuildTransaction.guild_id' => $guildId,
                ),
                'order' => 'id DESC',
                'limit' => 100,
            ));
            if ($transactions === false)
                return false;

            CacheWrite($cacheKey, $transactions);
        }

        foreach ($transactions as &$transaction) {
            $user = $this->User->GetUser($transaction['GuildTransaction']['initiator_id']);
            $transaction['GuildTransaction']['Initiator'] = $user['User'];
            if ($transaction['GuildTransaction']['receiver_id'] != '') {
                $user = $this->User->GetUser($transaction['GuildTransaction']['receiver_id']);
                $transaction['GuildTransaction']['Receiver'] = $user['User'];
            }
        }

        return $transactions;
    }

    //--------------------------------------------------------------------------------------------
    function ClearGuildTransactionsCache ($guildId) {
        CheckNumeric($guildId);

        $cacheKey = GenerateCacheKey(GUILD_TRANSACTIONS_CACHE, $guildId);
        CacheDelete($cacheKey);
    }

    //--------------------------------------------------------------------------------------------
    function LogTransaction ($guildId, $type, $amount, $initiatorId, $receiverId = null) {
        CheckNumeric($guildId);
        CheckNumeric($amount);
        CheckNumeric($initiatorId);

        $this->begin();
        try {
            $this->create();
            G($this->save(array(
                'guild_id' => $guildId,
                'type' => $type,
                'amount' => $amount,
                'initiator_id' => $initiatorId,
                'receiver_id' => $receiverId,
                'time' => date(DB_FORMAT)
            )));
            $this->ClearGuildTransactionsCache($guildId);

            $delta = ($type == 'withdrawal' ? -$amount : $amount);
            $userId = $receiverId == null ? $initiatorId : $receiverId;

            G($this->GuildMembership->query(
                "UPDATE
                    `guild_membership`
                SET
                    `guild_balance` = `guild_balance` + {$delta}
                WHERE
                    `user_id` = {$userId}
            "));

            $this->GuildMembership->ClearGuildMembershipCache($guildId);
            $this->commit();
        } catch (AppException $e) {
            $this->rollback();
            throw $e;
        }
    }
};

?>