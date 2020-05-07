<?php
error_reporting(E_ERROR);
ini_set('display_errors', 0);

require_once('connection.php');

require_once('nusoap/nusoap.php');

include 'dao_functions.php';

nusoap_base::setGlobalDebugLevel(o);

$server = new soap_server();

$server->setDebugLevel(o);

$server->configureWSDL('WORLDwsdl', 'urn:WORLDwsdl');

include 'soap_world_types.php';

$server->register('findCountry', // method name
  array(
    'continent' => 'xsd:string',
  ), // input parameters
  array(
    'return' => 'tns:ArrayOfCountry'
  ), // output parameters
  'urn:WORLDwsdl',              // namespace
  'urn:WORLDwsdl#findCountry',  // soapaction
  'rpc',                        // style
  'encoded',                    // use
  'Find countries by continent' // documentation
);

function findCountry($continent) {
  error_log("[soap_country.php] findCountry(" . $continent . ")");
  global $server;
  
  if (strlen($continent) < 4) {
    return $server->fault("Invalid continent.");
  }
  
  return dao_country_find($continent);
}

$HTTP_RAW_POST_DATA = isset($HTTP_RAW_POST_DATA) ? $HTTP_RAW_POST_DATA : '';
$server->service($HTTP_RAW_POST_DATA);
?>
