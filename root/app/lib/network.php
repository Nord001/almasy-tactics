<?

$GLOBALS['THRIFT_ROOT'] = 'vendors/src';
require_once 'vendors/almasy/Almasy.php';
require_once $GLOBALS['THRIFT_ROOT'].'/protocol/TBinaryProtocol.php';
require_once $GLOBALS['THRIFT_ROOT'].'/transport/TSocket.php';
require_once $GLOBALS['THRIFT_ROOT'].'/transport/TBufferedTransport.php';

class Network {
    private static $instance;

    private $socket;
    private $gameTransport;
    private $gameClient;

    //----------------------------------------------------------------------------------------------
    private function __construct() {
    }

    //----------------------------------------------------------------------------------------------
    public function __destruct () {
        $this->DestroyGameClient();
    }

    //----------------------------------------------------------------------------------------------
    public static function GetInstance()
    {
        if (!isset(self::$instance)) {
            $c = __CLASS__;
            self::$instance = new $c;
        }

        return self::$instance;
    }

    //----------------------------------------------------------------------------------------------
    public function GetGameClient () {
        if ($this->gameClient !== null)
            return $this->gameClient;

        $this->socket = new TSocket(GAME_SERVER, GAME_SERVER_PORT);
        $this->socket->setRecvTimeout(3000);
        $this->gameTransport = new TBufferedTransport($this->socket);
        $protocol = new TBinaryProtocol($this->gameTransport);
        $this->gameClient = new AlmasyClient($protocol);

        $this->gameTransport->open();

        return $this->gameClient;
    }

    //----------------------------------------------------------------------------------------------
    private function DestroyGameClient () {
        if ($this->gameClient === null)
            return;

        $this->gameTransport->close();
        $this->socket->close();
        $this->gameClient = null;
    }

    //----------------------------------------------------------------------------------------------
    public function __clone()
    {
        trigger_error('Clone is not allowed.', E_USER_ERROR);
    }

}
?>