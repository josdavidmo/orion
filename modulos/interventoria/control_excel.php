<?php

header('Content-type: application/vnd.ms-excel');
header("Content-Disposition: attachment; filename=Reporte Control.xls");
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
header("Pragma: public");

error_reporting(E_ALL - E_NOTICE - E_DEPRECATED - E_WARNING);

require('../../clases/datos/CBasicaRelacionadaData.php');
require('../../clases/datos/CPlaneacionAutocontrolData.php');
require_once '../../clases/aplicacion/CDataLog.php';
require('../../clases/datos/CData.php');
require('../../clases/interfaz/CHtml.php');
// Incluimos el archivo de configuracion
include('../../config/conf.php');
include('../../config/constantes.php');
require('../../lang/es-co/autocontrol-es.php');

$html = new CHtml('');
$operador = OPERADOR_DEFECTO;
$daoControl = new CPlaneacionAutocontrolData($db);

$titulos = array(CONTROL_OBLIGACIONES, 
                 CONTROL_VERIFICACION, 
                 CONTROL_NUMERO_DOCUMENTO_CONTRACTUAL, 
                 CONTROL_RESPONSABLE,
                 CONTROL_CRITERIOS_ACEPTACION,
                 CONTROL_METODOLOGIA,
                 CONTROL_REGISTRO);

$fechaCantidad = $daoControl->getFechaAndCantidad();
$fechaInicio = $fechaCantidad['fechaMinima'];
$periodo = explode("-", $fechaInicio);
$cantidad = (int) $fechaCantidad['meses'];
$ancho = $cantidad*2 + 8;

echo "<table width='80%' border='1' align='center'>";
//encabezado
echo"<tr><th colspan = '$ancho'><center></center></th></tr>";
echo"<tr><th colspan = '$ancho' bgcolor='#CCCCCC'><center>" . $html->traducirTildes(REPORTE_CONTROL) . "</center></th></tr>";

//titulos
echo "<tr>";
echo "<th>" . $html->traducirTildes('No') . "</th>";
foreach ($titulos as $titulo) {
    echo "<th>" . $html->traducirTildes($titulo) . "</th>";
}
for ($i = 0; $i < $cantidad; $i++) {
    $fecha = mktime(0, 0, 0, $periodo[1] + ($i+1), $periodo[2], $periodo[0]);
    echo "<th>" . $html->traducirTildes(date("Y-m", $fecha)) . "</th>";
    echo "<th>" . $html->traducirTildes(ESTADO_CONTROL) . "</th>";
}
echo "</tr>";


//datos 
$controles = $daoControl->getPlaneacionControl();
$cont = count($controles);
$contador = 0;
while ($contador < $cont) {
    $idControl = $controles[$contador]['idPlaneacionControl'];
    $observaciones = $daoControl->getObservacionesByIdControl($idControl);
    echo "<tr>";
    echo "<td>" . ($contador + 1) . "</td> 
        <td>" . $html->traducirTildes($controles[$contador]['obligaciones']) . "</td>
        <td>" . $html->traducirTildes($controles[$contador]['verificacion']) . "</td>
        <td>" . $html->traducirTildes($controles[$contador]['numeroDocumentoContractual']) . "</td>
        <td>" . $html->traducirTildes($controles[$contador]['usuario']) . "</td>
        <td>" . $html->traducirTildes($controles[$contador]['criteriosAceptacion']) . "</td>
        <td>" . $html->traducirTildes($controles[$contador]['metodologia']) . "</td>
        <td>" . $html->traducirTildes($controles[$contador]['registro']) . "</td>";
    foreach ($observaciones as $observacion) {
        echo "<td>". $html->traducirTildes($observacion['descripcion']). "</td>"
           . "<td>".  $html->traducirTildes($observacion['estado']). "</td>";
    }
    echo "</tr>";
    $contador++;
}
echo "</table>";
?>