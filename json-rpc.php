<?php

require 'JsonRPC/Server.php';

use JsonRPC\Server;

require_once('connection.php');

include 'dao_functions.php';

$server = new Server;

$server->register('COUNTRIES.findByContinent', function ($continent) {
  
  return dao_country_find($continent);
  
});

// Return the response to the client
echo $server->execute();