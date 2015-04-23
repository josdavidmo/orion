<?php

/**
 * Modulo PQR
 * Maneja el modulo pqr en union con CPQR, CPQRData
 *
 * @see \CPQR
 * @see \CPQRData
 *
 * @package modulos
 * @subpackage indicadores
 * @author SERTIC SAS
 * @version 2014.11.26
 * @copyright SERTIC SAS
 */
//no permite el acceso directo
defined('_VALID_PRY') or die('Restricted access');
$daoBeneficiarios = new CBeneficiarioData($db);
$daoBasicas = new CBasicaData($db);
$daoPQR = new CAtencionMesaAyudaData($db);
$task = $_REQUEST['task'];
$modulo = $_REQUEST['mod'];
$niv = $_REQUEST['niv'];
if (empty($task))
    $task = 'list';

switch ($task) {
    
    case 'list':
        $id_element = $_REQUEST['id_element'];
        $form = new CHtmlForm();
        $form->setId('frm_filtrar_beneficiarios');
        $form->setTitle(TITULO_ATENCION_MESA_AYUDA);
        $form->setAction('?mod=' . $modulo . '&niv=' . $niv . '&task=list');
        $form->setMethod("post");
        $form->setOptions("autoClean", false);
        $form->writeForm();

        $dt = new CHtmlDataTable();
        $dt->setTitleTable(TITULO_ATENCION_MESA_AYUDA);
        $titulos = array(PQR_NUMERO, PQR_DESCRIPCION, PQR_FECHA_REPORTE, PQR_FECHA_SOLUCION, PQR_DIFERENCIA,
            PQR_DIAGNOSTICO, PQR_RESPUESTA, PQR_ESTADO);
        $pqrs = $daoPQR->getPQRByBeneficiario();
        $dt->setTitleRow($titulos);
        $dt->setDataRows($pqrs);
        $dt->setEditLink("?mod=" . $modulo . "&niv=" . $niv . "&task=edit");
        $dt->setDeleteLink("?mod=" . $modulo . "&niv=" . $niv . "&task=delete");
        $dt->setAddLink("?mod=" . $modulo . "&niv=" . $niv . "&task=add");
        $otros = array('link' => "?mod=" . $modulo . "&niv=" . $niv . "&task=answer", 'img' => 'marcado.gif', 'alt' => ALT_ENVIAR);
        $dt->addOtrosLink($otros);
        $dt->setType(1);
        $pag_crit = "";
        $dt->setPag(1, $pag_crit);
        $dt->writeDataTable($niv);

        break;

    /**
     * la variable add, permite hacer la carga la página con las variables
     * que componen el objeto beneficiario @see \CBeneficiario
     */
    case 'add':
        date_default_timezone_set('America/Bogota');
        $form = new CHtmlForm();

        $opciones = null;

        $form->setTitle(TITULO_AGREGAR_ATENCION_MESA_AYUDA);
        $form->setId('frm_add_PQR');
        $form->setAction('?mod=' . $modulo . '&niv=1&task=saveAdd' . $idBeneficiario);
        $form->setMethod('post');

        $form->addEtiqueta(PQR_DESCRIPCION);
        $form->addTextArea('text', 'txt_descripcion', 'txt_descripcion', '200', '200', '', null, 'autofocus pattern="' . PATTERN_ALFANUMERICO . '" title="' . $html->traducirTildes(TITLE_ALFANUMERICO) . '" required');

        $form->addEtiqueta(PQR_FECHA_REPORTE);
        $form->addInputDate('date', 'txt_fecha', 'txt_fecha', '', '%Y-%m-%d', '18', '18', '', 'pattern="' . PATTERN_FECHA . '" title="' . $html->traducirTildes(TITLE_FECHA) . '" required');

        $form->addEtiqueta(PQR_HORA_REPORTE);
        $form->addInputText('time', 'txt_hora', 'txt_hora', '200', '200', date('H:i'), '', 'autofocus pattern="' . PATTERN_ALFANUMERICO . '" title="' . $html->traducirTildes(TITLE_ALFANUMERICO) . '"');

        $form->addInputButton('submit', 'btn_enviar', 'btn_enviar', BTN_ACEPTAR, 'button', '');
        $form->addInputButton('button', 'cancel', 'cancel', BTN_CANCELAR, 'button', 'onclick="cancelarAccion(\'frm_add_PQR\',\'?mod=' . $modulo . '&niv=' . $niv . '\');"');

        $form->writeForm();

        break;
    /**
     * la variable saveAdd, permite almacenar el objeto beneficiario en la
     * base de datos @see \CBeneficiario
     */
    case 'saveAdd':
        $descripcionRequerimiento = $_REQUEST['txt_descripcion'];
        $fechaReporte = $_REQUEST['txt_fecha'] . ' ' . $_REQUEST['txt_hora'];

        $pqr = new CPQR(null, $descripcionRequerimiento, $fechaReporte, null, null, null, null);

        $r = $daoPQR->insertPQR($pqr);
        $m = ERROR_AGREGAR_ATENCION_MESA_AYUDA;
        if ($r == 'true') {
            $m = EXITO_AGREGAR_ATENCION_MESA_AYUDA;
        }
        echo $html->generaAviso($m, "?mod=" . $modulo . "&niv=1");

        break;

    /**
     * la variable add, permite hacer la carga la página con las variables
     * que componen el objeto beneficiario @see \CBeneficiario
     */
    case 'answer':
        date_default_timezone_set('America/Bogota');
        $idPQR = $_REQUEST['id_element'];
        $pqr = $daoPQR->getPQRById($idPQR);
        $idBeneficiario = $_REQUEST['idBeneficiario'];
        $form = new CHtmlForm();

        $fecha = preg_split("/[\s,]+/", $pqr->getFechaSolucion())[0];
        $hora = preg_split("/[\s,]+/", $pqr->getFechaSolucion())[1];

        $form->setTitle(TITULO_RESPONDER_ATENCION_MESA_AYUDA);
        $form->setId('frm_add_PQR');
        $form->setAction('?mod=' . $modulo . '&niv=1&task=saveAnswer' . '&id_element=' . $idPQR);
        $form->setMethod('post');

        $form->addEtiqueta(PQR_FECHA_SOLUCION);
        $form->addInputDate('date', 'txt_solucion', 'txt_solucion', $fecha, '%Y-%m-%d', '18', '18', '', 'pattern="' . PATTERN_FECHA . '" title="' . $html->traducirTildes(TITLE_FECHA) . '" required');

        $form->addEtiqueta(PQR_HORA_SOLUCION);
        $form->addInputText('time', 'txt_hora', 'txt_hora', '200', '200', $hora, '', 'autofocus pattern="' . PATTERN_ALFANUMERICO . '" title="' . $html->traducirTildes(TITLE_ALFANUMERICO) . '"');

        $form->addEtiqueta(PQR_DIAGNOSTICO);
        $form->addTextArea('text', 'txt_diagnostico', 'txt_diagnostico', '200', '200', $pqr->getDiagnostico(), null, 'autofocus pattern="' . PATTERN_ALFANUMERICO . '" title="' . $html->traducirTildes(TITLE_ALFANUMERICO) . '" required');

        $form->addEtiqueta(PQR_RESPUESTA);
        $form->addTextArea('text', 'txt_respúesta', 'txt_respúesta', '200', '200', $pqr->getRespuesta(), null, 'autofocus pattern="' . PATTERN_ALFANUMERICO . '" title="' . $html->traducirTildes(TITLE_ALFANUMERICO) . '" required');

        $form->addInputButton('submit', 'btn_enviar', 'btn_enviar', BTN_ACEPTAR, 'button', '');
        $form->addInputButton('button', 'cancel', 'cancel', BTN_CANCELAR, 'button', 'onclick="cancelarAccion(\'frm_add_PQR\',\'?mod=' . $modulo . '&niv=' . $niv . '\');"');

        $form->writeForm();

        break;
    /**
     * la variable saveAdd, permite almacenar el objeto beneficiario en la
     * base de datos @see \CBeneficiario
     */
    case 'saveAnswer':
        $idPQR = $_REQUEST['id_element'];
        $fechaSolucion = $_REQUEST['txt_solucion'] . ' ' . $_REQUEST['txt_hora'];
        $diagnostico = $_REQUEST['txt_diagnostico'];
        $respuesta = $_REQUEST['txt_respúesta'];

        $pqr = new CPQR($idPQR, null, null, $fechaSolucion, $diagnostico, $respuesta, null);

        $r = $daoPQR->answerPQR($pqr);
        $m = ERROR_RESPONDER_ATENCION_MESA_AYUDA;
        if ($r == 'true') {
            $m = EXITO_RESPONDER_ATENCION_MESA_AYUDA;
        }
        echo $html->generaAviso($m, "?mod=" . $modulo . "&niv=1");

        break;


    /**
     * la variable delete, permite hacer la carga del objeto beneficiario
     * y espera confirmacion de eliminarlo @see \CBeneficiario
     */
    case 'delete':
        $id_delete = $_REQUEST['id_element'];
        echo $html->generaAdvertencia(CONFIRMAR_BORRAR_PQR, '?mod=' . $modulo . '&niv=1&task=confirmDelete&id_element=' . $id_delete, 'onclick=location.href=\'?mod=' . $modulo . '&niv=1\'');
        break;

    /**
     * la variable confirmDelete, permite eliminar el objeto beneficiario de la
     * base de datos @see \CBeneficiario
     */
    case 'confirmDelete':
        $idBeneficiario = $_REQUEST['idBeneficiario'];
        $id_delete = $_REQUEST['id_element'];
        $r = $daoPQR->deletePQRById($id_delete);
        $m = ERROR_BORRAR_ATENCION_MESA_AYUDA;
        if ($r == 'true') {
            $m = EXITO_BORRAR_ATENCION_MESA_AYUDA;
        }
        echo $html->generaAviso($m, "?mod=" . $modulo . "&niv=1");
        break;

    /**
     * la variable edit, permite hacer la carga del objeto beneficiario y espera
     * confirmacion de edicion @see \CBeneficiario
     */
    case 'edit':
        $id_edit = $_REQUEST['id_element'];
        $pqr = $daoPQR->getPQRById($id_edit);
        $idBeneficiario = $_REQUEST['idBeneficiario'];

        $form = new CHtmlForm();

        $fecha = preg_split("/[\s,]+/", $pqr->getFechaReporte())[0];
        $hora = preg_split("/[\s,]+/", $pqr->getFechaReporte())[1];

        $form->setTitle(TITULO_EDITAR_ATENCION_MESA_AYUDA);
        $form->setId('frm_edit_PQR');
        $form->setAction('?mod=' . $modulo . '&niv=1&task=saveEdit&idBeneficiario=' . $idBeneficiario . '&id_element=' . $id_edit);
        $form->setMethod('post');

        $form->addEtiqueta(PQR_DESCRIPCION);
        $form->addTextArea('text', 'txt_descripcion', 'txt_descripcion', '200', '200', $pqr->getDescripcionRequerimiento(), null, 'autofocus pattern="' . PATTERN_ALFANUMERICO . '" title="' . $html->traducirTildes(TITLE_ALFANUMERICO) . '" required');

        $form->addEtiqueta(PQR_FECHA_REPORTE);
        $form->addInputDate('date', 'txt_fecha', 'txt_fecha', $fecha, '%Y-%m-%d', '18', '18', '', 'pattern="' . PATTERN_FECHA . '" title="' . $html->traducirTildes(TITLE_FECHA) . '" required');

        $form->addEtiqueta(PQR_HORA_REPORTE);
        $form->addInputText('time', 'txt_hora', 'txt_hora', '200', '200', $hora, '', 'autofocus pattern="' . PATTERN_ALFANUMERICO . '" title="' . $html->traducirTildes(TITLE_ALFANUMERICO) . '"');

        $form->addInputButton('submit', 'btn_enviar', 'btn_enviar', BTN_ACEPTAR, 'button', '');
        $form->addInputButton('button', 'cancel', 'cancel', BTN_CANCELAR, 'button', 'onclick="cancelarAccion(\'frm_edit_PQR\',\'?mod=' . $modulo . '&niv=' . $niv . '\');"');

        $form->writeForm();

        break;

    /**
     * la variable saveEdit, permite actualizar el objeto bodega en la base
     * de datos @see \CBeneficiario
     */
    case 'saveEdit':
        $id = $_REQUEST['id_element'];
        $descripcionRequerimiento = $_REQUEST['txt_descripcion'];
        $fechaReporte = $_REQUEST['txt_fecha'] . ' ' . $_REQUEST['txt_hora'];

        $pqr = new CPQR($id, $descripcionRequerimiento, $fechaReporte, null, null, null, $beneficiario);

        $r = $daoPQR->updatePQR($pqr);
        $m = ERROR_EDITAR_ATENCION_MESA_AYUDA;
        if ($r == 'true') {
            $m = EXITO_EDITAR_ATENCION_MESA_AYUDA;
        }
        echo $html->generaAviso($m, "?mod=" . $modulo . "&niv=1");

        break;

    /**
     * en caso de que la variable task no este definida carga la página
     * en construcción
     */
    default:
        include('templates/html/under.html');

        break;
}
