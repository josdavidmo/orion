<?php
/**

 */
        header('Content-type: application/vnd.ms-excel');
        header("Content-Disposition: attachment; filename=MovimientosInterventoria.xls");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
        header("Pragma: public");

        error_reporting(E_ALL - E_NOTICE - E_DEPRECATED - E_WARNING);

        require('../../clases/datos/CExtractoFinancieroInterventoriaData.php');
        require_once '../../clases/aplicacion/CDataLog.php';
        require('../../clases/datos/CData.php');
        require('../../clases/interfaz/CHtml.php');
        // Incluimos el archivo de configuracion
        include('../../config/conf.php');
        include('../../config/constantes.php');
        require('../../lang/es-co/extractos-es.php');
        $data = new CExtractoFinancieroInterventoriaData($db);
        $cuentaFiltro2 = $_REQUEST['sel_cuenta2'];
        $movimiento = $_REQUEST['sel_mov'];
        $periodo = $_REQUEST['txt_periodo'];
        $criterio = " 1 ";
        if(isset($cuentaFiltro2) && $cuentaFiltro2 != '-1' && $cuentaFiltro2 != ''){
            $criterio =  $criterio." AND c.cfi_id = $cuentaFiltro2";
        }
        if(isset($movimiento) && $movimiento != '-1' && $movimiento != ''){
            $criterio =  $criterio." AND mov_id = $movimiento";
        }
        if(isset($periodo) && $periodo != ''){
            $criterio =  $criterio."  AND DATE_FORMAT(mov_fecha,'%Y-%m')=  '$periodo'";
        }
        $movimientos = $data->getMovimientosGeneral($criterio);
        
        
$html = new CHtml('');


echo "<table width='80%' border='1' align='center'>";
//encabezado
echo"<tr><th colspan = '4'><center></center></th></tr>";
echo"<tr><th colspan = '4' bgcolor='#CCCCCC'><center>" . $html->traducirTildes(TABLA_MOVIMIENTOS) . "</center></th></tr>";

//titulos
echo "<tr>";
echo "<th>" . $html->traducirTildes(EXTRACTO_CUENTA) . "</th>
      <th>" . $html->traducirTildes(EXTRACTO_FECHA) . "</th>
      <th>" . $html->traducirTildes(MOVIMIENTO_DESCRIPCION) . "</th>
      <th>" . $html->traducirTildes(MOVIMIENTO_VALOR) . "</th>";
echo "</tr>";
//datos 
$contador = 0;
$cont = count($movimientos);

while ($contador < $cont) {

    echo "<tr>";
    echo "<td>" . $html->traducirTildes( $html->traducirTildes($movimientos [$contador]['cuenta']) ). "</td>	
        <td>" . $html->traducirTildes( $movimientos [$contador]['fecha'] ). "</td> 	
        <td>" . $html->traducirTildes( $movimientos [$contador]['descripcion'] ). "</td> 		
        <td>" . $html->traducirTildes( $movimientos [$contador]['valor'] ). "</td> ";
    echo "</tr>";
    $contador++;
}
echo "</table>";
?>