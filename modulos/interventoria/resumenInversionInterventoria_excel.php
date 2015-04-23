<?php

/**

 */
header('Content-type: application/vnd.ms-excel');
header("Content-Disposition: attachment; filename=Resumen_FI_RRI.xls");
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
header("Pragma: public");

error_reporting(E_ALL - E_NOTICE - E_DEPRECATED - E_WARNING);

require('../../clases/datos/CActividadPIAData.php');
require('../../clases/datos/CRegistroInversionData.php');
require_once '../../clases/aplicacion/CDataLog.php';
require('../../clases/datos/CData.php');
require('../../clases/interfaz/CHtml.php');
// Incluimos el archivo de configuracion
include('../../config/conf.php');
include('../../config/constantes.php');
require('../../lang/es-co/resumenRegistroInversio-es.php');

$html = new CHtml('');
$docData = new CRegistroInversionData($db);
$docDataAct = new CActividadPIAData($db);

$actividades = $docDataAct->getActividadPIA('1');
$formas = null;
$num = count($actividades);
$cont = 0;

$dt = null;
while ($cont < $num) {

    echo "<table width='80%' border='1' align='center'>";
    //encabezado
    echo"<tr><th colspan = '4'><center></center></th></tr>";
    echo"<tr><th colspan = '4' bgcolor='#CCCCCC'><center>" . $html->traducirTildes(TITULO_TABLA_RESUMEN_REGISTRO_INVERSION_PARCIAL . $actividades[$cont]['descripcion'] .
                    ". " . CAMPO_MONTO . " " . number_format($actividades[$cont]['monto'], 0, ',', '.')) . "</center></th></tr>";
    //titulos
    echo "<tr>";
    $montoTotal=  $actividades[$cont]['monto'];
    echo "<th>" . $html->traducirTildes(CAMPO_NOMBRE_PROVEEDOR) . "</th>
      <th>" . $html->traducirTildes(CAMPO_NUMERO_DOCUMENTO) . "</th>
      <th>" . $html->traducirTildes(CAMPO_FECHA_INVERSION) . "</th>
      <th>" . $html->traducirTildes(CAMPO_VALOR_INVERSION) . "</th>";
    echo "</tr>";
    //datos 
    $r = $docData->getRegistroInversion(" rin.act_id = " . $actividades[$cont]['id_element']);
    $contador = 0;
    $sum = count($r);
    $registros = null;
    $totalEjecutado=null;
    while ($contador < $sum) {
        $valor = (int)str_replace('.', '', $r[$contador]['valor'] );
        echo "<tr>";
        echo "<td>" . $html->traducirTildes( $html->traducirTildes($r[$contador]['proveedor']) ). "</td>	
        <td>" . $html->traducirTildes( $r[$contador]['numero_documento'] ). "</td> 	
        <td>" . $html->traducirTildes( $r[$contador]['fecha'] ). "</td> 	
        <td>" . $html->traducirTildes( $valor). "</td> ";
        echo "</tr>";
        $totalEjecutado+=$valor;
        $contador++;
    }
    echo "<tr>";
        echo "<td></td>	
        <td></td> 	
        <td> Ejecutado </td> 	
        <td>" . $html->traducirTildes( $totalEjecutado ). "</td> ";
    echo "</tr>";
    echo "<tr>";
        echo "<td></td>	
        <td></td> 	
        <td> Por Ejecutar  </td> 	
        <td>" . $html->traducirTildes( ($montoTotal - $totalEjecutado) ). "</td> ";
    echo "</tr>";
    $cont++;
    echo "</table>";
}
?>