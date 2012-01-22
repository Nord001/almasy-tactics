<?

class AdminHomeController extends AppController {

    var $uses = array('User', 'Battle', 'Formation');
    var $pageTitle = 'Home';

    //---------------------------------------------------------------------------------------------
    function admi_index () {
        $this->set('numOnline', $this->User->GetNumOnlineUsers());
        $this->set('numUsersPlayedToday', $this->User->GetNumUsersPlayedToday());
        $this->set('numNewAccounts', $this->User->GetNumNewAccountsToday());
        $this->set('numNewReferredAccounts', $this->User->GetNumNewReferredAccountsToday());
        $this->set('numBattles', $this->Battle->GetNumBattlesToday());
        //$this->set('timeSpentToday', $this->User->GetTimeSpentOnSiteToday());

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

        $timeSpent = $this->User->GetTimeSpentOnSite(strtotime('-2 months'));
        $data = array();
        foreach ($timeSpent as $date => $value) {
            $data[] = array(($date - 5 * 60 * 60)  . '000', $value);
        }

        $this->set('timeSpentInLastTwoMonths', $data);

        $players = $this->User->GetPlayers(strtotime('-2 months'));
        $data = array();
        foreach ($players as $date => $value)
            $data[] = array(($date - 5 * 60 * 60) . '000', $value);
        $this->set('playersInLastTwoMonths', $data);
    }

    //---------------------------------------------------------------------------------------------
    function admi_clear_cache () {
        ClearDir('../tmp/cache/');
        ClearDir('../tmp/cache/models');
        ClearDir('../tmp/cache/persistent');
        Cache::clear();

        $this->autoRender = false;
        return 'Cache cleared';
    }

    //---------------------------------------------------------------------------------------------
    function admi_php_info () {
        phpinfo();
    }
}

?>