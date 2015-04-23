<?php

/**
 * Modulo Capacitacion
 * Maneja el modulo capacitacion en union con CDocumentoBasico, 
 * CDocumentoBasicoData
 *
 * @see \CDocumentoBasico
 * @see \CDocumentoBasicoData
 *
 * @package modulos
 * @subpackage interventoria
 * @author SERTIC SAS
 * @version 2015.04.23
 * @copyright SERTIC SAS
 */
//no permite el acceso directo
defined('_VALID_PRY') or die('Restricted access');
$table = 'capacitaciones';
$ruta = 'soporteCapaciones';
$daoCapacitacion = new CDocumentoBasicoData($db, $table, $ruta);
$task = $_REQUEST['task'];
$modulo = $_REQUEST['mod'];
$niv = $_REQUEST['niv'];
if (empty($task)) {
    $task = 'list';
}

switch ($task) {
    
    /**
     * la variable list, permite hacer la carga la página con la lista de 
     * objetos beneficiarios según los parámetros de entrada
     */
    case 'list':
        $form = new CHtmlForm();
        $form->setTitle(TITULO_CAPACITACION);
        $form->setAction("?mod=" . $modulo . "&niv=" . $niv);
        $form->setMethod("post");
        $form->setOptions('autoclean', FALSE);
        $form->writeForm();

        $dt = new CHtmlDataTable();
        $dt->setTitleTable(TITULO_CAPACITACION);
        $titulos = array(CAPACITACION_DESCRIPCION, CAPACITACION_ARCHIVO);
        $capacitaciones = $daoCapacitacion->getDocumentoBasico();
        $dt->setTitleRow($titulos);
        $dt->setDataRows($capacitaciones);
        $dt->setEditLink("?mod=" . $modulo . "&niv=" . $niv . "&task=edit");
        $dt->setDeleteLink("?mod=" . $modulo . "&niv=" . $niv . "&task=delete");
        $dt->setAddLink("?mod=" . $modulo . "&niv=" . $niv . "&task=add");
        $dt->setType(1);
        $pag_crit = "";
        $dt->setPag(1, $pag_crit);
        $dt->writeDataTable($niv);

        break;
    
    case 'add':
        $form = new CHtmlForm();
        $form->setTitle(TITULO_CAPACITACION_AGREGAR);
        $form->setId('frm_add_capacitacion');
        $form->setAction('?mod=' . $modulo . '&niv=1&task=saveAdd');
        $form->setMethod('post');

        $form->addEtiqueta(CAPACITACION_DESCRIPCION);
        $form->addTextArea('textarea', 'txt_descripcion', 'txt_descripcion', 100, 5, '', '', ' title="' . $html->traducirTildes(TITLE_ALFANUMERICO) . '" required');

        $form->addEtiqueta(CAPACITACION_ARCHIVO);
        $form->addInputFile('file', 'file_archivo', 'file_archivo', '25', 'file', ' required');

        $form->addInputButton('submit', 'btn_enviar', 'btn_enviar', BTN_ACEPTAR, 'button', '');
        $form->addInputButton('button', 'cancel', 'cancel', BTN_CANCELAR, 'button', 'onclick="cancelarAccion(\'frm_add_capacitacion\',\'?mod=' . $modulo . '&niv=' . $niv . '\');"');

        $form->writeForm();
        break;
    
    case 'saveAdd':
        $descripcion = $_REQUEST['txt_descripcion'];
        $archivo = $_FILES['file_archivo'];

        $documentoBasico = new CDocumentoBasico(NULL, $descripcion, $archivo);

        $r = $daoCapacitacion->insertDocumentoBasico($documentoBasico);
        $m = ERROR_AGREGAR_CAPACITACION;
        if ($r == "true") {
            $m = EXITO_AGREGAR_CAPACITACION;
        }
        echo $html->generaAviso($m, "?mod=" . $modulo . "&niv=1&task=list");

        break;
        
        
    /**
     * la variable delete, permite hacer la carga del objeto plan accion 
     * y espera confirmacion de eliminarlo @see \CPlanAccionData
     */
    case 'delete':
        $id_delete = $_REQUEST['id_element'];
        echo $html->generaAdvertencia(CONFIRMAR_BORRAR_CAPACITACION, '?mod=' . $modulo . '&id_delete=' . $id_delete . '&niv=1&task=confirmDelete', 'onclick=location.href=\'?mod=' . $modulo . '&niv=1\'');
        break;

    /**
     * la variable confirmDelete, permite eliminar el objeto actividad 
     * plan accion de la base de datos @see \CActividadBitacoraData
     */
    case 'confirmDelete':
        $id_delete = $_REQUEST['id_delete'];
        $r = $daoCapacitacion->deleteDocumentoBasicoById($id_delete);
        $m = ERROR_BORRAR_CAPACITACION;
        if ($r == 'true') {
            $m = EXITO_BORRAR_CAPACITACION;
        }
        echo $html->generaAviso($m, "?mod=" . $modulo . "&niv=1");
        break;
        
    case 'edit':
        $id_edit = $_REQUEST['id_element'];
        $capacitacion = $daoCapacitacion->getDocumentoBasicoById($id_edit);
        $form = new CHtmlForm();
        $form->setTitle(TITULO_EDITAR_CAPACITACION);
        $form->setId('frm_add_capacitacion');
        $form->setAction('?mod=' . $modulo . '&niv=1&task=saveEdit&id_element='.$id_edit);
        $form->setMethod('post');

        $form->addEtiqueta(CAPACITACION_DESCRIPCION);
        $form->addTextArea('textarea', 'txt_descripcion', 'txt_descripcion', 100, 5, $capacitacion->getDescripcion(), '', ' title="' . $html->traducirTildes(TITLE_ALFANUMERICO) . '" required');

        $form->addEtiqueta(CAPACITACION_ARCHIVO);
        $form->addInputFile('file', 'file_archivo', 'file_archivo', '25', 'file', '');

        $form->addInputButton('submit', 'btn_enviar', 'btn_enviar', BTN_ACEPTAR, 'button', '');
        $form->addInputButton('button', 'cancel', 'cancel', BTN_CANCELAR, 'button', 'onclick="cancelarAccion(\'frm_add_capacitacion\',\'?mod=' . $modulo . '&niv=' . $niv . '\');"');

        $form->writeForm();
        break;
    
    case 'saveEdit':
        $id = $_REQUEST['id_element'];
        $descripcion = $_REQUEST['txt_descripcion'];
        $archivo = $_FILES['file_archivo'];

        $documentoBasico = new CDocumentoBasico($id, $descripcion, $archivo);

        $r = $daoCapacitacion->updateDocumentoBasico($documentoBasico);
        $m = ERROR_EDITAR_CAPACITACION;
        if ($r == "true") {
            $m = EXITO_EDITAR_CAPACITACION;
        }
        echo $html->generaAviso($m, "?mod=" . $modulo . "&niv=1");

        break;
}
