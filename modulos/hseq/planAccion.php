<?php

/**
 * Modulo Plan de Accion
 * Maneja el modulo plan de accion en union con CPlanAccion, CPlanAccionData
 *
 * @see \CPlanAccion
 * @see \CPlanAccionData
 *
 * @package modulos
 * @subpackage hseq
 * @author SERTIC SAS
 * @version 2014.12.13
 * @copyright SERTIC SAS
 */
//no permite el acceso directo
defined('_VALID_PRY') or die('Restricted access');
$daoPlanAccion = new CPlanAccionData($db);
$daoBasicas = new CBasicaData($db);
$daoActividades = new CActividadPlanAccionData($db);
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
        $condicion = "1";
        $periodo = $_REQUEST['txt_periodo'];
        if ($periodo != "") {
            $condicion .= " AND DATE_FORMAT(fechaInicio, '%Y-%m') = '" . $periodo . "'";
        }
        $idFuente = $_REQUEST['select_fuente'];
        if ($idFuente != "-1" && $idFuente != "") {
            $condicion .= " AND idFuente = " . $idFuente;
        }
        $estado = $_REQUEST['select_estado'];
        if ($estado != "-1" && $estado != "") {
            switch ($estado) {
                case "1":
                    $condicion .= " AND (SELECT sum(isnull(fechaCumplimiento))/count(idActividadPlanAccion) "
                            . "FROM actividad_plan_accion "
                            . "WHERE actividad_plan_accion.idPlanAccion = planes_accion.idPlanAccion) = 0";
                    break;

                case "2":
                    $condicion .= " AND fechaLimite < NOW()";
                    break;

                case "3":
                    $condicion .= " AND fechaLimite > NOW() AND "
                            . "isnull((SELECT sum(isnull(fechaCumplimiento))/count(idActividadPlanAccion) "
                            . "FROM actividad_plan_accion "
                            . "WHERE actividad_plan_accion.idPlanAccion = planes_accion.idPlanAccion))";
                    break;

                default:
                    break;
            }
        }

        $form = new CHtmlForm();
        $form->setTitle(TITULO_PLAN_ACCION);
        $form->setAction("?mod=" . $modulo . "&niv=" . $niv);
        $form->setMethod("post");
        $form->addEtiqueta(PERIODO_PARAFISCALES);
        $form->addInputText('month', 'txt_periodo', 'txt_periodo', '50', '50', $periodo, '', '');
        $fuentes = $daoBasicas->getBasicas('fuente_plan_accion');
        $opciones = null;
        if (isset($fuentes)) {
            foreach ($fuentes as $fuente) {
                $opciones[count($opciones)] = array('value' => $fuente->getId(),
                    'texto' => $fuente->getDescripcion());
            }
        }

        $form->addEtiqueta(PLAN_ACCION_FUENTE);
        $form->addSelect('select', 'select_fuente', 'select_fuente', $opciones, '', $idFuente, '', ' required');

        $opciones = null;
        $opciones[count($opciones)] = array('value' => '1', 'texto' => 'Cerrado');
        $opciones[count($opciones)] = array('value' => '2', 'texto' => 'Vencido');
        $opciones[count($opciones)] = array('value' => '3', 'texto' => 'Vigente');


        $form->addEtiqueta(ACTIVIDAD_PLAN_ACCION_ESTADO);
        $form->addSelect('select', 'select_estado', 'select_estado', $opciones, '', $estado, '', ' required');

        $form->addInputButton('submit', 'ok', 'ok', BTN_ACEPTAR, 'button', '');
        $form->addInputButton('button', 'export', 'export', BTN_EXPORTAR, 'button', 'onclick=location.href=\'modulos/hseq/planAccion_excel.php?idFuente=' . $idFuente . '&periodo=' . $periodo . '&estado=' . $estado . '\'');
        $form->writeForm();

        $dt = new CHtmlDataTable();
        $dt->setTitleTable(TITULO_PLAN_ACCION);
        $titulos = array(PLAN_ACCION_DESCRIPCION, PLAN_ACCION_FECHA_INICIO,
            PLAN_ACCION_CONSECUTIVO, PLAN_ACCION_FECHA_LIMITE,
            PLAN_ACCION_SOPORTE, PLAN_ACCION_USUARIO_RESPONSABLE,
            PLAN_ACCION_FUENTE, ACTIVIDAD_PLAN_ACCION_ESTADO);
        $planesAccion = $daoPlanAccion->getPlanesAccion($condicion);
        $dt->setTitleRow($titulos);
        $dt->setDataRows($planesAccion);
        $dt->setEditLink("?mod=" . $modulo . "&niv=" . $niv . "&task=edit");
        $dt->setDeleteLink("?mod=" . $modulo . "&niv=" . $niv . "&task=delete");
        $dt->setSeeLink("?mod=" . $modulo . "&niv=" . $niv . "&task=see");
        $dt->setAddLink("?mod=" . $modulo . "&niv=" . $niv . "&task=add");
        $dt->setType(1);
        $pag_crit = "";
        $dt->setPag(1, $pag_crit);
        $dt->writeDataTable($niv);

        break;
    /**
     * la variable add, permite hacer la carga la página con las variables 
     * que componen el objeto plan accion @see \CPlanAccionData
     */
    case 'add':
        $form = new CHtmlForm();
        $form->setTitle(TITULO_AGREGAR_PLAN_ACCION);
        $form->setId('frm_add_plan_accion');
        $form->setAction('?mod=' . $modulo . '&niv=1&task=saveAdd');
        $form->setMethod('post');

        $fuentes = $daoBasicas->getBasicas('fuente_plan_accion');
        $opciones = null;
        if (isset($fuentes)) {
            foreach ($fuentes as $fuente) {
                $opciones[count($opciones)] = array('value' => $fuente->getId(),
                    'texto' => $fuente->getDescripcion());
            }
        }

        $form->addEtiqueta(PLAN_ACCION_FUENTE);
        $form->addSelect('select', 'select_fuente', 'select_fuente', $opciones, '', '', '', ' required');

        $usuarios = $daoPlanAccion->getUsuarios();
        $opciones = null;
        if (isset($usuarios)) {
            foreach ($usuarios as $usuario) {
                $opciones[count($opciones)] = array('value' => $usuario->getId(),
                    'texto' => $usuario->getDescripcion());
            }
        }

        $form->addEtiqueta(PLAN_ACCION_USUARIO_RESPONSABLE);
        $form->addSelect('select', 'select_usuario', 'select_usuario', $opciones, '', '', '', ' required');

        $form->addEtiqueta(PLAN_ACCION_DESCRIPCION);
        $form->addTextArea('textarea', 'txt_descripcion', 'txt_descripcion', 100, 5, '', '', ' title="' . $html->traducirTildes(TITLE_ALFANUMERICO) . '" required');

        $form->addEtiqueta(PLAN_ACCION_FECHA_INICIO);
        $form->addInputDate('date', 'txt_fecha_inicio', 'txt_fecha_inicio', '', '%Y-%m-%d', '16', '16', '', 'pattern="' . PATTERN_FECHA . '" title="' . $html->traducirTildes(TITLE_FECHA) . '" required');

        $form->addEtiqueta(PLAN_ACCION_CONSECUTIVO);
        $form->addInputText('text', 'txt_codigo_consecutivo', 'txt_codigo_consecutivo', '200', '200', '', '', 'pattern="' . PATTERN_NUMEROS . '" title="' . $html->traducirTildes(TITLE_NUMEROS) . '" required');

        $form->addEtiqueta(PLAN_ACCION_FECHA_LIMITE);
        $form->addInputDate('date', 'txt_fecha_limite', 'txt_fecha_limite', '', '%Y-%m-%d', '16', '16', '', 'pattern="' . PATTERN_FECHA . '" title="' . $html->traducirTildes(TITLE_FECHA) . '" required');

        $form->addEtiqueta(PLAN_ACCION_SOPORTE);
        $form->addInputFile('file', 'file_archivo', 'file_archivo', '25', 'file', ' required');

        $form->addInputButton('submit', 'btn_enviar', 'btn_enviar', BTN_ACEPTAR, 'button', '');
        $form->addInputButton('button', 'cancel', 'cancel', BTN_CANCELAR, 'button', 'onclick="cancelarAccion(\'frm_add_plan_accion\',\'?mod=' . $modulo . '&niv=' . $niv . '\');"');

        $form->writeForm();

        break;
    /**
     * la variable saveAdd, permite almacenar el objeto plan accion en la 
     * base de datos @see \CPlanAccionData
     */
    case 'saveAdd':
        $descripcion = $_REQUEST['txt_descripcion'];
        $fechaInicio = $_REQUEST['txt_fecha_inicio'];
        $consecutivo = $_REQUEST['txt_codigo_consecutivo'];
        $fechaLimite = $_REQUEST['txt_fecha_limite'];
        $soporte = $_FILES['file_archivo'];
        $usuario = $_REQUEST['select_usuario'];
        $fuente = $_REQUEST['select_fuente'];

        $planAccion = new CPlanAccion(NULL, $descripcion, $fechaInicio, $consecutivo, $fechaLimite, $soporte, $usuario, $fuente);

        $r = $daoPlanAccion->insertPlanAccion($planAccion);
        $m = ERROR_AGREGAR_PLAN_ACCION;
        if ($r == "true") {
            $m = EXITO_AGREGAR_PLAN_ACCION;
        }
        echo $html->generaAviso($m, "?mod=" . $modulo . "&niv=1&task=list");

        break;
    /**
     * la variable delete, permite hacer la carga del objeto plan accion 
     * y espera confirmacion de eliminarlo @see \CPlanAccionData
     */
    case 'delete':
        $id_delete = $_REQUEST['id_element'];
        echo $html->generaAdvertencia(CONFIRMAR_BORRAR_PLAN_ACCION, '?mod=' . $modulo . '&niv=1&task=confirmDelete&id_element=' . $id_delete, 'onclick=location.href=\'?mod=' . $modulo . '&niv=1\'');
        break;
    /**
     * la variable confirmDelete, permite eliminar el objeto plan accion de la 
     * base de datos @see \CPlanAccionData
     */
    case 'confirmDelete':
        $id_delete = $_REQUEST['id_element'];
        $r = $daoPlanAccion->deletePlanAccionById($id_delete);
        $m = ERROR_BORRAR_PLAN_ACCION;
        if ($r == 'true') {
            $m = EXITO_BORRAR_PLAN_ACCION;
        }
        echo $html->generaAviso($m, "?mod=" . $modulo . "&niv=1&task=list");
        break;

    /**
     * la variable edit, permite hacer la carga del objeto plan accion y espera 
     * confirmacion de edicion @see \CPlanAccionData
     */
    case 'edit':
        $id_edit = $_REQUEST['id_element'];
        $planAccion = $daoPlanAccion->getPlanAccionById($id_edit);
        $form = new CHtmlForm();
        $form->setTitle(TITULO_EDITAR_PLAN_ACCION);
        $form->setId('frm_edit_plan_accion');
        $form->setAction('?mod=' . $modulo . '&niv=1&task=saveEdit&id=' . $id_edit);
        $form->setMethod('post');

        $fuentes = $daoBasicas->getBasicas('fuente_plan_accion');
        $opciones = null;
        if (isset($fuentes)) {
            foreach ($fuentes as $fuente) {
                $opciones[count($opciones)] = array('value' => $fuente->getId(),
                    'texto' => $fuente->getDescripcion());
            }
        }

        $form->addEtiqueta(PLAN_ACCION_FUENTE);
        $form->addSelect('select', 'select_fuente', 'select_fuente', $opciones, '', $planAccion->getFuente(), '', ' required');

        $usuarios = $daoPlanAccion->getUsuarios();
        $opciones = null;
        if (isset($usuarios)) {
            foreach ($usuarios as $usuario) {
                $opciones[count($opciones)] = array('value' => $usuario->getId(),
                    'texto' => $usuario->getDescripcion());
            }
        }

        $form->addEtiqueta(PLAN_ACCION_USUARIO_RESPONSABLE);
        $form->addSelect('select', 'select_usuario', 'select_usuario', $opciones, '', $planAccion->getUsuario(), '', ' required');

        $form->addEtiqueta(PLAN_ACCION_DESCRIPCION);
        $form->addTextArea('textarea', 'txt_descripcion', 'txt_descripcion', 100, 5, $planAccion->getDescripcion(), '', ' title="' . $html->traducirTildes(TITLE_ALFANUMERICO) . '" required');

        $form->addEtiqueta(PLAN_ACCION_FECHA_INICIO);
        $form->addInputDate('date', 'txt_fecha_inicio', 'txt_fecha_inicio', $planAccion->getFechaInicio(), '%Y-%m-%d', '16', '16', '', 'pattern="' . PATTERN_FECHA . '" title="' . $html->traducirTildes(TITLE_FECHA) . '" required');

        $form->addEtiqueta(PLAN_ACCION_CONSECUTIVO);
        $form->addInputText('text', 'txt_codigo_consecutivo', 'txt_codigo_consecutivo', '200', '200', $planAccion->getConsecutivo(), '', 'pattern="' . PATTERN_NUMEROS . '" title="' . $html->traducirTildes(TITLE_NUMEROS) . '" required');

        $form->addEtiqueta(PLAN_ACCION_FECHA_LIMITE);
        $form->addInputDate('date', 'txt_fecha_limite', 'txt_fecha_limite', $planAccion->getFechaLimite(), '%Y-%m-%d', '16', '16', '', 'pattern="' . PATTERN_FECHA . '" title="' . $html->traducirTildes(TITLE_FECHA) . '" required');

        $form->addEtiqueta(PLAN_ACCION_SOPORTE);
        $form->addInputFile('file', 'file_archivo', 'file_archivo', '25', 'file', '');

        $form->addInputButton('submit', 'btn_enviar', 'btn_enviar', BTN_ACEPTAR, 'button', '');
        $form->addInputButton('button', 'cancel', 'cancel', BTN_CANCELAR, 'button', 'onclick="cancelarAccion(\'frm_edit_plan_accion\',\'?mod=' . $modulo . '&niv=' . $niv . '\');"');

        $form->writeForm();

        break;

    /**
     * la variable saveEdit, permite actualizar el objeto plan accion en la base 
     * de datos @see \CPlanAccionData
     */
    case 'saveEdit':
        $id = $_REQUEST['id'];
        $descripcion = $_REQUEST['txt_descripcion'];
        $fechaInicio = $_REQUEST['txt_fecha_inicio'];
        $consecutivo = $_REQUEST['txt_codigo_consecutivo'];
        $fechaLimite = $_REQUEST['txt_fecha_limite'];
        $soporte = $_FILES['file_archivo'];
        $usuario = $_REQUEST['select_usuario'];
        $fuente = $_REQUEST['select_fuente'];

        $planAccion = new CPlanAccion($id, $descripcion, $fechaInicio, $consecutivo, $fechaLimite, $soporte, $usuario, $fuente);

        $r = $daoPlanAccion->updatePlanAccion($planAccion);
        $m = ERROR_EDITAR_PLAN_ACCION;
        if ($r == 'true') {
            $m = EXITO_EDITAR_PLAN_ACCION;
        }
        echo $html->generaAviso($m, "?mod=" . $modulo . "&niv=1&task=list");

        break;

    /**
     * la variable see, permite ver el detalle de un plan de accion accediendo
     * a la clase @see \CActividadPlanAccionData
     */
    case 'see':
        $idPlanAccion = $_REQUEST['id_element'];
        $form = new CHtmlForm();
        $form->setTitle(TITULO_ACTIVIDAD_PLAN_ACCION);
        $form->setAction("?mod=" . $modulo . "&niv=" . $niv);
        $form->addInputButton('submit', 'ok', 'ok', BTN_ATRAS, 'button', '');
        $form->setOptions('autoClean', false);
        $form->setMethod("post");
        $form->writeForm();

        $dt = new CHtmlDataTable();
        $dt->setTitleTable(TITULO_ACTIVIDAD_PLAN_ACCION);
        $titulos = array(ACTIVIDAD_PLAN_ACCION_DESCRIPCION,
            ACTIVIDAD_PLAN_ACCION_FECHA,
            ACTIVIDAD_PLAN_ACCION_RECURSOS,
            ACTIVIDAD_PLAN_ACCION_FECHA_CUMPLIMIENTO,
            ACTIVIDAD_PLAN_ACCION_SOPORTE,
            ACTIVIDAD_PLAN_ACCION_USUARIO,
            ACTIVIDAD_PLAN_ACCION_ESTADO);
        $actividadesPlanesAccion = $daoActividades->getActividadesByPlanAccion($idPlanAccion);
        $dt->setTitleRow($titulos);
        $dt->setDataRows($actividadesPlanesAccion);
        $dt->setEditLink("?mod=" . $modulo . "&niv=" . $niv . "&task=editActividad&idPlan=" . $idPlanAccion);
        $dt->setDeleteLink("?mod=" . $modulo . "&niv=" . $niv . "&task=deleteActividad&idPlan=" . $idPlanAccion);
        $dt->setAddLink("?mod=" . $modulo . "&niv=" . $niv . "&task=addActividad&idPlan=" . $idPlanAccion);
        $otros = array('link' => "?mod=" . $modulo . "&niv=" . $niv . "&task=answer&idPlan=" . $idPlanAccion, 'img' => 'marcado.gif', 'alt' => "Completar");
        $dt->addOtrosLink($otros);
        $dt->setType(1);
        $pag_crit = "";
        $dt->setPag(1, $pag_crit);
        $dt->writeDataTable($niv);
        break;

    /**
     * la variable add, permite hacer la carga la página con las variables 
     * que componen el objeto actividad plan accion @see \CActividadPlanAccion
     */
    case 'addActividad':
        $idPlan = $_REQUEST['idPlan'];
        $form = new CHtmlForm();
        $form->setTitle(TITULO_AGREGAR_ACTIVIDAD_PLAN_ACCION);
        $form->setId('frm_edit_plan_accion');
        $form->setAction('?mod=' . $modulo . '&niv=1&task=saveAddActividad&idPlan=' . $idPlan);
        $form->setMethod('post');

        $usuarios = $daoPlanAccion->getUsuarios();
        $opciones = null;
        if (isset($usuarios)) {
            foreach ($usuarios as $usuario) {
                $opciones[count($opciones)] = array('value' => $usuario->getId(),
                    'texto' => $usuario->getDescripcion());
            }
        }

        $form->addEtiqueta(ACTIVIDAD_PLAN_ACCION_USUARIO);
        $form->addSelect('select', 'select_usuario', 'select_usuario', $opciones, '', '', '', ' required');

        $form->addEtiqueta(ACTIVIDAD_PLAN_ACCION_DESCRIPCION);
        $form->addTextArea('textarea', 'txt_descripcion', 'txt_descripcion', 100, 5, '', '', ' title="' . $html->traducirTildes(TITLE_ALFANUMERICO) . '" required');

        $form->addEtiqueta(ACTIVIDAD_PLAN_ACCION_FECHA);
        $form->addInputDate('date', 'txt_fecha', 'txt_fecha', '', '%Y-%m-%d', '16', '16', '', 'pattern="' . PATTERN_FECHA . '" title="' . $html->traducirTildes(TITLE_FECHA) . '" required');

        $form->addEtiqueta(ACTIVIDAD_PLAN_ACCION_RECURSOS);
        $form->addTextArea('textarea', 'txt_recursos', 'txt_recursos', 100, 5, '', '', ' title="' . $html->traducirTildes(TITLE_ALFANUMERICO) . '" required');

        $form->addInputButton('submit', 'btn_enviar', 'btn_enviar', BTN_ACEPTAR, 'button', '');
        $form->addInputButton('button', 'cancel', 'cancel', BTN_CANCELAR, 'button', 'onclick="cancelarAccion(\'frm_edit_plan_accion\',\'?mod=' . $modulo . '&niv=' . $niv . '&id_element=' . $idPlan . '&task=see\');"');

        $form->writeForm();
        break;

    /**
     * la variable saveEdit, permite actualizar el objeto actividad 
     * plan accion en la base de datos @see \CActividadBitacoraData
     */
    case 'saveAddActividad':
        $usuario = $_REQUEST['select_usuario'];
        $descripcion = $_REQUEST['txt_descripcion'];
        $fecha = $_REQUEST['txt_fecha'];
        $recursos = $_REQUEST['txt_recursos'];
        $planAccion = $_REQUEST['idPlan'];

        $actividad = new CActividadPlanAccion(NULL, $descripcion, $fecha, $recursos, $planAccion, $usuario);

        $r = $daoActividades->insertActividad($actividad);
        $m = ERROR_AGREGAR_ACTIVIDAD_PLAN_ACCION;
        if ($r == 'true') {
            $m = EXITO_AGREGAR_ACTIVIDAD_PLAN_ACCION;
        }
        echo $html->generaAviso($m, "?mod=" . $modulo . "&niv=1&task=see&id_element=" . $planAccion);

        break;

    /**
     * la variable delete, permite hacer la carga del objeto plan accion 
     * y espera confirmacion de eliminarlo @see \CPlanAccionData
     */
    case 'deleteActividad':
        $idPlan = $_REQUEST['idPlan'];
        $id_delete = $_REQUEST['id_element'];
        echo $html->generaAdvertencia(CONFIRMAR_BORRAR_ACTIVIDAD_PLAN_ACCION, '?mod=' . $modulo . '&idPlan=' . $idPlan . '&niv=1&task=confirmDeleteActividad&id_element=' . $id_delete, 'onclick=location.href=\'?mod=' . $modulo . '&niv=1\'');
        break;

    /**
     * la variable confirmDelete, permite eliminar el objeto actividad 
     * plan accion de la base de datos @see \CActividadBitacoraData
     */
    case 'confirmDeleteActividad':
        $idPlan = $_REQUEST['idPlan'];
        $id_delete = $_REQUEST['id_element'];
        $r = $daoActividades->deletePlanAccionById($id_delete);
        $m = ERROR_BORRAR_ACTIVIDAD_PLAN_ACCION;
        if ($r == 'true') {
            $m = EXITO_BORRAR_ACTIVIDAD_PLAN_ACCION;
        }
        echo $html->generaAviso($m, "?mod=" . $modulo . "&niv=1&task=see&id_element=" . $idPlan);
        break;

    /**
     * la variable add, permite hacer la carga la página con las variables 
     * que componen el objeto actividad plan accion @see \CActividadPlanAccion
     */
    case 'editActividad':
        $idPlan = $_REQUEST['idPlan'];
        $idActividad = $_REQUEST['id_element'];
        $actividad = $daoActividades->getActividadPlanAccionById($idActividad);
        $form = new CHtmlForm();
        $form->setTitle(TITULO_AGREGAR_ACTIVIDAD_PLAN_ACCION);
        $form->setId('frm_edit_plan_accion');
        $form->setAction('?mod=' . $modulo . '&niv=1&task=saveEditActividad&idPlan=' . $idPlan . '&idActividad=' . $idActividad);
        $form->setMethod('post');

        $usuarios = $daoPlanAccion->getUsuarios();
        $opciones = null;
        if (isset($usuarios)) {
            foreach ($usuarios as $usuario) {
                $opciones[count($opciones)] = array('value' => $usuario->getId(),
                    'texto' => $usuario->getDescripcion());
            }
        }

        $form->addEtiqueta(ACTIVIDAD_PLAN_ACCION_USUARIO);
        $form->addSelect('select', 'select_usuario', 'select_usuario', $opciones, '', $actividad->getUsuario(), '', ' required');

        $form->addEtiqueta(ACTIVIDAD_PLAN_ACCION_DESCRIPCION);
        $form->addTextArea('textarea', 'txt_descripcion', 'txt_descripcion', 100, 5, $actividad->getDescripcion(), '', ' title="' . $html->traducirTildes(TITLE_ALFANUMERICO) . '" required');

        $form->addEtiqueta(ACTIVIDAD_PLAN_ACCION_FECHA);
        $form->addInputDate('date', 'txt_fecha', 'txt_fecha', $actividad->getFecha(), '%Y-%m-%d', '16', '16', '', 'pattern="' . PATTERN_FECHA . '" title="' . $html->traducirTildes(TITLE_FECHA) . '" required');

        $form->addEtiqueta(ACTIVIDAD_PLAN_ACCION_RECURSOS);
        $form->addTextArea('textarea', 'txt_recursos', 'txt_recursos', 100, 5, $actividad->getRecursos(), '', ' title="' . $html->traducirTildes(TITLE_ALFANUMERICO) . '" required');

        $form->addInputButton('submit', 'btn_enviar', 'btn_enviar', BTN_ACEPTAR, 'button', '');
        $form->addInputButton('button', 'cancel', 'cancel', BTN_CANCELAR, 'button', 'onclick="cancelarAccion(\'frm_edit_plan_accion\',\'?mod=' . $modulo . '&niv=' . $niv . '&id_element=' . $idPlan . '&task=see\');"');

        $form->writeForm();
        break;

    /**
     * la variable saveEdit, permite actualizar el objeto actividad 
     * plan accion en la base de datos @see \CActividadBitacoraData
     */
    case 'saveEditActividad':
        $idActividad = $_REQUEST['idActividad'];
        $usuario = $_REQUEST['select_usuario'];
        $descripcion = $_REQUEST['txt_descripcion'];
        $fecha = $_REQUEST['txt_fecha'];
        $recursos = $_REQUEST['txt_recursos'];
        $planAccion = $_REQUEST['idPlan'];

        $actividad = new CActividadPlanAccion($idActividad, $descripcion, $fecha, $recursos, $planAccion, $usuario);

        $r = $daoActividades->updatePlanAccion($actividad);
        $m = ERROR_EDITAR_ACTIVIDAD_PLAN_ACCION;
        if ($r == 'true') {
            $m = EXITO_EDITAR_ACTIVIDAD_PLAN_ACCION;
        }
        echo $html->generaAviso($m, "?mod=" . $modulo . "&niv=1&task=see&id_element=" . $planAccion);

        break;

    /**
     * la variable answer, permite actualizar el objeto actividad 
     * plan accion en la base de datos @see \CActividadBitacoraData
     */
    case 'answer':
        $idPlan = $_REQUEST['idPlan'];
        $idActividad = $_REQUEST['id_element'];
        $actividad = $daoActividades->getActividadPlanAccionById($idActividad);
        $form = new CHtmlForm();
        $form->setTitle(TITULO_RESPONDER_ACTIVIDAD_PLAN_ACCION);
        $form->setId('frm_edit_plan_accion');
        $form->setAction('?mod=' . $modulo . '&niv=1&task=saveAnswer&idPlan=' . $idPlan . '&idActividad=' . $idActividad);
        $form->setMethod('post');

        $form->addEtiqueta(ACTIVIDAD_PLAN_ACCION_FECHA_CUMPLIMIENTO);
        $form->addInputDate('date', 'txt_fecha_limite', 'txt_fecha_limite', $actividad->getFechaCumplimiento(), '%Y-%m-%d', '16', '16', '', 'pattern="' . PATTERN_FECHA . '" title="' . $html->traducirTildes(TITLE_FECHA) . '" required');

        $form->addEtiqueta(ACTIVIDAD_PLAN_ACCION_SOPORTE);
        $form->addInputFile('file', 'file_archivo', 'file_archivo', '25', 'file', ' required');

        $form->addInputButton('submit', 'btn_enviar', 'btn_enviar', BTN_ACEPTAR, 'button', '');
        $form->addInputButton('button', 'cancel', 'cancel', BTN_CANCELAR, 'button', 'onclick="cancelarAccion(\'frm_edit_plan_accion\',\'?mod=' . $modulo . '&niv=' . $niv . '&id_element=' . $idPlan . '&task=see\');"');

        $form->writeForm();
        break;

    /**
     * la variable saveAnswer, permite actualizar el objeto actividad 
     * plan accion en la base de datos @see \CActividadBitacoraData
     */
    case 'saveAnswer':
        $idPlan = $_REQUEST['idPlan'];
        $idActividad = $_REQUEST['idActividad'];
        $fechaLimite = $_REQUEST['txt_fecha_limite'];
        $archivo = $_FILES['file_archivo'];

        $actividad = new CActividadPlanAccion($idActividad, null, null, null, $idPlan, NULL, $fechaLimite, $archivo);

        $r = $daoActividades->answerPlanAccion($actividad);
        $m = ERROR_RESPONDER_ACTIVIDAD_PLAN_ACCION;
        if ($r == 'true') {
            $m = EXITO_RESPONDER_ACTIVIDAD_PLAN_ACCION;
        }
        echo $html->generaAviso($m, "?mod=" . $modulo . "&niv=1&task=see&id_element=" . $idPlan);
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

