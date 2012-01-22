<?

//==============================================================================================
// Auth Constants
//==============================================================================================

define('AUTH_ADMIN_ONLY', 1 << 2);
define('AUTH_USER_ONLY',  1 << 1);
define('AUTH_NONE_ONLY',  1 << 0);
define('AUTH_ALL', AUTH_NONE_ONLY | AUTH_USER_ONLY | AUTH_ADMIN_ONLY);
define('AUTH_USERS', AUTH_USER_ONLY | AUTH_ADMIN_ONLY);

//---------------------------------------------------------------------------------------------
function AffinityNameFromAffinity ($num) {
    switch ($num) {
        case AFFINITY_FIRE:
            return "Fire";
        case AFFINITY_STEEL:
            return "Steel";
        case AFFINITY_WOOD:
            return "Wood";
        case AFFINITY_EARTH:
            return "Earth";
        case AFFINITY_WATER:
            return "Water";
        case AFFINITY_NEUTRAL:
            return "Neutral";
        default:
            return "Error";
    }
    return "Error";
}

//==============================================================================================
// App Controller
//==============================================================================================

class AppController extends Controller {
    var $helpers = array('Html', 'Form', 'Ui', 'Javascript', 'Time');
    var $components = array('RequestHandler', 'GameAuth', 'GameValidate', 'Captcha');
    var $uses = array('User', 'Guild');
    var $persistModel = true;

    var $stopFof = false;
    var $didFof = false;
    var $disableAjaxCheck = false;
    var $stopRedirect = false;
    var $redirectDest = false;

    var $suppressHeader = false;

    //---------------------------------------------------------------------------------------------
    function setPageTitle ($title) {
        $this->pageTitle = h($title);
    }

    //---------------------------------------------------------------------------------------------
    // Wrapper for Request Handler.
    function ShouldUseAjax () {
        if ($this->disableAjaxCheck || Configure::read('debug') > 0)
            return true;

        return $this->RequestHandler->isAjax();
    }

    //---------------------------------------------------------------------------------------------
    function fof () {
        if (!$this->stopFof)
            $this->cakeError('error404');
        $this->didFof = true;
    }

    //---------------------------------------------------------------------------------------------
    function redirect ($dest) {
        if (!$this->stopRedirect)
            parent::redirect($dest);
        $this->redirectDest = $dest;
    }

    //----------------------------------------------------------------------------------------------
    function set ($name, $var = null, $escapeHtml = false) {
        // If $var is null, it's from cake and we just let it go.
        // Almasy code never uses one-variable set.
        if ($var === null)
            return parent::set($name);

        if (!$escapeHtml)
            return parent::set($name, $var);
        else
            return parent::set($name, h($var));
    }

    //==============================================================================================
    // Auth
    //==============================================================================================
    var $authList = array();

    //----------------------------------------------------------------------------------------------
    // Sets up authentication

    function beforeFilter () {
        list($usec, $sec) = explode(' ', microtime());
        srand($sec + $usec * 1000000);

        parent::beforeFilter();

        $user = $this->GameAuth->GetLoggedInUser();
        if ($user) {
            $this->set('a_user', $user, false);
            if (@$user['GuildMembership']) {
                $guild = $this->Guild->GetGuild($user['GuildMembership']['guild_id']);
                $this->set('a_guild', $guild, false);
            }
        }

        // Setup layout if you're admin.
        if (strpos($this->action, Configure::read('Routing.admin') . '_') !== false)
            $this->layout = 'admin';

        $path = $this->params['controller'] . '/' . $this->params['action'];

        if (!$this->RequestHandler->isAjax()) {
            $this->loadModel('Faq');
            $faqs = $this->Faq->GetFaqsForPath($path);
            if ($faqs !== false)
                $this->set('faqsForPage', $faqs, false);
        }

        if (in_array($path, CAPTCHA_ACTIONS())) {
            $this->Captcha->IncrementAction();
        }

        $this->set('actions', $this->Captcha->GetNumActions(), false);
    }

    //----------------------------------------------------------------------------------------------
    // Verifies that the csrf_token in $this->data exists and is set correctly.
    function CheckCSRFToken () {
        if (!isset($this->data['csrf_token'])) {
            $this->Session->setFlash(ERROR_STR);
            IERR('Form submitted without CSRF token.');
        }

        $token = $this->data['csrf_token'];
        $user = $this->GameAuth->GetLoggedInUser();
        if ($token != $user['User']['csrf_token']) {
            $this->Session->setFlash(ERROR_STR);
            IERR('Form submitted with incorrect CSRF token.');
        }
        return true;
    }
}

?>
