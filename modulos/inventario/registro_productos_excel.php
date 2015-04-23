<?php

header('Content-type: application/vnd.ms-excel');
header("Content-Disposition: attachment; filename=registroProductos.xls");
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
header("Pragma: public");

error_reporting(E_ALL - E_NOTICE - E_DEPRECATED - E_WARNING);

require('../../clases/datos/CRegistroProductosData.php');
require('../../clases/datos/CBasicaData.php');
require('../../clases/aplicacion/CFamilia.php');
require('../../clases/aplicacion/CBasica.php');
require('../../clases/aplicacion/CProductos.php');
require_once '../../clases/aplicacion/CDataLog.php';
require('../../clases/datos/CData.php');
require('../../clases/interfaz/CHtml.php');
// Incluimos el archivo de configuracion
include('../../config/conf.php');
include('../../config/constantes.php');
require('../../lang/es-co/productos-es.php');

$html = new CHtml('');
$daoRegistroProductos = new CRegistroProductosData($db);

echo "<table width='80%' border='0' align='center'>";
//encabezado
echo"<tr><th colspan = '7'><center></center></th></tr>";
echo"<tr><th colspan = '7' bgcolor='#CCCCCC'><center>" . $html->traducirTildes(TITULO_REGISTRO_PRODUCTOS) . "</center></th></tr>";


//titulos
echo "<tr>";

echo "<th>" . $html->traducirTildes(DESCRIPCION_PRODUCTOS) . "</th>
      <th>" . $html->traducirTildes(VALOR_UNITARIO_PRODUCTOS) . "</th>
      <th>" . $html->traducirTildes(CANTIDAD_PRODUCTOS) . "</th>
      <th>" . $html->traducirTildes(TIPO_REGISTRO_PRODUCTOS) . "</th>
      <th>" . $html->traducirTildes(FAMILIA_PRODUCTOS) . "</th>
      <th>" . $html->traducirTildes(ORDEN_PAGO_PRODUCTOS) . "</th>
      <th>" . $html->traducirTildes(VALOR_TOTAL_PRODUCTOS) . "</th>";
echo "</tr>";
//datos 
$idOrdenPago = $_REQUEST['idOrden'];
$registroProductos = $daoRegistroProductos->getRegistroProductosByOrdenPago($idOrdenPago);
$contador = 0;
$cont = count($registroProductos);

while ($contador < $cont) {
    echo "<tr>";
    echo "<td>" . $html->traducirTildes($registroProductos[$contador]['descripcion']) . "</td>
        <td>" . $html->traducirTildes($registroProductos[$contador]['valorUnitario']) . "</td>
        <td>" . $html->traducirTildes($registroProductos[$contador]['cantidad']) . "</td>
        <td>" . $html->traducirTildes($registroProductos[$contador]['servicio']) . "</td>
        <td>" . $html->traducirTildes($registroProductos[$contador]['descripcionFamilia']) . "</td>
        <td>" . $html->traducirTildes($registroProductos[$contador]['Numero_Orden_Pago']) . "</td>
        <td>" . $html->traducirTildes($registroProductos[$contador]['valorTotal']) . "</td> ";
    echo "</tr>";
    $contador++;
}
echo "</table>";
?>