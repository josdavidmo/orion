<?php

header('Content-type: application/vnd.ms-excel');
header("Content-Disposition: attachment; filename=Reporte Autocontrol.xls");
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

$titulos = array(AUTOCONTROL_OBJETIVOS, 
                 AUTOCONTROL_ACTIVIDADES, 
                 AUTOCONTROL_RESPONSABLE,
                 AUTOCONTROL_RESPONSABLE_PNC,
                 AUTOCONTROL_FUENTE_DATOS,
                 AUTOCONTROL_REGISTRO);

$fechaCantidad = $daoControl->getFechaAndCantidad(FALSE);
$fechaInicio = $fechaCantidad['fechaMinima'];
$periodo = explode("-", $fechaInicio);
$cantidad = (int) $fechaCantidad['meses'];
$ancho = $cantidad*2 + 9;

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
$autocontroles = $daoControl->getPlaneacionAutocontrol();
$cont = count($autocontroles);
$contador = 0;
while ($contador < $cont) {
    $idActividad = $autocontroles[$contador]['idActividades'];
    $observaciones = $daoControl->getObservacionesByIdAutocontrol($idActividad);
    echo "<tr>";
    echo "<td>" . ($contador + 1) . "</td> 
        <td>" . $html->traducirTildes($autocontroles[$contador]['objetivo']) . "</td>
        <td>" . $html->traducirTildes($autocontroles[$contador]['actividad']) . "</td>
        <td>" . $html->traducirTildes($autocontroles[$contador]['responsable']) . "</td>
        <td>" . $html->traducirTildes($autocontroles[$contador]['responsablePNC']) . "</td>
        <td>" . $html->traducirTildes($autocontroles[$contador]['fuentedatos']) . "</td>
        <td>" . $html->traducirTildes($autocontroles[$contador]['registro']) . "</td>";
    foreach ($observaciones as $observacion) {
        echo "<td>". $html->traducirTildes($observacion['descripcion']). "</td>"
           . "<td>".  $html->traducirTildes($observacion['estado']). "</td>";
    }
    echo "</tr>";
    $contador++;
}
echo "</table>";
?>