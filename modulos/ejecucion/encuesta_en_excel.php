<?php

/**
 * Clase destinada a la exportación de datos referentes a encuestas
 * @version 1.0
 * @since 31/07/2014
 * @author Brian Kings
 */
header('Content-type: application/vnd.ms-excel');
header("Content-Disposition: attachment; filename=Reporte_ejecucion.xls");

error_reporting(E_ALL - E_NOTICE - E_DEPRECATED - E_WARNING);
require('../../clases/datos/CPlaneacionData.php');
require('../../clases/datos/CEjecucionData.php');
require_once '../../clases/aplicacion/CDataLog.php';
require('../../clases/datos/CData.php');
require('../../clases/interfaz/CHtml.php');
// Incluimos el archivo de configuracion
include('../../config/conf.php');
include('../../config/constantes.php');
require('../../lang/es-co/planeacion-es.php');
require('../../lang/es-co/ejecucion-es.php');

$html = new CHtml('');
$ejeData = new CEjecucionData($db);
$plaData = new CPlaneacionData($db);
$operador = OPERADOR_DEFECTO;
$pla_id = $_REQUEST['hdd_id_element'];
//Primera tabla
echo "<table width='80%' border='1' align='center'>";
//encabezado
//echo"<tr><th colspan = '0' bgcolor='#CCCCCC'><center>" . $html->traducirTildes(ENCUESTA) . "</center></th></tr>";
//titulos
$encuestas = $plaData->getIdsEncuestas($pla_id);
$cont = 0;
$tipoEncuesta = $ejeData->getTipoEncuesta($encuestas[$cont]['enc_id']);
$secciones = $ejeData->getSecciones($tipoEncuesta);
echo "<tr>";
echo "<th>" . $html->traducirTildes("Consecutivo Encuesta") . "</th>";
foreach ($secciones as $s) {
    $preguntas_base = $ejeData->getPreguntasBaseBySeccion($s['id']);
    foreach ($preguntas_base as $pb) {
        echo "<th>" . $html->traducirTildes("" . $pb['nombre'] . ". " . $pb['texto'] . "") . "</th>";
    }
}
echo "</tr>";
//datos
while ($cont < count($encuestas)) {
    $respuestas = $ejeData->getRespuestasByEncuesta($encuestas[$cont]['enc_id']);
    echo "<tr>";
    echo "<td>" . $html->traducirTildes( $ejeData->getConsecutivoByEncuestaId($encuestas[$cont]['enc_id'])) . "</td>";
    $contRespuestas = 0;
    while ($contRespuestas < count($respuestas)) {
        echo "<td>" . $html->traducirTildes( $respuestas[$contRespuestas]['respuesta'] ). "</td>";
        $contRespuestas++;
    }
    echo "</tr>";
    $cont++;
}
echo "</table>";
