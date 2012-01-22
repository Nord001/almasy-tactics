<?php

class UsersController extends AppController {

    var $authList = array(
            'register'          => AUTH_ALL,
            'usernameAvailable' => AUTH_ALL,
            'login'             => AUTH_ALL,
            'forgot'            => AUTH_ALL,
            'reset_password'    => AUTH_ALL
    );

    //=============================================================================================
    // User functions
    //=============================================================================================

    //---------------------------------------------------------------------------------------------
    function login () {
        $this->pageTitle = 'Login';

        if ($this->GameAuth->GetLoggedInUser())
            $this->redirect('/');

        $this->layout = 'default';
        if (!empty($this->data)) {
            if ($loggedInUser = $this->GameAuth->Login($this->data['User']['username'], $this->data['User']['password'], $this->data['User']['remember_me'])) {
                $forumUserId = $loggedInUser['User']['forum_user_id'];

                // Login on forum
                if ($forumUserId != '') {
                    $result = file_get_contents(sprintf(
                        'http://%s%s?key=%s&mode=login&forumUserId=%s',
                        $_SERVER['SERVER_NAME'],
                        FORUM_HANDLER,
                        FORUM_HANDLER_KEY,
                        $forumUserId
                    ));

                    if (strlen($result) == 0) {
                        $this->log('Could not login user to forum.');
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
        }

        $this->GameAuth->logout();

        // Logout on forum
        if ($loggedInUser['User']['forum_user_id']) {
            $result = file_get_contents(sprintf(
                'http://%s%s?key=%s&mode=logout&forumUserId=%s',
                $_SERVER['SERVER_NAME'],
                FORUM_HANDLER,
                FORUM_HANDLER_KEY,
                $loggedInUser['User']['forum_user_id']
            ));

            if (strlen($result) == 0) {
                $this->log('Could not logout user from forum.');
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
        $this->pageTitle = 'Change Password';

        if (!empty($this->data)) {
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
            if ($user['User']['password'] != Security::hash($oldPassword, 'md5', true)) {
                $this->Session->setFlash('Your old password was incorrect.');
                return;
            }

            $success = $this->User->ChangePassword($user['User']['id'], $password1);
            if ($success) {
                // Update cookie
                $this->GameAuth->Login($user['User']['username'], $password1);
                $this->Session->setFlash('Password changed successfully.');
                $this->redirect('/'); // FIX: where to?
            } else {
                $this->Session->setFlash('Your password could not be changed.');
                return;
            }
        }
    }

    //---------------------------------------------------------------------------------------------
    // Async postback for updating money
    function get_money () {
        if ($this->ShouldUseAjax()) {
            Configure::write('debug', 0);
            $this->autoRender = false;
            $user = $this->GameAuth->GetLoggedInUser();
            if ($user) {
                echo $user['User']['money'];
            }
            return;
        }
        $this->fof();
    }

    //---------------------------------------------------------------------------------------------
    function register ($referrerUsername = null) {
        $this->pageTitle = 'Register';

        if (!empty($this->data)) {
            $username = $this->data['User']['reg_username'];
            $password = $this->data['User']['reg_password'];
            $email = $this->data['User']['email'];
            $firstCharacterName = $this->data['Character']['name'];
            $referringUserId = isset($this->data['User']['referring_id']) ? $this->data['User']['referring_id'] : false;

            // Invalid characters screen
            if (preg_match('/^[a-z0-9]*$/', strtolower($username)) == 0) {
                $this->Session->setFlash('Your username can only contain alphanumeric characters. ');
                return;
            }

            if (strlen($username) == 0) {
                $this->Session->setFlash('Your username cannot be blank.');
                return;
            }

            if (strlen($password) < PASSWORD_MIN_CHARS) {
                $this->Session->setFlash('Your password must be at least ' . PASSWORD_MIN_CHARS . ' characters.');
                return;
            }

            if (preg_match('/^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/', $email) == 0) {
                $this->Session->setFlash('Your email was not valid.');
                return;
            }

            if (!$this->GameValidate->IsValidCharacterName($firstCharacterName)) {
                $this->Session->setFlash('Your character name is not valid.');
                return;
            }

            $existingUser = $this->User->GetUserByUsername($username);
            if ($existingUser) {
                $this->Session->setFlash('That username is taken.');
                return;
            }

            if (!$this->User->CreateNewUser($username, $password, $email, $firstCharacterName, $referringUserId)) {
                $this->Session->setFlash('An error has occurred. Please contact help.');
                return;
            }

            $this->Session->setFlash('Congratulations, your account has been created! If you\'re new, check out the Quickstart Guide in the Help section!');
            $this->GameAuth->Login($username, $password, false);

            $this->redirect(array('controller' => 'army', 'action' => 'index'));
        } else {
            if ($referrerUsername != null) {
                $user = $this->User->GetUserByUsername($referrerUsername);
                if ($user !== false)
                    $this->set('referringUser', $user);
            }
        }
    }

    //---------------------------------------------------------------------------------------------
    function usernameAvailable () {
        if ($this->ShouldUseAjax()) {
            Configure::write('debug', 0);
            $this->autoRender = false;

            if (!isset($this->params['form']['username'])) {
                $this->autoRender = false;
                echo -1;
                return;
            }

            $user = $this->User->findByUsername($this->params['form']['username']);
            echo $user === false ? 1 : 0;
            return;
        }
        $this->fof();
    }

    //---------------------------------------------------------------------------------------------
    function profile ($id = null) {
        if ($id == null) {
            $user = $this->GameAuth->GetLoggedInUser();
            $this->set('user', $user);
            $this->pageTitle = $user['User']['username'];

            if ($user['User']['referring_id'] != null)
                $this->set('referringUser', $this->User->GetUser($user['User']['referring_id']));
        }
    }

    //---------------------------------------------------------------------------------------------
    function referrals () {
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

        $this->set('friendshipsBond', $this->User->UserItem->GetUserItem(REFERRAL_SYSTEM_LEVEL_2_USER_ITEM_ID));
    }

    //---------------------------------------------------------------------------------------------
    function change_portrait ($name = null) {
        $this->pageTitle = 'Change Portrait';
        if ($name != null) {
            if (!in_array($name, PORTRAIT_LIST())) {
                $this->Session->setFlash('An error has occured.');
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
    function reset () {
        $this->pageTitle = 'Reset';
        if (!empty($this->data)) {
            $loggedInUserId = $this->GameAuth->GetLoggedInUserId();
            if ($this->User->ResetUser($loggedInUserId))
                $this->Session->setFlash('Account reset.');
            else
                $this->Session->setFlash('Error: Your account could not be reset.');

            $this->redirect(array('controller' => 'users', 'action' => 'profile'));
        }
    }

    //---------------------------------------------------------------------------------------------
    function forgot () {
        $this->pageTitle = 'Forgot Password?';

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

            if ($user['User']['email'] != $email) {
                $this->Session->setFlash('That user doesn\'t exist, or the email was incorrect.');
                return;
            }

            if ($this->User->SendResetKey($user['User']['id'])) {
                $this->Session->setFlash('Instructions have been emailed to you.');
            } else {
                $this->Session->setFlash('An error has occurred.');
            }
            $this->redirect('/');
        }
    }

    //---------------------------------------------------------------------------------------------
    function reset_password ($username = null, $key = null) {
        $this->pageTitle = 'Reset Password';

        if (empty($this->data)) {
            if ($username === null || $key === null)
                $this->fof();

            $user = $this->User->GetUserByUsername($username);
            if ($user === false)
                $this->fof();

            if ($user['User']['reset_key'] != $key)
                $this->fof();

            $this->set('user', $user);

        } else {
            $user = $this->User->GetUser($this->data['User']['id']);

            $key = $this->data['User']['reset_key'];
            $newPassword = $this->data['User']['password'];

            if ($user['User']['reset_key'] != $key) {
                $this->log('Someone tried to submit form with wrong reset key.');
                $this->fof();
            }

            $success = $this->User->ChangePassword($user['User']['id'], $newPassword);
            $this->User->id = $userId;

            if ($success) {
                $this->User->fastSave('reset_key', '');

                $this->Session->setFlash('Your password has been changed!');
                $this->redirect('/');
            } else {
                $this->Session->setFlash('An error has occured.');
                $this->redirect(array('controller' => 'users', 'action' => 'reset_password', $user['User']['Username'], $key));
            }
        }
    }

    //---------------------------------------------------------------------------------------------
    function stats () {
        $this->pageTitle = 'Tactician Traits';

        $user = $this->GameAuth->GetLoggedInUser();
        $this->set('user', $user);
    }

    //---------------------------------------------------------------------------------------------
    function increase_stat () {
        if ($this->ShouldUseAjax()) {
            if (!isset($this->params['form']['stat'])) {
                $this->log('increase_stat: stat was not set.');
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
        if (isset($this->data['has_data'])) {
            $this->User->id = $this->GameAuth->GetLoggedInUserId();
            $this->User->save(array(
                'hide_help_bar' => !isset($this->data['User']['hide_help_bar']) ? 0 : $this->data['User']['hide_help_bar'],
                'disable_shortcuts' => !isset($this->data['User']['disable_shortcuts']) ? 0 : $this->data['User']['disable_shortcuts'],
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
    function admin_index () {
        $this->set('users', $this->paginate());
    }

    //---------------------------------------------------------------------------------------------
    function admin_view ($id = null) {
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
            ),
        ));
        $this->set('user', $user);
    }

    //---------------------------------------------------------------------------------------------
    function admin_add () {
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
    function admin_edit ($id = null) {
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
    function admin_delete ($id = null) {
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
    function admin_wipe () {
        if ($this->RequestHandler->isAjax()) {
            Configure::write('debug', 0);
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
    function admin_find () {
        if (!empty($this->data)) {
            $user = $this->User->find('first', array(
                'conditions' => array(
                    'User.username' => $this->data['User']['username'],
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
    function admin_impersonate ($id) {
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
    function admin_email () {
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
    function admin_give_item ($userId = null) {
        if (!empty($this->data)) {
            $this->User->UserItem->GiveUserItemToUser($this->data['User']['user_item_id'], $this->data['User']['id']);
            $this->Session->setFlash('Item given.');
            $this->redirect(array('controller' => 'users', 'action' => 'view', $this->data['User']['id']));
        } else {
            $this->set('userId', $userId);
        }
    }
}
?>