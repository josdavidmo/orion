<?php

header('Content-type: application/vnd.ms-excel');
header("Content-Disposition: attachment; filename=Informe_Desembolsos.xls");

error_reporting(E_ALL - E_NOTICE - E_DEPRECATED - E_WARNING);
require('../../clases/datos/CPlaneacionData.php');
require_once '../../clases/aplicacion/CDataLog.php';
require('../../clases/datos/CDesembolsoData.php');
require('../../clases/datos/CData.php');
require('../../clases/interfaz/CHtml.php');
include('../../config/conf.php');
include('../../config/constantes.php');
require('../../lang/es-co/desembolso-es.php');

$html = new CHtml('');
$docData = new CDesembolsoData($db);
$numero = $_REQUEST['txt_numero'];
$estado = $_REQUEST['sel_estado'];
$criterio = " des_id LIKE '$numero%' ";
$desembolsos = $docData->getDesembolsoFormat($criterio, 'des_id');

echo "<table width='80%' border='0' align='center'>";
//encabezado
echo"<tr><th colspan = '8'><center></center></th></tr>";
echo"<tr><th colspan = '8' bgcolor='#CCCCCC'><center>" . $html->traducirTildes(TITULO_DESEMBOLSOS) . "</center></th></tr>";

//titulos
echo "<tr>";
echo "<th>" . $html->traducirTildes(CAMPO_NUMERO_DESEMBOLSO) . "</th>
        <th>" . $html->traducirTildes(CAMPO_FECHA) . "</th>
        <th>" . $html->traducirTildes(CAMPO_PORCENTAJE_DESEMBOLSO) . "</th>
        <th>" . $html->traducirTildes(CAMPO_APROBADO) . "</th>
        <th>" . $html->traducirTildes(CAMPO_PORCENTAJE_AMORTIZACION) . "</th>
        <th>" . $html->traducirTildes(CAMPO_AMORTIZACION) . "</th>
        <th>" . $html->traducirTildes(CAMPO_VALOR_NETO) . "</th>
        <th>" . $html->traducirTildes(CAMPO_FECHA_CUMPLIMIENTO) . "</th>
        <th>" . $html->traducirTildes(CAMPO_FECHA_TRAMITE) . "</th>
        <th>" . $html->traducirTildes(CAMPO_FECHA_LIMITE) . "</th>
        <th>" . $html->traducirTildes(CAMPO_FECHA_EFECTIVA) . "</th>
        <th>" . $html->traducirTildes(CAMPO_DESEMBOLSO) . "</th>
        <th>" . $html->traducirTildes(CAMPO_OBSERVACIONES) . "</th>
        <th>" . $html->traducirTildes(CAMPO_ESTADO) . "</th>";
echo "</tr>";



//datos 
$contador = 0;
$cont = count($desembolsos);
while ($contador < $cont) {
    $temp = '';
    echo "<tr>";
    $temp = "<td>".$html->traducirTildes($desembolsos[$contador]['id'])."</td>" .
            "<td>" . $html->traducirTildes($desembolsos[$contador]['fecha']) . "</td>" .
            "<td>" . $html->traducirTildes($desembolsos[$contador]['porcentaje']) . "</td>" .
            "<td>" . $html->traducirTildes($desembolsos[$contador]['aprobado']) . "</td>" .
            "<td>" . $html->traducirTildes($desembolsos[$contador]['porcentaje_amortizacion']) . "</td>" .
            "<td>" . $html->traducirTildes($desembolsos[$contador]['amortizacion']) . "</td>" .
            "<td>" . $html->traducirTildes($desembolsos[$contador]['neto']) . "</td>" .
            "<td>" . $html->traducirTildes($desembolsos[$contador]['fecha_cumplimiento']) . "</td>" .
            "<td>" . $html->traducirTildes($desembolsos[$contador]['fecha_tramite']) . "</td>" .
            "<td>" . $html->traducirTildes($desembolsos[$contador]['fecha_limite']) . "</td>" .
            "<td>" . $html->traducirTildes($desembolsos[$contador]['fecha_efectiva']) . "</td>" .
            "<td>" . $html->traducirTildes($desembolsos[$contador]['efectuado']) . "</td>" .
            "<td>" . $html->traducirTildes($desembolsos[$contador]['observaciones']) . "</td>" .
            "<td>" . $html->traducirTildes($desembolsos[$contador]['estado']) . "</td>";
    echo $temp;
    echo "</tr>";
    $contador++;
}
echo "</table>";
?>