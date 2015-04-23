<?php
/**

 */
        header('Content-type: application/vnd.ms-excel');
        header("Content-Disposition: attachment; filename=ExtractosInterventoria.xls");
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
        $cuentaFiltro = $_REQUEST['sel_cuenta'];
        $criterio = " 1";
        if(isset($cuentaFiltro) && $cuentaFiltro != '-1' && $cuentaFiltro != ''){
            $criterio = "e.cfi_id = ".$cuentaFiltro;
        }
        $extractos = $data->getExtractos($criterio, "efi_anio, efi_mes");
        $contador = 0;
        $cont = count($extractos);
        $elementos = null;
        $dirOperador = $data->getDirectorioOperador(OPERADOR_DEFECTO);
        $ruta = (RUTA_EXTRACTOS_SOPORTES . "/" . $dirOperador . "/");
        while ($contador < $cont) {
            $elementos[$contador]['id'] = $extractos[$contador]['id'];
            $elementos[$contador]['cuenta'] = $extractos[$contador]['cuenta_numero'];
            $elementos[$contador]['fecha'] = $extractos[$contador]['mes']."-".$extractos[$contador]['anio'];
            $elementos[$contador]['saldo_inicial'] = number_format($data->getSaldoFinalByFecha($extractos[$contador]['cuenta'], 
                    $extractos[$contador]['mes'], $extractos[$contador]['anio']),2);
            $elementos[$contador]['saldo_final'] = $elementos[$contador]['saldo_inicial'];
            $elementos[$contador]['observaciones'] = $extractos[$contador]['observaciones'];
            $elementos[$contador]['documento_soporte'] = "<a href='".$ruta.$extractos[$contador]['documento_soporte']."'>".$extractos[$contador]['documento_soporte']."</a>";
            if($contador!=0){
                $elementos[$contador]['saldo_final'] = 0;
            }
            $contador++;
        }
        $contador=0;
        while($contador<$cont-1){
            $elementos[$contador]['saldo_final']=number_format($data->getSaldoFinalByFecha($extractos[$contador]['cuenta'], 
                    $extractos[$contador+1]['mes'], $extractos[$contador+1]['anio']),2);
            $contador++;
        }
        $elementos[$contador]['saldo_final']=number_format($data->getSaldoFinalByFecha($extractos[$contador]['cuenta'], 
                    12, 2200),2);
        
$html = new CHtml('');


echo "<table width='80%' border='1' align='center'>";
//encabezado
echo"<tr><th colspan = '4'><center></center></th></tr>";
echo"<tr><th colspan = '4' bgcolor='#CCCCCC'><center>" . $html->traducirTildes(TABLA_EXTRACTO) . "</center></th></tr>";


//titulos
echo "<tr>";
echo "<th>" . $html->traducirTildes(EXTRACTO_CUENTA) . "</th>
      <th>" . $html->traducirTildes(EXTRACTO_FECHA) . "</th>
      <th>" . $html->traducirTildes(EXTRACTO_SALDO_INICIAL) . "</th>
      <th>" . $html->traducirTildes(EXTRACTO_SALDO_FINAL) . "</th>
      <th>" . $html->traducirTildes(EXTRACTO_OBSERVACIONES) . "</th>
      <th>" . $html->traducirTildes(EXTRACTO_DOCUMENTO_SOPORTE) . "</th>";
echo "</tr>";
//datos 
$contador = 0;
$cont = count($elementos);

while ($contador < $cont) {

    echo "<tr>";
    echo "<td>" . $html->traducirTildes( $html->traducirTildes($elementos[$contador]['cuenta']) ). "</td>	
        <td>" . $html->traducirTildes( $elementos[$contador]['fecha'] ). "</td> 	
        <td>" . $html->traducirTildes( $elementos[$contador]['saldo_inicial'] ). "</td> 	
        <td>" . $html->traducirTildes( $elementos[$contador]['saldo_final'] ). "</td> 	
        <td>" . $html->traducirTildes( $elementos[$contador]['observaciones'] ). "</td> 	
        <td>" . $html->traducirTildes( $elementos[$contador]['documento_soporte'] ). "</td> ";
    echo "</tr>";
    $contador++;
}
echo "</table>";
?>