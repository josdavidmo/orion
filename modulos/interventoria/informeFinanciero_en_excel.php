<?php
header('Content-type: application/vnd.ms-excel');
header("Content-Disposition: attachment; filename=Informes_Financieros.xls");
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
header("Pragma: public");

error_reporting(E_ALL - E_NOTICE - E_DEPRECATED - E_WARNING);

require('../../clases/datos/CInformeFinancieroData.php');
require_once '../../clases/aplicacion/CDataLog.php';
require('../../clases/datos/CData.php');
require('../../clases/interfaz/CHtml.php');
// Incluimos el archivo de configuracion
include('../../config/conf.php');
include('../../config/constantes.php');
require('../../lang/es-co/informeFinanciero-es.php');

$html = new CHtml('');
$operador = OPERADOR_DEFECTO;
$ifiData = new CInformeFinancieroData($db);
//Variables
$vigencia = $_REQUEST['txt_vigencia'];
$descripcion = $_REQUEST['txt_descripcion'];
$estado = $_REQUEST['sel_estado'];
//-------------------------------criterios---------------------------
$criterio = "";
if (isset($vigencia) && $vigencia != '') {
    if ($criterio == "") {
        $criterio = "  ifi.ifi_vigencia = '" . $vigencia . "'";
    } else {
        $criterio .= " and ifi.ifi_vigencia = '" . $vigencia . "'";
    }
}
if (isset($descripcion) && $descripcion != '') {
    if ($criterio == "") {
        $criterio = " ifi.ifi_descripcion LIKE '" . $descripcion . "%'";
    } else {
        $criterio .= " and ifi.ifi_descripcion LIKE '" . $descripcion . "%'";
    }
}
if (isset($estado) && $estado != '-1') {
    if ($criterio == "") {
        $criterio = "  ifi.ife_id = '" . $estado . "'";
    } else {
        $criterio .= " and ifi.ife_id = '" . $estado . "'";
    }
}
if ($criterio == "") {
    $criterio = "1";
}

echo "<table width='80%' border='1' align='center'>";
//encabezado
echo"<tr><th colspan = '15'><center></center></th></tr>";
echo"<tr><th colspan = '15' bgcolor='#CCCCCC'><center>" . $html->traducirTildes(INFORME_FINANCIERO_REPORTE_EXCEL) . "</center></th></tr>";

//titulos
echo "<tr>";
echo "<th>" . $html->traducirTildes('No') . "</th>
        <th>" . $html->traducirTildes(INFORME_FINANCIERO_NUMERO_PAGO) . "</th>
        <th>" . $html->traducirTildes(INFORME_FINANCIERO_VIGENCIA) . "</th>
        <th>" . $html->traducirTildes(INFORME_FINANCIERO_NUMERO_FACTURA) . "</th>
        <th>" . $html->traducirTildes(INFORME_FINANCIERO_FECHA_FACTURA) . "</th>
        <th>" . $html->traducirTildes(INFORME_FINANCIERO_NUMERO_RADICADO_MINISTERIO) . "</th>
        <th>" . $html->traducirTildes(INFORME_FINANCIERO_DOCUMENTO_SOPORTE) . "</th>
        <th>" . $html->traducirTildes(INFORME_FINANCIERO_DESCRIPCION) . "</th>
        <th>" . $html->traducirTildes(INFORME_FINANCIERO_VALOR_FACTURA) . "</th>
        <th>" . $html->traducirTildes(INFORME_FINANCIERO_AMORTIZACION) . "</th>
        <th>" . $html->traducirTildes(INFORME_FINANCIERO_SALDOP_CONTRATO) . "</th>
        <th>" . $html->traducirTildes(INFORME_FINANCIERO_SALDOP_AMORTIZACION) . "</th>
        <th>" . $html->traducirTildes(INFORME_FINANCIERO_ESTADO) . "</th>
        <th>" . $html->traducirTildes(INFORME_FINANCIERO_FECHA_PAGO) . "</th>
        <th>" . $html->traducirTildes(INFORME_FINANCIERO_OBSERVACIONES) .  "</th>";
echo "</tr>";


//datos 
$contador = 0;
$informeFinanciero = $ifiData->getInformeFinanciero($criterio);
$cont = count($informeFinanciero);

while ($contador < $cont) {
    $temp = $contador + 1;
    echo "<tr>";
    echo "<td>" . $html->traducirTildes( $temp ). "</td>
        <td>" . $html->traducirTildes( $informeFinanciero[$contador]['numero_pago'] ). "</td>
        <td>" . $html->traducirTildes( $informeFinanciero[$contador]['vigencia'] ). "</td>
        <td>" . $html->traducirTildes( $informeFinanciero[$contador]['numero_factura'] ). "</td>
        <td>" . $html->traducirTildes( $informeFinanciero[$contador]['fecha_factura'] ). "</td>
        <td>" . $html->traducirTildes( $informeFinanciero[$contador]['numero_radicado_ministerio'] ). "</td>
        <td>" . $html->traducirTildes( $informeFinanciero[$contador]['documento_soporte'] ). "</td>
        <td>" . $html->traducirTildes( $informeFinanciero[$contador]['descripcion'] ). "</td>
        <td>" . $html->traducirTildes( $informeFinanciero[$contador]['valor_factura'] ). "</td>
        <td>" . $html->traducirTildes( $informeFinanciero[$contador]['amortizacion'] ). "</td>
        <td>" . $html->traducirTildes( $informeFinanciero[$contador]['saldop_contrato'] ). "</td>
        <td>" . $html->traducirTildes( $informeFinanciero[$contador]['saldop_amortizacion'] ). "</td>
        <td>" . $html->traducirTildes( $informeFinanciero[$contador]['estado'] ). "</td>
        <td>" . $html->traducirTildes( $informeFinanciero[$contador]['fecha_pago'] ). "</td>
        <td>" . $html->traducirTildes( $informeFinanciero[$contador]['observaciones'] ). "</td>";
    echo "</tr>";
    $contador++;
}
echo "</table>";
?>