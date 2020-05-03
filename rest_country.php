<?php
header('Content-Type: application/json');

error_reporting(E_ERROR);
ini_set('display_errors', 0);

require_once('connection.php');

include 'dao_functions.php';

$continent = null;
if(isset($_GET['c'])) {
  $continent = $_GET['c'];
}
if(isset($_POST['c'])) {
  $continent = $_POST['c'];
}

$result = dao_country_find($continent);

echo json_encode($result);
?>