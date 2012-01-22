<?

class AdminHomeController extends AppController {

    var $uses = array('User', 'Battle', 'Formation');
    var $pageTitle = 'Home';

    //---------------------------------------------------------------------------------------------
    function admin_index () {
        $this->set('numOnline', $this->User->GetNumOnlineUsers());
        $this->set('numUsersPlayedToday', $this->User->GetNumUsersPlayedToday());
        $this->set('numNewAccounts', $this->User->GetNumNewAccountsToday());
        $this->set('numNewReferredAccounts', $this->User->GetNumNewReferredAccountsToday());
        $this->set('numBattles', $this->Battle->GetNumBattlesToday());
        $this->set('timeSpentToday', $this->User->GetTimeSpentOnSiteToday());

        $timeSpent = $this->User->GetTimeSpentOnSite(strtotime('-2 weeks'));
        $data = array();
        foreach ($timeSpent as $date => $value) {
            $data[] = array(($date - 5 * 60 * 60)  . '000', $value);
        }

        $this->set('timeSpentInLastTwoWeeks', $data);

        $players = $this->User->GetPlayers(strtotime('-2 weeks'));
        $data = array();
        foreach ($players as $date => $value)
            $data[] = array(($date - 5 * 60 * 60) . '000', $value);
        $this->set('players', $data);

        $topUserIdsByWins = $this->User->GetTopUserIdsByWins();
        $this->set('topUsersByWins', $this->User->GetUsers($topUserIdsByWins));

        $topUserIdsByEarnings = $this->User->GetTopUserIdsByEarnings();
        $this->set('topUsersByEarnings', $this->User->GetUsers($topUserIdsByEarnings));

        $topFormationIdsByWins = $this->Formation->GetTopFormationIdsByWins();
        $formations = $this->Formation->GetFormations($topFormationIdsByWins);
        foreach ($formations as &$formation) {
            $user = $this->User->GetUser($formation['Formation']['user_id']);
            $formation['User'] = $user['User'];
        }

        $this->set('topFormationsByWins', $formations);
    }

    //---------------------------------------------------------------------------------------------
    function admin_clear_cache () {
        ClearDir('../tmp/cache');
        ClearDir('../tmp/cache/models');
        ClearDir('../tmp/cache/persistent');
        $this->Session->setFlash('Cache cleared.');
        $this->redirect(array('controller' => 'admin_home', 'action' => 'index'));
    }

    //---------------------------------------------------------------------------------------------
    function admin_php_info () {
        phpinfo();
    }
}

?>