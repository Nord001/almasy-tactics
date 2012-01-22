<?

$GLOBALS['THRIFT_ROOT'] = '../src';

require 'Almasy.php';
require_once $GLOBALS['THRIFT_ROOT'].'/protocol/TBinaryProtocol.php';
require_once $GLOBALS['THRIFT_ROOT'].'/transport/TSocket.php';
require_once $GLOBALS['THRIFT_ROOT'].'/transport/TBufferedTransport.php';

$socket = new TSocket('localhost', '1337');
$transport = new TBufferedTransport($socket);
$protocol = new TBinaryProtocol($transport);
$client = new AlmasyClient($protocol);

$transport->open();
$result = $client->getBattle(1, 133);
$transport->close();

echo 'Result: ' . $result;

?>