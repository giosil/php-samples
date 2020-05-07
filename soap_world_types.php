<?php
defComplexType('country', array(
  'code' => 'xsd:string',
  'name' => 'xsd:string',
  'continent' => 'xsd:string',
  'region' => 'xsd:string',
  'population' => 'xsd:integer'
  ), 'all'
);

defArrayType('ArrayOfCountry', 'tns:country', '0', 'unbounded');

function defComplexType($name, $input, $compositor = 'all', $minoccur = null, $maxoccur = null) {
  global $server;
  foreach ($input as $key => $in) {
    if (is_Array($in)) {
      $in['name'] = $key;
      $dati[$key] = $in;
    } else
    if (($minoccur == null) || ($maxoccur == null))
      $dati[$key] = array('name' => $key, 'type' => $in);
    else
      $dati[$key] = array('name' => $key, 'type' => $in, 'minOccurs' => $minoccur, 'maxOccurs' => $maxoccur);
  }
  $server->wsdl->addComplexType(
      $name, 'complexType', 'struct', $compositor, '', $dati
  );
}

function defScalarType($name, $fromType, $valori) {
  global $server;
  $server->wsdl->addSimpleType(
      $name, $fromType, 'simpleType', 'scalar', $valori
  );
}

function defArrayType($name, $type, $min, $max) {
  global $server;
  $server->wsdl->addComplexType(
      str_replace("tns:", "", $name), 
      'complexType', 
      'array', 
      '', 
      'SOAP-ENC:Array', 
      array(), 
      array(array('ref' => 'SOAP-ENC:arrayType', 'wsdl:arrayType' => $type . '[]', 'minOccurs' => $min, 'maxOccurs' => $max)),
      $type
  );
}

function defSequenceType($name, $type, $min, $max) {
  global $server;
  $server->wsdl->addComplexType(
      str_replace("tns:", "", $name), 
      'complexType', 
      'array', 
      'sequence', 
      '', 
      array(str_replace("tns:", "", $type) . '' => array('name' => str_replace("tns:", "", $type) . '', 'type' => $type, 'minOccurs' => $min, 'maxOccurs' => $max)),
      array(), 
      $type
  );
}
?>