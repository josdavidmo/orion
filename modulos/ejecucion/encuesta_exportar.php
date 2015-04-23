<?php
/**
 * Clase destinada a la exportación de datos referentes a encuestas
 * @version 1.0
 * @since 31/07/2014
 * @author Brian Kings
 */

header("Content-Disposition: attachment; filename=\"prueba.pncav\";");

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
echo $pla_id."\n";
//Planeacion Id
$encuestas = $plaData->getIdsEncuestas($pla_id);
$cont = 0;
//datos
while ($cont < count($encuestas)) {
    $respuestas = $ejeData->getRespuestasByEncuesta($encuestas[$cont]['enc_id']);
    echo $ejeData->getConsecutivoByEncuestaId($encuestas[$cont]['enc_id']) . "\n";
    $contRespuestas = 0;
    while ($contRespuestas < count($respuestas)) {
        echo $respuestas[$contRespuestas]['respuesta'] . "$%$";
        $contRespuestas++;
    }
    echo "\n";
    $cont++;
}
?>