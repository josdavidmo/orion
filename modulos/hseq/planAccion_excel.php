<?php

header('Content-type: application/vnd.ms-excel;charset=utf-8');
header("Content-Disposition: attachment; filename=Reporte Planes Accion.xls");
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
header("Pragma: public");

error_reporting(E_ALL - E_NOTICE - E_DEPRECATED - E_WARNING);

require('../../clases/datos/CPlanAccionData.php');
require_once '../../clases/aplicacion/CDataLog.php';
require('../../clases/datos/CData.php');
require('../../clases/interfaz/CHtml.php');
// Incluimos el archivo de configuracion
include('../../config/conf.php');
include('../../config/constantes.php');
require('../../lang/es-co/planAccion-es.php');

$html = new CHtml('');
$daoPlanAccion = new CPlanAccionData($db);

echo "<table width='80%' border='1' align='center'>";
//encabezado
echo"<tr><th colspan = '9'><center></center></th></tr>";
echo"<tr><th colspan = '9' bgcolor='#CCCCCC'><center>" . $html->traducirTildes(REPORTE_PLAN_ACCION) . "</center></th></tr>";

//titulos
echo "<tr>";
echo "<th>" . $html->traducirTildes('No') . "</th>
        <th>" . $html->traducirTildes(PLAN_ACCION_DESCRIPCION) . "</th>
        <th>" . $html->traducirTildes(PLAN_ACCION_FECHA_INICIO) . "</th>
        <th>" . $html->traducirTildes(PLAN_ACCION_CONSECUTIVO) . "</th>
        <th>" . $html->traducirTildes(PLAN_ACCION_FECHA_LIMITE) . "</th>
        <th>" . $html->traducirTildes(PLAN_ACCION_SOPORTE) . "</th>
        <th>" . $html->traducirTildes(PLAN_ACCION_USUARIO_RESPONSABLE) . "</th>
        <th>" . $html->traducirTildes(PLAN_ACCION_FUENTE) . "</th>
        <th>" . $html->traducirTildes(ACTIVIDAD_PLAN_ACCION_ESTADO) . "</th>";
echo "</tr>";
//datos 
$contador = 0;
$condicion = "1";
$periodo = $_REQUEST['periodo'];
if ($periodo != "") {
    $condicion .= " AND DATE_FORMAT(fechaInicio, '%Y-%m') = '" . $periodo . "'";
}
$idFuente = $_REQUEST['idFuente'];
if ($idFuente != "-1" && $idFuente != "") {
    $condicion .= " AND idFuente = " . $idFuente;
}
$estado = $_REQUEST['estado'];
if ($estado != "-1" && $estado != "") {
    switch ($estado) {
        case "1":
            $condicion .= " AND (SELECT sum(isnull(fechaCumplimiento))/count(idActividadPlanAccion) "
                    . "FROM actividad_plan_accion "
                    . "WHERE actividad_plan_accion.idPlanAccion = planes_accion.idPlanAccion) = 0";
            break;

        case "2":
            $condicion .= " AND fechaLimite < NOW()";
            break;

        case "3":
            $condicion .= " AND fechaLimite > NOW() AND "
                    . "isnull((SELECT sum(isnull(fechaCumplimiento))/count(idActividadPlanAccion) "
                    . "FROM actividad_plan_accion "
                    . "WHERE actividad_plan_accion.idPlanAccion = planes_accion.idPlanAccion))";
            break;

        default:
            break;
    }
}
$planesAccion = $daoPlanAccion->getPlanesAccion($condicion);
$cont = count($planesAccion);

while ($contador < $cont) {
    $pos = strpos($planesAccion[$contador]['imagen'], ">");
    $estado = substr($planesAccion[$contador]['imagen'], $pos + 1);
    $temp = $contador + 1;
    echo "<tr>";
    echo "<td>" . $html->traducirTildes( $temp ). "</td>
        <td>" . $html->traducirTildes( $html->traducirTildes($planesAccion[$contador]['descripcion']) ). "</td>
        <td>" . $html->traducirTildes( $planesAccion[$contador]['fechaInicio'] ). "</td>
        <td>" . $html->traducirTildes( $planesAccion[$contador]['consecutivo'] ). "</td>
        <td>" . $html->traducirTildes( $planesAccion[$contador]['fechaLimite'] ). "</td>
        <td>" . $html->traducirTildes( $planesAccion[$contador]['soporte'] ). "</td>
        <td>" . $html->traducirTildes( $planesAccion[$contador]['usuario'] ). "</td>
        <td>" . $html->traducirTildes( $planesAccion[$contador]['descripcionFuentePlanAccion'] ). "</td>
        <td>" . $html->traducirTildes( $estado ) . "</td>";
    echo "</tr>";
    $contador++;
}
echo "</table>";
?>
