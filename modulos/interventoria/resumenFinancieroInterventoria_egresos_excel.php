<?php

/**

 */
header('Content-type: application/vnd.ms-excel');
header("Content-Disposition: attachment; filename=Resumen_FI_Egresos.xls");
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
//obtenemos loa años de viegencias
$Years = $docData->ObtenerYears();

for ($i = 0; $i < count($Years); $i++) {
    $arrayear[$i] = $Years[$i]['ano_Vigencia'];
}
//generamos loa valores y la tabla de egresos
$vigenciaObjetivo = 2013;
$actividadObjetivo = ACTIVIDAD_PIA;
$presupuesto_ejecutado = 0;
$saldo = 0;
for ($i = 0; $i < count($arrayear); $i++) {
    if ($arrayear[$i] == VIGENCIA_OBJETIVO) {
        $presupuesto_ejecutado = $docData->obtenerTotalActividades();
    } else {
        $presupuesto_ejecutado = $docData->obtenerInformeFinanciero($arrayear[$i]);
    }
    $vigencias_Montos = $docData->obtenerValoresVigencia($arrayear[$i]);
    if (isset($presupuesto_ejecutado[0])) {
        $presupuesto_ejecutado = $presupuesto_ejecutado[0];
    } else {
        $presupuesto_ejecutado = 0;
    }
    $presupuesto_ejecutar = $vigencias_Montos[1] - $presupuesto_ejecutado;

    $porcentaje_ejecucion = $presupuesto_ejecutado / $vigencias_Montos[1] * 100;
    $tabla_egresos[$i]['id'] = $i;
    $tabla_egresos[$i]['anio'] = $arrayear[$i];
    $tabla_egresos[$i]['presAsignado'] = $vigencias_Montos[1];
    $tabla_egresos[$i]['presEjecutado'] = $presupuesto_ejecutado;
    $tabla_egresos[$i]['presPendiente'] = $presupuesto_ejecutar;
    //$tabla_egresos[$i]['porcentaje'] = $porcentaje_ejecucion;
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
$cont = count($tabla_egresos);

while ($contador < $cont) {

    echo "<tr>";
    echo "<td>" . $html->traducirTildes( $html->traducirTildes($tabla_egresos[$contador]['anio']) ). "</td>	
        <td>" . $html->traducirTildes( $tabla_egresos[$contador]['presAsignado'] ). "</td> 	
        <td>" . $html->traducirTildes( $tabla_egresos[$contador]['presEjecutado'] ). "</td> 	
        <td>" . $html->traducirTildes( $tabla_egresos[$contador]['presPendiente'] ). "</td> ";
    echo "</tr>";
    $contador++;
}
echo "</table>";
?>