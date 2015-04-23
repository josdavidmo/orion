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
    $task = 'add';


switch ($task){
    case 'add':
        $form = new CHtmlForm();
        $form->setClassEtiquetas('td_label');
        $form->setTitle(TITULO_AGREGAR_INCIDENCIA);
        $form->setId('frm_agregar_incidencia');
        $form->setMethod('post');
        $form->setAction('?mod=' . $modulo . '&niv=' . $niv . '&task=saveAdd');
        
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
        
        $form->addInputText('hidden', 'usuario', 'usuario', '', '', $id_usuario, '', '');
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
        
        $form->addInputButton('submit', 'ok', 'ok', BOTON_INSERTAR, 'button', '');
        $form->writeForm();
        
        
        break;
        
    case 'saveAdd':
        
        $id_usuario = $_REQUEST['usuario'];
        $opcion = $_REQUEST['sel_opcion'];
        $tipo = $_REQUEST['sel_tipo'];
        $descripcion = $_REQUEST['txt_descripcion'];
        $archivo = $_FILES['file_archivo'];
        
        $incidencia = new CIncidencia('',$incData);
        $incidencia->setUsuarioIncidencia($id_usuario);
        $incidencia->setOpcionIncidencia($opcion);
        $incidencia->setTipoIncidencia($tipo);
        $incidencia->setDescripcionIncidencia($descripcion);
        $incidencia->setArchivoIncidencia($archivo);
        
        $mesg = $incidencia->insertNewIncidencia();
        
        echo $html->generaAviso($mesg, "?mod=" . $modulo . "&niv=1&task=add");
        break;
}
?>
