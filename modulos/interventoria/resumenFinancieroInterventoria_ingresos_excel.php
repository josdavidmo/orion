<?php

/**

 */
header('Content-type: application/vnd.ms-excel');
header("Content-Disposition: attachment; filename=Resumen_FI_Ingresos.xls");
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
header("Pragma: public");

error_reporting(E_ALL - E_NOTICE - E_DEPRECATED - E_WARNING);

require('../../clases/datos/CResumenFinancieroInterventoriaData.php');
require_once '../../clases/aplicacion/CDataLog.php';
require('../../clases/datos/CData.php');
require('../../clases/interfaz/CHtml.php');
// Incluimos el archivo de configuracion
include('../../config/conf.php');
include('../../config/constantes.php');
require('../../lang/es-co/ingresos-es.php');

$html = new CHtml('');
$docData = new CResumenFinancieroInterventoriaData($db);
$ingresos = $docData->ObtenerVigencias();

echo "<table width='80%' border='1' align='center'>";
//encabezado
echo"<tr><th colspan = '2'><center></center></th></tr>";
echo"<tr><th colspan = '2' bgcolor='#CCCCCC'><center>" . $html->traducirTildes(INGRESOS_EXCEL) . "</center></th></tr>";


//titulos
echo "<tr>";

echo "<th>" . $html->traducirTildes(AnO_INGRESO_TIPO) . "</th>
      <th>" . $html->traducirTildes(MONTO_INGRESO_B) . "</th>";
echo "</tr>";
//datos 
$contador = 0;
$cont = count($ingresos);

while ($contador < $cont) {
    echo "<tr>";
    echo "<td>" . $html->traducirTildes( $html->traducirTildes($ingresos[$contador]['res_vigen']) ). "</td>	
        <td>" . $html->traducirTildes( $ingresos[$contador]['monto'] ). "</td> ";
    echo "</tr>";
    $contador++;
}
echo "</table>";
?>