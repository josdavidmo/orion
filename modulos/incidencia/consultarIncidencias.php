<?php

/**
 * PNCAV
 *
 * <ul>
 * <li> Redcom Ltda <www.redcom.com.co></li>
 * <li> Proyecto PNCAV</li>
 * </ul>
 */
/**
 * Modulo Incidencias
 * maneja el modulo INCIDENCIAS en union con CIncidencia y CIncidenciaData
 *
 * @see CIncidencia
 * @see CIncidenciaData
 *
 * @package  modulos
 * @subpackage incidencias
 * @author Redcom Ltda
 * @version 2014.09.07
 * @copyright SERTIC - MINTICS
 */

defined('_VALID_PRY') or die('Restricted access');

$incData = new CIncidenciaData($db);

$task = $_REQUEST['task'];
if (empty($task))
    $task = 'list';

switch($task){
    case 'list':
        
        if(isset($_REQUEST['sel_estado'])){
            $estado=$_REQUEST['sel_estado'];
            if($estado!=-1){
                $consultaEstado=" AND inc_estado = ".$estado;
            }
        }else{
            $consultaEstado="";
        }
        if(isset($_REQUEST['sel_tipo'])){
            $tipo=$_REQUEST['sel_tipo'];
            if($tipo!=-1){
                $consultaTipo=" AND inc_tipo = ".$tipo;
            }
        }else{
            $consultaTipo="";
        }
        if(isset($_REQUEST['sel_opcion'])){
            $opcion=$_REQUEST['sel_opcion'];
            if($opcion!=-1){
                $consultaOpcion=" AND inc_opcion = ".$opcion;
            }
        }else{
            $consultaOpcion="";
        }
        $estadosData = $incData->getEstadosIncidencia();
        $estados = null;
        if (isset($estadosData)) {
            foreach ($estadosData as $t) {
                $estados[count($estados)] = array('value' => $t['id'], 'texto' => $t['nombre']);
            }
        }
        
        $tiposData = $incData->getTiposIncidencia();
        $tipos = null;
        if (isset($tiposData)) {
            foreach ($tiposData as $t) {
                $tipos[count($tipos)] = array('value' => $t['id'], 'texto' => $t['nombre']);
            }
        }
        
        $opcionesData = $incData->getOpcionesModulo();
        $opciones = null;
        if (isset($opcionesData)) {
            foreach ($opcionesData as $t) {
                $opciones[count($opciones)] = array('value' => $t['id'], 'texto' => $t['nombre']);
            }
        }
        $form = new CHtmlForm();
        $form->setClassEtiquetas('td_label');
        $form->setTitle(TABLA_INCIDENCIAS);
        $form->setId('frm_filtro_incidencia');
        $form->setMethod('post');
        $form->setAction('?mod=' . $modulo . '&niv=' . $niv . '&task=list');
        
        $form->addEtiqueta(CAMPO_OPCION);
        $form->addSelect('select', 'sel_opcion', 'sel_opcion', $opciones, "", $opcion, '', 'pattern="' . 
                PATTERN_NUMEROS . '" title="' . $html->traducirTildes(TITLE_ALFANUMERICO) . '" required');
        
        $form->addEtiqueta(CAMPO_TIPO_INCIDENCIA);
        $form->addSelect('select', 'sel_tipo', 'sel_tipo', $tipos, "", $tipo, '', 'pattern="' . 
                PATTERN_NUMEROS . '" title="' . $html->traducirTildes(TITLE_ALFANUMERICO) . '" required');
        
        $form->addEtiqueta(CAMPO_ESTADO_INCIDENCIA);
        $form->addSelect('select', 'sel_estado', 'sel_estado', $estados, "", $estado, '', 'pattern="' . 
                PATTERN_NUMEROS . '" title="' . $html->traducirTildes(TITLE_ALFANUMERICO) . '" required');
        $form->addInputButton('submit', 'ok', 'ok', BTN_ACEPTAR, 'button', '');
        $form->writeForm();
        
        $criterio = "1".$consultaEstado.$consultaOpcion.$consultaTipo;
        $elementos = $incData->getIncidencias($criterio);
        $dt = new CHtmlDataTable();
        $titulos = array(
                        CAMPO_ID_INCIDENCIA,
                        CAMPO_FECHA_INCIDENCIA,
                        CAMPO_OPCION,
                        CAMPO_TIPO_INCIDENCIA,
                        CAMPO_DESCRIPCION_INCIDENCIA,
                        CAMPO_ARCHIVO_INCIDENCIA,
                        CAMPO_USUARIO_INCIDENCIA,
                        CAMPO_ESTADO_INCIDENCIA);
        $dt->setDataRows($elementos);
        $dt->setTitleRow($titulos);

        $dt->setTitleTable(TABLA_INCIDENCIAS);
        $dt->setEditLink("?mod=" . $modulo . "&niv=" . $niv . "&task=edit");
        $dt->setType(1);
        $dt->setPag(1);
        $dt->writeDataTable($niv);
        break;

    case 'edit':
        $id = $_REQUEST['id_element'];
        $form = new CHtmlForm();
        $form->setClassEtiquetas('td_label');
        $form->setTitle(TITULO_ATENDER_INCIDENCIA);
        $form->setId('frm_editar_incidencia');
        $form->setMethod('post');
        $form->setAction('?mod=' . $modulo . '&niv=' . $niv . '&task=saveEdit');
        
        $estadosData = $incData->getEstadosIncidencia();
        $estados = null;
        if (isset($estadosData)) {
            foreach ($estadosData as $t) {
                $estados[count($estados)] = array('value' => $t['id'], 'texto' => $t['nombre']);
            }
        }
        
        $tiposData = $incData->getTiposIncidencia();
        $tipos = null;
        if (isset($tiposData)) {
            foreach ($tiposData as $t) {
                $tipos[count($tipos)] = array('value' => $t['id'], 'texto' => $t['nombre']);
            }
        }
        
        $opcionesData = $incData->getOpcionesModulo();
        $opciones = null;
        if (isset($opcionesData)) {
            foreach ($opcionesData as $t) {
                $opciones[count($opciones)] = array('value' => $t['id'], 'texto' => $t['nombre']);
            }
        }

        $incidencia = new CIncidencia($id,$incData);
        $incidencia->loadIncidencia();
        $usuario=$incidencia->getUsuarioIncidencia();
        $opcion=$incidencia->getOpcionIncidencia();
        $tipo=$incidencia->getTipoIncidencia();
        $descripcion=$incidencia->getDescripcionIncidencia();
        $estado=$incidencia->getEstadoIncidencia();
        
        $form->addInputText('hidden', 'usuario', 'usuario', '', '', $usuario, '', '');
        $form->addInputText('hidden', 'id_element', 'id_element', '', '', $id, '', '');
        $form->addEtiqueta(CAMPO_OPCION);
        $form->addSelect('select', 'sel_opcion', 'sel_opcion', $opciones, "", $opcion, '', 'pattern="' . 
                PATTERN_NUMEROS . '" title="' . $html->traducirTildes(TITLE_ALFANUMERICO) . '" required');
        
        $form->addEtiqueta(CAMPO_TIPO_INCIDENCIA);
        $form->addSelect('select', 'sel_tipo', 'sel_tipo', $tipos, "", $tipo, '', 'pattern="' . 
                PATTERN_NUMEROS . '" title="' . $html->traducirTildes(TITLE_ALFANUMERICO) . '" required');
        
        $form->addEtiqueta(CAMPO_DESCRIPCION_INCIDENCIA);
        $form->addTextArea('textarea', 'txt_descripcion', 'txt_descripcion', 15, 19, $descripcion, '', 'pattern="' . 
                PATTERN_ALFANUMERICO . '" title="' . $html->traducirTildes(TITLE_ALFANUMERICO) . '" required');
        
        $form->addEtiqueta(CAMPO_ARCHIVO_INCIDENCIA);
        $form->addInputFile('file', 'file_archivo', 'file_archivo', 15,'', 'pattern="' . 
                PATTERN_ALFANUMERICO . '" title="' . $html->traducirTildes(TITLE_ALFANUMERICO) . '"');
        
        $form->addEtiqueta(CAMPO_OPCION);
        $form->addSelect('select', 'sel_estado', 'sel_estado', $estados, "", $estado, '', 'pattern="' . 
                PATTERN_NUMEROS . '" title="' . $html->traducirTildes(TITLE_ALFANUMERICO) . '" required');
        
        $form->addInputButton('submit', 'ok', 'ok', BOTON_EDITAR, 'button', '');
        $form->addInputButton('button', 'cancelar', 'cancelar', BOTON_CANCELAR, 'button', 'onClick=location.href="?mod=' . $modulo . '&niv=' . $niv . '"');

        $form->writeForm();
        break;
        
    case 'saveEdit':
        $id_element = $_REQUEST['id_element'];
        $id_usuario = $_REQUEST['usuario'];
        $opcion = $_REQUEST['sel_opcion'];
        $tipo = $_REQUEST['sel_tipo'];
        $descripcion = $_REQUEST['txt_descripcion'];
        $archivo = $_FILES['file_archivo'];
        $estado = $_REQUEST['sel_estado'];
        
        $incidencia = new CIncidencia($id_element,$incData);
        $incidencia->loadIncidencia();
        $incidencia->setUsuarioIncidencia($id_usuario);
        $incidencia->setOpcionIncidencia($opcion);
        $incidencia->setTipoIncidencia($tipo);
        $incidencia->setDescripcionIncidencia($descripcion);
        $incidencia->setArchivoIncidencia($archivo);
        $incidencia->setEstadoIncidencia($estado);
        $mesg = $incidencia->updateIncidencia();
        
        echo $html->generaAviso($mesg, "?mod=" . $modulo . "&niv=1&task=list");
        break;
}

?>