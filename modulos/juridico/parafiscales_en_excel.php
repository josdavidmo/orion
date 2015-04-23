<?php

header('Content-type: application/vnd.ms-excel');
header("Content-Disposition: attachment; filename=parafiscales.xls");
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
header("Pragma: public");

error_reporting(E_ALL - E_NOTICE - E_DEPRECATED - E_WARNING);

require('../../clases/datos/CParafiscalesData.php');
require_once '../../clases/aplicacion/CDataLog.php';
require('../../clases/datos/CData.php');
require('../../clases/interfaz/CHtml.php');
// Incluimos el archivo de configuracion
include('../../config/conf.php');
include('../../config/constantes.php');
require('../../lang/es-co/parafiscales-es.php');

$html = new CHtml('');
$daoParafiscales = new CParafiscalesData($db);

echo "<table width='80%' border='1' align='center'>";
//encabezado
echo"<tr><th colspan = '11'><center></center></th></tr>";
echo"<tr><th colspan = '11' bgcolor='#CCCCCC'><center>" . $html->traducirTildes(REPORTE_PARAFISCALES) . "</center></th></tr>";
                         
//titulos
echo "<tr>";
echo "<th>" . $html->traducirTildes('No') . "</th>
        <th>" . $html->traducirTildes(PERIODO_PARAFISCALES) . "</th>
        <th>" . $html->traducirTildes(FECHA_RADICACION_PARAFISCALES) . "</th>
        <th>" . $html->traducirTildes(COMUNICADO_ENTREGA_SOPORTES_PARAFISCALES) . "</th>
        <th>" . $html->traducirTildes(EVALUACION_CONTENIDO_DOCUMENTO) . "</th>
        <th>" . $html->traducirTildes(EVALUACION_REVISOR_FISCAL) . "</th>
        <th>" . $html->traducirTildes(CONCEPTO_FINAL_PARAFISCALES) . "</th>
        <th>" . $html->traducirTildes(USUARIO_PARAFISCALES) . "</th>
        <th>" . $html->traducirTildes(FECHA_COMUNICADO_CONCEPTO_INTERVENTORIA_PARAFISCALES) . "</th>
        <th>" . $html->traducirTildes(COMUNICADO_CONCEPTO_PARAFISCALES) . "</th>
	<th>" . $html->traducirTildes(OBSERVACIONES_PARAFISCALES) . "</th>";
echo "</tr>";
//datos 
$contador = 0;
$periodo = $_REQUEST['txt_periodo'];
$parafiscales = $daoParafiscales->getParafiscales();
if($periodo != null){
    $parafiscales = $daoParafiscales->getParafiscalesByPeriodo($periodo);
}
$cont = count($parafiscales);

while ($contador < $cont) {
    $temp = $contador + 1;
    echo "<tr>";
    echo "<td>" . $html->traducirTildes( $temp ). "</td>
        <td>" . $html->traducirTildes( $parafiscales[$contador]['periodo'] ). "</td>
        <td>" . $html->traducirTildes( $parafiscales[$contador]['fechaRealizacionComunicado'] ). "</td>
        <td>" . $html->traducirTildes( $parafiscales[$contador]['comunicadoEntregaSoportes'] ). "</td>
        <td>" . $html->traducirTildes( $parafiscales[$contador]['evaluacionContenido'] ). "</td>
        <td>" . $html->traducirTildes( $parafiscales[$contador]['evaluacionRevisor'] ). "</td>
        <td>" . $html->traducirTildes( $parafiscales[$contador]['evaluacionFinal'] ). "</td>
        <td>" . $html->traducirTildes( $parafiscales[$contador]['usuario'] ). "</td>
        <td>" . $html->traducirTildes( $parafiscales[$contador]['fechaComunicadoInterventoria'] ). "</td>
        <td>" . $html->traducirTildes( $parafiscales[$contador]['comunicadoConceptoInterventoria'] ). "</td>
        <td>" . $html->traducirTildes( $parafiscales[$contador]['observaciones']) . "</td>";
    echo "</tr>";
    $contador++;
}
echo "</table>";
?>
