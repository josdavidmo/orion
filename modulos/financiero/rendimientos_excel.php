<?php
/**

 */
        header('Content-type: application/vnd.ms-excel');
        header("Content-Disposition: attachment; filename=Rendimientos.xls");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
        header("Pragma: public");

        error_reporting(E_ALL - E_NOTICE - E_DEPRECATED - E_WARNING);

        require('../../clases/datos/CRendimientoFinancieroData.php');
        require_once '../../clases/aplicacion/CDataLog.php';
        require('../../clases/datos/CData.php');
        require('../../clases/interfaz/CHtml.php');
        // Incluimos el archivo de configuracion
        include('../../config/conf.php');
        include('../../config/constantes.php');
        require('../../lang/es-co/rendimientos-es.php');
        $data = new CRendimientoFinancieroData($db);
        $cuentaCons = $_REQUEST['sel_cuenta'];
        $criterio = "";
        if(isset($cuentaCons) && $cuentaCons != '-1'){
            $criterio = "r.cfi_id = ".$cuentaCons;
        }else{
            $criterio = "1";
        }
        $rendimientos = $data->getRendimientosInterventoria($criterio, "rfi_anio, rfi_mes");
        $contador = 0;
        $cont = count($rendimientos);
        $elementos = null;
        $dirOperador = $data->getDirectorioOperador(OPERADOR_DEFECTO);
        $ruta = (RUTA_RENDIMIENTOS_SOPORTES . "/" . $dirOperador . "/");
        while ($contador < $cont) {
            $elementos[$contador]['id'] = $rendimientos[$contador]['id'];
            $elementos[$contador]['cuenta_numero'] = $rendimientos[$contador]['cuenta_numero'];
            $elementos[$contador]['fecha'] = $rendimientos[$contador]['mes']."-".$rendimientos[$contador]['anio'];
            $elementos[$contador]['rendimiento_financiero'] = number_format($rendimientos[$contador]['rendimiento_financiero'],2);
            $elementos[$contador]['descuentos'] = number_format($rendimientos[$contador]['descuentos'],2);
            $elementos[$contador]['rendimiento_consignado'] = $rendimientos[$contador]['rendimiento_consignado'];
            $elementos[$contador]['fecha_consignacion'] = $rendimientos[$contador]['fecha_consignacion'];
            $elementos[$contador]['comprobante_consignacion'] = "<a href='".$ruta.$rendimientos[$contador]['comprobante_consignacion']."'>".$rendimientos[$contador]['comprobante_consignacion']."</a>";
            $elementos[$contador]['comprobante_emision'] = "<a href='".$ruta.$rendimientos[$contador]['comprobante_emision']."'>".$rendimientos[$contador]['comprobante_emision']."</a>";
            $elementos[$contador]['observaciones'] = $rendimientos[$contador]['observaciones'];
            $contador++;
        }
        
$html = new CHtml('');


echo "<table width='80%' border='1' align='center'>";
//encabezado
echo"<tr><th colspan = '4'><center></center></th></tr>";
echo"<tr><th colspan = '4' bgcolor='#CCCCCC'><center>" . $html->traducirTildes(TABLA_RENDIMIENTOS) . "</center></th></tr>";

//titulos
echo "<tr>";
echo "<th>" . $html->traducirTildes(RENDIMIENTOS_CUENTA) . "</th>
      <th>" . $html->traducirTildes(RENDIMIENTOS_FECHA) . "</th>
      <th>" . $html->traducirTildes(RENDIMIENTOS_RENDIMIENTO_FINANCIERO) . "</th>
      <th>" . $html->traducirTildes(RENDIMIENTOS_DESCUENTOS) . "</th>
      <th>" . $html->traducirTildes(RENDIMIENTOS_RENDIMIENTO_CONSIGNADO) . "</th>
      <th>" . $html->traducirTildes(RENDIMIENTOS_FECHA_CONSIGNACION) . "</th>
      <th>" . $html->traducirTildes(RENDIMIENTOS_COMPROBANTE_CONSIGNACION) . "</th>
      <th>" . $html->traducirTildes(RENDIMIENTOS_COMPROBANTE_EMISION) . "</th>
      <th>" . $html->traducirTildes(RENDIMIENTOS_OBSERVACIONES) . "</th>";
echo "</tr>";
//datos 
$contador = 0;
$cont = count($elementos);

while ($contador < $cont) {

    echo "<tr>";
    echo "<td>" . $html->traducirTildes($elementos[$contador]['cuenta_numero']) . "</td>	
        <td>" . $elementos[$contador]['fecha'] . "</td> 	
        <td>" . $elementos[$contador]['rendimiento_financiero'] . "</td> 	
        <td>" . $elementos[$contador]['descuentos'] . "</td> 	
        <td>" . $elementos[$contador]['rendimiento_consignado'] . "</td>
        <td>" . $elementos[$contador]['fecha_consignacion'] . "</td>
        <td>" . $elementos[$contador]['comprobante_consignacion'] . "</td>
        <td>" . $elementos[$contador]['comprobante_emision'] . "</td>
        <td>" . $elementos[$contador]['observaciones'] . "</td> ";
    echo "</tr>";
    $contador++;
}
echo "</table>";
?>