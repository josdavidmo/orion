<?php

/**
 * Modulo Archivo
 * Maneja el modulo archivos en union con CArchivoData

 * @see \CArchivoData
 *
 * @package modulos
 * @subpackage indicadores
 * @author SERTIC SAS
 * @version 2015.03.12
 * @copyright SERTIC SAS
 */
//no permite el acceso directo
defined('_VALID_PRY') or die('Restricted access');
$daoArchivo = new CArchivoData();
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
        $ruta = $_REQUEST['ruta'] . "/";
        if ($ruta == "/") {
            $ruta = "./";
        }
        $form = new CHtmlForm();
        $form->setClassEtiquetas('td_label');
        $form->setTitle(ARCHIVOS);
        $form->setOptions('autoClean', false);
        $form->writeForm();
        $dt = new CHtmlDataTable();
        $dt->setTitleTable(ARCHIVOS);
        $titulos = array(ARCHIVO_NOMBRE, ARCHIVO_TAMANO, ARCHIVO_FECHA_MODIFICACION, ARCHIVO_TIPO);
        $archivos = $daoArchivo->getArchivos($ruta);
        $dt->setTitleRow($titulos);
        $dt->setDataRows($archivos);
        $dt->setEditLink("?mod=" . $modulo . "&niv=" . $nivel . "&task=edit&ruta=" . $ruta);
        $dt->setDeleteLink("?mod=" . $modulo . "&niv=" . $nivel . "&task=delete&ruta=" . $ruta);
        $dt->setAddLink("?mod=" . $modulo . "&niv=" . $nivel . "&task=add&ruta=" . $ruta);
        $dt->setType(1);
        $pag_crit = "";
        $dt->setFormatRow(array(null, array(2, ',', '.'), null, null));
        $dt->setPag(1, $pag_crit);
        $dt->writeDataTable($niv);
        break;

    case 'add':
        $ruta = substr($_REQUEST['ruta'], 0, -1);
        $form = new CHtmlForm();
        $form->setTitle(TITULO_SUBIR_ARCHIVO);
        $form->setId('frm_edit_registro_fotografico');
        $form->setAction('?mod=' . $modulo . '&niv=1&task=saveAdd&ruta=' . $ruta);
        $form->setMethod('post');
        $form->setClassEtiquetas('td_label');

        $form->addEtiqueta(ARCHIVO_ARCHIVO);
        $form->addInputFile('file', 'file_archivo', 'file_archivo', '25', 'file', ' required');

        $form->addInputButton('submit', 'ok', 'ok', BTN_ACEPTAR, 'button', '');
        $form->addInputButton('button', 'cancel', 'cancel', BTN_CANCELAR, 'button', 'onclick=location.href=\'?mod=' . $modulo . '&niv=1&ruta=' . $ruta . '\'');
        $form->writeForm();
        break;

    case 'saveAdd':
        $archivo = $_FILES['file_archivo'];
        $ruta = $_REQUEST['ruta'];
        $r = $daoArchivo->insertArchivo($archivo, $ruta);
        $m = ERROR_SUBIR_ARCHIVO;
        if ($r) {
            $m = EXITO_SUBIR_ARCHIVO;
        }
        echo $html->generaAviso($m, "?mod=" . $modulo . "&niv=1&ruta=" . $ruta);
        break;

    case 'delete':
        $id_delete = $_REQUEST['id_element'];
        $ruta = substr($_REQUEST['ruta'], 0, -1);
        echo $html->generaAdvertencia(CONFIRMAR_BORRAR_ARCHIVO, '?mod=' . $modulo . '&niv=1&task=confirmDelete&id_element=' . $id_delete . '&ruta=' . $ruta, 'onclick=location.href=\'?mod=' . $modulo . '&niv=1&ruta=' . $ruta . '\'');
        break;


    case 'confirmDelete':
        $id_delete = $_REQUEST['id_element'];
        $ruta = $_REQUEST['ruta'];
        $r = $daoArchivo->deleteArchivo($id_delete);
        $m = ERROR_BORRAR_ARCHIVO;
        if ($r == 'true') {
            $m = EXITO_BORRAR_ARCHIVO;
        }
        echo $html->generaAviso($m, "?mod=" . $modulo . "&niv=1&ruta=" . $ruta);
        break;
        
    case 'edit':
        $id_edit = $_REQUEST['id_element'];
        $ruta = substr($_REQUEST['ruta'], 0, -1);
        $form = new CHtmlForm();
        $form->setTitle(TITULO_EDITAR_ARCHIVO);
        $form->setId('frm_edit_registro_fotografico');
        $form->setAction('?mod=' . $modulo . '&niv=1&task=saveEdit&ruta=' . $ruta . "&id_edit=" . $id_edit);
        $form->setMethod('post');
        $form->setClassEtiquetas('td_label');

        $form->addEtiqueta(ARCHIVO_ARCHIVO);
        $form->addInputFile('file', 'file_archivo', 'file_archivo', '25', 'file', ' required');

        $form->addInputButton('submit', 'ok', 'ok', BTN_ACEPTAR, 'button', '');
        $form->addInputButton('button', 'cancel', 'cancel', BTN_CANCELAR, 'button', 'onclick=location.href=\'?mod=' . $modulo . '&niv=1&ruta=' . $ruta . '\'');
        $form->writeForm();
        break;

    case 'saveEdit':
        $id_edit = $_REQUEST['id_edit'];
        $archivo = $_FILES['file_archivo'];
        $ruta = $_REQUEST['ruta'];
        $r = $daoArchivo->updateArchivo($archivo, $ruta, $id_edit);
        $m = ERROR_SUBIR_ARCHIVO;
        if ($r) {
            $m = EXITO_SUBIR_ARCHIVO;
        }
        echo $html->generaAviso($m, "?mod=" . $modulo . "&niv=1&ruta=" . $ruta);
        break;
        
    case 'download':
        $file = $_GET['file'];
        header("Content-disposition: attachment; filename=$file");
        header("Content-type: application/octet-stream");
        readfile($file);
        break;


    /**
     * en caso de que la variable task no este definida carga la página
     * en construcción
     */
    default:
        include('templates/html/under.html');
        break;
}
?>


