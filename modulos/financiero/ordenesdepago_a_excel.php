<?php

header('Content-type: application/vnd.ms-excel');
header("Content-Disposition: attachment; filename=ordenesdepago.xls");

error_reporting(E_ALL - E_NOTICE - E_DEPRECATED - E_WARNING);
require('../../clases/datos/CPlaneacionData.php');
require_once '../../clases/aplicacion/CDataLog.php';
require('../../clases/datos/COrdenesdepagoData.php');
require('../../clases/datos/CCompromisoResponsableData.php');
require('../../clases/datos/CData.php');
require('../../clases/interfaz/CHtml.php');
include('../../config/conf.php');
include('../../config/constantes.php');
require('../../lang/es-co/ordenesdepago-es.php');
require('../../lang/es-co/es-co.php');

$html = new CHtml('');


$docData = new COrdenesdepagoData($db);


$tipopago = $_REQUEST['sel_tipo_pago'];
$tipoactividad = $_REQUEST['sel_tipo_actividad'];
$actividad = $_REQUEST['sel_actividad'];
$familia = $_REQUEST['sel_familia'];
$proveedor = $_REQUEST['sel_proveedor'];
$tipo = $_REQUEST['sel_tipo'];
$CheckAprovado = $_REQUEST['check_estados_1'];
$CheckPagado = $_REQUEST['check_estados_4'];
$CheckPendiente = $_REQUEST['check_estados_3'];
$CheckRechazado = $_REQUEST['check_estados_2'];

if (isset($CheckAprovado) || isset($CheckPagado) || isset($CheckPendiente) || isset($CheckRechazado)) {

    $criterio = "";
    if (isset($tipoactividad) && $tipoactividad > 0 && $tipoactividad != -1) {
        $criterio = "(at.Id_Tipo=" . $tipoactividad . ")";
    }
    if (isset($actividad) && $actividad > 0 && $actividad != -1) {
        $criterio = $criterio . "(a.Id_Actividad=" . $actividad . ")";
    }

    if (isset($proveedor) && $proveedor > 0 && $proveedor != -1) {
        $criterio = $criterio . "(p.Id_Prove=" . $proveedor . ")";
    }
    if (isset($moneda) && $moneda > 0 && $moneda != -1) {
        $criterio = $criterio . "(m.Id_Moneda=" . $moneda . ")";
    }

    $criterio = explode(')', $criterio);
    $parametro = "";

    if (count($criterio) == 2) {
        $parametro = $criterio[0] . ") and ";
    } else {
        for ($i = 0; $i < count($criterio) - 1; $i++) {
            $parametro = $parametro . " " . $criterio[$i] . ") and ";
        }
    }

    if (isset($CheckAprovado)) {
        $filtroestado = $filtroestado . "Id_estado_Ordenes=1)";
    }
    if (isset($CheckPagado)) {
        $filtroestado = $filtroestado . "Id_estado_Ordenes=4)";
    }
    if (isset($CheckPendiente)) {
        $filtroestado = $filtroestado . "Id_estado_Ordenes=3)";
    }
    if (isset($CheckRechazado)) {
        $filtroestado = $filtroestado . "Id_estado_Ordenes=2)";
    }
    $filtroestado = explode(')', $filtroestado);
    $parametroestado = "";

    if (count($filtroestado) == 2) {
        $parametroestado = "(" . $filtroestado[0] . ")";
    } else {
        for ($i = 0; $i < count($filtroestado) - 1; $i++) {
            $parametroestado = $parametroestado . " " . $filtroestado[$i] . " or";
        }
        $parametroestado = "(" . substr($parametroestado, 0, count($parametroestado) - 4) . ")";
    }
} else if (!isset($CheckAprovado) && !isset($CheckPagado) && !isset($CheckPendiente) && !isset($CheckRechazado)) {

    $criterio = "";
    if (isset($tipoactividad) && $tipoactividad > 0 && $tipoactividad != -1) {
        $criterio = "(at.Id_Tipo=" . $tipoactividad . ")";
    }
    if (isset($actividad) && $actividad > 0 && $actividad != -1) {
        $criterio = $criterio . "(a.Id_Actividad=" . $actividad . ")";
    }

    if (isset($proveedor) && $proveedor > 0 && $proveedor != -1) {
        $criterio = $criterio . "(p.Id_Prove=" . $proveedor . ")";
    }
    if (isset($moneda) && $moneda > 0 && $moneda != -1) {
        $criterio = $criterio . "(m.Id_Moneda=" . $moneda . ")";
    }

    $criterio = explode(')', $criterio);
    $parametro = "";

    if (count($criterio) == 2) {
        $parametro = $criterio[0] . ")";
    } else {
        for ($i = 0; $i < count($criterio) - 1; $i++) {
            $parametro = $parametro . " " . $criterio[$i] . ") and";
        }
        $parametro = substr($parametro, 0, count($parametro) - 4);
    }
}
$parametroTipoPago = null;
if ($tipopago == 1) {
    $parametroTipoPago = "cobro_proveedor_reintegro IS NULL";
} else if ($tipopago == 2) {
    $parametroTipoPago = "cobro_proveedor_reintegro IS NOT NULL";
} else {
    $parametroTipoPago = "";
}
$criterio_Final = "1";
if ($parametro != "") {
    $criterio_Final = $parametro . $parametroestado;
}
if ($criterio_Final != '' && $parametroTipoPago != '') {
    $criterio_Final = $criterio_Final . " AND ";
}
$criterio_Final = $criterio_Final . $parametroTipoPago;
$contr = "";
if (isset($_REQUEST['sel_contrato']) && $_REQUEST['sel_contrato'] != "-1") {
    $contr = "contrato_idContrato = " . $_REQUEST['sel_contrato'];
    if ($criterio_Final == "") {
        $criterio_Final = $contr;
    } else {
        $criterio_Final .= " AND " . $contr;
    }
}
$ordenes = $docData->obtenerOrdenesdepago($criterio_Final);




//echo "<br>".$sql."<br>";
echo "<table width='80%' border='0' align='center'>";
//encabezado
echo"<tr><th colspan = '16'><center></center></th></tr>";
echo"<tr><th colspan = '16' bgcolor='#CCCCCC'><center>" . $html->traducirTildes(ORDENES_REPORTE_EXCEL) . "</center></th></tr>";


//titulos
echo "<tr>";

echo "
	<th>" . $html->traducirTildes(REINTEGRO) . "</th>
	<th>" . $html->traducirTildes(NUMERO_ORDEN_PAGO) . "</th>
	<th>" . $html->traducirTildes(FECHA_ORDEN) . "</th>
	<th>" . $html->traducirTildes(ESTADO_ORDEN) . "</th>
        <th>" . $html->traducirTildes(TIPO_ACTIVIDAD_ORDEN) . "</th>
        <th>" . $html->traducirTildes(ACTIVIDAD_ORDEN) . "</th>
        <th>" . $html->traducirTildes(NOMBRE_PROVEEDOR_ORDEN_PAGO) . "</th>
        <th>" . $html->traducirTildes(NUMERO_FACTURA) . "</th>
       	<th>" . $html->traducirTildes(MONEDA_ORDEN) . "</th>
        <th>" . $html->traducirTildes(TASA_ORDEN) . "</th>
	<th>" . $html->traducirTildes(VALOR_TOTAL_ORDEN) . "</th>
        <th>" . $html->traducirTildes(FECHA_PAGO_ORDEN) . "</th>
        <th>" . $html->traducirTildes(ARCHIVO_ORDEN) . "</th>
        <th>" . $html->traducirTildes(AMORTIZACION_ORDEN) . "</th>
        <th>" . $html->traducirTildes(OBSERVACIONES_ORDEN) . "</th>";
echo "</tr>";
//datos 
$cont = 0;
$conta = count($ordenes);
while ($cont < $conta) {
    echo "<tr>";
    echo
    "<td>" . $html->traducirTildes($ordenes[$cont]['tipoReintegro']) . "</td>
        <td>" . $html->traducirTildes($ordenes[$cont]['numerorden']) . "</td>
        <td>" . $html->traducirTildes($ordenes[$cont]['fecha']) . "</td>
        <td>" . $html->traducirTildes($ordenes[$cont]['estado']) . "</td>
        <td>" . $html->traducirTildes($ordenes[$cont]['tipoactividad']) . "</td>
        <td>" . $html->traducirTildes($ordenes[$cont]['actividad']) . "</td>
        <td>" . $html->traducirTildes($ordenes[$cont]['proveedor']) . "</td>
        <td>" . $html->traducirTildes($ordenes[$cont]['numerofactura']) . "</td>
        <td>" . $html->traducirTildes($ordenes[$cont]['moneda']) . "</td>
        <td>" . $html->traducirTildes($ordenes[$cont]['tasa']) . "</td>
        <td>" . $html->traducirTildes($ordenes[$cont]['valortotal']) . "</td>
        <td>" . $html->traducirTildes($ordenes[$cont]['fechapago']) . "</td>
        <td>" . $html->traducirTildes($ordenes[$cont]['archivo']) . "</td>
        <td>" . $html->traducirTildes($ordenes[$cont]['amortizacion']) . "</td>
        <td>" . $html->traducirTildes($ordenes[$cont]['observaciones']) . "</td>";
    echo "</tr>";
    $cont++;
}
echo "</table>";
?>	













