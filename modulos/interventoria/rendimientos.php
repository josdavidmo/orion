<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

defined('_VALID_PRY') or die('Restricted access');
$data = new CRendimientoFinancieroInterventoriaData($db);
$task = $_REQUEST['task'];
//$operador = $_REQUEST['operador'];
if (empty($task)) {
    $task = 'list';
}
switch ($task) {
    case 'list':
        $cuentaCons = $_REQUEST['sel_cuenta'];
        
        $form = new CHtmlForm();
        $form->setClassEtiquetas('td_label');
        $form->setTitle(TITULO_LISTAR_RENDIMIENTOS);
        $form->setId('frm_listar_rendimientos');
        $form->setMethod('post');
        //$form->setOptions('autoClean', false);

        $cuentas = $data->getCuentas('1', 'cft_id, cfi_nombre');
        $opciones = null;
        if (isset($cuentas)) {
            foreach ($cuentas as $c) {
                $opciones[count($opciones)] = array('value' => $c['id'], 'texto' => $c['nombre']);
            }
        }
        $form->addEtiqueta(EXTRACTO_CUENTA);
        $form->addSelect('select', 'sel_cuenta', 'sel_cuenta', $opciones, RENDIMIENTOS_CUENTA, $cuenta, '', 'onChange="ocultarDiv(\'error_cuenta\');"');
        $form->addError('error_cuenta', ERROR_RENDIMIENTOS_CUENTA);
        
        $form->addInputButton('button', 'ok', 'ok', BTN_ACEPTAR, 'button', 'onclick="submit();"');
        $form->addInputButton('button', 'btn_exportar', 'btn_exportar', BTN_EXPORTAR, 'button', 'onClick=exportar_archivo_excel_rendimiento_int();');
        $form->writeForm();
        
        $criterio = "";
        if(isset($cuentaCons) && $cuentaCons != '-1'){
            $criterio = "r.cfi_id = ".$cuentaCons;
        }else{
            $criterio = "1";
        }
        
        $rendimientos = $data->getRendimientosInterventoria($criterio, "rfi_anio, rfi_mes");
        
        $dt = new CHtmlDataTable();
        $titulos = array(RENDIMIENTOS_CUENTA, RENDIMIENTOS_FECHA,
            RENDIMIENTOS_RENDIMIENTO_FINANCIERO,
            RENDIMIENTOS_DESCUENTOS, RENDIMIENTOS_RENDIMIENTO_CONSIGNADO,
            RENDIMIENTOS_FECHA_CONSIGNACION,RENDIMIENTOS_COMPROBANTE_CONSIGNACION, 
            RENDIMIENTOS_COMPROBANTE_EMISION, RENDIMIENTOS_OBSERVACIONES);
        
        $contador = 0;
        $cont = count($rendimientos);
        $elementos = null;
        $dirOperador = $data->getDirectorioOperador(OPERADOR_DEFECTO);
        $ruta = (RUTA_RENDIMIENTOS_SOPORTES . "/" . $dirOperador . "/");
        while ($contador < $cont) {
            $elementos[$contador]['id'] = $rendimientos[$contador]['id'];
            $elementos[$contador]['cuenta_numero'] = $rendimientos[$contador]['cuenta_numero'];
            $elementos[$contador]['fecha'] = $rendimientos[$contador]['mes']."-".$rendimientos[$contador]['anio'];
            $elementos[$contador]['rendimiento_financiero'] = $rendimientos[$contador]['rendimiento_financiero'];
            $elementos[$contador]['descuentos'] = $rendimientos[$contador]['descuentos'];
            $elementos[$contador]['rendimiento_consignado'] = $rendimientos[$contador]['rendimiento_consignado'];
            $elementos[$contador]['fecha_consignacion'] = $rendimientos[$contador]['fecha_consignacion'];
            if($rendimientos[$contador]['nombre_consignacion']==null){
                $elementos[$contador]['comprobante_consignacion'] = "<a href='".$ruta.$rendimientos[$contador]['comprobante_consignacion']."'>".$rendimientos[$contador]['comprobante_consignacion']."</a>";
            }else{
                $elementos[$contador]['comprobante_consignacion'] = "<a href='".$ruta.$rendimientos[$contador]['comprobante_consignacion']."'>".$rendimientos[$contador]['nombre_consignacion']."</a>";
            }
            if($rendimientos[$contador]['nombre_emision']==null){
                $elementos[$contador]['comprobante_emision'] = "<a href='".$ruta.$rendimientos[$contador]['comprobante_emision']."'>".$rendimientos[$contador]['comprobante_emision']."</a>";
            }else{
                $elementos[$contador]['comprobante_emision'] = "<a href='".$ruta.$rendimientos[$contador]['comprobante_emision']."'>".$rendimientos[$contador]['nombre_emision']."</a>";
            }
            $elementos[$contador]['observaciones'] = $rendimientos[$contador]['observaciones'];
            $contador++;
        }

        
        $dt->setSumColumns(array(5));
        $dt->setFormatRow(array(null,null,array(2,',','.'),array(2,',','.'),array(2,',','.')));
        $dt->setDataRows($elementos);
        $dt->setTitleRow($titulos);
        $dt->setTitleTable(TABLA_RENDIMIENTOS);

        $dt->setEditLink("?mod=" . $modulo . "&niv=" . $niv. "&task=edit");
        $dt->setDeleteLink("?mod=" . $modulo . "&niv=" . $niv . "&task=delete&sel_cuenta=".$cuentaCons);
        //$dt->setAddLink("?mod=" . $modulo . "&niv=" . $niv . "&task=add");

        $dt->setType(1);
        $pag_crit = "";
        $dt->setPag(1, $pag_crit);
        $dt->writeDataTable($niv);

        
        break;
    case 'add':
        $cuenta = $_REQUEST['sel_cuenta'];
        $mes = $_REQUEST['txt_mes'];
        $anio = $_REQUEST['txt_anio'];
        $saldo = $data->getSaldoFinalByFecha($cuenta, $mes, $anio);
        $rendimiento_financiero = $saldo;
        $descuentos = $_REQUEST['txt_descuentos'];
        $rendimiento_consignado = $_REQUEST['txt_rendimiento_consignado'];
        $saldo_acumulado = $data->getSaldoConsignadoByFecha($cuenta, $mes, $anio);
        $fecha_consignacion = $_REQUEST['txt_fecha_consignacion'];
        $comprobante_consignacion = $_FILES['file_comprobante_consignacion'];
        $comprobante_emision = $_FILES['file_comprobante_emision'];
        $observaciones = $_REQUEST['txt_observaciones'];

        $form = new CHtmlForm();
        $form->setClassEtiquetas('td_label');
        $form->setTitle(TITULO_AGREGAR_RENDIMIENTOS);
        $form->setId('frm_agregar_rendimientos');
        $form->setMethod('post');
        $form->setOptions('autoClean', false);

        $cuentas = $data->getCuentas('1', 'cft_id, cfi_nombre');
        $opciones = null;
        if (isset($cuentas)) {
            foreach ($cuentas as $c) {
                $opciones[count($opciones)] = array('value' => $c['id'], 'texto' => $c['nombre']);
            }
        }
        $form->addEtiqueta(RENDIMIENTOS_CUENTA);
        $form->addSelect('select', 'sel_cuenta', 'sel_cuenta', $opciones, EXTRACTO_CUENTA, $cuenta, '', 'onChange="ocultarDiv(\'error_cuenta\');submit();"');
        $form->addError('error_cuenta', ERROR_EXTRACTO_CUENTA);

        $form->addEtiqueta(RENDIMIENTOS_MES);
        $form->addInputDate('date', 'txt_mes', 'txt_mes', $mes, '%m', '22', '22', '', 'onChange="ocultarDiv(\'error_mes\');submit();"');
        $form->addError('error_mes', ERROR_EXTRACTO_MES);

        $form->addEtiqueta(RENDIMIENTOS_ANIO);
        $form->addInputDate('date', 'txt_anio', 'txt_anio', $anio, '%Y', '22', '22', '', 'onChange="ocultarDiv(\'error_anio\');submit();"');
        $form->addError('error_anio', ERROR_EXTRACTO_ANIO);

        $form->addEtiqueta(RENDIMIENTOS_RENDIMIENTO_FINANCIERO);
        $form->addInputText('text', 'txt_rendimiento_financiero', 'txt_rendimiento_financiero', '15', '15', $rendimiento_financiero, '', 'onkeypress="ocultarDiv(\'error_rendimiento_financiero\');"');
        $form->addError('error_rendimiento_financiero', ERROR_RENDIMIENTOS_RENDIMIENTO_FINANCIERO);
        
        $form->addEtiqueta(RENDIMIENTOS_DESCUENTOS);
        $form->addInputText('text', 'txt_descuentos', 'txt_descuentos', '15', '15', $descuentos, '', 'onkeypress="ocultarDiv(\'error_descuentos\');" onChange="submit();"');
        $form->addError('error_descuentos', ERROR_RENDIMIENTOS_DESCUENTOS);

        $form->addEtiqueta(RENDIMIENTOS_RENDIMIENTO_CONSIGNADO);
        $form->addInputText('text', 'txt_rendimiento_consignado', 'txt_rendimiento_consignado', '15', '15', $rendimiento_consignado, '', 'onkeypress="ocultarDiv(\'error_rendimiento_consignado\');"');
        $form->addError('error_rendimiento_consignado', ERROR_RENDIMIENTOS_RENDIMIENTO_CONSIGNADO);

        $form->addEtiqueta(RENDIMIENTOS_FECHA_CONSIGNACION);
        $form->addInputDate('date', 'txt_fecha_consignacion', 'txt_fecha_consignacion', $fecha_consignacion, '%Y-%m-%d', '16', '16', '', 'onkeypress="ocultarDiv(\'error_fecha_consignacion\');"');
        $form->addError('error_fecha_consignacion', ERROR_RENDIMIENTOS_FECHA_CONSIGNACION);
       
        $form->addEtiqueta(RENDIMIENTOS_COMPROBANTE_CONSIGNACION);
        $form->addInputFile('file', 'file_comprobante_consignacion', 'file_comprobante_consignacion', '25', 'file', 'onChange="ocultarDiv(\'error_comprobante_consignacion\');"');
        $form->addError('error_comprobante_consignacion', ERROR_RENDIMIENTOS_COMPROBANTE_CONSIGNACION);

        $form->addEtiqueta(RENDIMIENTOS_COMPROBANTE_EMISION);
        $form->addInputFile('file', 'file_comprobante_emision', 'file_comprobante_emision', '25', 'file', 'onChange="ocultarDiv(\'error_comprobante_emision\');"');
        $form->addError('error_comprobante_emision', ERROR_RENDIMIENTOS_COMPROBANTE_EMISION);

        $form->addEtiqueta(RENDIMIENTOS_OBSERVACIONES);
        $form->addTextArea('textarea', 'txt_observaciones', 'txt_observaciones', '60', '6', $observaciones, '', 'onkeypress="ocultarDiv(\'error_observaciones\');"');
        $form->addError('error_observaciones', ERROR_RENDIMIENTOS_OBSERVACIONES);

        $form->addInputButton('button', 'ok', 'ok', BOTON_INSERTAR, 'button', 'onclick="validar_add_remdimiento_interventoria(\'frm_agregar_rendimientos\');"');
        $form->addInputButton('button', 'cancelar', 'cancelar', BOTON_CANCELAR, 'button', 'onclick="cancelarAccion(\'frm_agregar_rendimientos\',\'?mod=' . $modulo . '&task=list&niv=' . $niv . '\');"');
        $form->writeForm();

        break;

    case 'saveAdd':
        $cuenta = $_REQUEST['sel_cuenta'];
        $mes = $_REQUEST['txt_mes'];
        $anio = $_REQUEST['txt_anio'];
        $rendimiento_financiero = $_REQUEST['txt_rendimiento_financiero'];
        $descuentos = $_REQUEST['txt_descuentos'];
        $rendimiento_consignado = $_REQUEST['txt_rendimiento_consignado'];
        $fecha_consignacion = $_REQUEST['txt_fecha_consignacion'];
        $comprobante_consignacion = $_FILES['file_comprobante_consignacion'];
        $comprobante_emision = $_FILES['file_comprobante_emision'];
        $observaciones = $_REQUEST['txt_observaciones'];

        $rendimiento = new CRendimientoFinanciero('',$data);
        $rendimiento->cuenta = $cuenta;
        $rendimiento->mes = $mes;
        $rendimiento->anio = $anio;
        $rendimiento->rendimiento_financiero = $rendimiento_financiero;
        $rendimiento->descuentos = $descuentos;
        $rendimiento->rendimiento_consignado = $rendimiento_consignado;
        $rendimiento->fecha_consignacion = $fecha_consignacion;
        $rendimiento->comprobante_consignacion = $comprobante_consignacion;
        $rendimiento->comprobante_emision = $comprobante_emision;
        $rendimiento->observaciones = $observaciones;

        $m = $rendimiento->saveRendimiento();
        
        echo $html->generaAviso($m, "?mod=" . $modulo . "&niv=" . $niv . "&task=list");

        break;
    
    case 'edit':
        $id_edit = $_REQUEST['id_element'];
        $rendimiento = new CRendimientoFinancieroInterventoria($id_edit,$data);
        $rendimiento->loadRendimiento();
        
        if(isset($_REQUEST['sel_cuenta']) && $_REQUEST['sel_cuenta']!=-1)
            $cuenta = $_REQUEST['sel_cuenta'];
        else
            $cuenta = $rendimiento->cuenta;
        if(isset($_REQUEST['txt_mes']) && $_REQUEST['txt_mes']!="0000-00-00")
            $mes = $_REQUEST['txt_mes'];
        else
            $mes = $rendimiento->mes;
        if(isset($_REQUEST['txt_anio']) && $_REQUEST['txt_anio']!="0000-00-00")
            $anio = $_REQUEST['txt_anio'];
        else
            $anio = $rendimiento->anio;
        if(isset($_REQUEST['txt_anio']))
            $rendimiento_financiero = $_REQUEST['txt_rendimiento_financiero'];
        else
            $rendimiento_financiero = $rendimiento->rendimiento_financiero;
        if(isset($_REQUEST['txt_descuentos']))
            $descuentos = $_REQUEST['txt_descuentos'];
        else
            $descuentos = $rendimiento->descuentos;
        if(!isset($rendimiento->rendimiento_financiero)){
            $rendimiento_financiero=$data->getIntereses($id_edit, $anio, $mes);
        }
        if(isset($rendimiento->rendimiento_consignado)){
            $rendimiento_consignado = $rendimiento->rendimiento_consignado;
        }else{
            $rendimiento_consignado = $_REQUEST['txt_rendimiento_consignado'];
        }
        $saldo_acumulado = $data->getSaldoConsignadoByFecha($cuenta, $mes, $anio);
        $rendimiento_acumulado = $saldo_acumulado;
        
//        if(isset($_REQUEST['txt_rendimiento_consignado']))
//            $rendimiento_consignado = $_REQUEST['txt_rendimiento_consignado'];
//        else
//            $rendimiento_consignado = $rendimiento->rendimiento_consignado;
//        if(isset($_REQUEST['txt_rendimiento_acumulado']))
//            $rendimiento_acumulado = $_REQUEST['txt_rendimiento_acumulado'];
//        else
//            $rendimiento_acumulado = $rendimiento->rendimiento_acumulado;
        
        if(isset($_REQUEST['txt_fecha_consignacion']) && $_REQUEST['txt_fecha_consignacion']!="0000-00-00")
            $fecha_consignacion = $_REQUEST['txt_fecha_consignacion'];
        else
            $fecha_consignacion = $rendimiento->fecha_consignacion;
        if(isset($_REQUEST['txt_valor_fiduciaria']))
            $valor_fiduciaria = $_REQUEST['txt_valor_fiduciaria'];
        else
            $valor_fiduciaria = $rendimiento->valor_fiduciaria;
        if(isset($_REQUEST['txt_observaciones']))
            $observaciones = $_REQUEST['txt_observaciones'];
        else
            $observaciones = $rendimiento->observaciones;
        
        $comprobante_consignacion = $_FILES['file_comprobante_consignacion'];
        $comprobante_emision = $_FILES['file_comprobante_emision'];
        
        $form = new CHtmlForm();
        $form->setClassEtiquetas('td_label');
        $form->setTitle(TITULO_EDITAR_RENDIMIENTOS);
        $form->setId('frm_editar_rendimientos');
        $form->setAction('?mod=' . $modulo . '&niv=' . $niv . '&task=saveEdit');
        $form->setMethod('post');
        $form->setOptions('autoClean', false);
        
       
        $cuentas = $data->getCuentas('1', 'cft_id, cfi_nombre');
        $opciones = null;
        if (isset($cuentas)) {
            foreach ($cuentas as $c) {
                $opciones[count($opciones)] = array('value' => $c['id'], 'texto' => $c['nombre']);
            }
        }
        $form->addEtiqueta(RENDIMIENTOS_CUENTA);
        $form->addSelect('select', 'sel_cuenta', 'sel_cuenta', $opciones, EXTRACTO_CUENTA, $cuenta, '', 'onChange="ocultarDiv(\'error_cuenta\');submit();"');
        $form->addError('error_cuenta', ERROR_EXTRACTO_CUENTA);

        $form->addEtiqueta(RENDIMIENTOS_MES);
        $form->addInputDate('date', 'txt_mes', 'txt_mes', $mes, '%m', '22', '22', '', 'onChange="ocultarDiv(\'error_mes\');submit();"');
        $form->addError('error_mes', ERROR_EXTRACTO_MES);

        $form->addEtiqueta(RENDIMIENTOS_ANIO);
        $form->addInputDate('date', 'txt_anio', 'txt_anio', $anio, '%Y', '22', '22', '', 'onChange="ocultarDiv(\'error_anio\');submit();"');
        $form->addError('error_anio', ERROR_EXTRACTO_ANIO);

        $form->addEtiqueta(RENDIMIENTOS_RENDIMIENTO_FINANCIERO);
        $form->addInputText('text', 'txt_rendimiento_financiero', 'txt_rendimiento_financiero', '15', '15', $rendimiento_financiero, '', 'onkeypress="ocultarDiv(\'error_rendimiento_financiero\');"');
        $form->addError('error_rendimiento_financiero', ERROR_RENDIMIENTOS_RENDIMIENTO_FINANCIERO);
        
        $form->addEtiqueta(RENDIMIENTOS_DESCUENTOS);
        $form->addInputText('text', 'txt_descuentos', 'txt_descuentos', '15', '15', $descuentos, '', 'onkeypress="ocultarDiv(\'error_descuentos\');"');
        $form->addError('error_descuentos', ERROR_RENDIMIENTOS_DESCUENTOS);

        $form->addEtiqueta(RENDIMIENTOS_RENDIMIENTO_CONSIGNADO);
        $form->addInputText('text', 'txt_rendimiento_consignado', 'txt_rendimiento_consignado', '15', '15', $rendimiento_consignado, '', 'onkeypress="ocultarDiv(\'error_rendimiento_consignado\');"');
        $form->addError('error_rendimiento_consignado', ERROR_RENDIMIENTOS_RENDIMIENTO_CONSIGNADO);
        
        $form->addEtiqueta(RENDIMIENTOS_FECHA_CONSIGNACION);
        $form->addInputDate('date', 'txt_fecha_consignacion', 'txt_fecha_consignacion', $fecha_consignacion, '%Y-%m-%d', '16', '16', '', 'onkeypress="ocultarDiv(\'error_fecha_consignacion\');"');
        $form->addError('error_fecha_consignacion', ERROR_RENDIMIENTOS_FECHA_CONSIGNACION);
       
        $form->addEtiqueta(RENDIMIENTOS_COMPROBANTE_CONSIGNACION);
        $form->addInputFile('file', 'file_comprobante_consignacion', 'file_comprobante_consignacion', '25', 'file', 'onChange="ocultarDiv(\'error_comprobante_consignacion\');"');
        $form->addError('error_comprobante_consignacion', ERROR_RENDIMIENTOS_COMPROBANTE_CONSIGNACION);

        $form->addEtiqueta(RENDIMIENTOS_COMPROBANTE_EMISION);
        $form->addInputFile('file', 'file_comprobante_emision', 'file_comprobante_emision', '25', 'file', 'onChange="ocultarDiv(\'error_comprobante_emision\');"');
        $form->addError('error_comprobante_emision', ERROR_RENDIMIENTOS_COMPROBANTE_EMISION);

        $form->addEtiqueta(RENDIMIENTOS_OBSERVACIONES);
        $form->addTextArea('textarea', 'txt_observaciones', 'txt_observaciones', '60', '6', $observaciones, '', 'onkeypress="ocultarDiv(\'error_observaciones\');"');
        $form->addError('error_observaciones', ERROR_RENDIMIENTOS_OBSERVACIONES);
        
        $form->addInputDate('hidden', 'txt_id', 'txt_id', $id_edit, '15', '15',  '', '', '');
        
        $form->addInputButton('submit', 'ok', 'ok', BTN_ACEPTAR, 'button', '');
        $form->addInputButton('button', 'cancelar', 'cancelar', BOTON_CANCELAR, 'button', 'onClick=location.href="?mod=' . $modulo . '&niv=' . $niv . '"');
        $form->writeForm();

        break;
        
    case 'saveEdit':
        $id_edit = $_REQUEST['txt_id'];
        $cuenta = $_REQUEST['sel_cuenta'];
        $mes = $_REQUEST['txt_mes'];
        $anio = $_REQUEST['txt_anio'];
        $rendimiento_financiero = $_REQUEST['txt_rendimiento_financiero'];
        $descuentos = $_REQUEST['txt_descuentos'];
        $rendimiento_consignado = $_REQUEST['txt_rendimiento_consignado'];
        $fecha_consignacion = $_REQUEST['txt_fecha_consignacion'];
        $comprobante_consignacion = $_FILES['file_comprobante_consignacion'];
        $comprobante_emision = $_FILES['file_comprobante_emision'];
        $observaciones = $_REQUEST['txt_observaciones'];

        $rendimiento = new CRendimientoFinancieroInterventoria($id_edit,$data);
        $rendimiento->cuenta = $cuenta;
        $rendimiento->mes = $mes;
        $rendimiento->anio = $anio;
        $rendimiento->rendimiento_financiero = $rendimiento_financiero;
        $rendimiento->descuentos = $descuentos;
        $rendimiento->rendimiento_consignado = $rendimiento_consignado;
        $rendimiento->fecha_consignacion = $fecha_consignacion;
        $rendimiento->comprobante_consignacion = $comprobante_consignacion;
        $rendimiento->comprobante_emision = $comprobante_emision;
        $rendimiento->observaciones = $observaciones;

        

        $m = $rendimiento->updateRendimiento();
        
        echo $html->generaAviso($m, "?mod=" . $modulo . "&niv=" . $niv . "&task=list");

        break;
    case 'delete':
        $cuenta = $_REQUEST['sel_cuenta'];
        $id_delete = $_REQUEST['id_element'];
        
        $form = new CHtmlForm();
        $form->setId('frm_delete_rendimiento');
        $form->setMethod('post');
        $form->writeForm();

        
        echo $html->generaAdvertencia(RENDIMIENTOS_MSG_BORRADO, '?mod=' . $modulo . '&niv=' . $niv . '&task=confirmDelete&sel_cuenta='.$cuenta.'&id_element=' . $id_delete, 
                "cancelarAccion('frm_delete_rendimiento','?mod=" . $modulo . "&niv=" . $niv. "&sel_cuenta=".$cuenta."');");
      
        break;
    /**
     * la variable confirmDelete, permite eliminar el objeto Correspondencia de la base de datos
     * 
     */
    case 'confirmDelete':
        $cuenta = $_REQUEST['sel_cuenta'];
        $id_delete = $_REQUEST['id_element'];
        
        $m = $data->deleteRendimiento($id_delete);   
        if($m){
            $msg = RENDIMIENTOS_BORRADO;
        }else{
            $msg = ERROR_RENDIMIENTOS_BORRADO;
        }
                
        echo $html->generaAviso($msg, "?mod=" . $modulo . "&niv=" . $niv . "&sel_cuenta=".$cuenta."&task=list");
        
        break;

}