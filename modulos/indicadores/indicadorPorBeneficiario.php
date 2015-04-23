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
$daoPQR = new CPQRData($db);
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
        $form->setTitle(TITULO_PQRS);
        $form->setAction("?mod=" . $modulo . "&niv=" . $niv);
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
        $form->addSelect('select', 'sel_region', 'sel_region', $opciones, '', $_REQUEST['sel_region'], '', '');

        $departamentos = $daoPlaneacion->getDepartamento($_REQUEST['sel_region'], 'dep_nombre');
        $opciones = null;
        if (isset($departamentos)) {
            foreach ($departamentos as $departamento) {
                $opciones[count($opciones)] = array('value' => $departamento['id'],
                    'texto' => $departamento['nombre']);
            }
        }

        $form->addEtiqueta(PLANEACION_DEPARTAMENTO);
        $form->addSelect('select', 'sel_departamento', 'sel_departamento', $opciones, '', $_REQUEST['sel_departamento'], '', '');

        $muncipios = $daoPlaneacion->getMunicipio($_REQUEST['sel_departamento'], 'mun_nombre');
        $opciones = null;
        if (isset($muncipios)) {
            foreach ($muncipios as $municipio) {
                $opciones[count($opciones)] = array('value' => $municipio['id'],
                    'texto' => $municipio['nombre']);
            }
        }

        $form->addEtiqueta(PLANEACION_MUNICIPIO);
        $form->addSelect('select', 'sel_municipios', 'sel_municipios', $opciones, '', $_REQUEST['sel_municipios'], '', '');

        $centrosPoblados = $daoBeneficiarios->getCentrosPoblados($_REQUEST['sel_municipios']);
        $opciones = null;
        if (isset($centrosPoblados)) {
            foreach ($centrosPoblados as $centroPoblado) {
                $opciones[count($opciones)] = array('value' => $centroPoblado->getIdCentroPoblado(),
                    'texto' => $centroPoblado->getNombre());
            }
        }

        $form->addEtiqueta(CENTRO_POBLADO_BENEFICIARIO);
        $form->addSelect('select', 'sel_centro_poblado', 'sel_centro_poblado', $opciones, '', $_REQUEST['sel_centro_poblado'], '', '');

        $metasBeneficiario = $daoBasicas->getBasicas('metabeneficiario');
        $opciones = null;
        if (isset($metasBeneficiario)) {
            foreach ($metasBeneficiario as $metaBeneficiario) {
                $opciones[count($opciones)] = array('value' => $metaBeneficiario->getId(),
                    'texto' => $metaBeneficiario->getDescripcion());
            }
        }

        $form->addEtiqueta(META_BENEFICIARIO);
        $form->addSelect('select', 'sel_meta', 'sel_meta', $opciones, '', $_REQUEST['sel_meta'], '', ' required');

        $estadosBeneficiario = $daoBasicas->getBasicas('estadobeneficiario');
        $opciones = null;
        if (isset($estadosBeneficiario)) {
            foreach ($estadosBeneficiario as $estadoBeneficiario) {
                $opciones[count($opciones)] = array('value' => $estadoBeneficiario->getId(),
                    'texto' => $estadoBeneficiario->getDescripcion());
            }
        }

        $form->addEtiqueta(ESTADO_BENEFICIARIO);
        $form->addSelect('select', 'sel_estado', 'sel_estado', $opciones, '', $_REQUEST['sel_estado'], '', ' required');

        $ddaBeneficiarios = $daoBasicas->getBasicas('ddabeneficiario');
        $opciones = null;
        if (isset($ddaBeneficiarios)) {
            foreach ($ddaBeneficiarios as $ddaBeneficiario) {
                $opciones[count($opciones)] = array('value' => $ddaBeneficiario->getId(),
                    'texto' => $ddaBeneficiario->getDescripcion());
            }
        }

        $form->addEtiqueta(DDA_BENEFICIARIO);
        $form->addSelect('select', 'sel_dda', 'sel_dda', $opciones, '', $_REQUEST['sel_dda'], '', ' required');

        $gruposBeneficiarios = $daoBasicas->getBasicas('grupobeneficiario');
        $opciones = null;
        if (isset($gruposBeneficiarios)) {
            foreach ($gruposBeneficiarios as $grupoBeneficiario) {
                $opciones[count($opciones)] = array('value' => $grupoBeneficiario->getId(),
                    'texto' => $grupoBeneficiario->getDescripcion());
            }
        }

        $form->addEtiqueta(GRUPO_BENEFICIARIO);
        $form->addSelect('select', 'sel_grupo', 'sel_grupo', $opciones, '', $_REQUEST['sel_grupo'], '', ' required');

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
        $opciones[5] = array('value' => '5',
            'texto' => 'NODO');
        $opciones[6] = array('value' => '6',
            'texto' => 'NODO INT.');
        $opciones[7] = array('value' => '8',
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
        $form->addSelect('select', 'sel_tipo', 'sel_tipo', $opciones, '', $_REQUEST['sel_tipo'], '', ' required');

        $form->addInputButton('submit', 'btn_enviar', 'btn_enviar', BTN_ACEPTAR, 'button', '');
        $form->writeForm();

        $dt = new CHtmlDataTable();
        $dt->setTitleTable(TITULO_PQRS);
        $titulos = array(CODIGO_INTERVENTORIA_BENEFICIARIO,
            CODIGO_MINTIC_BENEFICIARIO,
            CODIGO_OPERADOR_BENEFICIARIO,
            CENTRO_POBLADO_BENEFICIARIO,
            NOMBRE_BENEFICIARIO,
            LATITUD_BENEFICIARIO,
            LONGITUD_BENEFICIARIO,
            FECHA_INICIO_BENEFICIARIO,
            ESTADO_BENEFICIARIO,
            DDA_BENEFICIARIO,
            GRUPO_BENEFICIARIO,
            TIPO_BENEFICIARIO);
        $beneficiarios = $daoBeneficiarios->getBeneficiarios($condicion);
        $dt->setTitleRow($titulos);
        $dt->setDataRows($beneficiarios);
        $dt->setSeeLink("?mod=" . $modulo . "&niv=" . $niv . "&task=seePQRs");
        $dt->setType(1);
        $pag_crit = "";
        $dt->setPag(1, $pag_crit);
        $dt->writeDataTable($niv);

        break;

    /**
     * la variable list, permite hacer la carga la página con la lista de
     * objetos beneficiarios según los parámetros de entrada
     */
    case 'seePQRs':
        $id_element = $_REQUEST['id_element'];
        $form = new CHtmlForm();
        $form->setId('frm_filtrar_beneficiarios');
		$beneficiario = $daoBeneficiarios->getBeneficiarioById($id_element);
        $form->setTitle(TITULO_PQRS . " ". $beneficiario->getNombre());
        $form->setAction('?mod=' . $modulo . '&niv=' . $niv . '&task=list');
        $form->setMethod("post");
        $form->setOptions("autoClean", false);
        $form->addInputButton('button', 'btn_ver', 'btn_ver', BTN_GRAFICA, 'button', 'onclick="location.href=\'?mod=' . $modulo . '&niv=' . $niv . '&task=seeGrafica&id_element='. $id_element . '\'"');
        $form->addInputButton('submit', 'btn_enviar', 'btn_enviar', BTN_VOLVER, 'button', '');
        $form->writeForm();

        $dt = new CHtmlDataTable();
        $dt->setTitleTable(TITULO_PQRS. " ". $beneficiario->getNombre() );
        $titulos = array(PQR_NUMERO, PQR_DESCRIPCION, PQR_FECHA_REPORTE, PQR_FECHA_SOLUCION, PQR_DIFERENCIA,
            PQR_DIAGNOSTICO, PQR_RESPUESTA, PQR_ESTADO);
        $pqrs = $daoPQR->getPQRByBeneficiario($id_element);
        $dt->setTitleRow($titulos);
        $dt->setDataRows($pqrs);
        $dt->setEditLink("?mod=" . $modulo . "&niv=" . $niv . "&task=edit&idBeneficiario=" . $id_element);
        $dt->setDeleteLink("?mod=" . $modulo . "&niv=" . $niv . "&task=delete&idBeneficiario=" . $id_element);
        $dt->setAddLink("?mod=" . $modulo . "&niv=" . $niv . "&task=add&idBeneficiario=" . $id_element);
        $otros = array('link' => "?mod=" . $modulo . "&niv=" . $niv . "&task=answer&idBeneficiario=" . $id_element, 'img' => 'marcado.gif', 'alt' => ALT_ENVIAR);
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
        $idBeneficiario = $_REQUEST['idBeneficiario'];
        $form = new CHtmlForm();

        $opciones = null;

        $form->setTitle(TITULO_AGREGAR_PQR);
        $form->setId('frm_add_PQR');
        $form->setAction('?mod=' . $modulo . '&niv=1&task=saveAdd&idBeneficiario=' . $idBeneficiario);
        $form->setMethod('post');

        $form->addEtiqueta(PQR_DESCRIPCION);
        $form->addTextArea('text', 'txt_descripcion', 'txt_descripcion', '200', '200', '', null, 'autofocus pattern="' . PATTERN_ALFANUMERICO . '" title="' . $html->traducirTildes(TITLE_ALFANUMERICO) . '" required');

        $form->addEtiqueta(PQR_FECHA_REPORTE);
        $form->addInputDate('date', 'txt_fecha', 'txt_fecha', '', '%Y-%m-%d', '18', '18', '', 'pattern="' . PATTERN_FECHA . '" title="' . $html->traducirTildes(TITLE_FECHA) . '" required');

        $form->addEtiqueta(PQR_HORA_REPORTE);
        $form->addInputText('time', 'txt_hora', 'txt_hora', '200', '200', date('H:i'), '', 'autofocus pattern="' . PATTERN_ALFANUMERICO . '" title="' . $html->traducirTildes(TITLE_ALFANUMERICO) . '"');

        $form->addInputButton('submit', 'btn_enviar', 'btn_enviar', BTN_ACEPTAR, 'button', '');
        $form->addInputButton('button', 'cancel', 'cancel', BTN_CANCELAR, 'button', 'onclick="cancelarAccion(\'frm_add_PQR\',\'?mod=' . $modulo . '&niv=' . $niv . '&task=seePQRs&id_element=' . $idBeneficiario . '\');"');

        $form->writeForm();

        break;
    /**
     * la variable saveAdd, permite almacenar el objeto beneficiario en la
     * base de datos @see \CBeneficiario
     */
    case 'saveAdd':
        $beneficiario = $_REQUEST['idBeneficiario'];
        $descripcionRequerimiento = $_REQUEST['txt_descripcion'];
        $fechaReporte = $_REQUEST['txt_fecha'] . ' ' . $_REQUEST['txt_hora'];

        $pqr = new CPQR(null, $descripcionRequerimiento, $fechaReporte, null, null, null, $beneficiario);

        $r = $daoPQR->insertPQR($pqr);
        $m = ERROR_AGREGAR_PQR;
        if ($r == 'true') {
            $m = EXITO_AGREGAR_PQR;
        }
        echo $html->generaAviso($m, "?mod=" . $modulo . "&niv=1&task=seePQRs&id_element=" . $beneficiario);

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

        $form->setTitle(TITULO_RESPONDER_PQR);
        $form->setId('frm_add_PQR');
        $form->setAction('?mod=' . $modulo . '&niv=1&task=saveAnswer&idBeneficiario=' . $idBeneficiario . '&id_element=' . $idPQR);
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
        $form->addInputButton('button', 'cancel', 'cancel', BTN_CANCELAR, 'button', 'onclick="cancelarAccion(\'frm_add_PQR\',\'?mod=' . $modulo . '&niv=' . $niv . '&task=seePQRs&id_element=' . $idBeneficiario . '\');"');

        $form->writeForm();

        break;
    /**
     * la variable saveAdd, permite almacenar el objeto beneficiario en la
     * base de datos @see \CBeneficiario
     */
    case 'saveAnswer':
        $beneficiario = $_REQUEST['idBeneficiario'];
        $idPQR = $_REQUEST['id_element'];
        $fechaSolucion = $_REQUEST['txt_solucion'] . ' ' . $_REQUEST['txt_hora'];
        $diagnostico = $_REQUEST['txt_diagnostico'];
        $respuesta = $_REQUEST['txt_respúesta'];

        $pqr = new CPQR($idPQR, null, null, $fechaSolucion, $diagnostico, $respuesta, null);

        $r = $daoPQR->answerPQR($pqr);
        $m = ERROR_RESPONDER_PQR;
        if ($r == 'true') {
            $m = EXITO_RESPONDER_PQR;
        }
        echo $html->generaAviso($m, "?mod=" . $modulo . "&niv=1&task=seePQRs&id_element=" . $beneficiario);

        break;


    /**
     * la variable delete, permite hacer la carga del objeto beneficiario
     * y espera confirmacion de eliminarlo @see \CBeneficiario
     */
    case 'delete':
        $idBeneficiario = $_REQUEST['idBeneficiario'];
        $id_delete = $_REQUEST['id_element'];
        echo $html->generaAdvertencia(CONFIRMAR_BORRAR_PQR, '?mod=' . $modulo . '&niv=1&task=confirmDelete&id_element=' . $id_delete . '&idBeneficiario=' . $idBeneficiario, 'onclick=location.href=\'?mod=' . $modulo . '&niv=1&task=seePQRs&id_element=' . $idBeneficiario . '\'');
        break;

    /**
     * la variable confirmDelete, permite eliminar el objeto beneficiario de la
     * base de datos @see \CBeneficiario
     */
    case 'confirmDelete':
        $idBeneficiario = $_REQUEST['idBeneficiario'];
        $id_delete = $_REQUEST['id_element'];
        $r = $daoPQR->deletePQRById($id_delete);
        $m = ERROR_BORRAR_PQR;
        if ($r == 'true') {
            $m = EXITO_BORRAR_PQR;
        }
        echo $html->generaAviso($m, "?mod=" . $modulo . "&niv=1&task=seePQRs&id_element=" . $idBeneficiario);
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

        $form->setTitle(TITULO_EDITAR_PQR);
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
        $form->addInputButton('button', 'cancel', 'cancel', BTN_CANCELAR, 'button', 'onclick="cancelarAccion(\'frm_edit_PQR\',\'?mod=' . $modulo . '&niv=' . $niv . '&task=seePQRs&id_element=' . $idBeneficiario . '\');"');

        $form->writeForm();

        break;

    /**
     * la variable saveEdit, permite actualizar el objeto bodega en la base
     * de datos @see \CBeneficiario
     */
    case 'saveEdit':
        $id = $_REQUEST['id_element'];
        $beneficiario = $_REQUEST['idBeneficiario'];
        $descripcionRequerimiento = $_REQUEST['txt_descripcion'];
        $fechaReporte = $_REQUEST['txt_fecha'] . ' ' . $_REQUEST['txt_hora'];

        $pqr = new CPQR($id, $descripcionRequerimiento, $fechaReporte, null, null, null, $beneficiario);

        $r = $daoPQR->updatePQR($pqr);
        $m = ERROR_EDITAR_PQR;
        if ($r == 'true') {
            $m = EXITO_EDITAR_PQR;
        }
        echo $html->generaAviso($m, "?mod=" . $modulo . "&niv=1&task=seePQRs&id_element=" . $beneficiario);

        break;

    case 'seeGrafica':
        $id_element = $_REQUEST['id_element'];
        $periodo = date('Y-m').'-00';
        if(isset($_REQUEST['txt_periodo'])){
            $periodo = $_REQUEST['txt_periodo'].'-00';
        }
        $indicador = $daoPQR->getIndicador($id_element, $periodo);
        $periodo = substr($periodo, 0, -3);
        $form = new CHtmlForm();
        $form->setClassEtiquetas('td_label');
        $form->setTitle(TITULO_GRAFICA_PQR);
        $form->setId('frm_indicador_ingreso');
        $form->setMethod('post');
        $form->setOptions('autoClean', false);
        $form->setAction('?mod=' . $modulo . '&niv=' . $niv . '&id_element=' . $id_element . '&task=seeGrafica');
        $form->addEtiqueta(PERIODO_PARAFISCALES);
        $form->addInputText('month', 'txt_periodo', 'txt_periodo', '50', '50', 
                            $periodo, '', '');
        $form->addInputButton('button', 'btn_ver', 'btn_ver', BTN_VOLVER, 'button', 'onclick="location.href=\'?mod=' . $modulo . '&niv=' . $niv . '&task=seePQRs&id_element='. $id_element . '\'"');
        $form->addInputButton('submit', 'ok', 'ok', BTN_ACEPTAR, 'button', '');
        $form->writeForm();
        ?>
        <script src="././clases/Graficas/amcharts/amcharts.js" type="text/javascript"></script>
        <script type="text/javascript" src="././clases/Graficas/amcharts/pie.js"></script>
        <script>
            var chart = AmCharts.makeChart("chartdiv", {
                "type": "pie",
                "theme": "none",
                "dataProvider": [
                    {
                        "label": "Minutos Disponible",
                        "minutos": <?= $indicador['minutosDisponible'] ?>
                    },
                    {
                        "label": "Minutos Indisponible",
                        "minutos": <?= $indicador['minutosIndisponible'] ?>
                    }],
                "valueField": "minutos",
                "titleField": "label",
                "radius": "35%",
                "labelRadius": 2,
                "colors": ["#54acd2", "#fcd202"],
                "labelText": "[[percents]]%"
            });
            
            
        </script>
        <div id="chartdiv" style="width: 100%; height: 500px;"></div>
        <?php

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


