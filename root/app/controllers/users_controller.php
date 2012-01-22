<?php

class UsersController extends AppController {

    var $authList = array(
            'register'          => AUTH_ALL,
            'usernameAvailable' => AUTH_ALL,
            'login'             => AUTH_ALL,
            'forgot'            => AUTH_ALL,
            'reset_password'    => AUTH_ALL
    );

    var $uses = array('User', 'Formation');

    //=============================================================================================
    // User functions
    //=============================================================================================

    //---------------------------------------------------------------------------------------------
    function login () {
        $this->setPageTitle('Login');

        if ($this->GameAuth->GetLoggedInUser()) {
            $this->redirect('/');
            return;
        }

        $this->layout = 'default';
        if (!empty($this->data) && !empty($this->data['User']['username']) && !empty($this->data['User']['password'])) {
            if ($loggedInUser = $this->GameAuth->Login($this->data['User']['username'], $this->data['User']['password'], $this->data['User']['remember_me'])) {
                if ($loggedInUser['User']['state'] != USER_STATE_NORMAL) {
                    $this->Session->setFlash('Your account has been terminated. If you feel this is injust, please contact the Almasy team.');
                    return;
                }

                $forumUserId = $loggedInUser['User']['forum_user_id'];

                // Login on forum
                if ($forumUserId != '' && Configure::read('test') != 1) {
                    $result = file_get_contents(sprintf(
                        'http://%s%s?key=%s&mode=login&forumUserId=%s',
                        $_SERVER['SERVER_NAME'],
                        FORUM_HANDLER,
                        FORUM_HANDLER_KEY,
                        $forumUserId
                    ));

                    if (strlen($result) == 0) {
                        IERR('Could not login user to forum.');
                        return;
                    }

                    $cookie = json_decode($result, true);

                    setcookie($cookie['name'], $cookie['content'], $cookie['expire'], '/');
                }

                $authRedirect = $this->Session->read('GameAuth.redirect');
                if ($authRedirect)
                    $this->redirect($authRedirect);
                else
                    $this->redirect('/');
            } else {
                $this->Session->setFlash('Could not login. Check your username and password.');
                return;
            }
        }
    }

    //---------------------------------------------------------------------------------------------
    function logout () {
        $loggedInUser = $this->GameAuth->GetLoggedInUser();
        if (!$loggedInUser) {
            $this->redirect('/');
            return;
        }

        $this->GameAuth->logout();

        // Logout on forum
        if ($loggedInUser['User']['forum_user_id'] && Configure::read('test') != 1) {
            $result = file_get_contents(sprintf(
                'http://%s%s?key=%s&mode=logout&forumUserId=%s',
                $_SERVER['SERVER_NAME'],
                FORUM_HANDLER,
                FORUM_HANDLER_KEY,
                $loggedInUser['User']['forum_user_id']
            ));

            if (strlen($result) == 0) {
                IERR('Could not logout user from forum.');
                return;
            }

            $cookie = json_decode($result, true);
            setcookie($cookie['name'], $cookie['content'], $cookie['expire'], '/');
        }

        $this->Session->del('GameAuth.redirect');

        $this->redirect('/');
    }

    //---------------------------------------------------------------------------------------------
    function change_password () {
        $this->setPageTitle('Change Password');

        if (!empty($this->data)) {
            if (!$this->CheckCSRFToken())
                return;

            // Validate
            $password1 = $this->data['User']['password_1'];
            $password2 = $this->data['User']['password_2'];
            $oldPassword = $this->data['User']['old_password'];

            if ($password1 != $password2) {
                $this->Session->setFlash('Your new passwords do not match.');
                return;
            }

            if (strlen($password1) < PASSWORD_MIN_CHARS) {
                $this->Session->setFlash('Your password must be at least ' . PASSWORD_MIN_CHARS . ' characters.');
                return;
            }

            $user = $this->GameAuth->GetLoggedInUser();
            if ($user['User']['password'] != $this->GameAuth->HashPassword($oldPassword)) {
                $this->Session->setFlash('Your old password was incorrect.');
                return;
            }

            $success = $this->User->ChangePassword($user['User']['id'], $password1);
            if ($success) {
                // Update cookie
                $this->GameAuth->Login($user['User']['username'], $password1, false);
                $this->Session->setFlash('Password changed successfully.');
                $this->redirect('/'); // FIX: where to?
            } else {
                IERR('Failed to change password.');
                $this->Session->setFlash('Your password could not be changed.');
                return;
            }
        }
    }

    //---------------------------------------------------------------------------------------------
    // Async postback for updating money
    function get_money () {
        if ($this->ShouldUseAjax()) {
            Configure::write('ajaxMode', 1);
            $this->autoRender = false;
            $user = $this->GameAuth->GetLoggedInUser();
            if ($user) {
                return $user['User']['money'];
            }
            return;
        }
        $this->fof();
    }

    //---------------------------------------------------------------------------------------------
    function register ($referrerUsername = null) {
        $this->setPageTitle('Register');

        if (!empty($this->data)) {
            do {
                $username = $this->data['User']['reg_username'];
                $password = $this->data['User']['reg_password'];
                $email = $this->data['User']['email'];
                $firstCharacterName = $this->data['Character']['name'];
                $referringUserId = isset($this->data['referring_id']) ? $this->data['referring_id'] : false;

                // Invalid characters screen
                if (preg_match('/^[a-z0-9]*$/', strtolower($username)) == 0) {
                    $this->Session->setFlash('Your username can only contain alphanumeric characters. ');
                    break;
                }

                if (strlen($username) == 0) {
                    $this->Session->setFlash('Your username cannot be blank.');
                    break;
                }

                if (strlen($password) < PASSWORD_MIN_CHARS) {
                    $this->Session->setFlash('Your password must be at least ' . PASSWORD_MIN_CHARS . ' characters.');
                    break;
                }

                if (!$this->GameValidate->IsValidEmail($email)) {
                    $this->Session->setFlash('Your email was not valid.');
                    break;
                }

                if (!$this->GameValidate->IsValidCharacterName($firstCharacterName)) {
                    $this->Session->setFlash('Your character name is not valid.');
                    break;
                }

                $existingUser = $this->User->GetUserByUsername($username);
                if ($existingUser) {
                    $this->Session->setFlash('That username is taken.');
                    break;
                }

                if (!$this->User->CreateNewUser($username, $password, $email, $firstCharacterName, $referringUserId)) {
                    $this->Session->setFlash('Sorry, an error has occurred :( Please contact help!');
                    IERR('Failed to create new user.');
                    break;
                }

                $this->Session->setFlash('Congratulations, your account has been created! If you\'re new, check out the Quickstart Guide in the Help section!');
                $this->GameAuth->Login($username, $password, false);

                $this->redirect('/');
                return;
            } while (false);
        }

        if ($referrerUsername != null) {
            $user = $this->User->GetUserByUsername($referrerUsername);
            if ($user !== false)
                $this->set('referringUser', $user);
        }
    }

    //---------------------------------------------------------------------------------------------
    function usernameAvailable () {
        if ($this->ShouldUseAjax()) {
            Configure::write('ajaxMode', 1);
            $this->autoRender = false;

            if (!isset($this->params['form']['username'])) {
                return AJAX_ERROR_CODE;
            }

            $user = $this->User->findByUsername($this->params['form']['username']);
            return $user === false ? 1 : 0;
        }
        $this->fof();
    }

    //---------------------------------------------------------------------------------------------
    function profile ($username = null) {
        $user = null;
        if ($username == null) {
            $user = $this->GameAuth->GetLoggedInUser();
        } else {
            $user = $this->User->GetUserByUsername($username);
            if ($user === false || $user['User']['id'] == ALMASY_USER_ID || $user['User']['state'] != USER_STATE_NORMAL) {
                $this->fof();
                return;
            }
        }

        $this->set('user', $user, true);
        $this->setPageTitle($user['User']['username']);

        if ($user['User']['referring_id'] != null)
            $this->set('referringUser', $this->User->GetUser($user['User']['referring_id']));

        if (!empty($user['GuildMembership'])) {
            $guild = $this->User->GuildMembership->Guild->GetGuild($user['GuildMembership']['guild_id']);
            $this->set('guild', $guild);
        }
    }

    //---------------------------------------------------------------------------------------------
    function referrals () {
        $this->setPageTitle('Referrals');
        $loggedInId = $this->GameAuth->GetLoggedInUserId();
        $referredPeople = $this->User->find('all', array(
            'conditions' => array(
                'referring_id' => $loggedInId,
            ),
        ));
        $this->set('referredPeople', $referredPeople);

        $bladeOfLoyalties = REFERRAL_SYSTEM_LEVEL_3_1_USER_ITEM_ID_LIST();
        $this->set('bladeOfLoyalty', $this->User->UserItem->GetUserItem($bladeOfLoyalties[0]));

        $devotionGems = REFERRAL_SYSTEM_LEVEL_3_2_USER_ITEM_ID_LIST();
        $this->set('devotionGem', $this->User->UserItem->GetUserItem($devotionGems[0]));

        $friendship = $this->User->UserItem->GetUserItem(REFERRAL_SYSTEM_LEVEL_2_USER_ITEM_ID);
        $this->set('friendshipsBond', $friendship);
    }

    //---------------------------------------------------------------------------------------------
    function change_portrait ($name = null) {
        $this->setPageTitle('Change Portrait');
        if ($name != null) {
            if (!in_array($name, PORTRAIT_LIST())) {
                $this->Session->setFlash(ERROR_STR);
                return;
            }

            $loggedInUserId = $this->GameAuth->GetLoggedInUserId();
            $this->User->id = $loggedInUserId;
            $this->User->fastSave('portrait', $name);
            $this->User->ClearUserCache($loggedInUserId);

            $this->Session->setFlash('Portrait changed!');
            $this->redirect(array('controller' => 'users', 'action' => 'profile'));
        }
    }

    //---------------------------------------------------------------------------------------------
    function change_email () {
        $this->setPageTitle('Change Email');
        if (!empty($this->data)) {
            do {
                if (!$this->CheckCSRFToken())
                    break;

                if (!isset($this->data['User']['email']) || !isset($this->data['User']['password'])) {
                    IERR('Form data incomplete.');
                    $this->Session->setFlash(ERROR_STR);
                    break;
                }

                $passwordHash = $this->GameAuth->HashPassword($this->data['User']['password']);
                $user = $this->GameAuth->GetLoggedInUser();
                if ($passwordHash != $user['User']['password']) {
                    $this->Session->setFlash('Incorrect password.');
                    break;
                }

                if (strtolower($user['User']['email']) != strtolower($this->data['User']['old_email'])) {
                    $this->Session->setFlash('Your old email was incorrect.');
                    break;
                }

                if (!$this->GameValidate->IsValidEmail($this->data['User']['email'])) {
                    $this->Session->setFlash('Your new email was not valid.');
                    break;
                }

                $this->User->id = $user['User']['id'];
                $this->User->fastSave('email', $this->data['User']['email']);
                $this->User->ClearUserCache($user['User']['id']);

                $this->Session->setFlash('Your email has been changed.');
                $this->redirect(array('controller' => 'users', 'action' => 'preferences'));

            } while (false);
        }
    }

    //---------------------------------------------------------------------------------------------
    function reset () {
        $this->setPageTitle('Reset Account');
        if (!empty($this->data)) {
            do {
                if (!$this->CheckCSRFToken())
                    break;

                if (!isset($this->data['User']['password'])) {
                    IERR('Form data incomplete.');
                    $this->Session->setFlash(ERROR_STR);
                    break;
                }

                $passwordHash = $this->GameAuth->HashPassword($this->data['User']['password']);
                $user = $this->GameAuth->GetLoggedInUser();
                if ($passwordHash != $user['User']['password']) {
                    $this->Session->setFlash('Incorrect password.');
                    break;
                }

                if ($this->User->ResetUser($user['User']['id'])) {
                    $this->Session->setFlash('Account reset.');
                    $this->redirect(array('controller' => 'users', 'action' => 'preferences'));
                } else {
                    $this->Session->setFlash('Error: Your account could not be reset.');
                    break;
                }
            } while (false);
        }
    }

    //---------------------------------------------------------------------------------------------
    function forgot () {
        $this->setPageTitle('Forgot Password?');

        if ($this->GameAuth->GetLoggedInUser())
            $this->redirect('/');

        if (!empty($this->data)) {
            $username = $this->data['User']['username'];
            $email = $this->data['User']['email'];
            $user = $this->User->GetUserByUsername($username);

            if ($user === false) {
                $this->Session->setFlash('That user doesn\'t exist, or the email was incorrect.');
                return;
            }

            if (strtolower($user['User']['email']) != strtolower($email)) {
                $this->Session->setFlash('That user doesn\'t exist, or the email was incorrect.');
                return;
            }

            if ($this->User->SendResetKey($user['User']['id'])) {
                $this->Session->setFlash('Instructions have been emailed to you.');
            } else {
                $this->Session->setFlash(ERROR_STR);
            }
            $this->redirect('/');
        }
    }

    //---------------------------------------------------------------------------------------------
    function reset_password ($username = null, $key = null) {
        $this->setPageTitle('Reset Password');

        if (empty($this->data)) {
            if ($username === null || $key === null) {
                $this->fof();
                return;
            }

            $user = $this->User->GetUserByUsername($username);
            if ($user === false){
                $this->fof();
                return;
            }

            if ($user['User']['reset_key'] != $key) {
                $this->fof();
                return;
            }

            $this->set('user', $user);

        } else {
            $user = $this->User->GetUser($this->data['User']['id']);

            $key = $this->data['User']['reset_key'];
            $newPassword = $this->data['User']['password'];

            if ($user['User']['reset_key'] != $key) {
                IERR('Someone tried to submit form with wrong reset key.');
                $this->fof();
            }

            if (strlen($newPassword) < PASSWORD_MIN_CHARS) {
                $this->Session->setFlash('Your password must be at least ' . PASSWORD_MIN_CHARS . ' characters.');
                return;
            }

            $success = $this->User->ChangePassword($user['User']['id'], $newPassword);
            $this->User->id = $user['User']['id'];

            if ($success) {
                $this->User->fastSave('reset_key', '');

                $this->Session->setFlash('Your password has been changed!');
                $this->redirect('/');
            } else {
                $this->Session->setFlash(ERROR_STR);
                $this->redirect(array('controller' => 'users', 'action' => 'reset_password', $user['User']['Username'], $key));
            }
        }
    }

    //---------------------------------------------------------------------------------------------
    function stats () {
        $this->fof();
        $this->setPageTitle('Tactician Traits');

        $user = $this->GameAuth->GetLoggedInUser();
        $this->set('user', $user);
    }

    //---------------------------------------------------------------------------------------------
    function increase_stat () {
        if ($this->ShouldUseAjax()) {
            if (!isset($this->params['form']['stat'])) {
                IERR('increase_stat: stat was not set.');
                return;
            }

            $stat = $this->params['form']['stat'];

            $loggedInUserId = $this->GameAuth->GetLoggedInUserId();
            $success = $this->User->IncreaseStat($loggedInUserId, $stat);

            $user = $this->User->GetUser($loggedInUserId);
            $this->set('user', $user);

            return;
        }
        $this->fof();
    }

    //---------------------------------------------------------------------------------------------
    function preferences () {
        $this->setPageTitle('Preferences');

        if (!empty($this->data)) {
            if (!$this->CheckCSRFToken())
                return;

            $this->data['User']['profile_text'] = substr($this->data['User']['profile_text'], 0, 500);
            $this->User->id = $this->GameAuth->GetLoggedInUserId();
            $this->User->save(array(
                'hide_help_bar' => !isset($this->data['User']['hide_help_bar']) ? 0 : $this->data['User']['hide_help_bar'],
                'disable_shortcuts' => !isset($this->data['User']['disable_shortcuts']) ? 0 : $this->data['User']['disable_shortcuts'],
                'profile_text' => $this->data['User']['profile_text'],
            ));
            $this->User->ClearUserCache($this->User->id);

            $this->Session->setFlash('Preferences saved!');
            $this->redirect(array('controller' => 'users', 'action' => 'profile'));
        }
    }

    //=============================================================================================
    // Admin functions
    //=============================================================================================

    //---------------------------------------------------------------------------------------------
    function admi_index () {
        $this->set('users', $this->paginate());
    }

    //---------------------------------------------------------------------------------------------
    function admi_view ($id = null) {
        if (!$id) {
            $this->Session->setFlash('Invalid user.');
            $this->redirect(array('action' => 'index'));
        }

        $user = $this->User->find('first', array(
            'conditions' => array(
                'User.id' => $id,
            ),
            'contain' => array(
                'UserItem' => array(
                    'fields' => array(
                        'UserItem.id',
                        'UserItem.name',
                    ),
                ),
                'Character' => array(
                    'fields' => array(
                        'Character.id',
                        'Character.name',
                    )
                ),
            ),
        ));
        $this->set('user', $user);
    }

    //---------------------------------------------------------------------------------------------
    function admi_add () {
        if (!empty($this->data)) {
            $this->User->create();
            if ($this->User->save($this->data)) {
                $this->Session->setFlash('User saved.');
                $this->redirect(array('action' => 'view', $this->User->id));
            } else {
                $this->Session->setFlash('Could not save user.');
            }
        }
    }

    //---------------------------------------------------------------------------------------------
    function admi_edit ($id = null) {
        if (!$id && empty($this->data)) {
            $this->Session->setFlash('Invalid user.');
            $this->redirect(array('action' => 'index'));
        }
        if (!empty($this->data)) {
            if ($this->User->save($this->data)) {
                $this->Session->setFlash('User saved.');
                $this->redirect(array('action' => 'view', $this->User->id));
            } else {
                $this->Session->setFlash('Could not save user.');
            }
        }
        if (empty($this->data)) {
            $this->data = $this->User->read(null, $id);
        }
    }

    //---------------------------------------------------------------------------------------------
    function admi_delete ($id = null) {
        if (!$id) {
            $this->Session->setFlash('Invalid ID for user.');
            $this->redirect(array('action' => 'index'));
        }
        if ($this->User->del($id)) {
            $this->Session->setFlash('User deleted.');
            $this->redirect(array('action' => 'index'));
        } else {
            $this->Session->setFlash('Could not delete user.');
            $this->redirect($this->referer());
        }
    }

    //---------------------------------------------------------------------------------------------
    function admi_wipe () {
        if ($this->RequestHandler->isAjax()) {
            Configure::write('ajaxMode', 1);
            $this->autoRender = false;

            $startId = $this->params['form']['startId'];

            $ids = $this->User->find('list', array(
                'conditions' => array(
                    'admin' => 0,
                    'id >' => $startId,
                ),
                'limit' => 1,
            ));

            if (empty($ids)) {
                echo 'complete';
                return;
            }

            foreach ($ids as $id) {
                $lastId = $id;
                $this->User->ResetUser($id);
            }

            echo $lastId;
            return;
        }

        $numIds = $this->User->find('count', array(
            'conditions' => array(
                'admin' => 0,
            )
        ));
        $this->set('numIds', $numIds);
    }

    //---------------------------------------------------------------------------------------------
    function admi_find ($username = null) {
        if (!empty($this->data) || $username !== null) {
            $username = $username !== null ? $username : $this->data['User']['username'];
            $user = $this->User->find('first', array(
                'conditions' => array(
                    'User.username' => $username,
                ),
            ));

            if ($user === false) {
                $this->Session->setFlash('User doesn\'t exist!');
                return;
            }

            $this->redirect(array('controller' => 'users', 'action' => 'view', $user['User']['id']));
        }
    }

    //---------------------------------------------------------------------------------------------
    function admi_find_lookup () {
        if ($this->ShouldUseAjax()) {
            $this->log($this->params);
            Configure::write('ajaxMode', 1);
            $this->autoRender = false;

            if (!isset($this->params['url']['term']))
                return;

            $term = $this->params['url']['term'];

            $users = $this->User->find('all', array(
                'fields' => array(
                    'User.username',
                ),
                'conditions' => array(
                    'User.username LIKE ' => $term . '%',
                ),
                'limit' => 10
            ));
            $users = Set::classicExtract($users, '{n}.User.username');

            return json_encode($users);
        }
        $this->fof();
    }

    //---------------------------------------------------------------------------------------------
    function admi_impersonate ($id) {
        if (!$id) {
            $this->Session->setFlash('Invalid user.');
            $this->redirect(array('action' => 'index'));
        }

        $user = $this->User->find('first', array(
            'conditions' => array(
                'User.id' => $id,
            ),
        ));

        $this->GameAuth->SetCookie($id, $user['User']['password'], true);
        $this->redirect('/');
    }

    //---------------------------------------------------------------------------------------------
    function admi_email () {
        if (!empty($this->data)) {
            if ($this->params['form']['submit'] == 'Test Email') {
                $success = $this->User->Email($this->data['User']['subject'], $this->data['User']['content'], 2);
            } else if ($this->params['form']['submit'] == 'Mass Send') {
                $success = $this->User->Email($this->data['User']['subject'], $this->data['User']['content']);
            }

            if ($success)
                $this->Session->setFlash('Email sent successfully.');
            else
                $this->Session->setFlash('Email failed to send.');
        }
    }

    //---------------------------------------------------------------------------------------------
    function admi_ban ($userId) {
        $this->User->id = $userId;
        $this->User->fastSave('state', USER_STATE_BANNED);
        $this->User->ClearUserCache($userId);
        $this->Session->setFlash('User banned.');
        $this->redirect($this->referer());
    }

    //---------------------------------------------------------------------------------------------
    function admi_unban ($userId) {
        $this->User->id = $userId;
        $this->User->fastSave('state', USER_STATE_NORMAL);
        $this->User->ClearUserCache($userId);
        $this->Session->setFlash('User unbanned.');
        $this->redirect($this->referer());
    }

    //---------------------------------------------------------------------------------------------
    function admi_give_item ($userId = null) {
        if (!empty($this->data)) {
            do {
                if (!$this->User->UserItem->GiveUserItemToUser($this->data['User']['user_item_id'], $this->data['User']['id'])) {
                    $this->Session->setFlash('Failed to give item to user.');
                    break;
                }
                $this->Session->setFlash('Item given.');
                $this->redirect(array('controller' => 'users', 'action' => 'view', $this->data['User']['id']));
                return;
            } while (false);
        }

        $this->set('userId', $userId);
    }

    //---------------------------------------------------------------------------------------------
    function admi_give_stackable ($userId = null) {
        if ($userId == null) {
            $this->fof();
            return;
        }

        if (!empty($this->data)) {
            try {
                $this->User->UserItem->Item->GiveItemToUser(
                    @$this->data['User']['item_id'],
                    $userId,
                    @$this->data['User']['quantity']
                );
                $this->Session->setFlash('Item given.');
                $this->redirect(array('controller' => 'users', 'action' => 'view', $userId));
            } catch (AppException $e) {
                $this->Session->setFlash($e->getMessage());
            }
        }
    }
}
?>
