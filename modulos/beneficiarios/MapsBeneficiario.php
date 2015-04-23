<?php

function traducirTildes($t) {
    $t = str_replace('á', '&aacute;', $t);
    $t = str_replace('é', '&eacute;', $t);
    $t = str_replace('í', '&iacute;', $t);
    $t = str_replace('ó', '&oacute;', $t);
    $t = str_replace('ú', '&uacute;', $t);
    $t = str_replace('ñ', '&ntilde;', $t);
    $t = str_replace('Á', '&Aacute;', $t);
    $t = str_replace('É', '&Eacute;', $t);
    $t = str_replace('Í', '&Iacute;', $t);
    $t = str_replace('Ó', '&Oacute;', $t);
    $t = str_replace('Ú', '&Uacute;', $t);
    $t = str_replace('Ñ', '&Ntilde;', $t);
    return $t;
}

$username = 'pncav';
$password = 'pnc4vpr0ducc10n';
$database = 'pncav2';

$dom = new DOMDocument("1.0", "ISO8859-5");
$node = $dom->createElement("markers");
$parnode = $dom->appendChild($node);

// Opens a connection to a MySQL server

$connection = mysql_connect('equipo04', $username, $password);
if (!$connection) {
    die('Not connected : ' . mysql_error());
}

// Set the active MySQL database

$db_selected = mysql_select_db($database, $connection);
if (!$db_selected) {
    die('Can\'t use db : ' . mysql_error());
}

$query = "";
$idBeneficiario = $_REQUEST['idBeneficiario'];
if ($idBeneficiario != "") {
    $query = "SELECT CONCAT(b.nombre,' (',t.descripcionTipoBeneficiario,')') as nombre,b.idTipoBeneficiario,"
            . "CONCAT(re.der_nombre,' - ',de.dep_nombre,' - ',mu.mun_nombre) as direccion,"
            . "longitudGrados,longitudMinutos,longitudSegundos,"
            . "latitudGrados,latitudMinutos,latitudSegundos,W,S "
            . "FROM beneficiario b "
            . "INNER JOIN tipobeneficiario t ON t.idTipoBeneficiario = b.idTipoBeneficiario "
            . "INNER JOIN centropoblado c ON c.idCentroPoblado = b.idCentroPoblado "
            . "INNER JOIN municipio mu ON mu.mun_id = c.mun_id "
            . "INNER JOIN departamento de ON de.dep_id = mu.dep_id "
            . "INNER JOIN departamento_region re ON re.der_id = de.der_id "
            . "WHERE b.idBeneficiario = '" . $idBeneficiario . "'";
} else {
    $tipo = $_REQUEST['tipo'];
    if ($tipo == 'true') {
        $query = "SELECT CONCAT(b.nombre,' (',t.descripcionTipoBeneficiario,')') as nombre,b.idTipoBeneficiario,"
                . "CONCAT(re.der_nombre,' - ',de.dep_nombre,' - ',mu.mun_nombre) as direccion,"
                . "longitudGrados,longitudMinutos,longitudSegundos,"
                . "latitudGrados,latitudMinutos,latitudSegundos,W,S "
                . "FROM beneficiario b "
                . "INNER JOIN tipobeneficiario t ON t.idTipoBeneficiario = b.idTipoBeneficiario "
                . "INNER JOIN centropoblado c ON c.idCentroPoblado = b.idCentroPoblado "
                . "INNER JOIN municipio mu ON mu.mun_id = c.mun_id "
                . "INNER JOIN departamento de ON de.dep_id = mu.dep_id "
                . "INNER JOIN departamento_region re ON re.der_id = de.der_id "
                . "WHERE (b.idTipoBeneficiario != 5 AND b.idTipoBeneficiario != 6)";
    } else {
        $query = "SELECT CONCAT(b.nombre,' (',t.descripcionTipoBeneficiario,')') as nombre,b.idTipoBeneficiario,"
                . "CONCAT(re.der_nombre,' - ',de.dep_nombre,' - ',mu.mun_nombre) as direccion,"
                . "longitudGrados,longitudMinutos,longitudSegundos,"
                . "latitudGrados,latitudMinutos,latitudSegundos,W,S "
                . "FROM beneficiario b "
                . "INNER JOIN tipobeneficiario t ON t.idTipoBeneficiario = b.idTipoBeneficiario "
                . "INNER JOIN centropoblado c ON c.idCentroPoblado = b.idCentroPoblado "
                . "INNER JOIN municipio mu ON mu.mun_id = c.mun_id "
                . "INNER JOIN departamento de ON de.dep_id = mu.dep_id "
                . "INNER JOIN departamento_region re ON re.der_id = de.der_id "
                . "WHERE (b.idTipoBeneficiario = 5 OR b.idTipoBeneficiario = 6)";
    }
}

$result = mysql_query($query);
if (!$result) {
    die('Invalid query: ' . mysql_error());
}

header("Content-type: text/xml");

// Iterate through the rows, adding XML nodes for each

while ($row = mysql_fetch_assoc($result)) {
    // ADD TO XML DOCUMENT NODE
    $node = $dom->createElement("marker");
    $newnode = $parnode->appendChild($node);
    $nombre = utf8_encode(traducirTildes($row['nombre']));
    $newnode->setAttribute("name", $nombre);
    $direccion = utf8_encode(traducirTildes($row['direccion']));
    $newnode->setAttribute("address", $direccion);
    $longitud = $row['longitudGrados'] + $row['longitudMinutos'] / 60 + $row['longitudSegundos'] / 3600;
    if ($row['W'] == "1") {
        $longitud = $longitud * -1;
    }
    $newnode->setAttribute("lng", $longitud);
    $latitud = $row['latitudGrados'] + $row['latitudMinutos'] / 60 + $row['latitudSegundos'] / 3600;
    if ($row['S'] == "1") {
        $latitud = $latitud * -1;
    }
    $newnode->setAttribute("lat", $latitud);
    $newnode->setAttribute("type", $row['idTipoBeneficiario']);
}

echo $dom->saveXML();
?>

