<?

class ErrorLog extends AppModel {

    var $useTable = 'error_log';

    //---------------------------------------------------------------------------------------------
    function GetErrorList () {
        $list = $this->find('all', array(
            'fields' => array(
                'id',
                'error',
                'file',
                'line',
                'time'
            ),
            'order' => 'time DESC',
            'limit' => 50
        ));

        return $list;
    }

    //---------------------------------------------------------------------------------------------
    function GetNumRecentErrors () {
        $numErrors = $this->find('count', array(
            'conditions' => array(
                'time >' => date(DB_FORMAT, strtotime('-1 day')),
            )
        ));
        return $numErrors;
    }

    //---------------------------------------------------------------------------------------------
    function GetError ($id) {
        CheckNumeric($id);

        return $this->findById($id);
    }

}

?>