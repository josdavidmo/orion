<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

header('Content-type: application/vnd.ms-excel');
header("Content-Disposition: attachment; filename=Reporte Encuestas Individuos.xls");

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
$planData = new CPlaneacionData($db);
$operador = OPERADOR_DEFECTO;

$pla_id = $_REQUEST['hdd_id_element'];

//Obtenemos las encuestas
$tipoEncuesta = 1;
$ejecuciones_tb = $ejeData->getEncuestasByTipoEncuestas($tipoEncuesta);
$numero_preguntas=$ejeData->cantidadPreByEncuestaId($ejecuciones_tb[0]['id_element']);
//Primera tabla
echo "<table width='80%' border='1' align='center'>";
//encabezado
echo"<tr><th colspan = '".$numero_preguntas."'><center></center></th></tr>";
echo"<tr><th colspan = '".$numero_preguntas."' bgcolor='#CCCCCC'><center>" . $html->traducirTildes('Reporte Encuestas Individuos') . "</center></th></tr>";

//titulos
echo "<tr>";
$secciones = $ejeData->getSecciones($tipoEncuesta);
foreach ($secciones as $s) {
    $preguntas_base = $ejeData->getPreguntasBaseBySeccion($s['id']);
    foreach ($preguntas_base as $pb) {
        echo "<th>" . $html->traducirTildes($pb['nombre'] . " " . $pb['texto'] . ' ' . $pb['descripcion']) . "</th>";
    }
}
echo "</tr>";
//datos 
//por cada encuesta
foreach ($ejecuciones_tb as $enc) {
    $enc_id = $enc['id_element'];
    if ($ejeData->getEncuestaEstado($enc_id) == 1) {
        echo "<tr>";
        foreach ($secciones as $sa) {
            $preguntas_base = $ejeData->getPreguntasBaseBySeccion($sa['id']);
            foreach ($preguntas_base as $pb) {
                switch ($pb['tipo']) {
                    case 1:
                        $checked = '';
                        if ($ejeData->getRespuestaPreguntaDeEncuesta($pb['id'], $enc_id) == '1') {
                            echo "<td>" . $html->traducirTildes('Si') . "</td>";
                        } else {
                            echo "<td>" . ('') . "</td>";
                        }
                        break;
                    case 2:
                        $respuesta = $ejeData->getOpcionesPreguntas($pb['id'] . ' and ipo_id= ' . $ejeData->getRespuestaPreguntaDeEncuesta($pb['id'], $enc_id));
                        echo "<td>" . $html->traducirTildes($respuesta[0]['nombre']) . "</td>";
                        break;
                    case 3:
                        $respuesta = '';
                        $respuestaMxM = explode('/', $ejeData->getRespuestaPreguntaDeEncuesta($pb['id'], $enc_id));
                        for ($i = 0; $i < count($respuestaMxM); $i++) {
                            if ($respuestaMxM[$i]) {
                                $temp = $ejeData->getOpcionesPreguntas($pb['id'] . ' and ipo_id= ' . $respuestaMxM[$i]);
                                $respuesta = $respuesta . '-' . $temp[0]['nombre'];
                            }
                        }
                        echo "<td>" . $html->traducirTildes($respuesta) . "</td>";
                        break;
                    case 4:
                    case 5:
                    case 7:
                    case 8:
                        echo "<td>" . $html->traducirTildes($ejeData->getRespuestaPreguntaDeEncuesta($pb['id'], $enc_id)) . "</td>";
                        break;
                    case 6:
                        echo "<td>" . ('') . "</td>";
                        break;
                    default :
                        echo "<td>" . ('') . "</td>";
                        break;
                }
            }
        }
        echo "</tr>";
    }
}
?>
