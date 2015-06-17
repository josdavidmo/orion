<?php

function obtenerInformacion($identificador) {
    $datos = null;
    switch ($identificador) {
        case 0:
            $datos['nombreTabla'] = AUTOCONTROL_FUENTE_DATOS;
            $datos['tabla'] = "fuentedatos";
            break;

        case 1:
            $datos['nombreTabla'] = AUTOCONTROL_REGISTRO;
            $datos['tabla'] = "registro";
    }
    return $datos;
}

function obtenerInformacionControl($identificador) {
    $datos = null;
    switch ($identificador) {
        case 0:
            $datos['nombreTabla'] = AUTOCONTROL_CRITERIOS_ACEPTACION;
            $datos['tabla'] = "criteriosaceptacioncontrol";
            break;

        case 1:
            $datos['nombreTabla'] = CONTROL_METODOLOGIA;
            $datos['tabla'] = "metodologiacontrol";
            break;

        case 2:
            $datos['nombreTabla'] = AUTOCONTROL_REGISTRO;
            $datos['tabla'] = "registrocontrol";
            break;
    }
    return $datos;
}

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
        $nombre = $_REQUEST['txt_nombre'];
        $criterio = "1";
        if ($nombre != NULL) {
            $criterio .= " AND CONCAT(usu_nombre,' ',usu_apellido) LIKE '%" . $nombre . "%'";
        }

        $form = new CHtmlForm();
        $form->setTitle(TITULO_PLANEACION_AUTOCONTROL);
        $form->setMethod('post');
        $form->setAction('?mod=' . $modulo . '&niv=' . $niv);
        $form->setId('form_filtro');
        $form->addEtiqueta(USUARIO_NOMBRE);
        $form->addInputText('text', 'txt_nombre', 'txt_nombre', '19', '19', $nombre, '', ' pattern="' . PATTERN_ALFANUMERICO . '" title="' . $html->traducirTildes(TITLE_ALFANUMERICO) . '"');
        $form->addInputButton('submit', 'btn_enviar', 'btn_enviar', BTN_ACEPTAR, 'button', '');
        $form->addInputButton('button', 'exportC', 'exportC', BTN_EXPORTAR_CONTROL, 'button', 'onclick=location.href=\'modulos/interventoria/control_excel.php\'');
        $form->addInputButton('button', 'exportA', 'exportA', BTN_EXPORTAR_AUTOCONTROL, 'button', 'onclick=location.href=\'modulos/interventoria/autocontrol_excel.php\'');
        $form->writeForm();

        $dt = new CHtmlDataTable();
        $dt->setTitleTable(TITULO_PLANEACION_AUTOCONTROL);
        $titulos = array(USUARIO_NOMBRE, USUARIO_DOCUMENTO, USUARIO_CORREO);
        $usuarios = $daoUsuarios->getInformacionBasicaPersonal($criterio, "usu_nombre");
        $dt->setTitleRow($titulos);
        $dt->setDataRows($usuarios);
        $dt->setSeeLink("?mod=" . $modulo . "&niv=" . $nivel . "&task=seePersonal");
        $dt->setType(1);
        $pag_crit = "";
        $dt->setPag(1, $pag_crit);
        $dt->writeDataTable($niv);
        break;

    /**
     * la variable list, permite hacer la carga la página con la lista de 
     * objetos autocontrol según los parámetros de entrada
     */
    case 'seePersonal':
        $id = $_REQUEST['id_element'];
        $usuario = $daoUsuarios->getUserById($id);
        $form = new CHtmlForm();
        $form->setTitle(TITULO_PLANEACION_AUTOCONTROL_PERSONAL . " " . $usuario['usu_nombre'] . " " . $usuario['usu_apellido']);
        $form->setMethod('post');
        $form->setOptions('autoClean', false);
        $form->setAction('?mod=' . $modulo . '&niv=' . $niv);
        $form->addInputButton('submit', 'ok', 'ok', BTN_ATRAS, 'button', '');
        $form->writeForm();
        $dt = new CHtmlDataTable();
        $dt->setTitleTable(TITULO_PLANEACION_AUTOCONTROL_PERSONAL . " " . $usuario['usu_nombre'] . " " . $usuario['usu_apellido']);
        $titulos = array(AUTOCONTROL_OBJETIVOS, AUTOCONTROL_RESPONSABLE_PNC);
        $autocontroles = $daoAutocontrol->getPlaneacionAutocontrolByResponsable($id);
        $dt->setTitleRow($titulos);
        $dt->setDataRows($autocontroles);
        $dt->setSeeLink("?mod=" . $modulo . "&niv=" . $niv . "&task=seeAutocontrol&idResponsable=" . $id);
        $dt->setEditLink("?mod=" . $modulo . "&niv=" . $niv . "&task=edit&idResponsable=" . $id);
        $dt->setDeleteLink("?mod=" . $modulo . "&niv=" . $niv . "&task=delete&idResponsable=" . $id);
        $otros = array('link' => "?mod=" . $modulo . "&niv=" . $niv . "&task=seeObservaciones&idResponsable=" . $id, 'img' => 'hcalc.png', 'alt' => ALT_ENVIAR);
        $dt->addOtrosLink($otros);
        $dt->setAddLink("?mod=" . $modulo . "&niv=" . $niv . "&task=add&idResponsable=" . $id);
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
        $controles = $daoAutocontrol->getPlaneacionControlByResponsable($id);
        $dt->setTitleRow($titulos);
        $dt->setDataRows($controles);
        $dt->setSeeLink("?mod=" . $modulo . "&niv=" . $niv . "&task=seeControl&idResponsable=" . $id);
        $dt->setEditLink("?mod=" . $modulo . "&niv=" . $niv . "&task=editControl&idResponsable=" . $id);
        $dt->setDeleteLink("?mod=" . $modulo . "&niv=" . $niv . "&task=deleteControl&idResponsable=" . $id);
        $otros = array('link' => "?mod=" . $modulo . "&niv=" . $niv . "&task=seeObservacionesControl&idResponsable=" . $id, 'img' => 'hcalc.png', 'alt' => ALT_ENVIAR);
        $dt->addOtrosLink($otros);
        $dt->setAddLink("?mod=" . $modulo . "&niv=" . $niv . "&task=addControl&idResponsable=" . $id);
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
        $form = new CHtmlForm();
        $id = $_REQUEST['idResponsable'];
        $form->setTitle(TITULO_AGREGAR_PLANEACION_AUTOCONTROL);
        $form->setId('frm_add_autocontrol');
        $form->setAction('?mod=' . $modulo . '&niv=1&task=saveAdd&idResponsable=' . $id);
        $form->setMethod('post');
        $form->setClassEtiquetas('td_label');
        $form->setTableId('frm_add_autocontrol');

        $responsablesPNC = $daoUsuarios->getInformacionBasicaPersonal("1", "usu_nombre");
        $opciones = null;
        if (isset($responsablesPNC)) {
            foreach ($responsablesPNC as $responsablePNC) {
                $opciones[count($opciones)] = array('value' => $responsablePNC['id'],
                    'texto' => $responsablePNC['nombre']);
            }
        }

        $form->addEtiqueta(AUTOCONTROL_RESPONSABLE_PNC);
        $form->addSelect('select', 'sel_responsablePNC', 'sel_responsablePNC', $opciones, '', '', '', ' required');

        $form->addEtiqueta(AUTOCONTROL_OBJETIVOS);
        $form->addTextArea('textarea', 'txt_objetivos', 'txt_objetivos', '100', '5', '', '', ' title="' . $html->traducirTildes(TITLE_ALFANUMERICO) . '" required');

        $form->addInputButton('submit', 'btn_enviar', 'btn_enviar', BTN_ACEPTAR, 'button', '');
        $form->addInputButton('button', 'cancel', 'cancel', BTN_CANCELAR, 'button', 'onclick="cancelarAccion(\'frm_add_autocontrol\',\'?mod=' . $modulo . '&niv=' . $niv . '&id_element=' . $id . '&task=seePersonal\');"');

        $form->writeForm();

        break;
    /**
     * la variable saveAdd, permite almacenar el objeto bitacora en la 
     * base de datos @see \CBitacora
     */
    case 'saveAdd':
        $responsable = $_REQUEST['idResponsable'];
        $responsablePNC = $_REQUEST['sel_responsablePNC'];
        $objetivos = $_REQUEST['txt_objetivos'];

        $autocontrol = new CPlaneacionAutocontrol(null, $objetivos, $responsable, $responsablePNC);

        $r = $daoAutocontrol->insertAutocontrol($autocontrol);
        $m = ERROR_AGREGAR_PLANEACION_AUTOCONTROL;
        if ($r == 'true') {
            $m = EXITO_AGREGAR_PLANEACION_AUTOCONTROL;
        }
        echo $html->generaAviso($m, "?mod=" . $modulo . "&niv=1&task=seePersonal&id_element=" . $responsable);

        break;

    /**
     * la variable add, permite hacer la carga la página con las variables 
     * que componen el objeto autocontrol @see \CPlaneacionAutocontrol
     */
    case 'addControl':
        $form = new CHtmlForm();
        $id = $_REQUEST['idResponsable'];
        $form->setTitle(TITULO_AGREGAR_PLANEACION_CONTROL);
        $form->setId('frm_add_control');
        $form->setAction('?mod=' . $modulo . '&niv=1&task=saveAddControl&idResponsable=' . $id);
        $form->setMethod('post');
        $form->setClassEtiquetas('td_label');
        $form->setTableId('frm_add_control');

        $form->addEtiqueta(CONTROL_OBLIGACIONES);
        $form->addTextArea('textarea', 'txt_obligaciones', 'txt_obligaciones', '100', '5', '', '', ' title="' . $html->traducirTildes(TITLE_ALFANUMERICO) . '" required');

        $form->addEtiqueta(CONTROL_VERIFICACION);
        $form->addTextArea('textarea', 'txt_verificacion', 'txt_verificacion', '100', '5', '', '', ' title="' . $html->traducirTildes(TITLE_ALFANUMERICO) . '" required');

        $form->addEtiqueta(CONTROL_NUMERO_DOCUMENTO_CONTRACTUAL);
        $form->addTextArea('textarea', 'txt_numero', 'txt_numero', '100', '5', '', '', ' title="' . $html->traducirTildes(TITLE_ALFANUMERICO) . '" required');

        $form->addInputButton('submit', 'btn_enviar', 'btn_enviar', BTN_ACEPTAR, 'button', '');
        $form->addInputButton('button', 'cancel', 'cancel', BTN_CANCELAR, 'button', 'onclick="cancelarAccion(\'frm_add_control\',\'?mod=' . $modulo . '&niv=' . $niv . '&id_element=' . $id . '&task=seePersonal\');"');

        $form->writeForm();

        break;

    /**
     * la variable saveAdd, permite almacenar el objeto bitacora en la 
     * base de datos @see \CBitacora
     */
    case 'saveAddControl':
        $responsable = $_REQUEST['idResponsable'];
        $obligaciones = $_REQUEST['txt_obligaciones'];
        $verificacion = $_REQUEST['txt_verificacion'];
        $numeroDocumentoContractual = $_REQUEST['txt_numero'];

        $control = new CControl(NULL, $obligaciones, $responsable, $verificacion, $numeroDocumentoContractual);

        $r = $daoAutocontrol->insertControl($control);
        $m = ERROR_AGREGAR_PLANEACION_CONTROL;
        if ($r == 'true') {
            $m = EXITO_AGREGAR_PLANEACION_CONTROL;
        }
        echo $html->generaAviso($m, "?mod=" . $modulo . "&niv=1&task=seePersonal&id_element=" . $responsable);

        break;

    /**
     * la variable delete, permite hacer la carga del objeto autocontrol 
     * y espera confirmacion de eliminarlo @see \CPlaneacionAutocontrol
     */
    case 'delete':
        $id_delete = $_REQUEST['id_element'];
        $idResponsable = $_REQUEST['idResponsable'];
        echo $html->generaAdvertencia(CONFIRMAR_BORRAR_PLANEACION_CONTROL, '?mod=' . $modulo . '&niv=1&task=confirmDelete&id_element=' . $id_delete . '&idResponsable=' . $idResponsable, 'onclick=location.href=\'?mod=' . $modulo . '&niv=1&id_element=' . $idResponsable . '&task=seePersonal\'');
        break;


    /**
     * la variable confirmDelete, permite eliminar el objeto autocontrol 
     * de la base de datos @see \CPlaneacionAutocontrol
     */
    case 'confirmDelete':
        $idResponsable = $_REQUEST['idResponsable'];
        $id_delete = $_REQUEST['id_element'];
        $r = $daoAutocontrol->deleteAutocontrolById($id_delete);
        $m = ERROR_BORRAR_CONTROL;
        if ($r == 'true') {
            $m = EXITO_BORRAR_CONTROL;
        }
        echo $html->generaAviso($m, "?mod=" . $modulo . "&niv=1&task=seePersonal&id_element=" . $idResponsable);
        break;

    /**
     * la variable delete, permite hacer la carga del objeto autocontrol 
     * y espera confirmacion de eliminarlo @see \CPlaneacionAutocontrol
     */
    case 'deleteControl':
        $id_delete = $_REQUEST['id_element'];
        $idResponsable = $_REQUEST['idResponsable'];
        echo $html->generaAdvertencia(CONFIRMAR_BORRAR_PLANEACION_CONTROL, '?mod=' . $modulo . '&niv=1&task=confirmDeleteControl&id_element=' . $id_delete . '&idResponsable=' . $idResponsable, 'onclick=location.href=\'?mod=' . $modulo . '&niv=1&id_element=' . $idResponsable . '&task=seePersonal\'');
        break;

    /**
     * la variable confirmDelete, permite eliminar el objeto autocontrol 
     * de la base de datos @see \CPlaneacionAutocontrol
     */
    case 'confirmDeleteControl':
        $idResponsable = $_REQUEST['idResponsable'];
        $id_delete = $_REQUEST['id_element'];
        $r = $daoAutocontrol->deleteControlById($id_delete);
        $m = ERROR_BORRAR_CONTROL;
        if ($r == 'true') {
            $m = EXITO_BORRAR_CONTROL;
        }
        echo $html->generaAviso($m, "?mod=" . $modulo . "&niv=1&task=seePersonal&id_element=" . $idResponsable);
        break;


    /**
     * la variable edit, permite hacer la carga del objeto autocontrol y espera 
     * confirmacion de edicion @see \CPlaneacionAutocontrol
     */
    case 'editControl':
        $id = $_REQUEST['idResponsable'];
        $id_edit = $_REQUEST['id_element'];
        $control = $daoAutocontrol->getControlById($id_edit);
        $form = new CHtmlForm();
        $form->setTitle(TITULO_EDITAR_PLANEACION_CONTROL);
        $form->setId('frm_edit_control');
        $form->setAction('?mod=' . $modulo . '&niv=1&task=saveEditControl&idResponsable=' . $id . '&id=' . $id_edit);
        $form->setMethod('post');
        $form->setClassEtiquetas('td_label');
        $form->setTableId('frm_add_control');

        $form->addEtiqueta(CONTROL_OBLIGACIONES);
        $form->addTextArea('textarea', 'txt_obligaciones', 'txt_obligaciones', '100', '5', $control->getObligaciones(), '', ' title="' . $html->traducirTildes(TITLE_ALFANUMERICO) . '" required');

        $form->addEtiqueta(CONTROL_VERIFICACION);
        $form->addTextArea('textarea', 'txt_verificacion', 'txt_verificacion', '100', '5', $control->getVerificacion(), '', ' title="' . $html->traducirTildes(TITLE_ALFANUMERICO) . '" required');

        $form->addEtiqueta(CONTROL_NUMERO_DOCUMENTO_CONTRACTUAL);
        $form->addTextArea('textarea', 'txt_numero', 'txt_numero', '100', '5', $control->getNumeroDocumentoContractual(), '', ' title="' . $html->traducirTildes(TITLE_ALFANUMERICO) . '" required');

        $form->addInputButton('submit', 'btn_enviar', 'btn_enviar', BTN_ACEPTAR, 'button', '');
        $form->addInputButton('button', 'cancel', 'cancel', BTN_CANCELAR, 'button', 'onclick="cancelarAccion(\'frm_edit_control\',\'?mod=' . $modulo . '&niv=' . $niv . '&id_element=' . $id . '&task=seePersonal\');"');

        $form->writeForm();
        break;

    /**
     * la variable saveEdit, permite actualizar el autocontrol en la base 
     * de datos @see \CPlaneacionAutocontrolData
     */
    case 'saveEditControl':
        $id = $_REQUEST['id'];
        $responsable = $_REQUEST['idResponsable'];
        $obligaciones = $_REQUEST['txt_obligaciones'];
        $verificacion = $_REQUEST['txt_verificacion'];
        $numeroDocumentoContractual = $_REQUEST['txt_numero'];

        $control = new CControl($id, $obligaciones, $responsable, $verificacion, $numeroDocumentoContractual);

        $r = $daoAutocontrol->updateControl($control);
        $m = ERROR_EDITAR_PLANEACION_CONTROL;
        if ($r == 'true') {
            $m = EXITO_EDITAR_PLANEACION_CONTROL;
        }
        echo $html->generaAviso($m, "?mod=" . $modulo . "&niv=1&task=seePersonal&id_element=" . $responsable);
        break;

    /**
     * la variable edit, permite hacer la carga del objeto autocontrol y espera 
     * confirmacion de edicion @see \CPlaneacionAutocontrol
     */
    case 'edit':
        $id = $_REQUEST['idResponsable'];
        $id_edit = $_REQUEST['id_element'];
        $autocontrol = $daoAutocontrol->getPlaneacionAutocontrolById($id_edit);
        $form = new CHtmlForm();
        $form->setTitle(TITULO_EDITAR_PLANEACION_AUTOCONTROL);
        $form->setId('frm_edit_autocontrol');
        $form->setAction('?mod=' . $modulo . '&niv=1&task=saveEdit&idResponsable=' . $id . '&id_edit=' . $id_edit);
        $form->setMethod('post');
        $form->setClassEtiquetas('td_label');
        $form->setTableId('frm_edit_autocontrol');

        $responsablesPNC = $daoUsuarios->getInformacionBasicaPersonal("1", "usu_nombre");
        $opciones = null;
        if (isset($responsablesPNC)) {
            foreach ($responsablesPNC as $responsablePNC) {
                $opciones[count($opciones)] = array('value' => $responsablePNC['id'],
                    'texto' => $responsablePNC['nombre']);
            }
        }

        $form->addEtiqueta(AUTOCONTROL_RESPONSABLE_PNC);
        $form->addSelect('select', 'sel_responsablePNC', 'sel_responsablePNC', $opciones, '', $autocontrol->getResponsablePNC(), '', ' required');

        $form->addEtiqueta(AUTOCONTROL_OBJETIVOS);
        $form->addTextArea('textarea', 'txt_objetivos', 'txt_objetivos', '100', '5', $autocontrol->getObjetivos(), '', ' title="' . $html->traducirTildes(TITLE_ALFANUMERICO) . '" required');

        $form->addInputButton('submit', 'btn_enviar', 'btn_enviar', BTN_ACEPTAR, 'button', '');
        $form->addInputButton('button', 'cancel', 'cancel', BTN_CANCELAR, 'button', 'onclick="cancelarAccion(\'frm_edit_autocontrol\',\'?mod=' . $modulo . '&niv=' . $niv . '&id_element=' . $id . '&task=seePersonal\');"');

        $form->writeForm();
        break;

    /**
     * la variable saveEdit, permite actualizar el autocontrol en la base 
     * de datos @see \CPlaneacionAutocontrolData
     */
    case 'saveEdit':
        $id = $_REQUEST['id_edit'];
        $responsable = $_REQUEST['idResponsable'];
        $responsablePNC = $_REQUEST['sel_responsablePNC'];
        $objetivos = $_REQUEST['txt_objetivos'];

        $autocontrol = new CPlaneacionAutocontrol($id, $objetivos, $responsable, $responsablePNC);

        $r = $daoAutocontrol->updateAutocontrol($autocontrol);
        $m = ERROR_EDITAR_PLANEACION_AUTOCONTROL;
        if ($r == 'true') {
            $m = EXITO_EDITAR_PLANEACION_AUTOCONTROL;
        }
        echo $html->generaAviso($m, "?mod=" . $modulo . "&niv=1&task=seePersonal&id_element=" . $responsable);
        break;

    /**
     * la variable seeAutocontrol, permite hacer la carga la página con la 
     * lista de objetos autocontrol según los parámetros de entrada
     */
    case 'seeAutocontrol':
        $idAutocontrol = $_REQUEST['id_element'];
        $id = $_REQUEST['idResponsable'];
        $form = new CHtmlForm();
        $form->setTitle(TITULO_DETALLE_AUTOCONTROL);
        $form->setMethod('post');
        $form->setOptions('autoClean', false);
        $form->setAction('?mod=' . $modulo . '&niv=' . $niv . '&id_element=' . $id . "&task=seePersonal");
        $form->addInputButton('submit', 'ok', 'ok', BTN_ATRAS, 'button', '');
        $form->writeForm();

        $dt = new CHtmlDataTable();
        $dt->setTitleTable(AUTOCONTROL_ACTIVIDADES);
        $titulos = array(DESCRIPCION_DETALLE_AUTOCONTROL);
        $condicion = "idPlaneacionAutocontrol = " . $idAutocontrol;
        $datos = $daoBasica->getBasicas('actividadescontrol', $condicion);
        $dt->setTitleRow($titulos);
        $dt->setDataRows($datos);
        $dt->setEditLink("?mod=" . $modulo . "&niv=" . $niv . "&task=editDetalle&idAutocontrol=" . $idAutocontrol . "&idResponsable=" . $id . "&tabla=actividadescontrol&from=seeAutocontrol");
        $dt->setDeleteLink("?mod=" . $modulo . "&niv=" . $niv . "&task=deleteDetalle&idAutocontrol=" . $idAutocontrol . "&idResponsable=" . $id . "&tabla=actividadescontrol&from=seeAutocontrol");
        $dt->setAddLink("?mod=" . $modulo . "&niv=" . $niv . "&task=addDetalle&idAutocontrol=" . $idAutocontrol . "&idResponsable=" . $id . "&tabla=actividadescontrol&from=seeAutocontrol");
        $dt->setSeeLink("?mod=" . $modulo . "&niv=" . $niv . "&task=seeActividad&idAutocontrol=" . $idAutocontrol . "&idResponsable=" . $id);
        $dt->setType(1);
        $pag_crit = "";
        $dt->setPag(1, $pag_crit);
        $dt->writeDataTable($niv);
        break;
    
    case 'seeActividad':
        $idAutocontrol = $_REQUEST['idAutocontrol'];
        $idActividad = $_REQUEST['id_element'];
        $id = $_REQUEST['idResponsable'];
        $form = new CHtmlForm();
        $form->setTitle(TITULO_DETALLE_AUTOCONTROL);
        $form->setMethod('post');
        $form->setOptions('autoClean', false);
        $form->setAction('?mod=' . $modulo . '&niv=' . $niv . '&id_element=' . $idAutocontrol . "&task=seeAutocontrol&idResponsable=$id");
        $form->addInputButton('submit', 'ok', 'ok', BTN_ATRAS, 'button', '');
        $form->writeForm();

        for ($i = 0; $i < 2; $i++) {
            $informacion = obtenerInformacion($i);
            $dt = new CHtmlDataTable();
            $dt->setTitleTable($informacion['nombreTabla']);
            $titulos = array(DESCRIPCION_DETALLE_AUTOCONTROL);
            $condicion = "idActividad = " . $idActividad;
            $datos = $daoBasica->getBasicas($informacion['tabla'], $condicion);
            $dt->setTitleRow($titulos);
            $dt->setDataRows($datos);
            $dt->setEditLink("?mod=" . $modulo . "&niv=" . $niv . "&task=editDetalleAutocontrol&idActividad=" . $idActividad . "&idResponsable=" . $id . "&tabla=" . $informacion['tabla'] . '&from=seeActividad&idAutocontrol='. $idAutocontrol);
            $dt->setDeleteLink("?mod=" . $modulo . "&niv=" . $niv . "&task=deleteDetalleAutocontrol&idActividad=" . $idActividad . "&idResponsable=" . $id . "&tabla=" . $informacion['tabla'] . '&from=seeActividad&idAutocontrol='. $idAutocontrol);
            $dt->setAddLink("?mod=" . $modulo . "&niv=" . $niv . "&task=addDetalleAutocontrol&idActividad=" . $idActividad . "&idResponsable=" . $id . "&tabla=" . $informacion['tabla'] . '&from=seeActividad&idAutocontrol='. $idAutocontrol);
            $dt->setType(1);
            $pag_crit = "";
            $dt->setPag(1, $pag_crit);
            $dt->writeDataTable($niv);
        }
        break;
        
    /**
     * la variable add, permite hacer la carga la página con las variables 
     * que componen el objeto autocontrol @see \CPlaneacionAutocontrol
     */
    case 'addDetalleAutocontrol':
        $from = $_REQUEST['from'];
        $form = new CHtmlForm();
        $tabla = $_REQUEST['tabla'];
        $idResponsable = $_REQUEST['idResponsable'];
        $idAutocontrol = $_REQUEST['idAutocontrol'];      
        $idActividad = $_REQUEST['idActividad'];
        $form->setTitle(TITULO_AGREGAR_DETALLE_AUTOCONTROL);
        $form->setId('frm_add_detalle');
        $form->setAction('?mod=' . $modulo . '&niv=1&task=saveAddDetalleAutocontrol&idResponsable=' . $idResponsable . '&idAutocontrol=' . $idAutocontrol . '&tabla=' . $tabla . '&from=' . $from . '&idActividad=' .$idActividad);
        $form->setMethod('post');
        $form->setClassEtiquetas('td_label');
        $form->setTableId('frm_add_autocontrol');

        $form->addEtiqueta(DESCRIPCION_DETALLE_AUTOCONTROL);
        $form->addTextArea('textarea', 'txt_descripcion', 'txt_descripcion', '100', '5', '', '', ' title="' . $html->traducirTildes(TITLE_ALFANUMERICO) . '" required');

        $form->addInputButton('submit', 'btn_enviar', 'btn_enviar', BTN_ACEPTAR, 'button', '');
        $form->addInputButton('button', 'cancel', 'cancel', BTN_CANCELAR, 'button', 'onclick="cancelarAccion(\'frm_add_detalle\',\'?mod=' . $modulo . '&niv=' . $niv . '&idResponsable=' . $idResponsable . '&id_element=' . $idActividad . '&idAutocontrol=' . $idAutocontrol . '&task=' . $from . '\');"');

        $form->writeForm();

        break;


    /**
     * la variable saveAdd, permite almacenar el objeto basica en la 
     * base de datos @see \CBasicaRelacionada
     */
    case 'saveAddDetalleAutocontrol':
        $from = $_REQUEST['from'];
        $idResponsable = $_REQUEST['idResponsable'];
        $descripcion = $_REQUEST['txt_descripcion'];
        $tabla = $_REQUEST['tabla'];
        $idTabla = $_REQUEST['idActividad'];
        $idAutocontrol = $_REQUEST['idAutocontrol'];

        $basica = new CBasicaRelacionada(null, $descripcion, $idTabla);

        $r = $daoBasica->insertBasicaRelacionada($basica, $tabla);
        $m = ERROR_AGREGAR_AUTOCONTROL_BASICA;
        if ($r == 'true') {
            $m = EXITO_AGREGAR_AUTOCONTROL_BASICA;
        }
        echo $html->generaAviso($m, "?mod=" . $modulo . "&niv=1&task=" . $from . "&id_element=" . $idTabla . "&idResponsable=" . $idResponsable . "&idAutocontrol=" . $idAutocontrol);
        break;


    /**
     * la variable saveAdd, permite editar el objeto basica en la 
     * base de datos @see \CBasicaRelacionada
     */
    case 'editDetalleAutocontrol':
        $from = $_REQUEST['from'];
        $id_edit = $_REQUEST['id_element'];
        $form = new CHtmlForm();
        $tabla = $_REQUEST['tabla'];
        $idResponsable = $_REQUEST['idResponsable'];
        $idActividad = $_REQUEST['idActividad'];
        $idAutocontrol = $_REQUEST['idAutocontrol'];
        $condicion = $daoBasica->getCampos($tabla)[0] . " = " . $id_edit;
        $basica = $daoBasica->getBasicaById($tabla, $condicion);
        $form->setTitle(TITULO_EDITAR_DETALLE_AUTOCONTROL);
        $form->setId('frm_edit_detalle');
        $form->setAction('?mod=' . $modulo . '&niv=1&task=saveEditDetalleAutocontrol&idResponsable=' . $idResponsable . '&idAutocontrol=' . $idAutocontrol . '&tabla=' . $tabla . '&id_edit=' . $id_edit . '&from=' . $from . '&idActividad=' . $idActividad);
        $form->setMethod('post');
        $form->setClassEtiquetas('td_label');
        $form->setTableId('frm_add_autocontrol');

        $form->addEtiqueta(DESCRIPCION_DETALLE_AUTOCONTROL);
        $form->addTextArea('textarea', 'txt_descripcion', 'txt_descripcion', '100', '5', $basica->getDescripcion(), '', ' title="' . $html->traducirTildes(TITLE_ALFANUMERICO) . '" required');

        $form->addInputButton('submit', 'btn_enviar', 'btn_enviar', BTN_ACEPTAR, 'button', '');
        $form->addInputButton('button', 'cancel', 'cancel', BTN_CANCELAR, 'button', 'onclick="cancelarAccion(\'frm_edit_detalle\',\'?mod=' . $modulo . '&niv=' . $niv . '&idResponsable=' . $idResponsable . '&id_element=' . $idActividad . '&task=' . $from . '&idAutocontrol=' . $idAutocontrol . '\');"');

        $form->writeForm();
        break;

    /**
     * la variable saveAdd, permite almacenar el objeto basica en la 
     * base de datos @see \CBasicaRelacionada
     */
    case 'saveEditDetalleAutocontrol':
        $from = $_REQUEST['from'];
        $id = $_REQUEST['id_edit'];
        $idResponsable = $_REQUEST['idResponsable'];
        $descripcion = $_REQUEST['txt_descripcion'];
        $tabla = $_REQUEST['tabla'];
        $idAutocontrol = $_REQUEST['idAutocontrol'];
        $idTabla = $_REQUEST['idActividad'];

        $basica = new CBasicaRelacionada($id, $descripcion, $idTabla);

        $r = $daoBasica->updateBasicaRelacionada($basica, $tabla);
        $m = ERROR_EDITAR_AUTOCONTROL_BASICA;
        if ($r == 'true') {
            $m = EXITO_EDITAR_AUTOCONTROL_BASICA;
        }
        echo $html->generaAviso($m, "?mod=" . $modulo . "&niv=1&task=" . $from . "&id_element=" . $idTabla . "&idResponsable=" . $idResponsable . '&idAutocontrol=' . $idAutocontrol);
        break;

    /**
     * la variable delete, permite hacer la carga del objeto autocontrol 
     * y espera confirmacion de eliminarlo @see \CPlaneacionAutocontrol
     */
    case 'deleteDetalleAutocontrol':
        $from = $_REQUEST['from'];
        $tabla = $_REQUEST['tabla'];
        $id_delete = $_REQUEST['id_element'];
        $idResponsable = $_REQUEST['idResponsable'];
        $idAutocontrol = $_REQUEST['idAutocontrol'];
        $idActividad = $_REQUEST['idActividad'];
        echo $html->generaAdvertencia(CONFIRMAR_BORRAR_AUTOCONTROL_BASICA, '?mod=' . $modulo . '&niv=1&task=confirmDeleteDetalleAutocontrol&id_element=' . $id_delete . '&idResponsable=' . $idResponsable . '&idAutocontrol=' . $idAutocontrol . '&tabla=' . $tabla . '&from=' . $from . '&idActividad=' . $idActividad, 'onclick=location.href=\'?mod=' . $modulo . '&niv=1&id_element=' . $idActividad . '&idResponsable=' . $idResponsable . '&task=' . $from . '&idAutocontrol=' . $idAutocontrol .'\'');
        break;
    /**
     * la variable confirmDelete, permite eliminar el objeto autocontrol 
     * de la base de datos @see \CPlaneacionAutocontrol
     */
    case 'confirmDeleteDetalleAutocontrol':
        $from = $_REQUEST['from'];
        $tabla = $_REQUEST['tabla'];
        $id_delete = $_REQUEST['id_element'];
        $idResponsable = $_REQUEST['idResponsable'];
        $idAutocontrol = $_REQUEST['idAutocontrol'];
        $idActividad = $_REQUEST['idActividad'];
        $r = $daoBasica->deleteBasicaRelacionadaById($id_delete, $tabla);
        $m = ERROR_BORRAR_AUTOCONTROL_BASICA;
        if ($r == 'true') {
            $m = EXITO_BORRAR_AUTOCONTROL_BASICA;
        }
        echo $html->generaAviso($m, "?mod=" . $modulo . "&niv=1&task=" . $from . "&id_element=" . $idActividad . '&idResponsable=' . $idResponsable . '&idAutocontrol=' . $idAutocontrol);
        break;

    /**
     * la variable seeAutocontrol, permite hacer la carga la página con la 
     * lista de objetos autocontrol según los parámetros de entrada
     */
    case 'seeControl':
        $idAutocontrol = $_REQUEST['id_element'];
        $id = $_REQUEST['idResponsable'];
        $form = new CHtmlForm();
        $form->setTitle(TITULO_DETALLE_AUTOCONTROL);
        $form->setMethod('post');
        $form->setOptions('autoClean', false);
        $form->setAction('?mod=' . $modulo . '&niv=' . $niv . '&id_element=' . $id . "&task=seePersonal");
        $form->addInputButton('submit', 'ok', 'ok', BTN_ATRAS, 'button', '');
        $form->writeForm();

        for ($i = 0; $i < 3; $i++) {
            $informacion = obtenerInformacionControl($i);
            $dt = new CHtmlDataTable();
            $dt->setTitleTable($informacion['nombreTabla']);
            $titulos = array(DESCRIPCION_DETALLE_AUTOCONTROL);
            $condicion = "idPlaneacionControl = " . $idAutocontrol;
            $datos = $daoBasica->getBasicas($informacion['tabla'], $condicion);
            $dt->setTitleRow($titulos);
            $dt->setDataRows($datos);
            $dt->setEditLink("?mod=" . $modulo . "&niv=" . $niv . "&task=editDetalle&idAutocontrol=" . $idAutocontrol . "&idResponsable=" . $id . "&tabla=" . $informacion['tabla'] . '&from=seeControl');
            $dt->setDeleteLink("?mod=" . $modulo . "&niv=" . $niv . "&task=deleteDetalle&idAutocontrol=" . $idAutocontrol . "&idResponsable=" . $id . "&tabla=" . $informacion['tabla'] . '&from=seeControl');
            $dt->setAddLink("?mod=" . $modulo . "&niv=" . $niv . "&task=addDetalle&idAutocontrol=" . $idAutocontrol . "&idResponsable=" . $id . "&tabla=" . $informacion['tabla'] . '&from=seeControl');
            $dt->setType(1);
            $pag_crit = "";
            $dt->setPag(1, $pag_crit);
            $dt->writeDataTable($niv);
        }
        break;


    /**
     * la variable add, permite hacer la carga la página con las variables 
     * que componen el objeto autocontrol @see \CPlaneacionAutocontrol
     */
    case 'addDetalle':
        $from = $_REQUEST['from'];
        $form = new CHtmlForm();
        $tabla = $_REQUEST['tabla'];
        $idResponsable = $_REQUEST['idResponsable'];
        $idAutocontrol = $_REQUEST['idAutocontrol'];
        $form->setTitle(TITULO_AGREGAR_DETALLE_AUTOCONTROL);
        $form->setId('frm_add_detalle');
        $form->setAction('?mod=' . $modulo . '&niv=1&task=saveAddDetalle&idResponsable=' . $idResponsable . '&idAutocontrol=' . $idAutocontrol . '&tabla=' . $tabla . '&from=' . $from);
        $form->setMethod('post');
        $form->setClassEtiquetas('td_label');
        $form->setTableId('frm_add_autocontrol');

        $form->addEtiqueta(DESCRIPCION_DETALLE_AUTOCONTROL);
        $form->addTextArea('textarea', 'txt_descripcion', 'txt_descripcion', '100', '5', '', '', ' title="' . $html->traducirTildes(TITLE_ALFANUMERICO) . '" required');

        $form->addInputButton('submit', 'btn_enviar', 'btn_enviar', BTN_ACEPTAR, 'button', '');
        $form->addInputButton('button', 'cancel', 'cancel', BTN_CANCELAR, 'button', 'onclick="cancelarAccion(\'frm_add_detalle\',\'?mod=' . $modulo . '&niv=' . $niv . '&idResponsable=' . $idResponsable . '&id_element=' . $idAutocontrol . '&task=' . $from . '\');"');

        $form->writeForm();

        break;


    /**
     * la variable saveAdd, permite almacenar el objeto basica en la 
     * base de datos @see \CBasicaRelacionada
     */
    case 'saveAddDetalle':
        $from = $_REQUEST['from'];
        $idResponsable = $_REQUEST['idResponsable'];
        $descripcion = $_REQUEST['txt_descripcion'];
        $tabla = $_REQUEST['tabla'];
        $idTabla = $_REQUEST['idAutocontrol'];

        $basica = new CBasicaRelacionada(null, $descripcion, $idTabla);

        $r = $daoBasica->insertBasicaRelacionada($basica, $tabla);
        $m = ERROR_AGREGAR_AUTOCONTROL_BASICA;
        if ($r == 'true') {
            $m = EXITO_AGREGAR_AUTOCONTROL_BASICA;
        }
        echo $html->generaAviso($m, "?mod=" . $modulo . "&niv=1&task=" . $from . "&id_element=" . $idTabla . "&idResponsable=" . $idResponsable);
        break;


    /**
     * la variable saveAdd, permite editar el objeto basica en la 
     * base de datos @see \CBasicaRelacionada
     */
    case 'editDetalle':
        $from = $_REQUEST['from'];
        $id_edit = $_REQUEST['id_element'];
        $form = new CHtmlForm();
        $tabla = $_REQUEST['tabla'];
        $idResponsable = $_REQUEST['idResponsable'];
        $idAutocontrol = $_REQUEST['idAutocontrol'];
        $condicion = $daoBasica->getCampos($tabla)[0] . " = " . $id_edit;
        $basica = $daoBasica->getBasicaById($tabla, $condicion);
        $form->setTitle(TITULO_EDITAR_DETALLE_AUTOCONTROL);
        $form->setId('frm_edit_detalle');
        $form->setAction('?mod=' . $modulo . '&niv=1&task=saveEditDetalle&idResponsable=' . $idResponsable . '&idAutocontrol=' . $idAutocontrol . '&tabla=' . $tabla . '&id_edit=' . $id_edit . '&from=' . $from);
        $form->setMethod('post');
        $form->setClassEtiquetas('td_label');
        $form->setTableId('frm_add_autocontrol');

        $form->addEtiqueta(DESCRIPCION_DETALLE_AUTOCONTROL);
        $form->addTextArea('textarea', 'txt_descripcion', 'txt_descripcion', '100', '5', $basica->getDescripcion(), '', ' title="' . $html->traducirTildes(TITLE_ALFANUMERICO) . '" required');

        $form->addInputButton('submit', 'btn_enviar', 'btn_enviar', BTN_ACEPTAR, 'button', '');
        $form->addInputButton('button', 'cancel', 'cancel', BTN_CANCELAR, 'button', 'onclick="cancelarAccion(\'frm_edit_detalle\',\'?mod=' . $modulo . '&niv=' . $niv . '&idResponsable=' . $idResponsable . '&id_element=' . $idAutocontrol . '&task=' . $from . '\');"');

        $form->writeForm();
        break;

    /**
     * la variable saveAdd, permite almacenar el objeto basica en la 
     * base de datos @see \CBasicaRelacionada
     */
    case 'saveEditDetalle':
        $from = $_REQUEST['from'];
        $id = $_REQUEST['id_edit'];
        $idResponsable = $_REQUEST['idResponsable'];
        $descripcion = $_REQUEST['txt_descripcion'];
        $tabla = $_REQUEST['tabla'];
        $idTabla = $_REQUEST['idAutocontrol'];

        $basica = new CBasicaRelacionada($id, $descripcion, $idTabla);

        $r = $daoBasica->updateBasicaRelacionada($basica, $tabla);
        $m = ERROR_EDITAR_AUTOCONTROL_BASICA;
        if ($r == 'true') {
            $m = EXITO_EDITAR_AUTOCONTROL_BASICA;
        }
        echo $html->generaAviso($m, "?mod=" . $modulo . "&niv=1&task=" . $from . "&id_element=" . $idTabla . "&idResponsable=" . $idResponsable . '&id_element=' . $idTabla);
        break;

    /**
     * la variable delete, permite hacer la carga del objeto autocontrol 
     * y espera confirmacion de eliminarlo @see \CPlaneacionAutocontrol
     */
    case 'deleteDetalle':
        $from = $_REQUEST['from'];
        $tabla = $_REQUEST['tabla'];
        $id_delete = $_REQUEST['id_element'];
        $idResponsable = $_REQUEST['idResponsable'];
        $idAutocontrol = $_REQUEST['idAutocontrol'];
        echo $html->generaAdvertencia(CONFIRMAR_BORRAR_AUTOCONTROL_BASICA, '?mod=' . $modulo . '&niv=1&task=confirmDeleteDetalle&id_element=' . $id_delete . '&idResponsable=' . $idResponsable . '&idAutocontrol=' . $idAutocontrol . '&tabla=' . $tabla . '&from=' . $from, 'onclick=location.href=\'?mod=' . $modulo . '&niv=1&id_element=' . $idAutocontrol . '&idResponsable=' . $idResponsable . '&task=' . $from . '\'');
        break;
    /**
     * la variable confirmDelete, permite eliminar el objeto autocontrol 
     * de la base de datos @see \CPlaneacionAutocontrol
     */
    case 'confirmDeleteDetalle':
        $from = $_REQUEST['from'];
        $tabla = $_REQUEST['tabla'];
        $id_delete = $_REQUEST['id_element'];
        $idResponsable = $_REQUEST['idResponsable'];
        $idAutocontrol = $_REQUEST['idAutocontrol'];
        $r = $daoBasica->deleteBasicaRelacionadaById($id_delete, $tabla);
        $m = ERROR_BORRAR_AUTOCONTROL_BASICA;
        if ($r == 'true') {
            $m = EXITO_BORRAR_AUTOCONTROL_BASICA;
        }
        echo $html->generaAviso($m, "?mod=" . $modulo . "&niv=1&task=" . $from . "&id_element=" . $idAutocontrol . '&idResponsable=' . $idResponsable);
        break;


    case 'seeObservaciones':
        $idResponsable = $_REQUEST['idResponsable'];
        $id_element = $_REQUEST['id_element'];
        $form = new CHtmlForm();
        $form->setTitle(TITULO_AUTOCONTROL_OBSERVACIONES);
        $form->setMethod('post');
        $form->setOptions('autoClean', false);
        $form->setAction('?mod=' . $modulo . '&niv=' . $niv . '&id_element=' . $idResponsable . '&task=seePersonal');
        $form->addInputButton('submit', 'ok', 'ok', BTN_ATRAS, 'button', '');
        $form->writeForm();
        $dt = new CHtmlDataTable();
        $dt->setTitleTable(TITULO_AUTOCONTROL_OBSERVACIONES);
        $titulos = array(AUTOCONTROL_OBSERVACIONES_PERIODO, AUTOCONTROL_OBSERVACIONES_DESCRIPCION, AUTOCONTROL_OBSERVACIONES_ESTADO);
        $observaciones = $daoAutocontrol->getObservacionesByIdAutocontrol($id_element);
        $dt->setTitleRow($titulos);
        $dt->setDataRows($observaciones);
        $dt->setType(1);
        $pag_crit = "";
        $dt->setPag(1, $pag_crit);
        $dt->writeDataTable($niv);
        break;

    case 'seeObservacionesControl':
        $idResponsable = $_REQUEST['idResponsable'];
        $id_element = $_REQUEST['id_element'];
        $form = new CHtmlForm();
        $form->setTitle(TITULO_AUTOCONTROL_OBSERVACIONES);
        $form->setMethod('post');
        $form->setOptions('autoClean', false);
        $form->setAction('?mod=' . $modulo . '&niv=' . $niv . '&id_element=' . $idResponsable . '&task=seePersonal');
        $form->addInputButton('submit', 'ok', 'ok', BTN_ATRAS, 'button', '');
        $form->writeForm();
        $dt = new CHtmlDataTable();
        $dt->setTitleTable(TITULO_AUTOCONTROL_OBSERVACIONES);
        $titulos = array(AUTOCONTROL_OBSERVACIONES_PERIODO, AUTOCONTROL_OBSERVACIONES_DESCRIPCION, AUTOCONTROL_OBSERVACIONES_ESTADO);
        $observaciones = $daoAutocontrol->getObservacionesByIdControl($id_element);
        $dt->setTitleRow($titulos);
        $dt->setDataRows($observaciones);
        $dt->setType(1);
        $pag_crit = "";
        $dt->setPag(1, $pag_crit);
        $dt->writeDataTable($niv);
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

