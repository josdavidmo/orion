<?php

/**
 * Modulo Autoconrol
 * Maneja el modulo autocontrol en union con CBasicaRelacionadaData, 
 * CPlaneacionAutocontrolData, CPlaneacionAutocontrol, CBasicaRelacionada
 *
 * @see \CBasicaRelacionadaData
 * @see \CPlaneacionAutocontrolData
 * @see \CPlaneacionAutocontrol
 * @see \CBasicaRelacionada
 *
 * @package modulos
 * @subpackage bitacora
 * @author SERTIC SAS
 * @version 2014.11.14
 * @copyright SERTIC SAS
 */
//no permite el acceso directo
defined('_VALID_PRY') or die('Restricted access');
$daoUsuarios = new CUserData($db);
$daoAutocontrol = new CPlaneacionAutocontrolData($db);
$daoBasicas = new CBasicaData($db);
$daoBasica = new CBasicaRelacionadaData($db);
$task = $_REQUEST['task'];
$modulo = $_REQUEST['mod'];
$niv = $_REQUEST['niv'];
if (empty($task)) {
    $task = 'list';
}

switch ($task) {
    /**
     * la variable list, permite hacer la carga la página con la lista de 
     * objetos usuarios según los parámetros de entrada
     */
    case 'list':
        $usuario = $daoUsuarios->getUserById($id_usuario);
        $form = new CHtmlForm();
        $form->setTitle(TITULO_PLANEACION_AUTOCONTROL_PERSONAL . " " . $usuario['usu_nombre'] . " " . $usuario['usu_apellido']);
        $form->setMethod('post');
        $form->setOptions('autoClean', false);
        $form->writeForm();
        $dt = new CHtmlDataTable();
        $dt->setTitleTable(TITULO_PLANEACION_AUTOCONTROL_PERSONAL . " " . $usuario['usu_nombre'] . " " . $usuario['usu_apellido']);
        $titulos = array(AUTOCONTROL_OBJETIVOS, AUTOCONTROL_RESPONSABLE_PNC, AUTOCONTROL_RESPONSABLE);
        $bitacoras = $daoAutocontrol->getPlaneacionAutocontrolByResponsablePNC($id_usuario);
        $dt->setTitleRow($titulos);
        $dt->setDataRows($bitacoras);
        $dt->setSeeLink("?mod=" . $modulo . "&niv=" . $niv . "&task=seeAutocontrol");
        $dt->setType(1);
        $pag_crit = "";
        $dt->setPag(1, $pag_crit);
        $dt->writeDataTable($niv);

        $form = new CHtmlForm();
        $form->setTitle(TITULO_PLANEACION_CONTROL_PERSONAL . " " . $usuario['usu_nombre'] . " " . $usuario['usu_apellido']);
        $form->setMethod('post');
        $form->setOptions('autoClean', false);
        $form->writeForm();
        $dt = new CHtmlDataTable();
        $dt->setTitleTable(TITULO_PLANEACION_CONTROL_PERSONAL . " " . $usuario['usu_nombre'] . " " . $usuario['usu_apellido']);
        $titulos = array(CONTROL_OBLIGACIONES, CONTROL_VERIFICACION, CONTROL_NUMERO_DOCUMENTO_CONTRACTUAL);
        $controles = $daoAutocontrol->getPlaneacionControlByResponsable($id_usuario);
        $dt->setTitleRow($titulos);
        $dt->setDataRows($controles);
        $dt->setSeeLink("?mod=" . $modulo . "&niv=" . $niv . "&task=seeControl&idResponsable=" . $id);
        $dt->setType(1);
        $pag_crit = "";
        $dt->setPag(1, $pag_crit);
        $dt->writeDataTable($niv);
        break;
    
    /**
     * la variable seeAutocontrol, permite hacer la carga la página con la 
     * lista de objetos autocontrol según los parámetros de entrada
     */
    case 'seeAutocontrol':
        $idAutocontrol = $_REQUEST['id_element'];
        $form = new CHtmlForm();
        $form->setTitle(TITULO_DETALLE_AUTOCONTROL);
        $form->setMethod('post');
        $form->setOptions('autoClean', false);
        $form->setAction('?mod=' . $modulo . '&niv=' . $niv);
        $form->addInputButton('submit', 'ok', 'ok', BTN_ATRAS, 'button', '');
        $form->writeForm();

        $dt = new CHtmlDataTable();
        $dt->setTitleTable(AUTOCONTROL_ACTIVIDADES);
        $titulos = array(DESCRIPCION_DETALLE_AUTOCONTROL);
        $condicion = "idPlaneacionAutocontrol = " . $idAutocontrol;
        $datos = $daoBasica->getBasicas('actividadescontrol', $condicion);
        $dt->setTitleRow($titulos);
        $dt->setDataRows($datos);
        $dt->setSeeLink("?mod=" . $modulo . "&niv=" . $niv . "&task=seeActividad&idAutocontrol=" . $idAutocontrol);
        $dt->setType(1);
        $pag_crit = "";
        $dt->setPag(1, $pag_crit);
        $dt->writeDataTable($niv);
        break;
    
    case 'seeActividad':
        $idAutocontrol = $_REQUEST['idAutocontrol'];
        $id_element = $_REQUEST['id_element'];
        $form = new CHtmlForm();
        $form->setTitle(TITULO_AUTOCONTROL_OBSERVACIONES);
        $form->setMethod('post');
        $form->setOptions('autoClean', false);
        $form->setAction('?mod=' . $modulo . '&niv=' . $niv . '&task=seeAutocontrol&id_element=' . $idAutocontrol);
        $form->addInputButton('submit', 'ok', 'ok', BTN_ATRAS, 'button', '');
        $form->writeForm();
        $dt = new CHtmlDataTable();
        $dt->setTitleTable(TITULO_AUTOCONTROL_OBSERVACIONES);
        $titulos = array(AUTOCONTROL_OBSERVACIONES_PERIODO, AUTOCONTROL_OBSERVACIONES_DESCRIPCION, AUTOCONTROL_OBSERVACIONES_ESTADO);
        $observaciones = $daoAutocontrol->getObservacionesByIdActividad($id_element);
        $dt->setTitleRow($titulos);
        $dt->setDataRows($observaciones);
        $dt->setEditLink("?mod=" . $modulo . "&niv=" . $niv . "&task=edit" . "&id=" . $id_element . "&from=seeActividad&idAutocontrol=$idAutocontrol");
        $dt->setDeleteLink("?mod=" . $modulo . "&niv=" . $niv . "&task=delete" . "&id=" . $id_element . "&from=seeActividad&idAutocontrol=$idAutocontrol");
        $dt->setAddLink("?mod=" . $modulo . "&niv=" . $niv . "&task=add" . "&id=" . $id_element . "&from=seeActividad&idAutocontrol=$idAutocontrol");
        $dt->setType(1);
        $pag_crit = "";
        $dt->setPag(1, $pag_crit);
        $dt->writeDataTable($niv);
        break;

    case 'seeControl':
        $id_element = $_REQUEST['id_element'];
        $form = new CHtmlForm();
        $form->setTitle(TITULO_AUTOCONTROL_OBSERVACIONES);
        $form->setMethod('post');
        $form->setOptions('autoClean', false);
        $form->setAction('?mod=' . $modulo . '&niv=' . $niv);
        $form->addInputButton('submit', 'ok', 'ok', BTN_ATRAS, 'button', '');
        $form->writeForm();
        $dt = new CHtmlDataTable();
        $dt->setTitleTable(TITULO_AUTOCONTROL_OBSERVACIONES);
        $titulos = array(AUTOCONTROL_OBSERVACIONES_PERIODO, AUTOCONTROL_OBSERVACIONES_DESCRIPCION, AUTOCONTROL_OBSERVACIONES_ESTADO);
        $observaciones = $daoAutocontrol->getObservacionesByIdControl($id_element);
        $dt->setTitleRow($titulos);
        $dt->setDataRows($observaciones);
        $dt->setEditLink("?mod=" . $modulo . "&niv=" . $niv . "&task=edit" . "&id=" . $id_element . "&from=seeControl");
        $dt->setDeleteLink("?mod=" . $modulo . "&niv=" . $niv . "&task=delete" . "&id=" . $id_element . "&from=seeControl");
        $dt->setAddLink("?mod=" . $modulo . "&niv=" . $niv . "&task=add" . "&id=" . $id_element . "&from=seeControl");
        $dt->setType(1);
        $pag_crit = "";
        $dt->setPag(1, $pag_crit);
        $dt->writeDataTable($niv);
        break;


    /**
     * la variable add, permite hacer la carga la página con las variables 
     * que componen el objeto autocontrol @see \CPlaneacionAutocontrol
     */
    case 'add':
        $idAutocontrol = $_REQUEST['idAutocontrol'];
        $from = $_REQUEST['from'];
        $id = $_REQUEST['id'];
        $form = new CHtmlForm();
        $form->setTitle(TITULO_AGREGAR_AUTOCONTROL_OBSERVACION);
        $form->setId('frm_add_observacion');
        $form->setAction('?mod=' . $modulo . '&niv=1&task=saveAdd&id=' . $id . '&from=' . $from . '&idAutocontrol=' . $idAutocontrol);
        $form->setMethod('post');
        $form->setClassEtiquetas('td_label');
        $form->setTableId('frm_add_autocontrol');

        $form->addEtiqueta(AUTOCONTROL_OBSERVACIONES_PERIODO);
        $form->addInputText('month', 'txt_periodo', 'txt_periodo', '50', '50', '', '', 'required');

        $form->addEtiqueta(AUTOCONTROL_OBSERVACIONES_DESCRIPCION);
        $form->addTextArea('textarea', 'txt_descripcion', 'txt_descripcion', '100', '5', '', '', ' title="' . $html->traducirTildes(TITLE_ALFANUMERICO) . '" required');

        $tipos = $daoBasicas->getBasicas("tipoobservacion");
        $opciones = null;
        if (isset($tipos)) {
            foreach ($tipos as $tipo) {
                $opciones[count($opciones)] = array('value' => $tipo->getId(),
                    'texto' => $tipo->getDescripcion());
            }
        }

        $form->addEtiqueta(AUTOCONTROL_OBSERVACIONES_ESTADO);
        $form->addSelect('select', 'sel_estado', 'sel_estado', $opciones, '', '', '', ' required');

        $form->addInputButton('submit', 'btn_enviar', 'btn_enviar', BTN_ACEPTAR, 'button', '');
        $form->addInputButton('button', 'cancel', 'cancel', BTN_CANCELAR, 'button', 'onclick="cancelarAccion(\'frm_add_observacion\',\'?mod=' . $modulo . '&niv=' . $niv . '&id_element=' . $id . '&task=' . $from . '\');"');

        $form->writeForm();

        break;
    /**
     * la variable saveAdd, permite almacenar el objeto bitacora en la 
     * base de datos @see \CBitacora
     */
    case 'saveAdd':
        $from = $_REQUEST['from'];
        $periodo = $_REQUEST['txt_periodo'] . '-00';
        $descripcion = $_REQUEST['txt_descripcion'];
        $actividad = null;
        $control = null;
        $autocontrol = null;
        if ($from == 'seeActividad') {
            $autocontrol = $_REQUEST['idAutocontrol'];
            $actividad = $_REQUEST['id'];
        } else {
            $control = $_REQUEST['id'];
        }
        $estado = $_REQUEST['sel_estado'];

        $observacion = new CObservaciones(null, $periodo, $descripcion, $actividad, $estado, $control);

        $r = $daoAutocontrol->insertObservacion($observacion);
        $m = ERROR_AGREGAR_AUTOCONTROL_OBSERVACIONES;
        if ($r == 'true') {
            $m = EXITO_AGREGAR_AUTOCONTROL_OBSERVACIONES;
        }
        if ($autocontrol == null) {
            echo $html->generaAviso($m, "?mod=" . $modulo . "&niv=1&task=" . $from . "&id_element=" . $control);
        } else {
            echo $html->generaAviso($m, "?mod=" . $modulo . "&niv=1&task=" . $from . "&id_element=" . $actividad . "&idAutocontrol=" . $autocontrol);
        }
        break;
    /**
     * la variable delete, permite hacer la carga del objeto autocontrol 
     * y espera confirmacion de eliminarlo @see \CPlaneacionAutocontrol
     */
    case 'delete':
        $from = $_REQUEST['from'];
        $id_delete = $_REQUEST['id_element'];
        $idAutocontrol = $_REQUEST['id'];
        $autocontrol = $_REQUEST['idAutocontrol'];
        echo $html->generaAdvertencia(CONFIRMAR_BORRAR_AUTOCONTROL_OBSERVACIONES, '?mod=' . $modulo . '&niv=1&task=confirmDelete&id_element=' . $id_delete . '&id=' . $idAutocontrol . '&from=' . $from . "&idAutocontrol=" .$autocontrol, 'onclick=location.href=\'?mod=' . $modulo . '&niv=1&id_element=' . $idAutocontrol . '&task=' . $from . '&idAutocontrol=' . $autocontrol . '\'');
        break;

    /**
     * la variable confirmDelete, permite eliminar el objeto autocontrol 
     * de la base de datos @see \CPlaneacionAutocontrol
     */
    case 'confirmDelete':
        $autocontrol = $_REQUEST['idAutocontrol'];
        $from = $_REQUEST['from'];
        $idAutocontrol = $_REQUEST['id'];
        $id_delete = $_REQUEST['id_element'];
        $r = $daoAutocontrol->deleteObservacionById($id_delete);
        $m = ERROR_BORRAR_AUTOCONTROL_OBSERVACIONES;
        if ($r == 'true') {
            $m = EXITO_BORRAR_AUTOCONTROL_OBSERVACIONES;
        }
        echo $html->generaAviso($m, "?mod=" . $modulo . "&niv=1&task=" . $from . "&id_element=" . $idAutocontrol . "&idAutocontrol=" . $autocontrol);
        break;

    /**
     * la variable edit, permite hacer la carga del objeto autocontrol y espera 
     * confirmacion de edicion @see \CPlaneacionAutocontrol
     */
    case 'edit':
        $autocontrol = $_REQUEST['idAutocontrol'];
        $from = $_REQUEST['from'];
        $id = $_REQUEST['id'];
        $id_edit = $_REQUEST['id_element'];
        $observacion = $daoAutocontrol->getObservacionesById($id_edit);
        $id = $_REQUEST['id'];
        $form = new CHtmlForm();
        $form->setTitle(TITULO_EDITAR_AUTOCONTROL_OBSERVACION);
        $form->setId('frm_edit_observacion');
        $form->setAction('?mod=' . $modulo . '&niv=1&task=saveEdit&id=' . $id . '&id_edit=' . $id_edit . '&from=' . $from . '&idAutocontrol=' . $autocontrol);
        $form->setMethod('post');
        $form->setClassEtiquetas('td_label');
        $form->setTableId('frm_add_autocontrol');

        $form->addEtiqueta(AUTOCONTROL_OBSERVACIONES_PERIODO);
        $form->addInputText('month', 'txt_periodo', 'txt_periodo', '50', '50', $observacion->getPeriodo(), '', 'required');

        $form->addEtiqueta(AUTOCONTROL_OBSERVACIONES_DESCRIPCION);
        $form->addTextArea('textarea', 'txt_descripcion', 'txt_descripcion', '100', '5', $observacion->getDescripcion(), '', ' title="' . $html->traducirTildes(TITLE_ALFANUMERICO) . '" required');

        $tipos = $daoBasicas->getBasicas("tipoobservacion");
        $opciones = null;
        if (isset($tipos)) {
            foreach ($tipos as $tipo) {
                $opciones[count($opciones)] = array('value' => $tipo->getId(),
                    'texto' => $tipo->getDescripcion());
            }
        }

        $form->addEtiqueta(AUTOCONTROL_OBSERVACIONES_ESTADO);
        $form->addSelect('select', 'sel_estado', 'sel_estado', $opciones, '', $observacion->getEstado(), '', ' required');

        $form->addInputButton('submit', 'btn_enviar', 'btn_enviar', BTN_ACEPTAR, 'button', '');
        $form->addInputButton('button', 'cancel', 'cancel', BTN_CANCELAR, 'button', 'onclick="cancelarAccion(\'frm_edit_observacion\',\'?mod=' . $modulo . '&niv=' . $niv . '&id_element=' . $id . '&task='. $from . '\');"');
        
        $form->writeForm();
        break;

    /**
     * la variable saveEdit, permite actualizar el autocontrol en la base 
     * de datos @see \CPlaneacionAutocontrolData
     */
    case 'saveEdit':
        $idAutocontrol = $_REQUEST['idAutocontrol'];
        $from = $_REQUEST['from'];
        $id = $_REQUEST['id_edit'];
        $periodo = $_REQUEST['txt_periodo'] . '-00';
        $descripcion = $_REQUEST['txt_descripcion'];
        $autocontrol = $_REQUEST['id'];
        $estado = $_REQUEST['sel_estado'];

        $observacion = new CObservaciones($id, $periodo, $descripcion, null, $estado, null);

        $r = $daoAutocontrol->updateObservacion($observacion);
        $m = ERROR_EDITAR_AUTOCONTROL_OBSERVACIONES;
        if ($r == 'true') {
            $m = EXITO_EDITAR_AUTOCONTROL_OBSERVACIONES;
        }
        echo $html->generaAviso($m, "?mod=" . $modulo . "&niv=1&task=" . $from . "&id_element=" . $autocontrol . "&idAutocontrol=" . $idAutocontrol);
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

