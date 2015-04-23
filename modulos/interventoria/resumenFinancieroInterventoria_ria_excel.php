<?php

/**

 */
header('Content-type: application/vnd.ms-excel');
header("Content-Disposition: attachment; filename=Resumen_FI_RIA.xls");
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
$actividades_inversion = $docData->ObtenerActividaedesInversiondelAnticipo();
for ($i = 0; $i < count($actividades_inversion); $i++) {
    $id_actividad[$i] = $actividades_inversion[$i]['Id_Actividad'];
    $descripcion_actividad[$i] = $actividades_inversion[$i]['Descripcion_Actividad'];
    $monto_actividad[$i] = $actividades_inversion[$i]['Monto_Actividad'];
    $valor_activdades_ordenes = $docData->obtenerValoresActividadesRegistroInversion($id_actividad[$i]);
    $actividades_ordenes[$i] = $valor_activdades_ordenes[0];
    if (!isset($actividades_ordenes[$i])) {
        $actividades_ordenes[$i] = 0;
    }
    $actividad_a_ejecutar = $monto_actividad[$i] - $actividades_ordenes[$i];
    $porcentaje_ejecucion_actividad = ($actividades_ordenes[$i] / $monto_actividad[$i]) * 100;
    $tabla_de_inversion[$i] = array($I + 1, $descripcion_actividad[$i],
        $monto_actividad[$i], $actividades_ordenes[$i], $actividad_a_ejecutar); //, $porcentaje_ejecucion_actividad . "%");
}

echo "<table width='80%' border='1' align='center'>";
//encabezado
echo"<tr><th colspan = '4'><center></center></th></tr>";
echo"<tr><th colspan = '4' bgcolor='#CCCCCC'><center>" . $html->traducirTildes(INGRESOS_EXCEL) . "</center></th></tr>";


//titulos
echo "<tr>";

echo "<th>" . $html->traducirTildes(REPORTE_ACTIVIDAD) . "</th>
      <th>" . $html->traducirTildes(REPORTE_MONTO_ACTIVIDAD) . "</th>
      <th>" . $html->traducirTildes(REPORTE_ORDEN_ACTIVIDAD) . "</th>
      <th>" . $html->traducirTildes(REPORTE_EJECUTAR) . "</th>";
echo "</tr>";
//datos 
$contador = 0;
$cont = count($tabla_de_inversion);

while ($contador < $cont) {

    echo "<tr>";
    echo "<td>" . $html->traducirTildes( $html->traducirTildes($tabla_de_inversion[$contador][1]) ). "</td>	
        <td>" . $html->traducirTildes( $tabla_de_inversion[$contador][2] ). "</td> 	
        <td>" . $html->traducirTildes( $tabla_de_inversion[$contador][3] ). "</td> 	
        <td>" . $html->traducirTildes( $tabla_de_inversion[$contador][4] ). "</td> ";
    echo "</tr>";
    $contador++;
}
echo "</table>";
?>