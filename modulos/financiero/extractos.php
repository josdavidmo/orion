<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

defined('_VALID_PRY') or die('Restricted access');
$data = new CExtractoFinancieroData($db);
$task = $_REQUEST['task'];
//$operador = $_REQUEST['operador'];
if (empty($task)) {
    $task = 'list';
}
switch ($task) {
    case 'list':
        $cuentaFiltro = $_REQUEST['sel_cuenta'];
        
        $form = new CHtmlForm();
        $form->setClassEtiquetas('td_label');
        $form->setTitle(TITULO_LISTAR_EXTRACTO);
        $form->setId('frm_listar_extracto');
        $form->setMethod('post');
        //$form->setOptions('autoClean', false);

        $cuentas = $data->getCuentas('1', 'cft_id, cfi_nombre');
        $opciones = null;
        if (isset($cuentas)) {
            foreach ($cuentas as $c) {
                $opciones[count($opciones)] = array('value' => $c['id'], 'texto' => $c['numero']);
            }
        }
        $form->addEtiqueta(EXTRACTO_CUENTA);
        $form->addSelect('select', 'sel_cuenta', 'sel_cuenta', $opciones, EXTRACTO_CUENTA, $cuentaFiltro, '', 'onChange="ocultarDiv(\'error_cuenta\');"');
        $form->addError('error_cuenta', ERROR_EXTRACTO_CUENTA);
        
        
        $form->addInputButton('button', 'ok', 'ok', BTN_ACEPTAR, 'button', 'onclick="submit();"');
        $form->addInputButton('button', 'btn_exportar', 'btn_exportar', BTN_EXPORTAR, 'button', 'onClick=exportar_archivo_excel_extracto();');
        $form->writeForm();
        
        $criterio = " 1";
        if(isset($cuentaFiltro) && $cuentaFiltro != '-1' && $cuentaFiltro != ''){
            $criterio = "e.cfi_id = ".$cuentaFiltro;
        }
        
        $extractos = $data->getExtractos($criterio, "c.cfi_id, efi_anio, efi_mes");
        
        $dt = new CHtmlDataTable();
        $titulos = array(EXTRACTO_CUENTA, EXTRACTO_FECHA,
            EXTRACTO_SALDO_INICIAL, EXTRACTO_SALDO_FINAL, 
            EXTRACTO_OBSERVACIONES, 
            EXTRACTO_DOCUMENTO_SOPORTE);
        
        
        $contador = 0;
        $cont = count($extractos);
        $elementos = null;
        $dirOperador = $data->getDirectorioOperador(OPERADOR_DEFECTO);
        $ruta = (RUTA_EXTRACTOS_SOPORTES . "/" . $dirOperador . "/");
        while ($contador < $cont) {
            $elementos[$contador]['id'] = $extractos[$contador]['id'];
            $elementos[$contador]['cuenta'] = $extractos[$contador]['cuenta_numero'];
            $elementos[$contador]['fecha'] = $extractos[$contador]['mes']."-".$extractos[$contador]['anio'];
            $elementos[$contador]['saldo_inicial'] = number_format($data->getSaldoInicialByFecha($extractos[$contador]['cuenta'], 
                    $extractos[$contador]['mes'], $extractos[$contador]['anio']),2);
            $elementos[$contador]['saldo_final'] = number_format($data->getSaldoFinalByFecha($extractos[$contador]['cuenta'], 
                    $extractos[$contador]['mes'], $extractos[$contador]['anio']),2);
            $elementos[$contador]['observaciones'] = $extractos[$contador]['observaciones'];
            $elementos[$contador]['documento_soporte'] = "<a href='".$ruta.$extractos[$contador]['documento_soporte']."'>".$extractos[$contador]['documento_soporte']."</a>";
            $contador++;
        }

        
        $dt->setDataRows($elementos);
        $dt->setTitleRow($titulos);
        $dt->setTitleTable(TABLA_EXTRACTO);

        $dt->setEditLink("?mod=" . $modulo . "&niv=" . $niv. "&task=edit");
        $dt->setDeleteLink("?mod=" . $modulo . "&niv=" . $niv . "&task=delete&sel_cuenta=".$cuentaFiltro);
        $dt->setAddLink("?mod=" . $modulo . "&niv=" . $niv . "&task=add");
        $dt->setSeeLink("?mod=" . $modulo . "&niv=" . $niv . "&task=movimiento");
        $dt->setType(1);
        $pag_crit = "";
        $dt->setPag(1, $pag_crit);
        $dt->writeDataTable($niv);
        
        
        $form2 = new CHtmlForm();
        $form2->setClassEtiquetas('td_label');
        $form2->setTitle(TABLA_MOVIMIENTOS);
        $form2->setId('frm_listar_movs');
        $form2->setMethod('post');
        //$form2->setOptions('autoClean', false);

        $cuentaFiltro2 = $_REQUEST['sel_cuenta2'];
        $movimiento = $_REQUEST['sel_mov'];
        $periodo = $_REQUEST['txt_periodo'];
        
        $cuentas = $data->getCuentas('1', 'cft_id, cfi_numero');
        $opciones2 = null;
        if (isset($cuentas)) {
            foreach ($cuentas as $c) {
                $opciones2[count($opciones2)] = array('value' => $c['id'], 'texto' => $c['numero']);
            }
        }
        
        $movimientosConsulta = $data->getMovimientos();
        $movimientosOpc = null;
        if (isset($movimientosConsulta)) {
            foreach ($movimientosConsulta as $c) {
                $movimientosOpc[count($movimientosOpc)] = array('value' => $c['id'], 'texto' => $c['nombre']);
            }
        }
        
        $form2->addEtiqueta("Periodo");
        $form2->addInputDate('date', 'txt_periodo', 'txt_periodo', $periodo, '%Y-%m', '18', '18', '', 'pattern="' . 
                PATTERN_NUMEROS . '" title="' . $html->traducirTildes(TITLE_NUMEROS) . '"');

        $form2->addEtiqueta(MOVIMIENTO_TIPO);
        $form2->addSelect('select', 'sel_mov', 'sel_mov', $movimientosOpc, '', $movimiento, '', ' ');
        
        $form2->addEtiqueta(EXTRACTO_CUENTA);
        $form2->addSelect('select', 'sel_cuenta2', 'sel_cuenta2', $opciones2, EXTRACTO_CUENTA, $cuentaFiltro2, '', '');
        $form2->addError('error_cuenta', ERROR_EXTRACTO_CUENTA);
        
        $form2->addInputButton('button', 'btn_exportar_mov', 'btn_exportar_mov', BTN_EXPORTAR, 'button', 'onClick=exportar_archivo_excel_movimiento();');
        $form2->addInputButton('button', 'ok', 'ok', BTN_ACEPTAR, 'button', 'onclick="submit();"');
        $form2->writeForm();
        
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
        
        $dt2 = new CHtmlDataTable();
        $titulos2 = array(EXTRACTO_CUENTA, EXTRACTO_FECHA,
            MOVIMIENTO_DESCRIPCION, MOVIMIENTO_VALOR);       
        $dt2->setDataRows($movimientos);
        $dt2->setTitleRow($titulos2);
        $dt2->setSumColumns(array(4));
        $dt2->setFormatRow(array(null,null,null,array(2,',','.')));
        $dt2->setType(1);
        $pag_crit = "";
        $dt2->setPag(1, $pag_crit);
        $dt2->writeDataTable($niv);
        
        break;
    case 'add':
        $cuenta = $_REQUEST['sel_cuenta'];
        $mes = $_REQUEST['txt_mes'];
        $anio = $_REQUEST['txt_anio'];
        $observaciones = $_REQUEST['txt_observaciones'];
        $documento_soporte = $_FILES['file_documento_soporte'];

        $form = new CHtmlForm();
        $form->setClassEtiquetas('td_label');
        $form->setTitle(TITULO_AGREGAR_EXTRACTO);
        $form->setId('frm_agregar_extracto');
        $form->setMethod('post');
        $form->setOptions('autoClean', false);
        $form->setAction('?mod=' . $modulo . '&niv=' . $niv . '&task=saveAdd');
        $cuentas = $data->getCuentas('1', 'cft_id, cfi_nombre');
        $opciones = null;
        if (isset($cuentas)) {
            foreach ($cuentas as $c) {
                $opciones[count($opciones)] = array('value' => $c['id'], 'texto' => $c['nombre']);
            }
        }
        $form->addEtiqueta(EXTRACTO_CUENTA);
        $form->addSelect('select', 'sel_cuenta', 'sel_cuenta', $opciones, EXTRACTO_CUENTA, $cuenta, '', ' required');

        $form->addEtiqueta(EXTRACTO_MES);
        $form->addInputDate('date', 'txt_mes', 'txt_mes', $mes, '%m', '22', '22', '', 'pattern="' . PATTERN_NUMEROS . '" title="' . $html->traducirTildes(TITLE_NUMEROS) . '" required');

        $form->addEtiqueta(EXTRACTO_ANIO);
        $form->addInputDate('date', 'txt_anio', 'txt_anio', $anio, '%Y', '22', '22', '', 'pattern="' . PATTERN_AÑO . '" title="' . $html->traducirTildes(TITLE_AÑO) . '" required');

        $form->addEtiqueta(EXTRACTO_OBSERVACIONES);
        $form->addTextArea('textarea', 'txt_observaciones', 'txt_observaciones', '60', '6', $observaciones, '', '');

        $form->addEtiqueta(EXTRACTO_DOCUMENTO_SOPORTE);
        $form->addInputFile('file', 'file_documento_soporte', 'file_documento_soporte', '25', 'file', '');
        
        $form->addInputButton('submit', 'ok', 'ok', BOTON_INSERTAR, 'button', '');
        $form->addInputButton('button', 'cancelar', 'cancelar', BOTON_CANCELAR, 'button', 'onClick=location.href="?mod=' . $modulo . '&niv=' . $niv . '"');
        $form->writeForm();

        break;

    case 'saveAdd':
        $cuenta = $_REQUEST['sel_cuenta'];
        $mes = $_REQUEST['txt_mes'];
        $anio = $_REQUEST['txt_anio'];
        $observaciones = $_REQUEST['txt_observaciones'];
        $documento_soporte = $_FILES['file_documento_soporte'];

        $extracto = new CExtractoFinanciero('',$data);
        $extracto->cuenta = $cuenta;
        $extracto->mes = $mes;
        $extracto->anio = $anio;
        $extracto->observaciones = $observaciones;
        $extracto->documento_soporte = $documento_soporte;
 
        $m = $extracto->saveExtracto();
        
        echo $html->generaAviso($m, "?mod=" . $modulo . "&niv=" . $niv . "&task=list");

        break;
    
    case 'edit':
        $id_edit = $_REQUEST['id_element'];
        $extracto = new CExtractoFinanciero($id_edit,$data);
        $extracto->loadExtracto();
        
        if(isset($_REQUEST['sel_cuenta']) && $_REQUEST['sel_cuenta']!= -1)
            $cuenta = $_REQUEST['sel_cuenta'];
        else
            $cuenta = $extracto->cuenta;
        
        if(isset($_REQUEST['txt_mes']) && $_REQUEST['txt_mes']!= "")
            $mes = $_REQUEST['txt_mes'];
        else
            $mes = $extracto->mes;
        
        if(isset($_REQUEST['txt_anio']) && $_REQUEST['txt_anio']!= "")
            $anio = $_REQUEST['txt_anio'];
        else
            $anio = $extracto->anio;

        $observaciones = $extracto->observaciones;
        $documento_soporte = $_FILES['file_documento_soporte'];

        $form = new CHtmlForm();
        $form->setClassEtiquetas('td_label');
        $form->setTitle(TITULO_EDITAR_EXTRACTO_INT);
        $form->setId('frm_editar_extracto');
        $form->setMethod('post');
        $form->setAction('?mod=' . $modulo . '&niv=' . $niv . '&task=saveEdit');
        $form->setOptions('autoClean', false);
        
       
        $cuentas = $data->getCuentas('1', 'cft_id, cfi_nombre');
        $opciones = null;
        if (isset($cuentas)) {
            foreach ($cuentas as $c) {
                $opciones[count($opciones)] = array('value' => $c['id'], 'texto' => $c['nombre']);
            }
        }
        
        $form->addInputText('hidden', 'txt_id', 'txt_id', '', '', $id_edit, '', '');
        $form->addEtiqueta(EXTRACTO_CUENTA);
        $form->addSelect('select', 'sel_cuenta', 'sel_cuenta', $opciones, EXTRACTO_CUENTA, $cuenta, '', ' required');

        $form->addEtiqueta(EXTRACTO_MES);
        $form->addInputDate('date', 'txt_mes', 'txt_mes', $mes, '%m', '22', '22', '', 'pattern="' . PATTERN_NUMEROS . '" title="' . $html->traducirTildes('') . '" required');

        $form->addEtiqueta(EXTRACTO_ANIO);
        $form->addInputDate('date', 'txt_anio', 'txt_anio', $anio, '%Y', '22', '22', '', 'pattern="' . PATTERN_AÑO . '" title="' . $html->traducirTildes(TITLE_AÑO) . '" required');

        $form->addEtiqueta(EXTRACTO_OBSERVACIONES);
        $form->addTextArea('textarea', 'txt_observaciones', 'txt_observaciones', '60', '6', $observaciones, '', '');

        $form->addEtiqueta(EXTRACTO_DOCUMENTO_SOPORTE);
        $form->addInputFile('file', 'file_documento_soporte', 'file_documento_soporte', '25', 'file', 'required');
        
        $form->addInputButton('submit', 'ok', 'ok', BOTON_INSERTAR, 'button', '');
        $form->addInputButton('button', 'cancelar', 'cancelar', BOTON_CANCELAR, 'button', 'onClick=location.href="?mod=' . $modulo . '&niv=' . $niv . '"');
        $form->writeForm();

        break;
        
    case 'saveEdit':
        $id_edit = $_REQUEST['txt_id'];
        $cuenta = $_REQUEST['sel_cuenta'];
        $mes = $_REQUEST['txt_mes'];
        $anio = $_REQUEST['txt_anio'];
        $observaciones = $_REQUEST['txt_observaciones'];
        $documento_soporte = $_FILES['file_documento_soporte'];

        $extracto = new CExtractoFinanciero($id_edit,$data);
        $extracto->cuenta = $cuenta;
        $extracto->mes = $mes;
        $extracto->anio = $anio;
        $extracto->observaciones = $observaciones;
        $extracto->documento_soporte = $documento_soporte;

        $m = $extracto->updateExtracto();
        
        echo $html->generaAviso($m, "?mod=" . $modulo . "&niv=" . $niv . "&task=list");

        break;
    case 'delete':
        $cuenta = $_REQUEST['sel_cuenta'];
        $id_delete = $_REQUEST['id_element'];
        
        $form = new CHtmlForm();
        $form->setId('frm_delete_extracto');
        $form->setMethod('post');
        $form->writeForm();

        
        echo $html->generaAdvertencia(EXTRACTO_MSG_BORRADO, '?mod=' . $modulo . '&niv=' . $niv . '&task=confirmDelete&sel_cuenta='.$cuenta.'&id_element=' . $id_delete, 
                "cancelarAccion('frm_delete_extracto','?mod=" . $modulo . "&niv=" . $niv. "&sel_cuenta=".$cuenta."');");
      
        break;
    /**
     * la variable confirmDelete, permite eliminar el objeto Correspondencia de la base de datos
     * 
     */
    case 'confirmDelete':
        $cuenta = $_REQUEST['sel_cuenta'];
        $id_delete = $_REQUEST['id_element'];
        
        $m = $data->deleteExtracto($id_delete);   
        if($m){
            $msg = EXTRACTO_BORRADO;
        }else{
            $msg = ERROR_EXTRACTO_BORRADO;
        }
                
        echo $html->generaAviso($msg, "?mod=" . $modulo . "&niv=" . $niv . "&sel_cuenta=".$cuenta."&task=list");
        
        break;
        
    case 'movimiento':
        $id = $_REQUEST['id_element'];
        
        $dt = new CHtmlDataTable();
        $titulos = array(FECHA_ORDEN,DESCRIPCION_ACTIVIDAD,MOVIMIENTO_VALOR,MOVIMIENTO_SALDO);

        $contador = 0;
        $extracto = new CExtractoFinanciero($id,$data);
        $extracto->loadExtracto();
        $extractos = $data->getMovimientosByExtracto($id);
        $cont = count($extractos);
        
        $saldo_inicial = $data->getSaldoInicialByFecha($extracto->cuenta, $extracto->mes, $extracto->anio);
        $saldo = $saldo_inicial;
        while ($contador < $cont) {
            $extractosDatos[$contador]['id']                 = $extractos[$contador]['id'];
            $extractosDatos[$contador]['fecha']              = $extractos[$contador]['fecha'];
            $extractosDatos[$contador]['descripcion']        = $extractos[$contador]['descripcion'];
            if($extractos[$contador]['valor']<0){
                  
                $extractosDatos[$contador]['saldo_inicial']  = "<font color=\"red\">".number_format($extractos[$contador]['valor'],2)."</font>";
            }else{
                $extractosDatos[$contador]['saldo_inicial']  = number_format($extractos[$contador]['valor'],2);
            }
            $saldo += $extractos[$contador]['valor'];
            $extractosDatos[$contador]['saldo_final']        = number_format($saldo,2);
            $contador++;
        }

        $dt->setDataRows($extractosDatos);
        $dt->setTitleRow($titulos);
        $dt->setTitleTable(TABLA_MOVIMIENTOS);

        $dt->setDeleteLink("?mod=" . $modulo . "&niv=" . $niv. "&task=deleteMovimiento&id_ext=$id");
        $dt->setAddLink("?mod=" . $modulo . "&niv=" . $niv . "&task=addMovimiento&id_ext=$id");
        $dt->setEditLink("?mod=" . $modulo . "&niv=" . $niv . "&task=editMovimiento&id_ext=$id");
        $dt->setType(1);
        $dt->setPag(1);
        $dt->writeDataTable($niv);
        
        $form = new CHtmlForm();
        $form->setClassEtiquetas('td_label');
        $form->setId('frm_lista_movimientos');
        $form->setMethod('post');
        $form->setOptions('autoClean', false);
        $form->addInputButton('button', 'cancelar', 'cancelar', BTN_ATRAS, 'button', 'onClick=location.href="?mod=' . $modulo . '&niv=' . $niv . '"');
        $form->writeForm();
        
        break;
    case 'addMovimiento':
        $id_extracto = $_REQUEST['id_ext'];


        $form = new CHtmlForm();
        $form->setClassEtiquetas('td_label');
        $form->setTitle(TITULO_AGREGAR_MOVIMIENTO);
        $form->setId('frm_agregar_movimiento');
        $form->setMethod('post');
        $form->setOptions('autoClean', false);
        $form->setAction('?mod=' . $modulo . '&niv=' . $niv . '&task=saveAddMovimiento');
        
        $movimientosConsulta = $data->getMovimientos();
        $movimientos = null;
        if (isset($movimientosConsulta)) {
            foreach ($movimientosConsulta as $c) {
                $movimientos[count($movimientos)] = array('value' => $c['id'], 'texto' => $c['nombre']);
            }
        }
        $form->addInputText('hidden', 'id_ext', 'id_ext', '', '', $id_extracto, '', '');
        
        $form->addEtiqueta("Día");
        $form->addInputDate('date', 'txt_dia', 'txt_dia', '', '%d', '18', '18', '', 'pattern="' . 
                PATTERN_NUMEROS . '" title="' . $html->traducirTildes(TITLE_NUMEROS) . '" required');

        $form->addEtiqueta(MOVIMIENTO_TIPO);
        $form->addSelect('select', 'sel_mov', 'sel_mov', $movimientos, '', '', '', ' required');

        //onkeyup="formatearNumero(this);"
        $form->addEtiqueta("Valor");
        $form->addInputText('text', 'txt_valor', 'txt_valor', 25, 25, '','',' pattern="' 
                . PATTERN_NUMEROS_FINANCIEROS . '" title="' . $html->traducirTildes(TITLE_NUMEROS_FINANCIEROS) . '" required');
        
        $form->addInputButton('submit', 'ok', 'ok', BOTON_INSERTAR, 'button', '');
        $form->addInputButton('button', 'cancelar', 'cancelar', BOTON_CANCELAR, 'button', 'onClick=location.href="?mod=' . $modulo . '&niv=' . $niv . "&task=movimiento&id_element=$id_extracto\"");
        $form->writeForm();

        break;
    case 'saveAddMovimiento':
        $id_extracto = $_REQUEST['id_ext'];
        
        $extracto = new CExtractoFinanciero($id_extracto,$data);
        $extracto->loadExtracto();
        
        $dia = $_REQUEST['txt_dia'];
        $movimiento = $_REQUEST['sel_mov'];
        $valor = $_REQUEST['txt_valor'];
//		$valor = str_replace(".", "", $valor);
        $fecha = $extracto->anio."-".$extracto->mes."-".$dia;
        $r = $data->saveMovimiento($id_extracto, $movimiento, $fecha, $valor);
        if($r=='true'){
            $m=MOVIMIENTO_AGREGADO;
        }else{
            $m=ERROR_MOVIMIENTO_AGREGADO;
        }
        
        echo $html->generaAviso($m, "?mod=" . $modulo . "&niv=" . $niv . "&task=movimiento&id_element=$id_extracto");
        
        break;
        
    case 'editMovimiento':
        $id_extracto = $_REQUEST['id_ext'];
        $id_mov = $_REQUEST['id_element'];
        $dia = $data->getDiaMovimiento($id_mov);
        $mov_tipo=$data->getTipoMovimiento($id_mov);
        $valor=$data->getValorMovimiento($id_mov);
        $form = new CHtmlForm();
        $form->setClassEtiquetas('td_label');
        $form->setTitle(TITULO_AGREGAR_MOVIMIENTO);
        $form->setId('frm_agregar_movimiento');
        $form->setMethod('post');
        $form->setOptions('autoClean', false);
        $form->setAction('?mod=' . $modulo . '&niv=' . $niv . '&task=saveEditdMovimiento');
        
        $movimientosConsulta = $data->getMovimientos();
        $movimientos = null;
        if (isset($movimientosConsulta)) {
            foreach ($movimientosConsulta as $c) {
                $movimientos[count($movimientos)] = array('value' => $c['id'], 'texto' => $c['nombre']);
            }
        }
        $form->addInputText('hidden', 'id_ext', 'id_ext', '', '', $id_extracto, '', '');
        $form->addInputText('hidden', 'id_mov', 'id_mov', '', '', $id_mov, '', '');
        
        $form->addEtiqueta("Día");
        $form->addInputDate('date', 'txt_dia', 'txt_dia', $dia, '%d', '18', '18', '', 'pattern="' . 
                PATTERN_NUMEROS . '" title="' . $html->traducirTildes(TITLE_NUMEROS) . '" required');

        $form->addEtiqueta(MOVIMIENTO_TIPO);
        $form->addSelect('select', 'sel_mov', 'sel_mov', $movimientos, '', $mov_tipo, '', ' required');

        //onkeyup="formatearNumero(this);"
        $form->addEtiqueta("Valor");
        $form->addInputText('text', 'txt_valor', 'txt_valor', 25, 25, $valor,'',' pattern="' 
                . PATTERN_NUMEROS_FINANCIEROS . '" title="' . $html->traducirTildes(TITLE_NUMEROS_FINANCIEROS) . '" required');
        
        $form->addInputButton('submit', 'ok', 'ok', BOTON_EDITAR, 'button', '');
        $form->addInputButton('button', 'cancelar', 'cancelar', BOTON_CANCELAR, 'button', 'onClick=location.href="?mod=' . $modulo . '&niv=' . $niv . "&task=movimiento&id_element=$id_extracto\"");
        $form->writeForm();
        break;
        
    case 'saveEditdMovimiento':
        $id_extracto = $_REQUEST['id_ext'];
        $id_mov = $_REQUEST['id_mov'];
        $extracto = new CExtractoFinanciero($id_extracto,$data);
        $extracto->loadExtracto();
        
        $dia = $_REQUEST['txt_dia'];
        $movimiento = $_REQUEST['sel_mov'];
        $valor = $_REQUEST['txt_valor'];
        $fecha = $extracto->anio."-".$extracto->mes."-".$dia;
        $r = $data->editMovimiento($id_mov,$id_extracto, $movimiento, $fecha, $valor);
        if($r=='true'){
            $m=MOVIMIENTO_EDITADO;
        }else{
            $m=ERROR_MOVIMIENTO_EDITADO;
        }
        
        echo $html->generaAviso($m, "?mod=" . $modulo . "&niv=" . $niv . "&task=movimiento&id_element=$id_extracto");
        
        break;
        
    case 'deleteMovimiento':
        $id_delete = $_REQUEST['id_element'];
        $id_extracto = $_REQUEST['id_ext'];
        $form = new CHtmlForm();
        $form->setId('frm_delete_movimiento');
        $form->setMethod('post');
        $form->writeForm();

        
        echo $html->generaAdvertencia(BORRAR_MOVIMIENTO, '?mod=' . $modulo . '&niv=' . $niv . '&task=confirmDeleteMovimiento&id_ext='.$id_extracto.'&id_element=' . $id_delete, 
                "cancelarAccion('frm_delete_extracto','?mod=" . $modulo . "&niv=" . $niv. "&sel_cuenta=".$cuenta."');");
      
        
        break;
    case 'confirmDeleteMovimiento':
        $id_delete = $_REQUEST['id_element'];
        $id_extracto = $_REQUEST['id_ext'];
        $m = $data->deleteMovimiento($id_delete);   
        if($m){
            $msg = MOVIMIENTO_BORRADO;
        }else{
            $msg = ERROR_MOVIMIENTO_BORRADO;
        }
                
        echo $html->generaAviso($msg, "?mod=" . $modulo . "&niv=" . $niv . "&sel_cuenta=".$cuenta."&task=movimiento&id_element=$id_extracto");
        
        break;

}