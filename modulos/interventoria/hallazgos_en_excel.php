<?php

header('Content-type: application/vnd.ms-excel');
header("Content-Disposition: attachment; filename=Reporte Hallazgos.xls");
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
header("Pragma: public");

error_reporting(E_ALL - E_NOTICE - E_DEPRECATED - E_WARNING);

require('../../clases/datos/CHallazgosPendientesData.php');
require('../../clases/datos/CActividadBitacoraData.php');
require('../../clases/datos/CBitacoraData.php');
require_once '../../clases/aplicacion/CDataLog.php';
require('../../clases/datos/CData.php');
require('../../clases/interfaz/CHtml.php');
// Incluimos el archivo de configuracion
include('../../config/conf.php');
include('../../config/constantes.php');
require('../../lang/es-co/bitacora-es.php');
require('../../lang/es-co/planeacion-es.php');
require('../../lang/es-co/beneficiarios-es.php');

$html = new CHtml('');
$operador = OPERADOR_DEFECTO;
$daoPendientes = new CHallazgosPendientesData($db);

//Variables
$condicion = "1";
if (isset($_REQUEST['sel_region']) &&
        $_REQUEST['sel_region'] != '-1') {
    $condicion .= " AND de.der_id = " . $_REQUEST['sel_region'];
}
if (isset($_REQUEST['sel_departamento']) &&
        $_REQUEST['sel_departamento'] != '-1') {
    $condicion .= " AND mu.dep_id = " . $_REQUEST['sel_departamento'];
}
if (isset($_REQUEST['sel_municipios']) &&
        $_REQUEST['sel_municipios'] != '-1') {
    $condicion .= " AND c.mun_id = " . $_REQUEST['sel_municipios'];
}
if (isset($_REQUEST['sel_centro_poblado']) &&
        $_REQUEST['sel_centro_poblado'] != '-1') {
    $condicion .= " AND c.idCentroPoblado = " . $_REQUEST['sel_centro_poblado'];
}
if (isset($_REQUEST['sel_area']) &&
        $_REQUEST['sel_area'] != '-1') {
    $condicion .= " AND ar.idAreaHallazgo = " . $_REQUEST['sel_area'];
}
if (isset($_REQUEST['tipohallazgo']) &&
        $_REQUEST['tipohallazgo'] != '-1') {
    $condicion .= " AND ti.idTipoHallazgo = " . $_REQUEST['tipohallazgo'];
}
if (isset($_REQUEST['txt_fecha_inicio']) &&
        $_REQUEST['txt_fecha_inicio'] != "") {
    if (isset($_REQUEST['txt_fecha_fin']) &&
            $_REQUEST['txt_fecha_fin'] != "") {
        $condicion .= " AND a.fecha BETWEEN '" . $_REQUEST['txt_fecha_inicio'] . "' AND '" . $_REQUEST['txt_fecha_fin'] . "'";
    } else {
        $condicion .= " AND a.fecha BETWEEN '" . $_REQUEST['txt_fecha_inicio'] . "' AND NOW()";
    }
}

$titulos = array(PLANEACION_REGION, PLANEACION_DEPARTAMENTO,
    PLANEACION_MUNICIPIO, CENTRO_POBLADO_BENEFICIARIO,
    HALLAZGOS_PENDIENTES_BENEFICIARIO,
    TIPO_BENEFICIARIO, ACTIVIDADES_BITACORA_HALLAZGOS_PENDIENTES_USUARIO,
    ACTIVIDADES_BITACORA_HALLAZGOS_PENDIENTES_FECHA,
    HALLAZGOS_PENDIENTES_ACTIVIDAD,
    ACTIVIDADES_BITACORA_HALLAZGOS_PENDIENTES_AREA,
    ACTIVIDADES_BITACORA_HALLAZGOS_PENDIENTES_CLASIFICACION,
    ACTIVIDADES_BITACORA_HALLAZGOS_PENDIENTES_OBSERVACION,
    BITACORA_ACTIVIDAD_GASTO_ARCHIVO, HALLAZGOS_FECHA_RESPUESTA,
    HALLAZGOS_OBSERVACION_RESPUESTA, HALLAZGOS_ARCHIVO_RESPUESTA,
    HALLAZGOS_ESTADO);

echo "<table width='80%' border='1' align='center'>";
//encabezado
echo"<tr><th colspan = '18'><center></center></th></tr>";
echo"<tr><th colspan = '18' bgcolor='#CCCCCC'><center>" . $html->traducirTildes(HALLAZGOS_REPORTE_EXCEL) . "</center></th></tr>";

//titulos
echo "<tr>";
echo "<th>" . $html->traducirTildes('No') . "</th>";
foreach ($titulos as $titulo) {
    echo "<th>" . $html->traducirTildes($titulo) . "</th>";
}
echo "</tr>";


//datos 
$contador = 0;
$hallazgosPendientes = $daoPendientes->getHallazgosPendientesExcel($condicion);
$cont = count($hallazgosPendientes);

while ($contador < $cont) {
    $pos = strpos($hallazgosPendientes[$contador]['estado'], ">");
    $estado = substr($hallazgosPendientes[$contador]['estado'], $pos + 1);
    $temp = $contador + 1;
    echo "<tr>";
    echo "<td>" . $html->traducirTildes($hallazgosPendientes[$contador]['idHallazgosPendientes']) . "</td> 
        <td>" . $html->traducirTildes($hallazgosPendientes[$contador]['region']) . "</td>
        <td>" . $html->traducirTildes($hallazgosPendientes[$contador]['departamento']) . "</td>
        <td>" . $html->traducirTildes($hallazgosPendientes[$contador]['municipio']) . "</td>
        <td>" . $html->traducirTildes($hallazgosPendientes[$contador]['centroPoblado']) . "</td>
        <td>" . $html->traducirTildes($hallazgosPendientes[$contador]['beneficiario']) . "</td>
        <td>" . $html->traducirTildes($hallazgosPendientes[$contador]['tipoBeneficiario']) . "</td>
        <td>" . $html->traducirTildes($hallazgosPendientes[$contador]['usuario']) . "</td>
        <td>" . $html->traducirTildes($hallazgosPendientes[$contador]['fechaInicio']) . "</td>
        <td>" . $html->traducirTildes($hallazgosPendientes[$contador]['actividad']) . "</td>
        <td>" . $html->traducirTildes($hallazgosPendientes[$contador]['area']) . "</td>
        <td>" . $html->traducirTildes($hallazgosPendientes[$contador]['clasificacion']) . "</td>
        <td>" . $html->traducirTildes($hallazgosPendientes[$contador]['observacion']) . "</td>
        <td>" . $html->traducirTildes($hallazgosPendientes[$contador]['archivo']) . "</td>
        <td>" . $html->traducirTildes($hallazgosPendientes[$contador]['fechaRespuesta']) . "</td>
        <td>" . $html->traducirTildes($hallazgosPendientes[$contador]['observacionRespuesta']) . "</td>
        <td>" . $html->traducirTildes($hallazgosPendientes[$contador]['archivoRespuesta']) . "</td>
        <td>" . $html->traducirTildes($estado) . "</td>";
    echo "</tr>";
    $contador++;
}
echo "</table>";
?>