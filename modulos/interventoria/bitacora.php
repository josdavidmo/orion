<?php

/**
 * Modulo Bitacora
 * Maneja el modulo bitacora en union con CRelacionTransporteData, 
 * CBitacoraData, CBitacora, CRelacionTransporte
 *
 * @see \CBitacoraData
 * @see \CBitacora
 * @see \CRelacionTransporte
 * @see \CRelacionTransporteData
 *
 * @package modulos
 * @subpackage bitacora
 * @author SERTIC SAS
 * @version 2014.10.31
 * @copyright SERTIC SAS
 */
//no permite el acceso directo
defined('_VALID_PRY') or die('Restricted access');
$daoUsuarios = new CUserData($db);
$daoBitacora = new CBitacoraData($db);
$daoPlaneacion = new CPlaneacionData($db);
$daoActividadBitacora = new CActividadBitacoraData($db);
$daoRelacionTransporte = new CRelacionTransporteData($db);
$daoRegistroFotografico = new CRegistroFotograficoData($db);
$daoHallazgosPendientes = new CHallazgosPendientesData($db);
$daoBeneficiarios = new CBeneficiarioData($db);
$daoGastos = new CGastoData($db);
$daoAnticipo = new CAnticipoData($db);
$daoBasicas = new CBasicaData($db);
$task = $_REQUEST['task'];
$modulo = $_REQUEST['mod'];
$niv = $_REQUEST['niv'];
if (empty($task)) {
    $task = 'list';
}

switch ($task) {
    /**
     * la variable list, permite hacer la carga la página con la lista de 
     * objetos productos según los parámetros de entrada
     */
    case 'list':
        $nombre = $_REQUEST['txt_nombre'];
        $criterio = "1";
        if ($nombre != NULL) {
            $criterio .= " AND CONCAT(usu_nombre,' ',usu_apellido) LIKE '%" . $nombre . "%'";
        }

        $form = new CHtmlForm();
        $form->setTitle(TITULO_PLANEACION_BITACORA);
        $form->setMethod('post');
        $form->setAction('?mod=' . $modulo . '&niv=' . $niv);
        $form->setId('form_filtro');
        $form->addEtiqueta(USUARIO_NOMBRE);
        $form->addInputText('text', 'txt_nombre', 'txt_nombre', '19', '19', $nombre, '', ' pattern="' . PATTERN_ALFANUMERICO . '" title="' . $html->traducirTildes(TITLE_ALFANUMERICO) . '"');
        $form->addInputButton('submit', 'btn_enviar', 'btn_enviar', BTN_ACEPTAR, 'button', '');
        $form->writeForm();

        $dt = new CHtmlDataTable();
        $dt->setTitleTable(TITULO_PLANEACION_BITACORA);
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
     * objetos productos según los parámetros de entrada
     */
    case 'seePersonal':
        $id = $_REQUEST['id_element'];
        $usuario = $daoUsuarios->getUserById($id);
        $form = new CHtmlForm();
        $form->setTitle(TITULO_PLANEACION_BITACORA_PERSONAL . " " . $usuario['usu_nombre'] . " " . $usuario['usu_apellido']);
        $form->setMethod('post');
        $form->setOptions('autoClean', false);
        $form->setAction('?mod=' . $modulo . '&niv=' . $niv);
        $form->addInputButton('submit', 'ok', 'ok', BTN_ATRAS, 'button', '');
        $form->writeForm();
        $dt = new CHtmlDataTable();
        $dt->setTitleTable(TITULO_PLANEACION_BITACORA_PERSONAL . " " . $usuario['usu_nombre'] . " " . $usuario['usu_apellido']);
        $titulos = array(BITACORA_MUNICIPIO, BITACORA_ACTIVIDAD, BITACORA_FECHA_INICIO, BITACORA_FECHA_FIN, BITACORA_ESTADO);
        $bitacoras = $daoBitacora->getBitacorasByUsuario($usuario['usu_id']);
        $dt->setTitleRow($titulos);
        $dt->setDataRows($bitacoras);
        $dt->setSeeLink("?mod=" . $modulo . "&niv=" . $nivel . "&task=see&idUsuario=" . $id);
        $dt->setEditLink("?mod=" . $modulo . "&niv=" . $niv . "&task=edit&idUsuario=" . $id);
        $dt->setDeleteLink("?mod=" . $modulo . "&niv=" . $niv . "&task=delete&idUsuario=" . $id);
        $dt->setAddLink("?mod=" . $modulo . "&niv=" . $niv . "&task=add&idUsuario=" . $id);
        $dt->setType(1);
        $pag_crit = "";
        $dt->setPag(1, $pag_crit);
        $dt->writeDataTable($niv);

        break;

    /**
     * la variable add, permite hacer la carga la página con las variables 
     * que componen el objeto registro producto @see \CRegistroProductos
     */
    case 'add':
        $form = new CHtmlForm();
        $id = $_REQUEST['idUsuario'];
        $condicion = "1";

        if (isset($_REQUEST['sel_region']) &&
                $_REQUEST['sel_region'] != '-1') {
            $condicion .= " AND de.der_id = " . $_REQUEST['sel_region'];
        }
        if (isset($_REQUEST['sel_departamento']) &&
                $_REQUEST['sel_departamento'] != '-1') {
            $condicion .= " AND mu.dep_id = " . $_REQUEST['sel_departamento'];
        }
        if (isset($_REQUEST['sel_municipios']) &&
                $_REQUEST['sel_municipios'] != '-1') {
            $condicion .= " AND c.mun_id = " . $_REQUEST['sel_municipios'];
        }
        if (isset($_REQUEST['sel_centro_poblado']) &&
                $_REQUEST['sel_centro_poblado'] != '-1') {
            $condicion .= " AND b.idCentroPoblado = " . $_REQUEST['sel_centro_poblado'];
        }

        $form->setTitle(TITULO_AGREGAR_PLANEACION_BITACORA);
        $form->setId('frm_add_bitacora');
        $form->setMethod('post');
        $form->setClassEtiquetas('td_label');
        $form->setTableId('tb_add_bitacora');
        $form->setAction('?mod=' . $modulo . '&niv=1&task=add&idUsuario=' . $id);

        $regiones = $daoPlaneacion->getRegiones('der_nombre');
        $opciones = null;
        if (isset($regiones)) {
            foreach ($regiones as $region) {
                $opciones[count($opciones)] = array('value' => $region['id'],
                    'texto' => $region['nombre']);
            }
        }

        $form->addEtiqueta(PLANEACION_REGION);
        $form->addSelect('select', 'sel_region', 'sel_region', $opciones, '', $_REQUEST['sel_region'], '', 'onChange="submit();" required');

        $departamentos = $daoPlaneacion->getDepartamento($_REQUEST['sel_region'], 'dep_nombre');
        $opciones = null;
        if (isset($departamentos)) {
            foreach ($departamentos as $departamento) {
                $opciones[count($opciones)] = array('value' => $departamento['id'],
                    'texto' => $departamento['nombre']);
            }
        }

        $form->addEtiqueta(PLANEACION_DEPARTAMENTO);
        $form->addSelect('select', 'sel_departamento', 'sel_departamento', $opciones, '', $_REQUEST['sel_departamento'], '', 'onChange="submit();" required');

        $muncipios = $daoPlaneacion->getMunicipio($_REQUEST['sel_departamento'], 'mun_nombre');
        $opciones = null;
        if (isset($muncipios)) {
            foreach ($muncipios as $municipio) {
                $opciones[count($opciones)] = array('value' => $municipio['id'],
                    'texto' => $municipio['nombre']);
            }
        }

        $form->addEtiqueta(PLANEACION_MUNICIPIO);
        $form->addSelect('select', 'sel_municipios', 'sel_municipios', $opciones, '', $_REQUEST['sel_municipios'], '', 'onChange="submit();" required');

        $centrosPoblados = $daoBeneficiarios->getCentrosPoblados($_REQUEST['sel_municipios']);
        $opciones = null;
        if (isset($centrosPoblados)) {
            foreach ($centrosPoblados as $centroPoblado) {
                $opciones[count($opciones)] = array('value' => $centroPoblado->getIdCentroPoblado(),
                    'texto' => $centroPoblado->getNombre());
            }
        }

        $form->addEtiqueta(CENTRO_POBLADO_BENEFICIARIO);
        $form->addSelect('select', 'sel_centro_poblado', 'sel_centro_poblado', $opciones, '', $_REQUEST['sel_centro_poblado'], '', 'onChange="submit();" required');

        $beneficiarios = $daoBeneficiarios->getBeneficiarios($condicion);
        $opciones = null;
        if (isset($beneficiarios)) {
            foreach ($beneficiarios as $beneficiario) {
                $opciones[count($opciones)] = array('value' => $beneficiario['idBeneficiario'],
                    'texto' => $beneficiario['centropoblado'] . " - " . $beneficiario['tipo'] . " - " . $beneficiario['nombre']);
            }
        }

        $form->addEtiqueta(BITACORA_BENEFICIARIO);
        $form->addSelect('select', 'sel_beneficiario', 'sel_beneficiario', $opciones, '', $_REQUEST['sel_beneficiario'], '', ' required');

        $form->addEtiqueta(BITACORA_ACTIVIDAD);
        $form->addTextArea('textarea', 'txt_descripcion', 'txt_descripcion', '100', '5', '', '', ' title="' . $html->traducirTildes(TITLE_ALFANUMERICO) . '" required');

        $form->addEtiqueta(BITACORA_FECHA_INICIO);
        $form->addInputDate('date', 'txt_fecha_inicio', 'txt_fecha_inicio', '', '%Y-%m-%d', '16', '16', '', 'pattern="' . PATTERN_FECHA . '" title="' . $html->traducirTildes(TITLE_FECHA) . '" required');

        $form->addEtiqueta(BITACORA_FECHA_FIN);
        $form->addInputDate('date', 'txt_fecha_fin', 'txt_fecha_fin', '', '%Y-%m-%d', '16', '16', '', 'pattern="' . PATTERN_FECHA . '" title="' . $html->traducirTildes(TITLE_FECHA) . '" required');

        $form->addInputButton('button', 'btn_add', 'btn_add', BTN_ACEPTAR, 'button', 'onclick="cancelarAccion(\'frm_add_bitacora\',\'?mod=' . $modulo . '&niv=' . $niv . '&idUsuario=' . $id . '&task=saveAdd\');"');
        $form->addInputButton('button', 'cancel', 'cancel', BTN_CANCELAR, 'button', 'onclick="cancelarAccion(\'frm_add_bitacora\',\'?mod=' . $modulo . '&niv=' . $niv . '&id_element=' . $id . '&task=seePersonal\');"');

        $form->writeForm();

        break;
    /**
     * la variable saveAdd, permite almacenar el objeto bitacora en la 
     * base de datos @see \CBitacora
     */
    case 'saveAdd':
        $usuario = $_REQUEST['idUsuario'];
        $beneficiario = $_REQUEST['sel_beneficiario'];
        $descripcionActividad = $_REQUEST['txt_descripcion'];
        $fechaInicio = $_REQUEST['txt_fecha_inicio'];
        $fechaFin = $_REQUEST['txt_fecha_fin'];

        $bitacora = new CBitacora(NULL, $usuario, $beneficiario, $descripcionActividad, $fechaInicio, $fechaFin);

        $r = $daoBitacora->insertBitacora($bitacora);
        $m = ERROR_AGREGAR_PLANEACION_BITACORA;
        if ($r == 'true') {
            $m = EXITO_AGREGAR_PLANEACION_BITACORA;
        }
        echo $html->generaAviso($m, "?mod=" . $modulo . "&niv=1&task=seePersonal&id_element=" . $usuario);

        break;
    /**
     * la variable delete, permite hacer la carga del objeto bitacora 
     * y espera confirmacion de eliminarlo @see \CBitacora
     */
    case 'delete':
        $id_delete = $_REQUEST['id_element'];
        $idUsuario = $_REQUEST['idUsuario'];
        echo $html->generaAdvertencia(CONFIRMAR_BORRAR_PLANEACION_BITACORA, '?mod=' . $modulo . '&niv=1&task=confirmDelete&id_element=' . $id_delete . '&idUsuario=' . $idUsuario, 'onclick=location.href=\'?mod=' . $modulo . '&niv=1&id_element=' . $idUsuario . '&task=seePersonal\'');
        break;
    /**
     * la variable confirmDelete, permite eliminar el objeto registro producto 
     * de la base de datos @see \CRegistroInversion
     */
    case 'confirmDelete':
        $idUsuario = $_REQUEST['idUsuario'];
        $id_delete = $_REQUEST['id_element'];
        $r = $daoBitacora->deleteBitacoraById($id_delete);
        $m = ERROR_BORRAR_BITACORA;
        if ($r == 'true') {
            $m = EXITO_BORRAR_BITACORA;
        }
        echo $html->generaAviso($m, "?mod=" . $modulo . "&niv=1&task=seePersonal&id_element=" . $idUsuario);
        break;

    /**
     * la variable edit, permite hacer la carga del objeto registro producto y espera 
     * confirmacion de edicion @see \CRegistroProductos
     */
    case 'edit':
        $id = $_REQUEST['idUsuario'];
        $id_edit = $_REQUEST['id_element'];
        $bitacora = $daoBitacora->getBitacoraById($id_edit);
        $ubicacion = $daoBeneficiarios->getUbicacionBeneficiarioById($bitacora->getBeneficiario());

        $condicion = "1";
        $regionAc = $ubicacion['region'];
        if (isset($_REQUEST['sel_region']) &&
                $_REQUEST['sel_region'] != '-1') {
            $regionAc = $_REQUEST['sel_region'];
            $condicion .= " AND de.der_id = " . $regionAc;
        }
        $departamentoAc = $ubicacion['departamento'];
        if (isset($_REQUEST['sel_departamento']) &&
                $_REQUEST['sel_departamento'] != '-1') {
            $departamentoAc = $_REQUEST['sel_departamento'];
            $condicion .= " AND mu.dep_id = " . $departamentoAc;
        }
        $muncipioAc = $ubicacion['municipio'];
        if (isset($_REQUEST['sel_municipios']) &&
                $_REQUEST['sel_municipios'] != '-1') {
            $muncipioAc = $_REQUEST['sel_municipios'];
            $condicion .= " AND c.mun_id = " . $muncipioAc;
        }
        $centroPobladoAc = $ubicacion['centroPoblado'];
        if (isset($_REQUEST['sel_centro_poblado']) &&
                $_REQUEST['sel_centro_poblado'] != '-1') {
            $centroPobladoAc = $_REQUEST['sel_centro_poblado'];
            $condicion .= " AND b.idCentroPoblado = " . $centroPobladoAc;
        }
        $beneficiarioAc = $bitacora->getBeneficiario();
        if (isset($_REQUEST['sel_beneficiario']) &&
                $_REQUEST['sel_beneficiario'] != '-1') {
            $beneficiarioAc = $_REQUEST['sel_beneficiario'];
        }

        $form = new CHtmlForm();
        $form->setTitle(TITULO_EDITAR_PLANEACION_BITACORA);
        $form->setId('frm_edit_bitacora');
        $form->setAction('?mod=' . $modulo . '&niv=1&task=edit&idUsuario=' . $id . '&id_element=' . $id_edit);
        $form->setMethod('post');
        $form->setClassEtiquetas('td_label');
        $form->setTableId('tb_add_bitacora');

        $regiones = $daoPlaneacion->getRegiones('der_nombre');
        $opciones = null;
        if (isset($regiones)) {
            foreach ($regiones as $region) {
                $opciones[count($opciones)] = array('value' => $region['id'],
                    'texto' => $region['nombre']);
            }
        }

        $form->addEtiqueta(PLANEACION_REGION);
        $form->addSelect('select', 'sel_region', 'sel_region', $opciones, '', $regionAc, '', 'onChange="submit();" required');

        $departamentos = $daoPlaneacion->getDepartamento($regionAc, 'dep_nombre');
        $opciones = null;
        if (isset($departamentos)) {
            foreach ($departamentos as $departamento) {
                $opciones[count($opciones)] = array('value' => $departamento['id'],
                    'texto' => $departamento['nombre']);
            }
        }

        $form->addEtiqueta(PLANEACION_DEPARTAMENTO);
        $form->addSelect('select', 'sel_departamento', 'sel_departamento', $opciones, '', $departamentoAc, '', 'onChange="submit();" required');

        $muncipios = $daoPlaneacion->getMunicipio($departamentoAc, 'mun_nombre');
        $opciones = null;
        if (isset($muncipios)) {
            foreach ($muncipios as $municipio) {
                $opciones[count($opciones)] = array('value' => $municipio['id'],
                    'texto' => $municipio['nombre']);
            }
        }

        $form->addEtiqueta(PLANEACION_MUNICIPIO);
        $form->addSelect('select', 'sel_municipios', 'sel_municipios', $opciones, '', $muncipioAc, '', 'onChange="submit();" required');

        $centrosPoblados = $daoBeneficiarios->getCentrosPoblados($muncipioAc);
        $opciones = null;
        if (isset($centrosPoblados)) {
            foreach ($centrosPoblados as $centroPoblado) {
                $opciones[count($opciones)] = array('value' => $centroPoblado->getIdCentroPoblado(),
                    'texto' => $centroPoblado->getNombre());
            }
        }

        $form->addEtiqueta(CENTRO_POBLADO_BENEFICIARIO);
        $form->addSelect('select', 'sel_centro_poblado', 'sel_centro_poblado', $opciones, '', $centroPobladoAc, '', 'onChange="submit();" required');

        $beneficiarios = $daoBeneficiarios->getBeneficiarios($condicion);
        $opciones = null;
        if (isset($beneficiarios)) {
            foreach ($beneficiarios as $beneficiario) {
                $opciones[count($opciones)] = array('value' => $beneficiario['idBeneficiario'],
                    'texto' => $beneficiario['centropoblado'] . " - " . $beneficiario['tipo'] . " - " . $beneficiario['nombre']);
            }
        }

        $form->addEtiqueta(BITACORA_BENEFICIARIO);
        $form->addSelect('select', 'sel_beneficiario', 'sel_beneficiario', $opciones, '', $beneficiarioAc, '', ' required');

        $form->addEtiqueta(BITACORA_ACTIVIDAD);
        $form->addTextArea('textarea', 'txt_descripcion', 'txt_descripcion', '100', '5', $bitacora->getDescripcionActividad(), '', ' title="' . $html->traducirTildes(TITLE_ALFANUMERICO) . '" required');

        $form->addEtiqueta(BITACORA_FECHA_INICIO);
        $form->addInputDate('date', 'txt_fecha_inicio', 'txt_fecha_inicio', $bitacora->getFechaInicio(), '%Y-%m-%d', '16', '16', '', 'pattern="' . PATTERN_FECHA . '" title="' . $html->traducirTildes(TITLE_FECHA) . '" required');

        $form->addEtiqueta(BITACORA_FECHA_FIN);
        $form->addInputDate('date', 'txt_fecha_fin', 'txt_fecha_fin', $bitacora->getFechaFin(), '%Y-%m-%d', '16', '16', '', 'pattern="' . PATTERN_FECHA . '" title="' . $html->traducirTildes(TITLE_FECHA) . '" required');

        $form->addInputButton('submit', 'btn_add', 'btn_add', BTN_ACEPTAR, 'button', 'onclick="cancelarAccion(\'frm_edit_bitacora\',\'?mod=' . $modulo . '&niv=' . $niv . '&idUsuario=' . $id . '&task=saveEdit&id_element=' . $id_edit . '\');"');
        $form->addInputButton('button', 'cancel', 'cancel', BTN_CANCELAR, 'button', 'onclick="cancelarAccion(\'frm_edit_bitacora\',\'?mod=' . $modulo . '&niv=' . $niv . '&id_element=' . $id . '&task=seePersonal\');"');

        $form->writeForm();
        break;
    /**
     * la variable saveEdit, permite actualizar el bitacora en la base 
     * de datos @see \CBitacora
     */
    case 'saveEdit':
        $idBitacora = $_REQUEST['id_element'];
        $usuario = $_REQUEST['idUsuario'];
        $beneficiario = $_REQUEST['sel_beneficiario'];
        $descripcionActividad = $_REQUEST['txt_descripcion'];
        $fechaInicio = $_REQUEST['txt_fecha_inicio'];
        $fechaFin = $_REQUEST['txt_fecha_fin'];

        $bitacora = new CBitacora($idBitacora, $usuario, $beneficiario, $descripcionActividad, $fechaInicio, $fechaFin);

        $r = $daoBitacora->updateBitacora($bitacora);
        $m = ERROR_EDITAR_PLANEACION_BITACORA;
        if ($r == 'true') {
            $m = EXITO_EDITAR_PLANEACION_BITACORA;
        }
        echo $html->generaAviso($m, "?mod=" . $modulo . "&niv=1&task=seePersonal&id_element=" . $usuario);
        break;

    /**
     * la variable list, permite hacer la carga la página con la lista de 
     * objetos productos según los parámetros de entrada
     */
    case 'see':
        $idUsuario = $_REQUEST['idUsuario'];
        $idBitacora = $_REQUEST['id_element'];
        $form = new CHtmlForm();
        $form->setTitle(TITULO_ACTIVIDADES_BITACORA);
        $form->setMethod('post');
        $form->setOptions('autoClean', false);
        $form->setAction('?mod=' . $modulo . '&niv=' . $niv . '&id_element=' . $idUsuario . '&task=seePersonal');
        $form->addInputButton('submit', 'ok', 'ok', BTN_ATRAS, 'button', '');
        $form->writeForm();

        $dt = new CHtmlDataTable();
        $dt->setTitleTable(TITULO_ACTIVIDADES_BITACORA);
        $titulos = array(BITACORA_ACTIVIDAD_FECHA, BITACORA_ACTIVIDAD_EJECUTADA_DESCRIPCION, TITULO_ACTIVIDADES_BITACORA_HALLAZGOS_PENDIENTES, TITULO_ACTIVIDADES_BITACORA_ESTADO);
        $actividades = $daoActividadBitacora->getActividadByBitacora($idBitacora);
        $dt->setTitleRow($titulos);
        $dt->setDataRows($actividades);
        $otros = array('link' => "?mod=" . $modulo . "&niv=" . $niv . "&task=aprobarActividad&idUsuario=" . $idUsuario . "&idBitacora=" . $idBitacora, 'img' => 'marcado.gif', 'alt' => "Completar");
        $dt->addOtrosLink($otros);
        $dt->setSeeLink("?mod=" . $modulo . "&niv=" . $niv . "&task=seeActividad&idBitacora=" . $idBitacora . "&idUsuario=" . $idUsuario);
        $dt->setType(1);
        $pag_crit = "";
        $dt->setPag(1, $pag_crit);
        $dt->writeDataTable($niv);

        $form = new CHtmlForm();
        $form->setTitle(TITULO_ANTICIPO_BITACORA);
        $form->setMethod('post');
        $form->setOptions('autoClean', false);
        $form->writeForm();

        $dt = new CHtmlDataTable();
        $dt->setTitleTable(TITULO_ANTICIPO_BITACORA);
        $titulos = array(BITACORA_ANTICIPO_FECHA, BITACORA_ANTICIPO_VALOR);
        $anticipos = $daoAnticipo->getAnticiposByBitacora($idBitacora);
        $dt->setTitleRow($titulos);
        $dt->setDataRows($anticipos);
        $dt->setEditLink("?mod=" . $modulo . "&niv=" . $niv . "&task=editGasto&idBitacora=" . $idBitacora . "&idUsuario=" . $idUsuario);
        $dt->setDeleteLink("?mod=" . $modulo . "&niv=" . $niv . "&task=deleteGasto&idBitacora=" . $idBitacora . "&idUsuario=" . $idUsuario);
        $dt->setAddLink("?mod=" . $modulo . "&niv=" . $niv . "&task=addGasto&idBitacora=" . $idBitacora . "&idUsuario=" . $idUsuario);
        $dt->setType(1);
        $dt->setFormatRow(array(null, array(2, ',', '.')));
        $dt->setSumColumns(array(2));
        $pag_crit = "";
        $dt->setPag(1, $pag_crit);
        $dt->writeDataTable($niv);
        break;

    /**
     * la variable aprobarActividad, permite hacer el cambio de estado de 
     * actividad
     */
    case 'aprobarActividad':
        $idUsuario = $_REQUEST['idUsuario'];
        $idBitacora = $_REQUEST['idBitacora'];
        $id_completar = $_REQUEST['id_element'];
        $r = $daoActividadBitacora->cambiarEstado($id_completar);
        $m = ERROR_COMPLETAR_ACTIVIDAD_BITACORA;
        if ($r == 'true') {
            $actividad = $daoActividadBitacora->getActividadById($id_completar);
            $m = "Completado";
            if ($actividad->getEstado() == "0") {
                $m = "No Completado";
            }
            $m = EXITO_COMPLETAR_ACTIVIDAD_BITACORA . $m;
        }
        echo $html->generaAviso($m, "?mod=" . $modulo . "&niv=1&idUsuario=" . $idUsuario . "&id_element=" . $idBitacora . "&task=see");

        break;

    /**
     * Permite agregar un nuevo gasto.
     */
    case 'addGasto':
        $idUsuario = $_REQUEST['idUsuario'];
        $idBitacora = $_REQUEST['idBitacora'];
        $form = new CHtmlForm();
        $form->setTitle(TITULO_AGREGAR_BITACORA_ANTICIPO);
        $form->setId('frm_add_gasto');
        $form->setAction('?mod=' . $modulo . '&niv=1&task=saveAddGasto&idUsuario=' . $idUsuario . '&idBitacora=' . $idBitacora);
        $form->setMethod('post');
        $form->setTableId('tb_add_bitacora');

        $form->addEtiqueta(BITACORA_ANTICIPO_FECHA);
        $form->addInputDate('date', 'txt_fecha', 'txt_fecha', '', '%Y-%m-%d', '16', '16', '', 'pattern="' . PATTERN_FECHA . '" title="' . $html->traducirTildes(TITLE_FECHA) . '" required');

        $form->addEtiqueta(BITACORA_ANTICIPO_VALOR);
        $form->addInputText('text', 'txt_valor', 'txt_valor', '19', '19', '', '', ' pattern="' . PATTERN_NUMEROS_FINANCIEROS . '" title="' . $html->traducirTildes(TITLE_NUMEROS_FINANCIEROS) . '" onkeyup="formatearNumero(this);" required');

        $form->addInputButton('submit', 'btn_enviar', 'btn_enviar', BTN_ACEPTAR, 'button', '');
        $form->addInputButton('button', 'cancel', 'cancel', BTN_CANCELAR, 'button', 'onclick="cancelarAccion(\'frm_add_gasto\',\'?mod=' . $modulo . '&niv=' . $niv . '&id_element=' . $idBitacora . '&idUsuario=' . $idUsuario . '&task=see\');"');

        $form->writeForm();
        break;

    /**
     * Guarda los datos de nuevo registro y muestra el mensaje de éxito o error.
     */
    case 'saveAddGasto':
        $idBitacora = $_REQUEST['idBitacora'];
        $idUsuario = $_REQUEST['idUsuario'];
        $fecha = $_REQUEST['txt_fecha'];
        $valor = str_replace(".", "", $_REQUEST['txt_valor']);

        $anticipo = new CAnticipo(null, $fecha, $valor, $idBitacora);

        $r = $daoAnticipo->insertAnticipo($anticipo);
        $m = ERROR_AGREGAR_ANTICIPO_BITACORA;
        if ($r == 'true') {
            $m = EXITO_AGREGAR_ANTICIPO_BITACORA;
        }
        echo $html->generaAviso($m, "?mod=" . $modulo . "&niv=1&task=see&id_element=" . $idBitacora . "&idUsuario=" . $idUsuario);
        break;

    /**
     * Permite la edición de un gasto.
     */
    case 'editGasto':
        $idAnticipo = $_REQUEST['id_element'];
        $idUsuario = $_REQUEST['idUsuario'];
        $idBitacora = $_REQUEST['idBitacora'];

        $anticipo = $daoAnticipo->getAnticipoById($idAnticipo);

        $form = new CHtmlForm();
        $form->setTitle(TITULO_EDITAR_BITACORA_ANTICIPO);
        $form->setId('frm_edit_gasto');
        $form->setAction('?mod=' . $modulo . '&niv=1&task=saveEditGasto&idUsuario=' . $idUsuario . '&idBitacora=' . $idBitacora . '&idAnticipo=' . $idAnticipo);
        $form->setMethod('post');
        $form->setTableId('tb_add_bitacora');

        $form->addEtiqueta(BITACORA_ANTICIPO_FECHA);
        $form->addInputDate('date', 'txt_fecha', 'txt_fecha', $anticipo->getFecha(), '%Y-%m-%d', '16', '16', '', 'pattern="' . PATTERN_FECHA . '" title="' . $html->traducirTildes(TITLE_FECHA) . '" required');

        $form->addEtiqueta(BITACORA_ANTICIPO_VALOR);
        $form->addInputText('text', 'txt_valor', 'txt_valor', '19', '19', $anticipo->getValor(), '', ' pattern="' . PATTERN_NUMEROS_FINANCIEROS . '" title="' . $html->traducirTildes(TITLE_NUMEROS_FINANCIEROS) . '" onkeyup="formatearNumero(this);" required');

        $form->addInputButton('submit', 'btn_enviar', 'btn_enviar', BTN_ACEPTAR, 'button', '');
        $form->addInputButton('button', 'cancel', 'cancel', BTN_CANCELAR, 'button', 'onclick="cancelarAccion(\'frm_edit_gasto\',\'?mod=' . $modulo . '&niv=' . $niv . '&id_element=' . $idBitacora . '&idUsuario=' . $idUsuario . '&task=see\');"');

        $form->writeForm();
        break;

    /**
     * Permite guardar la edición del gasto.
     */
    case 'saveEditGasto':
        $idAnticipo = $_REQUEST['idAnticipo'];
        $idBitacora = $_REQUEST['idBitacora'];
        $idUsuario = $_REQUEST['idUsuario'];
        $fecha = $_REQUEST['txt_fecha'];
        $valor = str_replace(".", "", $_REQUEST['txt_valor']);

        $anticipo = new CAnticipo($idAnticipo, $fecha, $valor, $idBitacora);

        $r = $daoAnticipo->updateAnticipo($anticipo);
        $m = ERROR_EDITAR_ANTICIPO_BITACORA;
        if ($r == 'true') {
            $m = EXITO_EDITAR_ANTICIPO_BITACORA;
        }
        echo $html->generaAviso($m, "?mod=" . $modulo . "&niv=1&task=see&id_element=" . $idBitacora . "&idUsuario=" . $idUsuario);
        break;

    /**
     * la variable delete, permite hacer la carga del objeto bitacora 
     * y espera confirmacion de eliminarlo @see \CBitacora
     */
    case 'deleteGasto':
        $id_delete = $_REQUEST['id_element'];
        $idUsuario = $_REQUEST['idUsuario'];
        $idBitacora = $_REQUEST['idBitacora'];
        echo $html->generaAdvertencia(CONFIRMAR_BORRAR_ANTICIPO_BITACORA, '?mod=' . $modulo . '&niv=1&task=confirmDeleteGasto&id_element=' . $id_delete . '&idUsuario=' . $idUsuario . '&idBitacora=' . $idBitacora, 'onclick=location.href=\'?mod=' . $modulo . '&niv=1&id_element=' . $idBitacora . '&idUsuario=' . $idUsuario . '&task=see\'');
        break;
    /**
     * la variable confirmDelete, permite eliminar el objeto registro producto 
     * de la base de datos @see \CRegistroInversion
     */
    case 'confirmDeleteGasto':
        $idUsuario = $_REQUEST['idUsuario'];
        $idBitacora = $_REQUEST['idBitacora'];
        $id_delete = $_REQUEST['id_element'];
        $r = $daoAnticipo->deleteAnticipoById($id_delete);
        $m = ERROR_BORRAR_ANTICIPO_BITACORA;
        if ($r == 'true') {
            $m = EXITO_BORRAR_ANTICIPO_BITACORA;
        }
        echo $html->generaAviso($m, "?mod=" . $modulo . "&niv=1&task=see&id_element=" . $idBitacora . '&idUsuario=' . $idUsuario);
        break;

    /**
     * Permite ver las tablas asociadas a la tabla actividad
     */
    case 'seeActividad':
        $idUsuario = $_REQUEST['idUsuario'];
        $idActividad = $_REQUEST['id_element'];
        $idBitacora = $_REQUEST['idBitacora'];

        $form = new CHtmlForm();
        $form->setTitle(TITULO_ACTIVIDADES_BITACORA_REGISTRO_FOTOGRAFICO);
        $form->setMethod('post');
        $form->setOptions('autoClean', false);
        $form->setAction('?mod=' . $modulo . '&niv=' . $niv . '&task=see&id_element=' . $idBitacora . "&idUsuario=" . $idUsuario);
        $form->addInputButton('submit', 'ok', 'ok', BTN_ATRAS, 'button', '');
        $form->writeForm();

        $dt = new CHtmlDataTable();
        $dt->setTitleTable(TITULO_ACTIVIDADES_BITACORA_REGISTRO_FOTOGRAFICO);
        $titulos = array(ACTIVIDADES_BITACORA_REGISTRO_FOTOGRAFICO_DESCRIPCION, ACTIVIDADES_BITACORA_REGISTRO_FOTOGRAFICO_ARCHIVO);
        $registrosFotografico = $daoRegistroFotografico->getRegistroFotograficoByActividad($idActividad);
        $dt->setTitleRow($titulos);
        $dt->setDataRows($registrosFotografico);
        $dt->setType(1);
        $pag_crit = "";
        $dt->setPag(1, $pag_crit);
        $dt->writeDataTable($niv);

        $form = new CHtmlForm();
        $form->setTitle(TITULO_ACTIVIDADES_BITACORA_HALLAZGOS_PENDIENTES);
        $form->setMethod('post');
        $form->setOptions('autoClean', false);
        $form->writeForm();

        $dt = new CHtmlDataTable();
        $dt->setTitleTable(TITULO_ACTIVIDADES_BITACORA_HALLAZGOS_PENDIENTES);
        $titulos = array(ACTIVIDADES_BITACORA_HALLAZGOS_PENDIENTES_CLASIFICACION, ACTIVIDADES_BITACORA_HALLAZGOS_PENDIENTES_OBSERVACION, BITACORA_ACTIVIDAD_GASTO_ARCHIVO);
        $hallazgosPendientes = $daoHallazgosPendientes->getHallazgosPendientesByActividad($idActividad);
        $dt->setTitleRow($titulos);
        $dt->setDataRows($hallazgosPendientes);
        $dt->setType(1);
        $pag_crit = "";
        $dt->setPag(1, $pag_crit);
        $dt->writeDataTable($niv);

        $form = new CHtmlForm();
        $form->setTitle(TITULO_ACTIVIDADES_BITACORA_GASTOS);
        $form->setMethod('post');
        $form->setOptions('autoClean', false);
        $form->writeForm();

        $dt = new CHtmlDataTable();
        $dt->setTitleTable(TITULO_ACTIVIDADES_BITACORA_GASTOS);
        $titulos = array(BITACORA_ACTIVIDAD_GASTO_DESCRIPCION, BITACORA_ACTIVIDAD_GASTO_VALOR, BITACORA_ACTIVIDAD_GASTO_ARCHIVO, BITACORA_ACTIVIDAD_GASTO_TIPO, BITACORA_ACTIVIDAD_GASTO_ESTADO);
        $gastos = $daoGastos->getGastosByActividad($idActividad);
        $dt->setTitleRow($titulos);
        $dt->setDataRows($gastos);
        $otros = array('link' => "?mod=" . $modulo . "&niv=" . $niv . "&task=aprobarGasto&idUsuario=" . $idUsuario . "&idActividad=" . $idActividad . "&idBitacora=" . $idBitacora, 'img' => 'marcado.gif', 'alt' => "Completar");
        $dt->addOtrosLink($otros);
        $dt->setType(1);
        $pag_crit = "";
        $dt->setPag(1, $pag_crit);
        $dt->setFormatRow(array(null, array(2, ',', '.'), null, null, null));
        $dt->setSumColumns(array(2));
        $dt->writeDataTable($niv);

        break;

    /**
     * Cambia el estado de un gasto.
     */
    case 'aprobarGasto':
        $idUsuario = $_REQUEST['idUsuario'];
        $idActividad = $_REQUEST['idActividad'];
        $idBitacora = $_REQUEST['idBitacora'];
        $id_completar = $_REQUEST['id_element'];
        $r = $daoGastos->cambiarEstado($id_completar);
        $m = ERROR_APROBAR_GASTO;
        if ($r == 'true') {
            $gasto = $daoGastos->getGastoById($id_completar);
            $m = "No Aprobado";
            if ($gasto->getEstado() == "1") {
                $m = "Aprobado";
            }
            $m = EXITO_APROBAR_GASTO . $m;
        }
        echo $html->generaAviso($m, "?mod=" . $modulo . "&niv=1&idUsuario=" . $idUsuario . "&id_element=" . $idActividad . "&idBitacora=" . $idBitacora . "&task=seeActividad");
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

