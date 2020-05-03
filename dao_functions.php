<?php

function dao_country_find($continent) {
  global $conn;
  
  $result = array();
  
  $sql = "SELECT code,name,continent,region,population FROM country WHERE continent='" . str_replace("'", "''", $continent) . "' ORDER BY name";
  $rs  = $conn->query($sql);
  
  while($row = $rs->fetch_assoc()) {
    $record = array(
      "code" => $row["code"],
      "name" => $row["name"],
      "continent" => $row["continent"],
      "region" => $row["region"],
      "population" => $row["population"]
    );
    array_push($result, $record);
  }
  
  return $result;
}

?>