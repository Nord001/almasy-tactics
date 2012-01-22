<?php

class MessagesController extends AppController {

    //---------------------------------------------------------------------------------------------
    function index ($page = null) {
        $this->setPageTitle('Inbox');
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
        $this->setPageTitle('Send Message');
        if (!empty($this->data)) {
            if (!$this->CheckCSRFToken())
                return;

            $user = $this->Message->User->GetUserByUsername($this->data['Message']['username']);
            if ($user === false) {
                $this->Session->setFlash('That user doesn\'t exist.');
                return;
            }

            try {
                $this->Message->SendMessage(
                    $this->GameAuth->GetLoggedInUserId(),
                    $user['User']['id'],
                    $this->data['Message']['subject'],
                    $this->data['Message']['message']
                );
                $this->Session->setFlash('Your message has been sent.');
                $this->redirect(array('controller' => 'messages', 'action' => 'index'));
            } catch (UserException $e) {
                $this->Session->setFlash($e->getMessage());
            } catch (AppException $e) {
                $this->Session->setFlash(ERROR_STR);
            }
        }

        if ($replyMessageId != null) {
            $message = $this->Message->GetMessage($replyMessageId);
            if ($message === false) {
                $this->fof();
                return;
            }

            if ($message['Message']['receiver_id'] != $this->GameAuth->GetLoggedInUserId()){
                $this->fof();
                return;
            }

            $this->set('replyMessage', $message);
        }
    }

    //---------------------------------------------------------------------------------------------
    function mark_read () {
        if ($this->ShouldUseAjax()) {
            $this->autoRender = false;
            if (!isset($this->params['form']['messageId'])) {
                IERR('Form data incomplete.');
                return;
            }

            $messageId = $this->params['form']['messageId'];

            $message = $this->Message->GetMessage($messageId);

            $loggedInUserId = $this->GameAuth->GetLoggedInUserId();
            if ($message['Message']['receiver_id'] != $loggedInUserId) {
                IERR('Message did not belong to user.', array(
                    'loggedInId' => $loggedInUserId,
                    'messageOwnerId' => $message['Message']['receiver_id'],
                ));
                return;
            }

            $this->Message->MarkMessageRead($messageId);

            return;
        }
        $this->fof();
    }

    //---------------------------------------------------------------------------------------------
    function clean () {
        if (!empty($this->data)) {
            if (!$this->CheckCSRFToken()) {
                $this->Session->setFlash(ERROR_STR);
            } else {
                $userId = $this->GameAuth->GetLoggedInUserId();
                $this->Message->deleteAll(array('Message.receiver_id' => $userId, 'Message.read' => 1));
                $this->Session->setFlash('Complete.');
                $this->Message->ClearMessageIdsCache($userId);
            }

            $this->redirect(array('controller' => 'messages', 'action' => 'index'));
        }
        $this->fof();
    }

    //---------------------------------------------------------------------------------------------
    function mark_all_read () {
        if (!empty($this->data)) {

            if (!$this->CheckCSRFToken()) {
                $this->Session->setFlash(ERROR_STR);
            } else {

                $userId = $this->GameAuth->GetLoggedInUserId();

                $messageIds = $this->Message->GetMessageIdsByReceiverId($this->GameAuth->GetLoggedInUserId());
                foreach ($messageIds as $messageId)
                    $this->Message->MarkMessageRead($messageId);
            }

            $this->redirect(array('controller' => 'messages', 'action' => 'index'));
        }
        $this->fof();
    }

    //---------------------------------------------------------------------------------------------
    function delete () {
        if (!empty($this->data)) {
            if (!$this->CheckCSRFToken()) {
                $this->Session->setFlash(ERROR_STR);
                return;
            }

            $loggedInUserId = $this->GameAuth->GetLoggedInUserId();

            $ids = $this->data['Message']['id'];
            foreach ($ids as $id) {
                $message = $this->Message->GetMessage($id);
                if ($message['Message']['receiver_id'] != $loggedInUserId) {
                    IERR('Message did not belong to user.');
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
