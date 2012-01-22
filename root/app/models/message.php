<?

define('MESSAGE_CACHE', 'message');
define('MESSAGE_CACHE_DURATION', 'long');

define('MESSAGE_BY_RECEIVER_CACHE', 'user_messages');
define('MESSAGE_BY_RECEIVER_CACHE_DURATION', 'long');

define('NUM_UNREAD_MESSAGES_CACHE', 'user_num_unread_messages');
define('NUM_UNREAD_MESSAGES_BY_RECEIVER_CACHE_DURATION', 'long');

class Message extends AppModel {

    var $belongsTo = array(
        'User' => array(
            'className' => 'User',
            'foreignKey' => 'receiver_id',
        ),
    );

    //--------------------------------------------------------------------------------------------
    function GetMessage ($messageId) {
        CheckNumeric($messageId);

        $cacheKey = GenerateCacheKey(MESSAGE_CACHE, $messageId);
        $message = Cache::read($cacheKey, MESSAGE_CACHE_DURATION);
        if ($message === false) {
            $message = $this->findById($messageId);
            if ($message === false)
                return false;

            Cache::write($cacheKey, $message, MESSAGE_CACHE_DURATION);
        }

        $sender = $this->User->GetUser($message['Message']['sender_id']);
        $message['Message']['Sender'] = $sender['User'];
        return $message;
    }

    //--------------------------------------------------------------------------------------------
    function MarkMessageRead ($messageId) {
        CheckNumeric($messageId);

        $message = $this->GetMessage($messageId);

        if ($message['Message']['read'] == 1)
            return;

        $this->id = $messageId;
        $this->fastSave('read', 1);

        $this->ClearMessageIdsCache($message['Message']['receiver_id']);
        $this->ClearMessageCache($messageId);
    }

    //--------------------------------------------------------------------------------------------
    function GetMessages ($messageIds) {
        $data = array();
        foreach ($messageIds as $messageId)
            $data[] = $this->GetMessage($messageId);

        return $data;
    }

    //--------------------------------------------------------------------------------------------
    function GetNumUnreadMessagesByReceiverId ($userId) {
        CheckNumeric($userId);

        $cacheKey = GenerateCacheKey(NUM_UNREAD_MESSAGES_CACHE, $userId);
        $numUnread = Cache::read($cacheKey, NUM_UNREAD_MESSAGES_BY_RECEIVER_CACHE_DURATION);
        if ($numUnread !== false)
            return $numUnread;

        $numUnread = $this->find('count', array(
            'conditions' => array(
                'Message.receiver_id' => $userId,
                'Message.read'        => 0,
            ),
        ));
        if ($numUnread === false)
            $numUnread = 0;

        Cache::write($cacheKey, $numUnread, NUM_UNREAD_MESSAGES_BY_RECEIVER_CACHE_DURATION);
        return $numUnread;
    }

    //--------------------------------------------------------------------------------------------
    function ClearMessageCache ($messageId) {
        CheckNumeric($messageId);

        $cacheKey = GenerateCacheKey(MESSAGE_CACHE, $messageId);
        Cache::delete($cacheKey, MESSAGE_CACHE_DURATION);
    }

    //--------------------------------------------------------------------------------------------
    function ClearMessageIdsCache ($userId) {
        CheckNumeric($userId);

        $cacheKey = GenerateCacheKey(MESSAGE_BY_RECEIVER_CACHE, $userId);
        Cache::delete($cacheKey, MESSAGE_BY_RECEIVER_CACHE_DURATION);

        $cacheKey = GenerateCacheKey(NUM_UNREAD_MESSAGES_CACHE, $userId);
        Cache::delete($cacheKey, NUM_UNREAD_MESSAGES_BY_RECEIVER_CACHE_DURATION);
    }

    //--------------------------------------------------------------------------------------------
    function GetMessageIdsByReceiverId ($userId) {
        CheckNumeric($userId);

        $cacheKey = GenerateCacheKey(MESSAGE_BY_RECEIVER_CACHE, $userId);
        $messageIds = Cache::read($cacheKey, MESSAGE_BY_RECEIVER_CACHE_DURATION);

        if ($messageIds !== false)
            return $messageIds;

        $messageIds = $this->find('all', array(
            'fields' => array(
                'Message.id',
            ),
            'conditions' => array(
                'Message.receiver_id' => $userId,
            ),
            'order' => 'time_sent DESC',
        ));
        $messageIds = Set::classicExtract($messageIds, '{n}.Message.id');

        Cache::write($cacheKey, $messageIds, MESSAGE_BY_RECEIVER_CACHE_DURATION);
        return $messageIds;
    }

    //--------------------------------------------------------------------------------------------
    function SendMessage ($fromId, $toId, $subject, $message, $escape = true) {
        CheckNumeric($fromId);
        CheckNumeric($toId);

        $message = substr($message, 0, MESSAGE_MAX_LENGTH);
        if ($escape)
            $message = h($message);

        $subject = substr($subject, 0, MESSAGE_SUBJECT_MAX_LENGTH);
        if ($escape)
            $subject = h($subject);

        if (strlen($message) == 0 || strlen($subject) == 0)
            UERR('Subject/Message required.');

        $receiverMessageCount = $this->GetMessageIdsByReceiverId($toId);
        $receiverMessageCount = count($receiverMessageCount);
        if ($receiverMessageCount > MAX_MESSAGES_PER_USER)
            UERR('The recipient\'s inbox is full!');

        $this->create();
        $data = array(
            'subject' => $subject,
            'message' => $message,
            'sender_id' => $fromId,
            'receiver_id' => $toId,
            'time_sent' => date(DB_FORMAT),
            'read' => 0,
        );
        $success = $this->save($data);

        if ($success === false)
            IERR('Failed to send message.');

        $this->ClearMessageIdsCache($toId);
    }

    //--------------------------------------------------------------------------------------------
    function SendNotification ($receiverId, $subject, $message) {
        CheckNumeric($receiverId);

        // Stifle user exceptions.
        try {
            $this->SendMessage(ALMASY_USER_ID, $receiverId, $subject, $message, false);
        } catch (UserException $e) { }
    }
};

?>