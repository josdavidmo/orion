<?php

header('Content-type: application/vnd.ms-excel');
header("Content-Disposition: attachment; filename=productos.xls");
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
header("Pragma: public");

error_reporting(E_ALL - E_NOTICE - E_DEPRECATED - E_WARNING);

require('../../clases/datos/CRegistroProductosData.php');
require('../../clases/datos/CBasicaData.php');
require('../../clases/aplicacion/CBasica.php');
require('../../clases/aplicacion/CProductos.php');
require('../../clases/aplicacion/CRegistroProductos.php');
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
echo"<tr><th colspan = '6' bgcolor='#CCCCCC'><center>" . $html->traducirTildes(TITULO_PRODUCTOS) . "</center></th></tr>";


//titulos
echo "<tr>";

echo "<th bgcolor='#CCCCCC'>" . $html->traducirTildes(ID_PRODUCTO) . "</th>
      <th bgcolor='#CCCCCC'>" . $html->traducirTildes(SERIAL_PRODUCTOS) . "</th>
      <th bgcolor='#CCCCCC'>" . $html->traducirTildes(DESCRIPCION_REGISTRO_PRODUCTOS) . "</th>
      <th bgcolor='#CCCCCC'>" . $html->traducirTildes(DESCRIPCION_PRODUCTOS) . "</th>
      <th bgcolor='#CCCCCC'>" . $html->traducirTildes(ESTADO_PRODUCTOS) . "</th>
      <th bgcolor='#CCCCCC'>" . $html->traducirTildes(FECHA_RECEPCION_PRODUCTOS) . "</th>";
echo "</tr>";
//datos 
$idRegistroProducto = $_REQUEST['idRegistroProducto'];
$criterio = "rp.idRegistroProducto = $idRegistroProducto";
$productos = $daoRegistroProductos->getProductos($criterio);
$contador = 0;
$cont = count($productos);

while ($contador < $cont) {
    echo "<tr>";
    echo "<td>" . $html->traducirTildes($productos[$contador]['idProducto']) . "</td>
        <td>" . $html->traducirTildes($productos[$contador]['serial']) . "</td>
        <td>" . $html->traducirTildes($productos[$contador]['registroproducto']) . "</td>
        <td>" . $html->traducirTildes($productos[$contador]['descripcion']) . "</td>
        <td>" . $html->traducirTildes($productos[$contador]['estado']) . "</td>
        <td>" . $html->traducirTildes($productos[$contador]['fechaEnvio']) . "</td>";
    echo "</tr>";
    $contador++;
}
echo "</table>";
?>