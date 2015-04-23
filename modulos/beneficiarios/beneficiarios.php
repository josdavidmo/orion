<?php
/**
 * Modulo Beneficiarios
 * Maneja el modulo beneficiarios en union con CBeneficiario, CBeneficiarioData
 *
 * @see \CBeneficiario
 * @see \CBeneficiarioData
 *
 * @package modulos
 * @subpackage inventarios
 * @author SERTIC SAS
 * @version 2014.09.21
 * @copyright SERTIC SAS
 */
//no permite el acceso directo
defined('_VALID_PRY') or die('Restricted access');
$daoBeneficiarios = new CBeneficiarioData($db);
$daoBasicas = new CBasicaData($db);
$daoPlaneacion = new CPlaneacionData($db);
$task = $_REQUEST['task'];
$modulo = $_REQUEST['mod'];
$niv = $_REQUEST['niv'];
if (empty($task))
    $task = 'list';

switch ($task) {
    /**
     * la variable list, permite hacer la carga la página con la lista de 
     * objetos beneficiarios según los parámetros de entrada
     */
    case 'list':
        $condicion = "1";
        $tipoPagina = $_REQUEST['tipo'];
        $titulo = TITULO_BENEFICIARIOS;
        if ($tipoPagina == 'false') {
            $titulo = TITULO_NODOS;
        }
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
        if (isset($_REQUEST['sel_meta']) &&
                $_REQUEST['sel_meta'] != '-1') {
            $condicion .= " AND b.idMetaBeneficiario = " . $_REQUEST['sel_meta'];
        }
        if (isset($_REQUEST['sel_estado']) &&
                $_REQUEST['sel_estado'] != '-1') {
            $condicion .= " AND b.idEstadoBeneficiario = " . $_REQUEST['sel_estado'];
        }
        if (isset($_REQUEST['sel_dda']) &&
                $_REQUEST['sel_dda'] != '-1') {
            $condicion .= " AND b.idDDABeneficiario = " . $_REQUEST['sel_dda'];
        }
        if (isset($_REQUEST['sel_grupo']) &&
                $_REQUEST['sel_grupo'] != '-1') {
            $condicion .= " AND b.idGrupoBeneficiario = " . $_REQUEST['sel_grupo'];
        }
        if (isset($_REQUEST['sel_tipo']) &&
                $_REQUEST['sel_tipo'] != '-1') {
            $condicion .= " AND b.idTipoBeneficiario = " . $_REQUEST['sel_tipo'];
        }
        $form = new CHtmlForm();
        $form->setId('frm_filtrar_beneficiarios');
        $form->setTitle($titulo);
        $form->setAction("?mod=" . $modulo . "&niv=" . $niv . "&tipo=" . $tipoPagina);
        $form->setMethod("post");

        $regiones = $daoPlaneacion->getRegiones('der_nombre');
        $opciones = null;
        if (isset($regiones)) {
            foreach ($regiones as $region) {
                $opciones[count($opciones)] = array('value' => $region['id'],
                    'texto' => $region['nombre']);
            }
        }

        $form->addEtiqueta(PLANEACION_REGION);
        $form->addSelect('select', 'sel_region', 'sel_region', $opciones, '', $_REQUEST['sel_region'], '', ' onChange="submit();"');

        $departamentos = $daoPlaneacion->getDepartamento($_REQUEST['sel_region'], 'dep_nombre');
        $opciones = null;
        if (isset($departamentos)) {
            foreach ($departamentos as $departamento) {
                $opciones[count($opciones)] = array('value' => $departamento['id'],
                    'texto' => $departamento['nombre']);
            }
        }

        $form->addEtiqueta(PLANEACION_DEPARTAMENTO);
        $form->addSelect('select', 'sel_departamento', 'sel_departamento', $opciones, '', $_REQUEST['sel_departamento'], '', ' onChange="submit();"');

        $muncipios = $daoPlaneacion->getMunicipio($_REQUEST['sel_departamento'], 'mun_nombre');
        $opciones = null;
        if (isset($muncipios)) {
            foreach ($muncipios as $municipio) {
                $opciones[count($opciones)] = array('value' => $municipio['id'],
                    'texto' => $municipio['nombre']);
            }
        }

        $form->addEtiqueta(PLANEACION_MUNICIPIO);
        $form->addSelect('select', 'sel_municipios', 'sel_municipios', $opciones, '', $_REQUEST['sel_municipios'], '', ' onChange="submit();"');

        $centrosPoblados = $daoBeneficiarios->getCentrosPoblados($_REQUEST['sel_municipios']);
        $opciones = null;
        if (isset($centrosPoblados)) {
            foreach ($centrosPoblados as $centroPoblado) {
                $opciones[count($opciones)] = array('value' => $centroPoblado->getIdCentroPoblado(),
                    'texto' => $centroPoblado->getNombre());
            }
        }

        $form->addEtiqueta(CENTRO_POBLADO_BENEFICIARIO);
        $form->addSelect('select', 'sel_centro_poblado', 'sel_centro_poblado', $opciones, '', $_REQUEST['sel_centro_poblado'], '', ' onChange="submit();"');

        $metasBeneficiario = $daoBasicas->getBasicas('metabeneficiario');
        $opciones = null;
        if (isset($metasBeneficiario)) {
            foreach ($metasBeneficiario as $metaBeneficiario) {
                $opciones[count($opciones)] = array('value' => $metaBeneficiario->getId(),
                    'texto' => $metaBeneficiario->getDescripcion());
            }
        }

        $form->addEtiqueta(META_BENEFICIARIO);
        $form->addSelect('select', 'sel_meta', 'sel_meta', $opciones, '', $_REQUEST['sel_meta'], '', ' onChange="submit();"');

        $estadosBeneficiario = $daoBasicas->getBasicas('estadobeneficiario');
        $opciones = null;
        if (isset($estadosBeneficiario)) {
            foreach ($estadosBeneficiario as $estadoBeneficiario) {
                $opciones[count($opciones)] = array('value' => $estadoBeneficiario->getId(),
                    'texto' => $estadoBeneficiario->getDescripcion());
            }
        }

        $form->addEtiqueta(ESTADO_BENEFICIARIO);
        $form->addSelect('select', 'sel_estado', 'sel_estado', $opciones, '', $_REQUEST['sel_estado'], '', ' onChange="submit();"');

        $ddaBeneficiarios = $daoBasicas->getBasicas('ddabeneficiario');
        $opciones = null;
        if (isset($ddaBeneficiarios)) {
            foreach ($ddaBeneficiarios as $ddaBeneficiario) {
                $opciones[count($opciones)] = array('value' => $ddaBeneficiario->getId(),
                    'texto' => $ddaBeneficiario->getDescripcion());
            }
        }

        $form->addEtiqueta(DDA_BENEFICIARIO);
        $form->addSelect('select', 'sel_dda', 'sel_dda', $opciones, '', $_REQUEST['sel_dda'], '', ' onChange="submit();"');

        $gruposBeneficiarios = $daoBasicas->getBasicas('grupobeneficiario');
        $opciones = null;
        if (isset($gruposBeneficiarios)) {
            foreach ($gruposBeneficiarios as $grupoBeneficiario) {
                $opciones[count($opciones)] = array('value' => $grupoBeneficiario->getId(),
                    'texto' => $grupoBeneficiario->getDescripcion());
            }
        }

        $form->addEtiqueta(GRUPO_BENEFICIARIO);
        $form->addSelect('select', 'sel_grupo', 'sel_grupo', $opciones, '', $_REQUEST['sel_grupo'], '', ' onChange="submit();"');

        $opciones = null;
        $opciones[0] = array('value' => '1',
            'texto' => 'CP');
        $opciones[1] = array('value' => '2',
            'texto' => 'MUN');
        $opciones[2] = array('value' => '3',
            'texto' => 'PVD');
        $opciones[3] = array('value' => '4',
            'texto' => 'ZONA WIFI');
        $opciones[4] = array('value' => '7',
            'texto' => 'IP');
        $opciones[7] = array('value' => '8',
            'texto' => 'PVD+');
        $opciones[8] = array('value' => '9',
            'texto' => 'KVD');
        $opciones[9] = array('value' => '10',
            'texto' => 'KVDE');
        $opciones[10] = array('value' => '11',
            'texto' => 'CD');

        if ($tipoPagina == 'false') {
            $opciones = null;
            $opciones[0] = array('value' => '5',
                'texto' => 'NODO');
            $opciones[1] = array('value' => '6',
                'texto' => 'NODO INT.');
        }

        $form->addEtiqueta(TIPO_BENEFICIARIO);
        $form->addSelect('select', 'sel_tipo', 'sel_tipo', $opciones, '', $_REQUEST['sel_tipo'], '', ' onChange="submit();"');

        $form->addInputButton('button', 'btn_ver', 'btn_ver', BTN_VER, 'button', 'onclick="location.href=\'?mod=' . $modulo . '&niv=' . $niv . '&task=see&tipo=' . $tipoPagina . '\'"');
        $form->addInputButton('submit', 'btn_enviar', 'btn_enviar', BTN_ACEPTAR, 'button', '');
        $form->writeForm();

        $dt = new CHtmlDataTable();
        $dt->setTitleTable($titulo);
        $titulos = array(CODIGO_INTERVENTORIA_BENEFICIARIO,
            CODIGO_MINTIC_BENEFICIARIO,
            CODIGO_OPERADOR_BENEFICIARIO,
            CENTRO_POBLADO_BENEFICIARIO,
            NOMBRE_BENEFICIARIO,
            LATITUD_BENEFICIARIO,
            LONGITUD_BENEFICIARIO,
            FECHA_INICIO_BENEFICIARIO,
            ESTADO_BENEFICIARIO,
            GRUPO_BENEFICIARIO,
            TIPO_BENEFICIARIO);
        $beneficiarios = $daoBeneficiarios->getBeneficiariosByTipo($tipoPagina, $condicion);
        $dt->setTitleRow($titulos);
        $dt->setDataRows($beneficiarios);
        $dt->setEditLink("?mod=" . $modulo . "&niv=" . $niv . "&task=edit&tipo=" . $tipoPagina);
        $dt->setDeleteLink("?mod=" . $modulo . "&niv=" . $niv . "&task=delete&tipo=" . $tipoPagina);
        $dt->setSeeLink("?mod=" . $modulo . "&niv=" . $niv . "&task=see&tipo=" . $tipoPagina);
        $dt->setAddLink("?mod=" . $modulo . "&niv=" . $niv . "&task=add&tipo=" . $tipoPagina);
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
        $form = new CHtmlForm();
        $tipoPagina = $_REQUEST['tipo'];

        $opciones = null;

        $form->setTitle(TITULO_AGREGAR_BENEFICIARIOS);
        $form->setId('frm_add_beneficiario');
        $form->setAction('?mod=' . $modulo . '&niv=1&task=saveAdd&tipo=' . $tipoPagina);
        $form->setMethod('post');

        $form->addEtiqueta(CODIGO_INTERVENTORIA_BENEFICIARIO);
        $form->addInputText('text', 'txt_codigo_interventoria', 'txt_codigo_interventoria', '200', '200', '', null, 'autofocus pattern="' . PATTERN_ALFANUMERICO . '" title="' . $html->traducirTildes(TITLE_ALFANUMERICO) . '"');

        $form->addEtiqueta(CODIGO_MINTIC_BENEFICIARIO);
        $form->addInputText('text', 'txt_codigo_mintic', 'txt_codigo_mintic', '200', '200', '', null, 'pattern="' . PATTERN_ALFANUMERICO . '" title="' . $html->traducirTildes(TITLE_ALFANUMERICO) . '"');

        $form->addEtiqueta(CODIGO_OPERADOR_BENEFICIARIO);
        $form->addInputText('text', 'txt_codigo_operador', 'txt_codigo_operador', '200', '200', '', null, 'pattern="' . PATTERN_ALFANUMERICO . '" title="' . $html->traducirTildes(TITLE_ALFANUMERICO) . '"');

        $centrosPoblados = $daoBeneficiarios->getCentrosPoblados();
        $opciones = null;
        if (isset($centrosPoblados)) {
            foreach ($centrosPoblados as $centroPoblado) {
                $opciones[count($opciones)] = array('value' => $centroPoblado->getIdCentroPoblado(),
                    'texto' => $centroPoblado->getNombre());
            }
        }

        $form->addEtiqueta(CENTRO_POBLADO_BENEFICIARIO);
        $form->addSelect('select', 'sel_centro_poblado', 'sel_centro_poblado', $opciones, '', '', '', ' required');

        $form->addEtiqueta(NOMBRE_BENEFICIARIO);
        $form->addInputText('text', 'txt_nombre', 'txt_nombre', '45', '45', '', null, 'pattern="' . PATTERN_ALFANUMERICO . '" title="' . $html->traducirTildes(TITLE_ALFANUMERICO) . '"  required');

        $form->addEtiqueta(MSNM_BENEFICIARIO);
        $form->addInputText('text', 'txt_msnm', 'txt_msnm', '45', '45', '', null, 'pattern="' . PATTERN_ALFANUMERICO . '" title="' . $html->traducirTildes(TITLE_ALFANUMERICO) . '"');

        $form->addEtiqueta(LATITUD_GRADOS_BENEFICIARIO);
        $form->addInputText('text', 'txt_latitud_grados', 'txt_latitud_grados', '5', '5', '', null, 'pattern="' . PATTERN_NUMEROS . '" title="' . $html->traducirTildes(TITLE_NUMEROS) . '"');

        $form->addEtiqueta(LATITUD_MINUTOS_BENEFICIARIO);
        $form->addInputText('text', 'txt_latitud_minutos', 'txt_latitud_minutos', '5', '5', '', null, 'pattern="' . PATTERN_NUMEROS . '" title="' . $html->traducirTildes(TITLE_NUMEROS) . '"');

        $form->addEtiqueta(LATITUD_SEGUNDOS_BENEFICIARIO);
        $form->addInputText('text', 'txt_latitud_segundos', 'txt_latitud_segundos', '5', '5', '', null, 'pattern="' . PATTERN_NUMEROS . '" title="' . $html->traducirTildes(TITLE_NUMEROS) . '"');

        $opciones = null;
        $opciones[0] = array('value' => '1',
            'texto' => 'Sur');
        $opciones[1] = array('value' => '0',
            'texto' => 'Norte');

        $form->addEtiqueta(LATITUD_N_S_BENEFICIARIO);
        $form->addSelect('select', 'sel_n_s', 'sel_n_s', $opciones, '', '', '', ' required');

        $form->addEtiqueta(LONGITUD_GRADOS_BENEFICIARIO);
        $form->addInputText('text', 'txt_longitud_grados', 'txt_longitud_grados', '5', '5', '', null, 'pattern="' . PATTERN_NUMEROS . '" title="' . $html->traducirTildes(TITLE_NUMEROS) . '"');

        $form->addEtiqueta(LONGITUD_MINUTOS_BENEFICIARIO);
        $form->addInputText('text', 'txt_longitud_minutos', 'txt_longitud_minutos', '5', '5', '', null, 'pattern="' . PATTERN_NUMEROS . '" title="' . $html->traducirTildes(TITLE_NUMEROS) . '"');

        $form->addEtiqueta(LONGITUD_SEGUNDOS_BENEFICIARIO);
        $form->addInputText('text', 'txt_longitud_segundos', 'txt_longitud_segundos', '5', '5', '', null, 'title="' . $html->traducirTildes(TITLE_NUMEROS) . '"');

        $opciones = null;
        $opciones[0] = array('value' => '1',
            'texto' => 'Oeste');
        $opciones[1] = array('value' => '0',
            'texto' => 'Este');

        $form->addEtiqueta(LONGITUD_W_E_BENEFICIARIO);
        $form->addSelect('select', 'sel_w_e', 'sel_w_e', $opciones, '', '', '', ' required');

        $form->addEtiqueta(FECHA_INICIO_BENEFICIARIO);
        $form->addInputDate('date', 'txt_fecha_inicio', 'txt_fecha_inicio', '', '%Y-%m-%d', '16', '16', '', 'pattern="' . PATTERN_FECHA . '" title="' . $html->traducirTildes(TITLE_FECHA) . '"');

        $metasBeneficiario = $daoBasicas->getBasicas('metabeneficiario');
        $opciones = null;
        if (isset($metasBeneficiario)) {
            foreach ($metasBeneficiario as $metaBeneficiario) {
                $opciones[count($opciones)] = array('value' => $metaBeneficiario->getId(),
                    'texto' => $metaBeneficiario->getDescripcion());
            }
        }

        $form->addEtiqueta(META_BENEFICIARIO);
        $form->addSelect('select', 'sel_meta', 'sel_meta', $opciones, '', '', '', ' required');

        $estadosBeneficiario = $daoBasicas->getBasicas('estadobeneficiario');
        $opciones = null;
        if (isset($estadosBeneficiario)) {
            foreach ($estadosBeneficiario as $estadoBeneficiario) {
                $opciones[count($opciones)] = array('value' => $estadoBeneficiario->getId(),
                    'texto' => $estadoBeneficiario->getDescripcion());
            }
        }

        $form->addEtiqueta(ESTADO_BENEFICIARIO);
        $form->addSelect('select', 'sel_estado', 'sel_estado', $opciones, '', '', '', ' required');

        $ddaBeneficiarios = $daoBasicas->getBasicas('ddabeneficiario');
        $opciones = null;
        if (isset($ddaBeneficiarios)) {
            foreach ($ddaBeneficiarios as $ddaBeneficiario) {
                $opciones[count($opciones)] = array('value' => $ddaBeneficiario->getId(),
                    'texto' => $ddaBeneficiario->getDescripcion());
            }
        }

        $form->addEtiqueta(DDA_BENEFICIARIO);
        $form->addSelect('select', 'sel_dda', 'sel_dda', $opciones, '', '', '', ' required');

        $gruposBeneficiarios = $daoBasicas->getBasicas('grupobeneficiario');
        $opciones = null;
        if (isset($gruposBeneficiarios)) {
            foreach ($gruposBeneficiarios as $grupoBeneficiario) {
                $opciones[count($opciones)] = array('value' => $grupoBeneficiario->getId(),
                    'texto' => $grupoBeneficiario->getDescripcion());
            }
        }

        $form->addEtiqueta(GRUPO_BENEFICIARIO);
        $form->addSelect('select', 'sel_grupo', 'sel_grupo', $opciones, '', '', '', ' required');

        $opciones = null;
        $opciones[0] = array('value' => '1',
            'texto' => 'CP');
        $opciones[1] = array('value' => '2',
            'texto' => 'MUN');
        $opciones[2] = array('value' => '3',
            'texto' => 'PVD');
        $opciones[3] = array('value' => '4',
            'texto' => 'ZONA WIFI');
        $opciones[4] = array('value' => '7',
            'texto' => 'IP');
        $opciones[5] = array('value' => '8',
            'texto' => 'PVD+');
        $opciones[8] = array('value' => '9',
            'texto' => 'KVD');
        $opciones[9] = array('value' => '10',
            'texto' => 'KVDE');
        if ($tipoPagina == 'false') {
            $opciones = null;
            $opciones[0] = array('value' => '5',
                'texto' => 'NODO');
            $opciones[1] = array('value' => '6',
                'texto' => 'NODO INT.');
        }

        $form->addEtiqueta(TIPO_BENEFICIARIO);
        $form->addSelect('select', 'sel_tipo', 'sel_tipo', $opciones, '', '', '', ' required');

        $form->addInputButton('button', 'btn_importar', 'btn_importar', BTN_IMPORTAR, 'button', 'onclick="location.href=\'?mod=' . $modulo . '&niv=' . $niv . '&task=carga&tipo=' . $tipoPagina . '\'"');
        $form->addInputButton('submit', 'btn_enviar', 'btn_enviar', BTN_ACEPTAR, 'button', '');
        $form->addInputButton('button', 'cancel', 'cancel', BTN_CANCELAR, 'button', 'onclick="cancelarAccion(\'frm_add_beneficiario\',\'?mod=' . $modulo . '&niv=' . $niv . '&tipo=' . $tipoPagina . '\');"');

        $form->writeForm();

        break;
    /**
     * la variable saveAdd, permite almacenar el objeto beneficiario en la 
     * base de datos @see \CBeneficiario
     */
    case 'saveAdd':
        $tipoPagina = $_REQUEST['tipo'];
        $codigoInterventoria = $_REQUEST['txt_codigo_interventoria'];
        $codigoMintic = $_REQUEST['txt_codigo_mintic'];
        $codigoOperador = $_REQUEST['txt_codigo_operador'];
        $centroPoblado = $_REQUEST['sel_centro_poblado'];
        $nombre = $_REQUEST['txt_nombre'];
        $msnm = $_REQUEST['txt_msnm'];
        $latitudGrados = $_REQUEST['txt_latitud_grados'];
        $latitudMinutos = $_REQUEST['txt_latitud_minutos'];
        $latitudSegundos = $_REQUEST['txt_latitud_segundos'];
        $south = $_REQUEST['sel_n_s'];
        $longitudGrados = $_REQUEST['txt_longitud_grados'];
        $longitudMinutos = $_REQUEST['txt_longitud_minutos'];
        $longitudSegundos = $_REQUEST['txt_longitud_segundos'];
        $west = $_REQUEST['sel_w_e'];
        $fechaInicio = $_REQUEST['txt_fecha_inicio'];
        $meta = $_REQUEST['sel_meta'];
        $estado = $_REQUEST['sel_estado'];
        $dda = $_REQUEST['sel_dda'];
        $grupo = $_REQUEST['sel_grupo'];
        $tipo = $_REQUEST['sel_tipo'];
        $beneficiario = new CBeneficiario($idBeneficiario, $codigoInterventoria, $codigoMintic, $codigoOperador, $nombre, $msnm, $latitudGrados, $latitudMinutos, $latitudSegundos, $south, $longitudGrados, $longitudMinutos, $longitudSegundos, $west, $fechaInicio, $meta, $estado, $dda, $grupo, $centroPoblado, $tipo);

        $r = $daoBeneficiarios->insertBeneficiario($beneficiario);
        $m = ERROR_AGREGAR_BENEFICIARIOS;
        if ($r == 'true') {
            $m = EXITO_AGREGAR_BENEFICIARIOS;
        }
        echo $html->generaAviso($m, "?mod=" . $modulo . "&niv=1&task=list&tipo=" . $tipoPagina);

        break;
    /**
     * la variable delete, permite hacer la carga del objeto beneficiario 
     * y espera confirmacion de eliminarlo @see \CBeneficiario
     */
    case 'delete':
        $tipoPagina = $_REQUEST['tipo'];
        $id_delete = $_REQUEST['id_element'];
        echo $html->generaAdvertencia(CONFIRMAR_BORRAR_BENEFICIARIOS, '?mod=' . $modulo . '&niv=1&task=confirmDelete&id_element=' . $id_delete . '&tipo=' . $tipoPagina, 'onclick=location.href=\'?mod=' . $modulo . '&niv=1&tipo=' . $tipoPagina . '\'');
        break;
    /**
     * la variable confirmDelete, permite eliminar el objeto beneficiario de la 
     * base de datos @see \CBeneficiario
     */
    case 'confirmDelete':
        $tipoPagina = $_REQUEST['tipo'];
        $id_delete = $_REQUEST['id_element'];
        $r = $daoBeneficiarios->deleteBodegaById($id_delete);
        $m = ERROR_BORRAR_BENEFICIARIOS;
        if ($r == 'true') {
            $m = EXITO_BORRAR_BENEFICIARIOS;
        }
        echo $html->generaAviso($m, "?mod=" . $modulo . "&niv=1&task=list&tipo=" . $tipoPagina);
        break;

    /**
     * la variable edit, permite hacer la carga del objeto beneficiario y espera 
     * confirmacion de edicion @see \CBeneficiario
     */
    case 'edit':
        $id_edit = $_REQUEST['id_element'];
        $tipoPagina = $_REQUEST['tipo'];
        $beneficiario = $daoBeneficiarios->getBeneficiarioById($id_edit);
        $form = new CHtmlForm();

        $opciones = null;

        $form->setTitle(TITULO_EDITAR_BENEFICIARIOS);
        $form->setId('frm_edit_beneficiario');
        $form->setAction('?mod=' . $modulo . '&niv=1&task=saveEdit&id_element=' . $id_edit . '&tipo=' . $tipoPagina);
        $form->setMethod('post');

        $form->addEtiqueta(CODIGO_INTERVENTORIA_BENEFICIARIO);
        $form->addInputText('text', 'txt_codigo_interventoria', 'txt_codigo_interventoria', '200', '200', $beneficiario->getCodigoInterventoria(), null, 'autofocus pattern="' . PATTERN_ALFANUMERICO . '" title="' . $html->traducirTildes(TITLE_ALFANUMERICO) . '"');

        $form->addEtiqueta(CODIGO_MINTIC_BENEFICIARIO);
        $form->addInputText('text', 'txt_codigo_mintic', 'txt_codigo_mintic', '200', '200', $beneficiario->getCodigoMintic(), null, 'pattern="' . PATTERN_ALFANUMERICO . '" title="' . $html->traducirTildes(TITLE_ALFANUMERICO) . '"');

        $form->addEtiqueta(CODIGO_OPERADOR_BENEFICIARIO);
        $form->addInputText('text', 'txt_codigo_operador', 'txt_codigo_operador', '200', '200', $beneficiario->getCodigoOperador(), null, 'pattern="' . PATTERN_ALFANUMERICO . '" title="' . $html->traducirTildes(TITLE_ALFANUMERICO) . '"');

        $centrosPoblados = $daoBeneficiarios->getCentrosPoblados();
        $opciones = null;
        if (isset($centrosPoblados)) {
            foreach ($centrosPoblados as $centroPoblado) {
                $opciones[count($opciones)] = array('value' => $centroPoblado->getIdCentroPoblado(),
                    'texto' => $centroPoblado->getNombre());
            }
        }

        $form->addEtiqueta(CENTRO_POBLADO_BENEFICIARIO);
        $form->addSelect('select', 'sel_centro_poblado', 'sel_centro_poblado', $opciones, '', $beneficiario->getCentroPoblado(), '', ' required');

        $form->addEtiqueta(NOMBRE_BENEFICIARIO);
        $form->addInputText('text', 'txt_nombre', 'txt_nombre', '45', '45', $beneficiario->getNombre(), null, 'pattern="' . PATTERN_ALFANUMERICO . '" title="' . $html->traducirTildes(TITLE_ALFANUMERICO) . '"  required');

        $form->addEtiqueta(MSNM_BENEFICIARIO);
        $form->addInputText('text', 'txt_msnm', 'txt_msnm', '45', '45', $beneficiario->getMsnm(), null, 'pattern="' . PATTERN_ALFANUMERICO . '" title="' . $html->traducirTildes(TITLE_ALFANUMERICO) . '"');

        $form->addEtiqueta(LATITUD_GRADOS_BENEFICIARIO);
        $form->addInputText('text', 'txt_latitud_grados', 'txt_latitud_grados', '5', '5', $beneficiario->getLatitudGrados(), null, 'pattern="' . PATTERN_NUMEROS . '" title="' . $html->traducirTildes(TITLE_NUMEROS) . '"');

        $form->addEtiqueta(LATITUD_MINUTOS_BENEFICIARIO);
        $form->addInputText('text', 'txt_latitud_minutos', 'txt_latitud_minutos', '5', '5', $beneficiario->getLatitudMinutos(), null, 'pattern="' . PATTERN_NUMEROS . '" title="' . $html->traducirTildes(TITLE_NUMEROS) . '"');

        $form->addEtiqueta(LATITUD_SEGUNDOS_BENEFICIARIO);
        $form->addInputText('text', 'txt_latitud_segundos', 'txt_latitud_segundos', '5', '5', $beneficiario->getLatitudSegundos(), null, 'pattern="' . PATTERN_NUMEROS . '" title="' . $html->traducirTildes(TITLE_NUMEROS) . '"');

        $opciones = null;
        $opciones[0] = array('value' => '1',
            'texto' => 'Sur');
        $opciones[1] = array('value' => '0',
            'texto' => 'Norte');

        $form->addEtiqueta(LATITUD_N_S_BENEFICIARIO);
        $form->addSelect('select', 'sel_n_s', 'sel_n_s', $opciones, '', $beneficiario->getSouth(), '', ' required');

        $form->addEtiqueta(LONGITUD_GRADOS_BENEFICIARIO);
        $form->addInputText('text', 'txt_longitud_grados', 'txt_longitud_grados', '5', '5', $beneficiario->getLongitudGrados(), null, 'pattern="' . PATTERN_NUMEROS . '" title="' . $html->traducirTildes(TITLE_NUMEROS) . '"');

        $form->addEtiqueta(LONGITUD_MINUTOS_BENEFICIARIO);
        $form->addInputText('text', 'txt_longitud_minutos', 'txt_longitud_minutos', '5', '5', $beneficiario->getLongitudMinutos(), null, 'pattern="' . PATTERN_NUMEROS . '" title="' . $html->traducirTildes(TITLE_NUMEROS) . '"');

        $form->addEtiqueta(LONGITUD_SEGUNDOS_BENEFICIARIO);
        $form->addInputText('text', 'txt_longitud_segundos', 'txt_longitud_segundos', '5', '5', $beneficiario->getLongitudSegundos(), null, ' title="' . $html->traducirTildes(TITLE_NUMEROS) . '"');

        $opciones = null;
        $opciones[0] = array('value' => '1',
            'texto' => 'Oeste');
        $opciones[1] = array('value' => '0',
            'texto' => 'Este');

        $form->addEtiqueta(LONGITUD_W_E_BENEFICIARIO);
        $form->addSelect('select', 'sel_w_e', 'sel_w_e', $opciones, '', $beneficiario->getWest(), '', ' required');

        $form->addEtiqueta(FECHA_INICIO_BENEFICIARIO);
        $form->addInputDate('date', 'txt_fecha_inicio', 'txt_fecha_inicio', $beneficiario->getFechaInicio(), '%Y-%m-%d', '16', '16', '', 'pattern="' . PATTERN_FECHA . '" title="' . $html->traducirTildes(TITLE_FECHA) . '"');

        $metasBeneficiario = $daoBasicas->getBasicas('metabeneficiario');
        $opciones = null;
        if (isset($metasBeneficiario)) {
            foreach ($metasBeneficiario as $metaBeneficiario) {
                $opciones[count($opciones)] = array('value' => $metaBeneficiario->getId(),
                    'texto' => $metaBeneficiario->getDescripcion());
            }
        }

        $form->addEtiqueta(META_BENEFICIARIO);
        $form->addSelect('select', 'sel_meta', 'sel_meta', $opciones, '', $beneficiario->getMeta(), '', ' required');

        $estadosBeneficiario = $daoBasicas->getBasicas('estadobeneficiario');
        $opciones = null;
        if (isset($estadosBeneficiario)) {
            foreach ($estadosBeneficiario as $estadoBeneficiario) {
                $opciones[count($opciones)] = array('value' => $estadoBeneficiario->getId(),
                    'texto' => $estadoBeneficiario->getDescripcion());
            }
        }

        $form->addEtiqueta(ESTADO_BENEFICIARIO);
        $form->addSelect('select', 'sel_estado', 'sel_estado', $opciones, '', $beneficiario->getEstado(), '', ' required');

        $ddaBeneficiarios = $daoBasicas->getBasicas('ddabeneficiario');
        $opciones = null;
        if (isset($ddaBeneficiarios)) {
            foreach ($ddaBeneficiarios as $ddaBeneficiario) {
                $opciones[count($opciones)] = array('value' => $ddaBeneficiario->getId(),
                    'texto' => $ddaBeneficiario->getDescripcion());
            }
        }

        $form->addEtiqueta(DDA_BENEFICIARIO);
        $form->addSelect('select', 'sel_dda', 'sel_dda', $opciones, '', $beneficiario->getDda(), '', ' required');

        $gruposBeneficiarios = $daoBasicas->getBasicas('grupobeneficiario');
        $opciones = null;
        if (isset($gruposBeneficiarios)) {
            foreach ($gruposBeneficiarios as $grupoBeneficiario) {
                $opciones[count($opciones)] = array('value' => $grupoBeneficiario->getId(),
                    'texto' => $grupoBeneficiario->getDescripcion());
            }
        }

        $form->addEtiqueta(GRUPO_BENEFICIARIO);
        $form->addSelect('select', 'sel_grupo', 'sel_grupo', $opciones, '', $beneficiario->getGrupo(), '', ' required');

        $opciones = null;
        $opciones[0] = array('value' => '1',
            'texto' => 'CP');
        $opciones[1] = array('value' => '2',
            'texto' => 'MUN');
        $opciones[2] = array('value' => '3',
            'texto' => 'PVD');
        $opciones[3] = array('value' => '4',
            'texto' => 'ZONA WIFI');
        $opciones[4] = array('value' => '7',
            'texto' => 'IP');
        $opciones[5] = array('value' => '8',
            'texto' => 'PVD+');
        $opciones[8] = array('value' => '9',
            'texto' => 'KVD');
        $opciones[9] = array('value' => '10',
            'texto' => 'KVDE');

        if ($tipoPagina == 'false') {
            $opciones = null;
            $opciones[0] = array('value' => '5',
                'texto' => 'NODO');
            $opciones[1] = array('value' => '6',
                'texto' => 'NODO INT.');
        }

        $form->addEtiqueta(TIPO_BENEFICIARIO);
        $form->addSelect('select', 'sel_tipo', 'sel_tipo', $opciones, '', $beneficiario->getTipo(), '', ' required');

        $form->addInputButton('submit', 'btn_enviar', 'btn_enviar', BTN_ACEPTAR, 'button', '');
        $form->addInputButton('button', 'cancel', 'cancel', BTN_CANCELAR, 'button', 'onclick="cancelarAccion(\'frm_edit_beneficiario\',\'?mod=' . $modulo . '&niv=' . $niv . '&tipo=' . $tipoPagina . '\');"');

        $form->writeForm();

        break;

    /**
     * la variable saveEdit, permite actualizar el objeto bodega en la base 
     * de datos @see \CBeneficiario 
     */
    case 'saveEdit':
        $tipoPagina = $_REQUEST['tipo'];
        $idBeneficiario = $_REQUEST['id_element'];
        $codigoInterventoria = $_REQUEST['txt_codigo_interventoria'];
        $codigoMintic = $_REQUEST['txt_codigo_mintic'];
        $codigoOperador = $_REQUEST['txt_codigo_operador'];
        $centroPoblado = $_REQUEST['sel_centro_poblado'];
        $nombre = $_REQUEST['txt_nombre'];
        $msnm = $_REQUEST['txt_msnm'];
        $latitudGrados = $_REQUEST['txt_latitud_grados'];
        $latitudMinutos = $_REQUEST['txt_latitud_minutos'];
        $latitudSegundos = $_REQUEST['txt_latitud_segundos'];
        $south = $_REQUEST['sel_n_s'];
        $longitudGrados = $_REQUEST['txt_longitud_grados'];
        $longitudMinutos = $_REQUEST['txt_longitud_minutos'];
        $longitudSegundos = $_REQUEST['txt_longitud_segundos'];
        $west = $_REQUEST['sel_w_e'];
        $fechaInicio = $_REQUEST['txt_fecha_inicio'];
        $meta = $_REQUEST['sel_meta'];
        $estado = $_REQUEST['sel_estado'];
        $dda = $_REQUEST['sel_dda'];
        $grupo = $_REQUEST['sel_grupo'];
        $tipo = $_REQUEST['sel_tipo'];
        $beneficiario = new CBeneficiario($idBeneficiario, $codigoInterventoria, $codigoMintic, $codigoOperador, $nombre, $msnm, $latitudGrados, $latitudMinutos, $latitudSegundos, $south, $longitudGrados, $longitudMinutos, $longitudSegundos, $west, $fechaInicio, $meta, $estado, $dda, $grupo, $centroPoblado, $tipo);
        $r = $daoBeneficiarios->updateBeneficiario($beneficiario);
        $m = ERROR_EDITAR_BENEFICIARIOS;
        if ($r == 'true') {
            $m = EXITO_EDITAR_BENEFICIARIOS;
        }
        echo $html->generaAviso($m, "?mod=" . $modulo . "&niv=1&task=list&tipo=" . $tipoPagina);

        break;

    case 'see':
        $idBeneficiario = $_REQUEST['id_element'];

        $tipo = $_REQUEST['tipo'];
        $form = new CHtmlForm();

        $opciones = null;

        $form->setTitle(TITULO_UBICACION);
        $form->setId('frm_volver_beneficiario');
        $form->setAction('?mod=' . $modulo . '&niv=1&tipo=' . $tipo);
        $form->setMethod('post');
        $form->setOptions("autoClean", false);
        $form->addInputButton('submit', 'btn_enviar', 'btn_enviar', BTN_ATRAS, 'button', '');
        $form->writeForm();
        ?>
        <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js"></script>
        <script type="text/javascript">
            //<![CDATA[

            var customIcons = {
                1: {
                    icon: './templates/img/ico/CP.png'
                },
                2: {
                    icon: './templates/img/ico/MUN.png'
                },
                3: {
                    icon: './templates/img/ico/PVD.png'
                },
                4: {
                    icon: './templates/img/ico/WIFI.png'
                },
                5: {
                    icon: './templates/img/ico/NODO.png'
                },
                6: {
                    icon: './templates/img/ico/NODOINT.png'
                },
                7: {
                    icon: './templates/img/ico/IP.png'
                },
                8: {
                    icon: './templates/img/ico/PVD+.png'
                },
                9: {
                    icon: './templates/img/ico/KVD.png'
                },
                10: {
                    icon: './templates/img/ico/KVDE.png'
                },
                11: {
                    icon: './templates/img/ico/CD.png'
                }
            };

            function load() {
                var map = new google.maps.Map(document.getElementById("map"), {
                    center: new google.maps.LatLng(4.08972222, -72.9619444),
                    zoom: 5,
                    mapTypeId: 'terrain'
                });
                var infoWindow = new google.maps.InfoWindow;
                // Change this depending on the name of your PHP file
                downloadUrl("modulos/beneficiarios/MapsBeneficiario.php?idBeneficiario=<?= $idBeneficiario ?>&tipo=<?= $tipo ?>", function (data) {
                    var xml = data.responseXML;
                    var markers = xml.documentElement.getElementsByTagName("marker");
                    for (var i = 0; i < markers.length; i++) {
                        var name = markers[i].getAttribute("name");
                        var address = markers[i].getAttribute("address");
                        var type = markers[i].getAttribute("type");
                        var point = new google.maps.LatLng(
                                parseFloat(markers[i].getAttribute("lat")),
                                parseFloat(markers[i].getAttribute("lng")));
                        var html = "<b>" + name + "</b> <br/>" + address;
                        var icon = customIcons[type] || {};
                        var marker = new google.maps.Marker({
                            map: map,
                            position: point,
                            icon: icon.icon
                        });
                        bindInfoWindow(marker, map, infoWindow, html);
                    }
                });
            }

            function bindInfoWindow(marker, map, infoWindow, html) {
                google.maps.event.addListener(marker, 'click', function () {
                    infoWindow.setContent(html);
                    infoWindow.open(map, marker);
                });
            }

            function downloadUrl(url, callback) {
                var request = window.ActiveXObject ?
                        new ActiveXObject('Microsoft.XMLHTTP') :
                        new XMLHttpRequest;

                request.onreadystatechange = function () {
                    if (request.readyState == 4) {
                        request.onreadystatechange = doNothing;
                        callback(request, request.status);
                    }
                };

                request.open('GET', url, true);
                request.send(null);
            }

            function doNothing() {
            }


            //]]>
        </script>
        <center><div id="map" style="width: 1200px; height: 600px"></div></center>
            <?php
            break;

        case 'carga':
            $tipoPagina = $_REQUEST['tipo'];
            $form = new CHtmlForm();
            $form->setTitle(BENEFICIARIOS_CARGA_MASIVA);
            $form->setId('frm_carga');
            $form->setAction('?mod=' . $modulo . '&niv=1&task=saveCarga&tipo=' . $tipoPagina);
            $form->setMethod('post');
            $form->setClassEtiquetas('td_label');

            $form->addEtiqueta(SOPORTE_CAMBIOSYTRANSFERENCIAS);
            $form->addInputFile('file', 'file_carga', 'file_carga', 25, 'file', 'required');

            $form->addInputButton('submit', 'btn_enviar', 'btn_enviar', BTN_ACEPTAR, 'button', '');
            $form->addInputButton('button', 'cancel', 'cancel', BTN_CANCELAR, 'button', 'onclick="cancelarAccion(\'frm_carga\',\'?mod=' . $modulo . '&niv=' . $niv . '&tipo=' . $tipoPagina . '\');"');
            $form->addInputButton('button', 'btn_plantilla', 'btn_plantilla', BTN_PLANTILLA, 'button', 'onclick="location.href=\'formatos/beneficiarios/formato_carga_masiva_beneficiarios.xls\'"');
            $form->writeForm();

            break;

        case 'saveCarga':
            $tipoPagina = $_REQUEST['tipo'];
            $file = $_FILES['file_carga'];
            $r = $daoBeneficiarios->cargaMasiva($file);
            $m = ERROR_GUARDAR_BENEFICIARIOS;
            if ($r) {
                $m = EXITO_GUARDAR_BENEFICIARIOS;
            }
            echo $html->generaAviso($m, "?mod=" . $modulo . "&niv=1&task=list&tipo=" . $tipoPagina);
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


