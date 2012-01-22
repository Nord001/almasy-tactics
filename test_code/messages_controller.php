<?php

class MessagesController extends AppController {

    //---------------------------------------------------------------------------------------------
    function index ($page = null) {
        $this->pageTitle = 'Inbox';
        if ($page == null)
            $page = 1;

        $messageIds = $this->Message->GetMessageIdsByReceiverId($this->GameAuth->GetLoggedInUserId());

        $numMessages = count($messageIds);

        $start = MESSAGES_PER_PAGE * ($page - 1);
        $messageIds = array_slice($messageIds, $start, MESSAGES_PER_PAGE);

        if (count($messageIds) == 0 && $page != 1) {
            $this->redirect(array('controller' => 'messages', 'action' => 'index'));
        }

        $lastPage = ceil($numMessages / MESSAGES_PER_PAGE);

        $messages = $this->Message->GetMessages($messageIds);

        $this->set('messages', $messages);
        $this->set('numMessages', $numMessages);
        $this->set('startMessage', $start);
        $this->set('page', $page);
        $this->set('lastPage', $lastPage);
    }

    //---------------------------------------------------------------------------------------------
    function send ($replyMessageId = null) {
        $this->pageTitle = 'Send Message';
        if (!empty($this->data)) {
            $user = $this->Message->User->GetUserByUsername($this->data['Message']['username']);
            if ($user === false) {
                $this->Session->setFlash('That user doesn\'t exist.');
                return;
            }

            $message = substr($this->data['Message']['message'], 0, MESSAGE_MAX_LENGTH);

            $subject = substr($this->data['Message']['subject'], 0, MESSAGE_SUBJECT_MAX_LENGTH);

            $success = $this->Message->SendMessage(
                $this->GameAuth->GetLoggedInUserId(),
                $user['User']['id'],
                $subject,
                $message
            );

            if ($success) {
                $this->Session->setFlash('Your message has been sent.');
            } else {
                $this->Session->setFlash('Your message could not be sent.');
            }
            $this->redirect(array('controller' => 'messages', 'action' => 'index'));
        }

        if ($replyMessageId != null) {
            $message = $this->Message->GetMessage($replyMessageId);
            if ($message === false)
                $this->fof();

            if ($message['Message']['receiver_id'] != $this->GameAuth->GetLoggedInUserId())
                $this->fof();

            $this->set('replyMessage', $message);
        }
    }

    //---------------------------------------------------------------------------------------------
    function mark_read () {
        if ($this->ShouldUseAjax()) {
            $this->autoRender = false;
            if (!isset($this->params['form']['messageId'])) {
                $this->log('mark_read: messageId was not set.');
                return;
            }

            $messageId = $this->params['form']['messageId'];

            $message = $this->Message->GetMessage($messageId);

            $loggedInUserId = $this->GameAuth->GetLoggedInUserId();
            if ($message['Message']['receiver_id'] != $loggedInUserId) {
                $this->log('mark_read: message did not actually belong to user ' . $loggedInUserId);
                return;
            }

            $this->Message->MarkMessageRead($messageId);

            return;
        }
        $this->fof();
    }

    //---------------------------------------------------------------------------------------------
    function clean () {
        $userId = $this->GameAuth->GetLoggedInUserId();
        $this->Message->deleteAll(array('Message.receiver_id' => $userId, 'Message.read' => 1));
        $this->Message->ClearMessageIdsCache($userId);
        $this->redirect(array('controller' => 'messages', 'action' => 'index'));
    }

    //---------------------------------------------------------------------------------------------
    function mark_all_read () {
        $userId = $this->GameAuth->GetLoggedInUserId();

        $messageIds = $this->Message->GetMessageIdsByReceiverId($this->GameAuth->GetLoggedInUserId());
        foreach ($messageIds as $messageId)
            $this->Message->MarkMessageRead($messageId);

        $this->redirect(array('controller' => 'messages', 'action' => 'index'));
    }

    //---------------------------------------------------------------------------------------------
    function delete () {
        if (!empty($this->data)) {
            $loggedInUserId = $this->GameAuth->GetLoggedInUserId();

            $ids = $this->data['Message']['id'];
            foreach ($ids as $id) {
                $message = $this->Message->GetMessage($id);
                if ($message['Message']['receiver_id'] != $loggedInUserId) {
                    $this->log('delete message: message did not actually belong to ' . $loggedInUserId);
                    $this->fof();
                    return;
                }
            }

            $this->Message->deleteAll(array('Message.id' => $ids));
            $this->Message->ClearMessageIdsCache($loggedInUserId);
            $this->redirect(array('controller' => 'messages', 'action' => 'index'));
        }
        $this->fof();
    }
}
?>