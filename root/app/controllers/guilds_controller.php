<?php

class GuildsController extends AppController {

    //---------------------------------------------------------------------------------------------
    function create () {
        $user = $this->GameAuth->GetLoggedInUser();
        if (!empty($user['GuildMembership'])) {
            $this->fof();
            return;
        }

        if ($this->data) {
            try {
                $this->CheckCSRFToken();

                $id = $this->Guild->CreateGuild(@$this->data['Guild']['name'], $user['User']['id']);
                $this->Session->setFlash('Your guild has been created!');
                $this->redirect(array('action' => 'view', $id));
            } catch (InternalException $e) {
                $this->Session->setFlash(ERROR_STR);
            } catch (UserException $e) {
                $this->Session->setFlash($e->getMessage());
            }
        }
    }

    //---------------------------------------------------------------------------------------------
    function member_list () {
        $user = $this->GameAuth->GetLoggedInUser();
        if (empty($user['GuildMembership'])) {
            $this->fof();
            return;
        }

        $guildId = $user['GuildMembership']['guild_id'];
        $guild = $this->Guild->GetGuild($guildId);
        $this->set('guild', $guild, true);
    }

    //---------------------------------------------------------------------------------------------
    function view ($guildId = null) {
        if ($guildId == null) {
            $user = $this->GameAuth->GetLoggedInUser();
            if (empty($user['GuildMembership'])) {
                $this->fof();
                return;
            }

            $guildId = $user['GuildMembership']['guild_id'];
        }

        $guild = $this->Guild->GetGuild($guildId);
        if ($guild === false) {
            $this->fof();
            return;
        }

        $this->set('guild', $guild, true);
    }

    //---------------------------------------------------------------------------------------------
    function get_new_messages () {
        if (!$this->ShouldUseAjax())
            $this->fof();

        Configure::write('ajaxMode', 1);
        $this->autoRender = false;

        $lastId = @$this->params['form']['lastId'];

        $user = $this->GameAuth->GetLoggedInUser();
        if (empty($user['GuildMembership'])) {
            return AJAX_ERROR_CODE;
        }

        $guildId = $user['GuildMembership']['guild_id'];
        $messages = $this->Guild->GuildMessage->GetGuildMessages($guildId);

        $data = array();
        $data['messages'] = array();

        foreach ($messages as $message) {
            if ($lastId != '' && $message['GuildMessage']['id'] < $lastId)
                continue;

            $data['messages'][] = array(
                'id' => $message['GuildMessage']['id'],
                'username' => $message['GuildMessage']['User']['username'],
                'content' => $message['GuildMessage']['message']
            );
        }

        return json_encode($data);
    }

    //---------------------------------------------------------------------------------------------
    function invite ($userId = null) {

        $user = $this->GameAuth->GetLoggedInUser();
        if (empty($user['GuildMembership'])) {
            $this->fof();
            return;
        }

        $guildId = $user['GuildMembership']['guild_id'];

        if ($this->data) {
            try {
                $this->CheckCSRFToken();

                $username = @$this->data['username'];

                $invitee = $this->User->GetUserByUsername($username);
                if ($invitee === false) {
                    $this->Session->setFlash('That user does not exist.');
                } else {
                    $this->Guild->InviteUser($guildId, $user['User']['id'], $invitee['User']['id'], @$this->data['message']);
                    $this->Session->setFlash('Invitation sent.');
                    $this->redirect(array('controller' => 'users', 'action' => 'profile', $invitee['User']['username']));
                }
            } catch (UserException $e) {
                $this->Session->setFlash($e->getMessage());
            } catch (InternalException $e) {
                $this->Session->setFlash(ERROR_STR);
            }
        }

        if ($userId != null) {
            $user = $this->User->GetUser($userId);
            if ($user === false) {
                $this->fof();
                return;
            }
            $this->set('user', $user);
        }

        $guild = $this->Guild->GetGuild($guildId);
        $this->set('guild', $guild);
    }

    //---------------------------------------------------------------------------------------------
    function expel ($userId = null) {
        if ($userId == null) {
            $this->fof();
            return;
        }

        $user = $this->GameAuth->GetLoggedInUser();
        if (empty($user['GuildMembership'])) {
            $this->fof();
            return;
        }

        $guildId = $user['GuildMembership']['guild_id'];

        if ($this->data) {
            try {
                $this->CheckCSRFToken();

                $userId = @$this->data['user_id'];

                $this->Guild->GuildMembership->ExpelUserFromGuild($userId, $guildId, false);
                $this->Session->setFlash('Member expelled.');
                $this->redirect(array('controller' => 'guilds', 'action' => 'view'));
            } catch (UserException $e) {
                $this->Session->setFlash($e->getMessage());
            } catch (InternalException $e) {
                $this->Session->setFlash(ERROR_STR);
            }
        }

        $user = $this->User->GetUser($userId);
        if ($user === false) {
            $this->fof();
            return;
        }
        if ($user['GuildMembership']['guild_id'] != $guildId) {
            $this->fof();
            return;
        }

        $this->set('user', $user);

        $guild = $this->Guild->GetGuild($guildId);
        $this->set('guild', $guild);
    }

    //---------------------------------------------------------------------------------------------
    function withdraw_money () {
        $user = $this->GameAuth->GetLoggedInUser();
        if (empty($user['GuildMembership'])) {
            $this->fof();
            return;
        }

        if (!$this->data) {
            $this->fof();
            return;
        }

        $guildId = $user['GuildMembership']['guild_id'];

        try {
            $this->CheckCSRFToken();

            $amount = @$this->data['amount'];
            $target = $this->User->GetUserByUsername(@$this->data['recipient']);

            if (!is_numeric($amount)) {
                $this->Session->setFlash('Invalid amount.');
            } else if ($target === false) {
                $this->Session->setFlash('That user doesn\'t exist.');
            } else {
                $this->Guild->WithdrawMoney($guildId, $user['User']['id'], $target['User']['id'], $amount);
                $this->Session->setFlash('Funds withdrawn.');
            }
        } catch (UserException $e) {
            $this->Session->setFlash($e->getMessage());
        } catch (InternalException $e) {
            $this->Session->setFlash(ERROR_STR);
        }
        $this->redirect(array('controller' => 'guilds', 'action' => 'transactions'));
    }

    //---------------------------------------------------------------------------------------------
    function emblem ($guildId = null) {
        Configure::write('ajaxMode', 1);
        $this->autoRender = false;
        $this->suppressHeader = true;

        if ($guildId == null)
            return;

        $guild = $this->Guild->GetGuild($guildId);
        if ($guild === false)
            return;

        if ($guild['Guild']['emblem'] === '')
            return;

        list($type, $data) = explode(';', $guild['Guild']['emblem']);
        $type = substr($type, 5);
        $data = substr($data, 7);
        header('Content-Type: ' . $type);
        return base64_decode($data);
    }

    //---------------------------------------------------------------------------------------------
    function deposit_money () {
        $user = $this->GameAuth->GetLoggedInUser();
        if (empty($user['GuildMembership'])) {
            $this->fof();
            return;
        }

        if (!$this->data) {
            $this->fof();
            return;
        }

        $guildId = $user['GuildMembership']['guild_id'];

        try {
            $this->CheckCSRFToken();

            $amount = @$this->data['amount'];
            if (is_numeric($amount)) {
                $this->Guild->DepositMoney($guildId, $user['User']['id'], $amount);
                $this->Session->setFlash('Deposited funds.');
            } else {
                $this->Session->setFlash('Invalid amount.');
            }
        } catch (UserException $e) {
            $this->Session->setFlash($e->getMessage());
        } catch (InternalException $e) {
            $this->Session->setFlash(ERROR_STR);
        }
        $this->redirect(array('controller' => 'guilds', 'action' => 'transactions'));
    }

    //---------------------------------------------------------------------------------------------
    function leave () {
        $user = $this->GameAuth->GetLoggedInUser();
        if (empty($user['GuildMembership'])) {
            $this->fof();
            return;
        }

        $guildId = $user['GuildMembership']['guild_id'];

        if ($this->data) {
            try {
                $this->CheckCSRFToken();

                $this->Guild->GuildMembership->ExpelUserFromGuild($user['User']['id'], $guildId, false);
                $this->Session->setFlash('You have left the guild.');
                $this->redirect('/');
            } catch (UserException $e) {
                $this->Session->setFlash($e->getMessage());
            } catch (InternalException $e) {
                $this->Session->setFlash(ERROR_STR);
            }
        }

        $guild = $this->Guild->GetGuild($guildId);
        $this->set('guild', $guild);
    }

    //---------------------------------------------------------------------------------------------
    function change_emblem () {
        $user = $this->GameAuth->GetLoggedInUser();
        if (empty($user['GuildMembership'])) {
            $this->fof();
            return;
        }

        $guildId = $user['GuildMembership']['guild_id'];

        if ($this->data) {
            try {
                $this->CheckCSRFToken();

                $file = @$this->params['form']['userfile']['tmp_name'];
                $type = @$this->params['form']['userfile']['type'];
                $size = @$this->params['form']['userfile']['size'];

                do {
                    if ($file === '') {
                        $this->Guild->ChangeEmblem($guildId, $user['User']['id'], '');
                        $this->Session->setFlash('Emblem cleared.');
                        $this->redirect(array('controller' => 'guilds', 'action' => 'view'));
                        break;
                    } else if ($type != 'image/png' && $type != 'image/jpeg') {
                        $this->Session->setFlash('Only PNGs and JPGs are allowed.');
                        break;
                    } else if ($size > 100000) {
                        $this->Session->setFlash('Your file is too big. 100 KB max.');
                        break;
                    }

                    $info = @getimagesize($file);
                    if ($info === false) {
                        $this->Session->setFlash('Only PNGs and JPGs are allowed.');
                        break;
                    } else if ($info['mime'] != 'image/png' && $info['mime'] != 'image/jpeg') {
                        $this->Session->setFlash('Only PNGs and JPGs are allowed.');
                        break;
                    } else if ($info[0] > 100 || $info[1] > 100) {
                        $this->Session->setFlash('Emblems must be 100x100 or smaller.');
                        break;
                    }

                    $fileData = file_get_contents($file);
                    $uri = sprintf('data:%s;base64,%s', $info['mime'], base64_encode($fileData));

                    $this->Guild->ChangeEmblem($guildId, $user['User']['id'], $uri);

                    $this->Session->setFlash('Emblem changed.');
                    $this->redirect(array('controller' => 'guilds', 'action' => 'view'));
                } while (false);
            } catch (UserException $e) {
                $this->Session->setFlash($e->getMessage());
            } catch (InternalException $e) {
                $this->Session->setFlash(ERROR_STR);
            }
        }

        $guild = $this->Guild->GetGuild($guildId);

        if ($guild['Guild']['leader_id'] != $user['User']['id']) {
            $this->fof();
            return;
        }

        $this->set('guild', $guild);
    }

    //---------------------------------------------------------------------------------------------
    function dissolve () {
        $user = $this->GameAuth->GetLoggedInUser();
        if (empty($user['GuildMembership'])) {
            $this->fof();
            return;
        }

        $guildId = $user['GuildMembership']['guild_id'];

        if ($this->data) {
            try {
                $this->CheckCSRFToken();

                $this->Guild->DissolveGuild($guildId, $user['User']['id']);
                $this->Session->setFlash('The guild has been dissolved.');
                $this->redirect(array('controller' => 'users', 'action' => 'profile'));
            } catch (UserException $e) {
                $this->Session->setFlash($e->getMessage());
            } catch (InternalException $e) {
                $this->Session->setFlash(ERROR_STR);
            }
        }

        $guild = $this->Guild->GetGuild($guildId);

        if ($guild['Guild']['leader_id'] != $user['User']['id']) {
            $this->fof();
            return;
        }

        $this->set('guild', $guild);
    }

    //---------------------------------------------------------------------------------------------
    function transactions () {
        $user = $this->GameAuth->GetLoggedInUser();
        if (empty($user['GuildMembership'])) {
            $this->fof();
            return;
        }

        $guildId = $user['GuildMembership']['guild_id'];
        $transactions = $this->Guild->GuildTransaction->GetGuildTransactions($guildId);
        $this->set('transactions', $transactions);

        $guild = $this->Guild->GetGuild($guildId);
        $this->set('guild', $guild);
    }

    //---------------------------------------------------------------------------------------------
    function post_message () {
        if (!$this->ShouldUseAjax())
            $this->fof();

        Configure::write('ajaxMode', 1);
        $this->autoRender = false;

        try {
            $userId = $this->GameAuth->GetLoggedInUserId();
            $this->Guild->GuildMessage->AddMessage(@$this->params['form']['guildId'], $userId, @$this->params['form']['message']);
            return 0;
        } catch (AppException $e) {
            return AJAX_ERROR_CODE;
        }
    }

    //---------------------------------------------------------------------------------------------
    function upgrade () {
        $user = $this->GameAuth->GetLoggedInUser();
        if (empty($user['GuildMembership'])) {
            $this->fof();
            return;
        }

        $guildId = $user['GuildMembership']['guild_id'];

        if ($this->data) {
            try {
                $this->CheckCSRFToken();

                switch (@$this->data['type']) {
                    case 'emblem':
                        $this->Guild->BuyEmblem($guildId, $user['User']['id']);
                    break;
                    case 'size':
                        $this->Guild->UpgradeSize($guildId, $user['User']['id']);
                    break;
                    case 'level':
                        $this->Guild->LevelUp($guildId, $user['User']['id']);
                    break;
                    default:
                        $this->fof();
                        return;
                }
                $this->Session->setFlash('Upgrade purchased!');
                $this->redirect(array('controller' => 'guilds', 'action' => 'view'));
            } catch (UserException $e) {
                $this->Session->setFlash($e->getMessage());
            } catch (InternalException $e) {
                $this->Session->setFlash(ERROR_STR);
            }
        }

        $guild = $this->Guild->GetGuild($guildId);

        if ($guild['Guild']['leader_id'] != $user['User']['id']) {
            $this->fof();
            return;
        }

        $this->set('guild', $guild);
    }

    //---------------------------------------------------------------------------------------------
    function change_announcement () {
        $user = $this->GameAuth->GetLoggedInUser();
        if (empty($user['GuildMembership'])) {
            $this->fof();
            return;
        }

        $guildId = $user['GuildMembership']['guild_id'];
        $guild = $this->Guild->GetGuild($guildId);

        if ($guild['Guild']['leader_id'] != $user['User']['id']) {
            $this->fof();
            return;
        }

        if (!$this->data) {
            $this->fof();
            return;
        }

        try {
            $this->CheckCSRFToken();

            $this->Guild->ChangeAnnouncement($guildId, $user['User']['id'], @$this->data['message']);
            $this->Session->setFlash('Announcement changed.');
        } catch (UserException $e) {
            $this->Session->setFlash($e->getMessage());
        } catch (InternalException $e) {
            $this->Session->setFlash(ERROR_STR);
        }
        $this->redirect(array('controller' => 'guilds', 'action' => 'view'));
    }

    //---------------------------------------------------------------------------------------------
    function view_invite ($inviteId = null) {
        if ($this->data) {
            try {
                $this->CheckCSRFToken();

                $accept = @$this->data['response'] != '';
                $this->Guild->RespondToInvitation(@$this->data['invite_id'],
                                                  $this->GameAuth->GetLoggedInUserId(), $accept);
                if ($accept) {
                    $this->Session->setFlash('You have accepted the invitation.');
                    $this->redirect(array('controller' => 'guilds', 'action' => 'view'));
                } else {
                    $this->Session->setFlash('You have rejected the invitation.');
                    $this->redirect(array('controller' => 'messages', 'action' => 'index'));
                }
            } catch (UserException $e) {
                $this->Session->setFlash($e->getMessage());
            } catch (InternalException $e) {
                $this->Session->setFlash(ERROR_STR);
            }
        }

        if ($inviteId == null) {
            $this->fof();
            return;
        }

        $invite = $this->Guild->GuildInvite->findById($inviteId);
        if ($invite === false) {
            $this->fof();
            return;
        }

        $user = $this->GameAuth->GetLoggedInUser();
        if ($invite['GuildInvite']['invitee_id'] != $user['User']['id']) {
            $this->fof();
            return;
        }

        $guild = $this->Guild->GetGuild($invite['GuildInvite']['guild_id']);
        if ($guild === false) {
            $this->fof();
            return;
        }

        $inviter = $this->User->GetUser($invite['GuildInvite']['inviter_id']);

        $this->set('invite', $invite, true);
        $this->set('guild', $guild);
        $this->set('inviter', $inviter);
    }

    //---------------------------------------------------------------------------------------------
    function edit_membership ($membershipId = null) {
        if ($membershipId == null) {
            $this->fof();
            return;
        }

        $membership = $this->Guild->GuildMembership->findById($membershipId);
        if ($membership === false) {
            $this->fof();
            return;
        }

        $user = $this->User->GetUser($membership['GuildMembership']['user_id']);

        $guild = $this->Guild->GetGuild($membership['GuildMembership']['guild_id']);
        if ($guild === false) {
            $this->fof();
            return;
        }

        if ($guild['Guild']['leader_id'] != $this->GameAuth->GetLoggedInUserId()) {
            $this->fof();
            return;
        }

        if ($this->data) {
            try {
                $this->CheckCSRFToken();

                $this->Guild->GuildMembership->id = $this->data['GuildMembership']['id'];
                if ($this->Guild->GuildMembership->field('guild_id') != $guild['Guild']['id'])
                    IERR('Membership does not belong to guild.');

                $this->data['GuildMembership']['can_invite'] = @$this->data['GuildMembership']['can_invite'];
                $this->data['GuildMembership']['can_expel'] = @$this->data['GuildMembership']['can_expel'];
                $this->data['GuildMembership']['can_transfer_money'] = @$this->data['GuildMembership']['can_transfer_money'];

                $this->Guild->GuildMembership->save($this->data['GuildMembership']);
                $this->Guild->GuildMembership->ClearGuildMembershipCache($guild['Guild']['id']);
                $this->User->ClearUserCache($this->Guild->GuildMembership->field('user_id'));

                $this->Session->setFlash('Membership saved.');
                $this->redirect(array('controller' => 'guilds', 'action' => 'member_list'));
            } catch (UserException $e) {
                $this->Session->setFlash($e->getMessage());
            } catch (InternalException $e) {
                $this->Session->setFlash(ERROR_STR);
            }
        }

        $this->set('membership', $membership, true);
        $this->set('user', $user);
        $this->set('guild', $guild);
    }
}
?>
