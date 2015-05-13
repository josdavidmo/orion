<?php

$username = 'root';
$password = '';
$database = 'pncav2';
$host = 'localhost';

// Opens a connection to a MySQL server
$connection = mysql_connect($host, $username, $password);
if (!$connection) {
    die('Not connected : ' . mysql_error());
}
// Set the active MySQL database
$db_selected = mysql_select_db($database, $connection);
if (!$db_selected) {
    die('Can\'t use db : ' . mysql_error());
}

$query = "SELECT * FROM generador_encuesta ORDER BY pla_id";
$result = mysql_query($query);
$numero = 1;
$planeacion_anterior = 0;
while ($row = mysql_fetch_assoc($result)) {
    $idEncuesta = $row['enc_id'];
    if ($planeacion_anterior != $row['pla_id']) {
        $numero = 1;
    }
    $nuevoId = $row['pla_id'] . "_" . $numero;
    $query2 = "UPDATE generador_encuesta SET enc_id = '$nuevoId' WHERE enc_id = '$idEncuesta'";
    $result2 = mysql_query($query2);
    $r = 'FALSE';
    if ($result2) {
        $r = 'TRUE';
    }
    echo "$nuevoId $query2 $r " . mysql_error() . "<hr>";
    $planeacion_anterior = $row['pla_id'];
    $numero++;
    $contador++;
}
    

