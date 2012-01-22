<?

class GameAuthComponent extends Object {

    var $components = array('Cookie', 'Session');

    var $controller = null;
    var $isAllowed = false;

    var $loggedInUser = false;

    //----------------------------------------------------------------------------------------------
    function GetControllerPermissions () {
        if ($this->controller == null)
            return null;

        // If it is a single value, then use that for all actions.
        if (!is_array($this->controller->authList)) return $this->controller->authList;

        // By default, only users and above can access anything, if no particular security level is specified.
        return isset($this->controller->authList[$this->controller->action]) ? $this->controller->authList[$this->controller->action] : AUTH_USERS;
    }

    //----------------------------------------------------------------------------------------------
    function IsAdminAction () {
        return strpos($this->controller->action, Configure::read('Routing.admin') . '_') !== false;
    }

    //----------------------------------------------------------------------------------------------
    function IsAuthorized () {
        $permissionBits = $this->GetControllerPermissions();

        $user = $this->GetLoggedInUser();
        if ($user == null && ($permissionBits & AUTH_NONE_ONLY))
            return true;

        if ($user == null)
            return false;

        $loggedInUser = $this->GetLoggedInUser();
        $isAdmin = $loggedInUser['User']['admin'];
;
        if ($this->IsAdminAction() && $isAdmin == 0)
            return false;

        if ($isAdmin == 0 && ($permissionBits & AUTH_USER_ONLY))
            return true;

        if ($isAdmin == 1 && ($permissionBits & AUTH_ADMIN_ONLY))
            return true;

        return false;
    }

    //--------------------------------------------------------------------------------------------
    function startup (&$controller) {
        $this->controller = $controller;

        // Only keep redirect around if you're looking at the login page
        if ($controller->here != '/users/login')
            $this->Session->del('GameAuth.redirect');

        if (!$this->IsAuthorized()) {
            // Don't give them a redirect, just fof
            if ($this->IsAdminAction()) {
                $controller->fof();
                return false;
            }
            $this->Session->write('GameAuth.redirect', $controller->here);
            $controller->redirect(array('controller' => 'users', 'action' => 'login', Configure::read('Routing.admin') => false));
            return false;
        }
    }

    //----------------------------------------------------------------------------------------------
    function Logout () {
        setcookie(USER_COOKIE_NAME, '', time() - 42000, '/');
    }

    //--------------------------------------------------------------------------------------------
    function SetCookie ($userId, $passwordHash, $rememberMe) {
        $userModel = ClassRegistry::init('User');
        $user = $userModel->GetUser($userId);

        $expiration = $rememberMe ? time() + USER_COOKIE_EXPIRATION : time() + USER_SESSION_COOKIE_EXPIRATION;
        $cookieData = BuildCookie($user['User']['id'], $passwordHash, $expiration, $_SERVER['REMOTE_ADDR']);
        setcookie(USER_COOKIE_NAME, $cookieData, $expiration, '/');
    }

    //--------------------------------------------------------------------------------------------
    function HashPassword ($password) {
        return Security::hash($password, 'md5', true);
    }

    //--------------------------------------------------------------------------------------------
    function Login ($username, $password, $rememberMe) {
        $userModel = ClassRegistry::init('User');

        $user = $userModel->GetUserByUsername($username);
        if (!$user)
            return false;

        $passwordHash = $this->HashPassword($password);

        if ($user['User']['password'] != $passwordHash)
            return false;

        $this->SetCookie($user['User']['id'], $passwordHash, $rememberMe);

        $userModel->RegisterLogin($user['User']['id']);

        return $user;
    }

    //--------------------------------------------------------------------------------------------
    function GetLoggedInUser () {
        if ($this->loggedInUser !== false)
            return $this->loggedInUser;

        if (!isset($_COOKIE[USER_COOKIE_NAME]))
            return false;

        $cookie = $_COOKIE[USER_COOKIE_NAME];
        $data = DecipherCookie($cookie);

        if ($data === false) {
            $this->log('Could not decipher cookie: ' . $cookie, 'login');
            $this->Logout();
            return false;
        }

        list($userId, $passwordHash, $expiration, $ip) = $data;

        //if ($ip != $_SERVER['REMOTE_ADDR']) {
        //    $this->log(sprintf('Ip didn\'t match. ips: %s %s user: %s', $ip, $_SERVER['REMOTE_ADDR'], $userId), 'login');
        //    $this->Logout();
        //    return false;
        //}

        $userModel = ClassRegistry::init('User');
        $user = $userModel->GetUser($userId);
        if (!$user) {
            $this->Logout();
            $this->log('User didn\'t exist.', 'login');
            return false;
        }

        if ($user['User']['password'] != $passwordHash) {
            $this->Logout();
            $this->log('Password didn\'t match.', 'login');
            return false;
        }

        if ($user['User']['state'] != USER_STATE_NORMAL) {
            $this->log('User was banned.', 'login');
            return false;
        }

        // Send new updated cookie
        // If expiration is longer than the session, it means it's a remember-me so update it again to be that length.
        // If it's not, then refresh it to last for another SESSION_COOKIE_EXPIRATION seconds.
        $expiration = ($expiration > time() + USER_SESSION_COOKIE_EXPIRATION) ? time() + USER_COOKIE_EXPIRATION : time() + USER_SESSION_COOKIE_EXPIRATION;

        setcookie(USER_COOKIE_NAME, $cookie, $expiration, '/');

        if (strtotime($user['User']['last_action']) < strtotime(USER_PING_INTERVAL))
            $userModel->Ping($userId);

        $userModel->OnUserPage($userId);

        // Refetch
        $user = $userModel->GetUser($userId);

        $this->loggedInUser = $user;

        return $user;
    }

    //--------------------------------------------------------------------------------------------
    function GetLoggedInUserId () {
        $user = $this->GetLoggedInUser();
        if (!$user)
            return false;

        return $user['User']['id'];
    }
};

?>
