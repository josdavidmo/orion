<?php

/**
 * Gestion Interventoria - Fenix
 *
 * <ul>
 * <li> Redcom Ltda <www.redcom.com.co></li>
 * <li> Proyecto RUNT</li>
 * </ul>
 */
/**
 * Modulo Manuales
 * maneja el modulo AUDITORIAS en union con CManual y CManualData
 *
 * @see CManual
 * @see CManualData
 *
 * @package  modulos
 * @subpackage manuales
 * @author Redcom Ltda
 * @version 2013.01.00
 * @copyright Ministerio de Transporte
 */
//no permite el acceso directo
defined('_VALID_PRY') or die('Restricted access');
$manData = new CAuditoriasData($db);
$docData = new CDocumentoData($db);
$operador = $_REQUEST['operador'];
$task = $_REQUEST['task'];
if (empty($task))
    $task = 'list';

switch ($task) {
    /**
     * la variable list, permite hacer la carga la página con la lista de objetos AUDITORIAS según los parámetros de entrada
     */
    case 'list':
        $criterio = '1';
        $dirOperador = $docData->getDirectorioOperador($operador);
        $manuales = $manData->getManuales($criterio, 'man_nombre', $dirOperador);

        $dt = new CHtmlDataTableAlignable();
        $titulos = array(AUDITORIAS_NOMBRE, AUDITORIAS_ARCHIVO);
        $dt->setDataRows($manuales);
        $dt->setTitleRow($titulos);

        $dt->setTitleTable(TABLA_AUDITORIAS);

        $dt->setEditLink("?mod=" . $modulo . "&niv=" . $niv . "&task=edit");
        $dt->setDeleteLink("?mod=" . $modulo . "&niv=" . $niv . "&task=delete");

        $dt->setAddLink("?mod=" . $modulo . "&niv=" . $niv . "&task=add");

        $dt->setType(1);
        $pag_crit = "";
        $dt->setPag(1, $pag_crit);
        $dt->writeDataTable($niv);

        break;
    /**
     * la variable add, permite hacer la carga la página con las variables que componen el objeto AUDITORIAS, ver la clase CManual
     */
    case 'add':
        $nombre = $_REQUEST['txt_nombre'];

        $form = new CHtmlForm();

        $form->setTitle(AGREGAR_AUDITORIAS);
        $form->setId('frm_add_manual');
        $form->setMethod('post');
        $form->setClassEtiquetas('td_label');

        $form->addEtiqueta(AUDITORIAS_NOMBRE);
        $form->addInputText('text', 'txt_nombre', 'txt_nombre', '50', '50', $nombre, '', 'onkeypress="ocultarDiv(\'error_nombre\');"');
        $form->addError('error_nombre', ERROR_AUDITORIAS_NOMBRE);

        $form->addEtiqueta(AUDITORIAS_ARCHIVO);
        $form->addInputFile('file', 'file_archivo', 'file_archivo', '25', 'file', 'onChange="ocultarDiv(\'error_archivo\');"');
        $form->addError('error_archivo', ERROR_AUDITORIAS_ARCHIVO);

        $form->addInputButton('button', 'ok', 'ok', BTN_ACEPTAR, 'button', 'onclick="validar_add_manual(\''.$modulo.'\');"');
        $form->addInputButton('button', 'cancel', 'cancel', BTN_CANCELAR, 'button', 'onclick="cancelarAccion(\'frm_add_manual\',\'?mod=' . $modulo . '&niv=' . $niv . '\');"');

        $form->writeForm();

        break;
    /**
     * la variable saveAdd, permite almacenar el objeto AUDITORIAS en la base de datos, ver la clase CManualData
     */
    case 'saveAdd':
        $nombre = $_REQUEST['txt_nombre'];
        $archivo = $_FILES['file_archivo'];

        $manual = new CManual($manData);
        $manual->setNombre($nombre);
        $manual->setArchivo($archivo);

        $m = $manual->saveNewManual();
        echo $html->generaAviso($m, "?mod=" . $modulo . "&niv=1&task=list");

        break;
    /**
     * la variable delete, permite hacer la carga del objeto AUDITORIAS y espera confirmacion de eliminarlo, ver la clase CManual
     */
    case 'delete':
        $id_delete = $_REQUEST['id_element'];
        $manual = new CManual($manData);
        $manual->setId($id_delete);

        $form = new CHtmlForm();
        $form->setId('frm_delete_manual');
        $form->setMethod('post');

        $form->addInputText('hidden', 'id_element', 'id_element', '15', '15', $manual->getId(), '', '');

        $form->writeForm();

        echo $html->generaAdvertencia(AUDITORIAS_MSG_BORRADO, '?mod=' . $modulo . '&niv=1&task=confirmDelete&id_element=' . $id_delete, "cancelarAccion('frm_delete_manual','?mod=" . $modulo . "&niv=1')");

        break;
    /**
     * la variable confirmDelete, permite eliminar el objeto AUDITORIAS de la base de datos, ver la clase CManualData
     */
    case 'confirmDelete':
        $id_delete = $_REQUEST['id_element'];
        $manual = new CManual($manData);
        $manual->setId($id_delete);
        $manual->loadManual();

        $m = $manual->deleteManual();

        echo $html->generaAviso($m, '?mod=' . $modulo . '&niv=1');

        break;
    /**
     * la variable edit, permite hacer la carga del objeto AUDITORIAS y espera confirmacion de edicion, ver la clase CManual
     */
    case 'edit':
        $id_edit = $_REQUEST['id_element'];
        $manual = new CManual($manData);
        $manual->setId($id_edit);
        $manual->loadManual();

        $form = new CHtmlForm();
        $form->setTitle(EDITAR_AUDITORIAS);
        $form->setId('frm_edit_manual');
        $form->setMethod('post');
        $form->setClassEtiquetas('td_label');

        $form->addInputText('hidden', 'txt_id', 'txt_id', '15', '15', $manual->getId(), '', '');

        $form->addEtiqueta(AUDITORIAS_NOMBRE);
        $form->addInputText('text', 'txt_nombre', 'txt_nombre', '50', '50', $manual->getNombre(), '', 'onkeypress="ocultarDiv(\'error_nombre\');"');
        $form->addError('error_nombre', ERROR_AUDITORIAS_NOMBRE);

        $form->addEtiqueta(AUDITORIAS_ARCHIVO);
        $form->addInputFile('file', 'file_archivo', 'file_archivo', '25', 'file', 'onChange="ocultarDiv(\'error_archivo\');"');
        $form->addError('error_archivo', ERROR_AUDITORIAS_ARCHIVO);

        $form->addInputButton('button', 'ok', 'ok', BTN_ACEPTAR, 'button', 'onclick="validar_edit_manual(\''.$modulo.'\');"');
        $form->addInputButton('button', 'cancel', 'cancel', BTN_CANCELAR, 'button', 'onclick="cancelarAccion(\'frm_edit_manual\',\'?mod=' . $modulo . '&niv=1\');"');

        $form->writeForm();

        break;
    /**
     * la variable saveEdit, permite actualizar el objeto AUDITORIAS en la base de datos, ver la clase CManualData
     */
    case 'saveEdit':
        $id_edit = $_POST['txt_id'];
        $nombre = $_POST['txt_nombre'];
        $archivo = $_FILES['file_archivo'];
        $manual = new CManual($manData);
        $manual->setId($id_edit);
        $manual->loadManual();
        $archivo_anterior = $manual->getArchivo();

        $manual->setNombre($nombre);
        $manual->setArchivo($archivo);

        $m = $manual->saveEditManual($archivo_anterior);

        echo $html->generaAviso($m, '?mod=' . $modulo . '&niv=1');

        break;
    /**
     * en caso de que la variable task no este definida carga la página en construcción
     */
    default:
        include('templates/html/under.html');

        break;
}
?>
