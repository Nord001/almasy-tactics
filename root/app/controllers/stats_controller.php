<?php

class StatsController extends AppController {

    var $uses = array('User', null);

    //---------------------------------------------------------------------------------------------
    function admi_new_account_activity () {
        $newUserIds = $this->User->query("
            SELECT
                DISTINCT `user_id`
            FROM
                `new_user_activity`
            ORDER BY
                `id` DESC
            LIMIT 10");

        $newUserIds = Set::extract($newUserIds, '{n}.new_user_activity.user_id');

        // Section by account. Each account is sectioned by sessions, which it divides based on how long it takes between pages.
        $accounts = array();
        foreach ($newUserIds as $id) {
            $account = array();
            $user = $this->User->GetUser($id);
            $account['User'] = $user['User'];

            $data = $this->User->query("
                SELECT
                    `time`,
                    `page`
                FROM
                    `new_user_activity`
                WHERE
                    `user_id` = {$id}");

            $sessions = array();
            $session = array();
            $lastPageTime = false;
            $lastPage = false;
            for ($i = 0; $i < count($data); $i++) {
                $row = $data[$i]['new_user_activity'];

                if (strtotime($row['time']) > $lastPageTime + 60 * 10 && $lastPageTime != false) {
                    $sessions[] = $session;
                    $session = array();
                } else {
                    if ($lastPageTime !== false)
                        $session[count($session) - 1]['duration'] = strtotime($row['time']) - $lastPageTime;
                }

                if ($lastPage === $row['page'] && isset($session[count($session) - 1]['duration'])) {
                    $session[count($session) - 1]['duration'] += strtotime($row['time']) - $lastPageTime;
                } else {
                    $session[] = $row;
                    $lastPageTime = strtotime($row['time']);
                }
                $lastPage = $row['page'];
            }

            if (!empty($session))
                $sessions[] = $session;

            $account['sessions'] = $sessions;

            $total = 0;
            $sessionTotals = array();
            foreach ($sessions as $session) {
                $totalDuration = 0;
                foreach ($session as $page)
                    $totalDuration += isset($page['duration']) ? $page['duration'] : 0;
                $sessionTotals[] = $totalDuration;
                $total += $totalDuration;
            }

            $account['sessionTotals'] = $sessionTotals;

            // Show only people who didn't stick around
            if ($total < 900)
                $accounts[] = $account;
        }

        $this->set('accounts', $accounts);
    }

    //---------------------------------------------------------------------------------------------
    function admi_funnel () {
        $data = $this->User->query("
            SELECT
                `week`,
                `numNewAccounts`,
                `referredAccounts`,
                `numStuckForTenMinutes`,
                CONCAT(ROUND(`numStuckForTenMinutes` / `numNewAccounts` * 100, 1), '%') AS `numStuckForTenMinutesCumul`,
                `numStuckForOneDay`,
                CONCAT(ROUND(`numStuckForOneDay` / `numStuckForTenMinutes` * 100, 1), '%') AS `numStuckForOneDayCumul`,
                `numStuckForOneWeek`,
                CONCAT(ROUND(`numStuckForOneWeek` / `numStuckForOneDay` * 100, 1), '%') AS `numStuckForOneWeekCumul`,
                `numStuckForOneWeekP` AS `numStuckForOneWeekTotal`
            FROM
                (
                    SELECT
                        STR_TO_DATE(CONCAT(YEARWEEK(DATE_SUB(`date_created`, INTERVAL 2 HOUR)), 'Sunday'), '%X%V%W') AS `week`,
                        COUNT(DISTINCT `email`) AS `numNewAccounts`,
                        SUM(IF(referring_id, 1, 0)) AS `referredAccounts`,
                        SUM(IF(last_action > date_created, 1, 0)) AS `numStuckForTenMinutes`,
                        CONCAT(ROUND(SUM(IF(last_action > date_created, 1, 0)) / COUNT(DISTINCT `email`) * 100, 1), '%') AS `numStuckForTenMinutesP`,
                        SUM(IF(last_action > date_created + INTERVAL 1 DAY, 1, 0)) AS `numStuckForOneDay`,
                        CONCAT(ROUND(SUM(IF(last_action > date_created + INTERVAL 1 DAY, 1, 0)) / COUNT(DISTINCT `email`) * 100, 1), '%') AS `numStuckForOneDayP`,
                        SUM(IF(last_action > date_created + INTERVAL 1 WEEK, 1, 0)) AS `numStuckForOneWeek`,
                        CONCAT(ROUND(SUM(IF(last_action > date_created + INTERVAL 1 WEEK, 1, 0)) / COUNT(DISTINCT `email`) * 100, 1), '%') AS `numStuckForOneWeekP`
                    FROM
                        `users`
                    GROUP BY
                        YEARWEEK(DATE_SUB(`date_created`, INTERVAL 2 HOUR))
                    ORDER BY
                        `week` DESC
                ) AS `temp`");
        foreach ($data as &$row)
            $row = array_merge($row['temp'], $row[0]);

        $this->set('data', $data);
    }
}
?>
