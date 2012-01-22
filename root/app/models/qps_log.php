<?

class QpsLog extends AppModel {

    var $useTable = 'qps_log';

    //---------------------------------------------------------------------------------------------
    function LogQPS ($userId, $page) {
        $this->query(sprintf(
            "INSERT INTO
                `qps_log`
             VALUES
                 (
                     '%s',
                     '{$page}',
                     '{$userId}'
                 )", date(DB_FORMAT)));

        $this->query("DELETE FROM qps_log WHERE date < '" . date(DB_FORMAT, strtotime('-15 minutes')) . "'");
    }

    //---------------------------------------------------------------------------------------------
    function GetQPS () {
        $qps = array();
        $value = $this->query("SELECT COUNT(*) from qps_log WHERE date > '" . date(DB_FORMAT, strtotime('-1 minutes')) . "'");
        $qps['one'] = round(array_shift($value[0][0]), 1);
        $value = $this->query("SELECT COUNT(*) from qps_log WHERE date > '" . date(DB_FORMAT, strtotime('-5 minutes')) . "'");
        $qps['five'] = round(array_shift($value[0][0]) / 5, 1);
        $value = $this->query("SELECT COUNT(*) from qps_log WHERE date > '" . date(DB_FORMAT, strtotime('-15 minutes')) . "'");
        $qps['fifteen'] = round(array_shift($value[0][0]) / 15, 1);

        return $qps;
    }
};

?>