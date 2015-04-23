<?php

/**
 * Modulo Hallazgos Pendientes
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
$daoHallazgosPendientes = new CHallazgosPendientesData($db);
$daoPlaneacion = new CPlaneacionData($db);
$daoBeneficiarios = new CBeneficiarioData($db);
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
        ?>
        <script>
            function exportHallazgo(){
                document.getElementById('form_hallazgos').action = 'modulos/interventoria/hallazgos_en_excel.php';
                document.getElementById('form_hallazgos').submit();
            }
            
            function filtrarHallazgo(){
                document.getElementById('form_hallazgos').action = '?mod=<?=$modulo?>&niv=<?=$niv?>';
                document.getElementById('form_hallazgos').submit();
            }
        </script>
        <?php
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
            $condicion .= " AND c.idCentroPoblado = " . $_REQUEST['sel_centro_poblado'];
        }
        $criterio = "1";
        if (isset($_REQUEST['sel_area']) &&
                $_REQUEST['sel_area'] != '-1') {
            $condicion .= " AND ar.idAreaHallazgo = " . $_REQUEST['sel_area'];
            $criterio = "idAreaHallazgo = " . $_REQUEST['sel_area'];
        }
        if (isset($_REQUEST['tipohallazgo']) &&
                $_REQUEST['tipohallazgo'] != '-1') {
            $condicion .= " AND ti.idTipoHallazgo = " . $_REQUEST['tipohallazgo'];
        }
        if(isset($_REQUEST['txt_fecha_inicio']) && 
                $_REQUEST['txt_fecha_inicio'] != ""){
            if(isset($_REQUEST['txt_fecha_fin']) &&
                    $_REQUEST['txt_fecha_fin'] != ""){
                $condicion .= " AND a.fecha BETWEEN '". $_REQUEST['txt_fecha_inicio']. "' AND '". $_REQUEST['txt_fecha_fin']."'";
            } else {
                $condicion .= " AND a.fecha BETWEEN '". $_REQUEST['txt_fecha_inicio']. "' AND NOW()";
            }
        }

        $form = new CHtmlForm();
        $form->setTitle(TITULO_ACTIVIDADES_BITACORA_HALLAZGOS_PENDIENTES);
        $form->setId("form_hallazgos");
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
        $form->addSelect('select', 'sel_region', 'sel_region', $opciones, '', $_REQUEST['sel_region'], '', ' onChange="filtrarHallazgo();"');

        $departamentos = $daoPlaneacion->getDepartamento($_REQUEST['sel_region'], 'dep_nombre');
        $opciones = null;
        if (isset($departamentos)) {
            foreach ($departamentos as $departamento) {
                $opciones[count($opciones)] = array('value' => $departamento['id'],
                    'texto' => $departamento['nombre']);
            }
        }

        $form->addEtiqueta(PLANEACION_DEPARTAMENTO);
        $form->addSelect('select', 'sel_departamento', 'sel_departamento', $opciones, '', $_REQUEST['sel_departamento'], '', ' onChange="filtrarHallazgo();"');

        $muncipios = $daoPlaneacion->getMunicipio($_REQUEST['sel_departamento'], 'mun_nombre');
        $opciones = null;
        if (isset($muncipios)) {
            foreach ($muncipios as $municipio) {
                $opciones[count($opciones)] = array('value' => $municipio['id'],
                    'texto' => $municipio['nombre']);
            }
        }

        $form->addEtiqueta(PLANEACION_MUNICIPIO);
        $form->addSelect('select', 'sel_municipios', 'sel_municipios', $opciones, '', $_REQUEST['sel_municipios'], '', ' onChange="filtrarHallazgo();"');

        $centrosPoblados = $daoBeneficiarios->getCentrosPoblados($_REQUEST['sel_municipios']);
        $opciones = null;
        if (isset($centrosPoblados)) {
            foreach ($centrosPoblados as $centroPoblado) {
                $opciones[count($opciones)] = array('value' => $centroPoblado->getIdCentroPoblado(),
                    'texto' => $centroPoblado->getNombre());
            }
        }

        $form->addEtiqueta(CENTRO_POBLADO_BENEFICIARIO);
        $form->addSelect('select', 'sel_centro_poblado', 'sel_centro_poblado', $opciones, '', $_REQUEST['sel_centro_poblado'], '', ' onChange="filtrarHallazgo();"');

        $clasificaciones = $daoBasicas->getBasicas('areashallazgospendientes');
        $opciones = null;
        if (isset($clasificaciones)) {
            foreach ($clasificaciones as $clasificacion) {
                $opciones[count($opciones)] = array('value' => $clasificacion->getId(),
                    'texto' => $clasificacion->getDescripcion());
            }
        }

        $form->addEtiqueta(ACTIVIDADES_BITACORA_HALLAZGOS_PENDIENTES_AREA);
        $form->addSelect('select', 'sel_area', 'sel_area', $opciones, '', $_REQUEST['sel_area'], '', 'onChange="filtrarHallazgo();"');

        $tipos = $daoBasicas->getBasicas('tipohallazgo', $criterio);
        $opciones = null;
        if (isset($tipos)) {
            foreach ($tipos as $tipo) {
                $opciones[count($opciones)] = array('value' => $tipo->getId(),
                    'texto' => $tipo->getDescripcion());
            }
        }

        $form->addEtiqueta(ACTIVIDADES_BITACORA_HALLAZGOS_PENDIENTES_CLASIFICACION);
        $form->addSelect('select', 'sel_tipo', 'sel_tipo', $opciones, '', $_REQUEST['sel_tipo'], '', 'onChange="filtrarHallazgo();"');

        $form->addEtiqueta(HALLAZGOS_FECHA_INICIO);
        $form->addInputDate('date', 'txt_fecha_inicio', 'txt_fecha_inicio', $_REQUEST['txt_fecha_inicio'], '%Y-%m-%d', '16', '16', '', 'pattern="' . PATTERN_FECHA . '" title="' . $html->traducirTildes(TITLE_FECHA) . '"');
        
        $form->addEtiqueta(HALLAZGOS_FECHA_FINAL);
        $form->addInputDate('date', 'txt_fecha_fin', 'txt_fecha_fin', $_REQUEST['txt_fecha_fin'], '%Y-%m-%d', '16', '16', '', 'pattern="' . PATTERN_FECHA . '" title="' . $html->traducirTildes(TITLE_FECHA) . '"');
        
        $form->addInputButton('button', 'export', 'export', BTN_EXPORTAR, 'button', 'onclick="exportHallazgo();"');
        $form->addInputButton('button', 'export', 'export', BTN_ACEPTAR, 'button', 'onclick="filtrarHallazgo();"');
        $form->writeForm();

        $dt = new CHtmlDataTable();
        $dt->setTitleTable(TITULO_ACTIVIDADES_BITACORA_HALLAZGOS_PENDIENTES);
        $titulos = array(HALLAZGOS_PENDIENTES_BENEFICIARIO, ACTIVIDADES_BITACORA_HALLAZGOS_PENDIENTES_CLASIFICACION, ACTIVIDADES_BITACORA_HALLAZGOS_PENDIENTES_USUARIO, ACTIVIDADES_BITACORA_HALLAZGOS_PENDIENTES_FECHA, ACTIVIDADES_BITACORA_HALLAZGOS_PENDIENTES_OBSERVACION, BITACORA_ACTIVIDAD_GASTO_ARCHIVO, HALLAZGOS_FECHA_RESPUESTA, HALLAZGOS_OBSERVACION_RESPUESTA, HALLAZGOS_ARCHIVO_RESPUESTA, HALLAZGOS_ESTADO);
        $hallazgosPendientes = $daoHallazgosPendientes->getHallazgosPendientes($condicion);
        $dt->setTitleRow($titulos);
        $dt->setDataRows($hallazgosPendientes);
        $dt->setType(1);
        $pag_crit = "";
        $dt->setPag(1, $pag_crit);
        $otros = array('link' => "?mod=" . $modulo . "&niv=" . $niv . "&task=responder", 'img' => 'marcado.gif', 'alt' => "Completar");
        $dt->addOtrosLink($otros);
        $dt->writeDataTable($niv);
        $dt->setType(1);
        break;


    /**
     * Permite la edición de un gasto.
     */
    case 'responder':
        $idHallazgosPendientes = $_REQUEST['id_element'];
        $hallazgosPendiente = $daoHallazgosPendientes->getHallazgosPendientesById($idHallazgosPendientes);

        $form = new CHtmlForm();
        $form->setTitle(TITULO_CERRAR_HALLAZGO_PENDIENTE);
        $form->setId('frm_cerrar_hallazgo');
        $form->setAction('?mod=' . $modulo . '&niv=1&task=saveResponder&idHallazgo=' . $idHallazgosPendientes);
        $form->setMethod('post');
        $form->setTableId('tb_add_bitacora');

        $form->addEtiqueta(HALLAZGOS_FECHA_RESPUESTA);
        $form->addInputDate('date', 'txt_fecha', 'txt_fecha', $hallazgosPendiente->getFechaRespuesta(), '%Y-%m-%d', '16', '16', '', 'pattern="' . PATTERN_FECHA . '" title="' . $html->traducirTildes(TITLE_FECHA) . '" required');

        $form->addEtiqueta(HALLAZGOS_OBSERVACION_RESPUESTA);
        $form->addTextArea('textarea', 'txt_observacion', 'txt_observacion', '100', '5', $hallazgosPendiente->getObservacionRespuesta(), '', ' title="' . $html->traducirTildes(TITLE_ALFANUMERICO) . '" required');

        $form->addEtiqueta(HALLAZGOS_ARCHIVO_RESPUESTA);
        $form->addInputFile('file', 'file_archivo', 'file_archivo', '25', 'file', '');

        $form->addInputButton('submit', 'btn_enviar', 'btn_enviar', BTN_ACEPTAR, 'button', '');
        $form->addInputButton('button', 'cancel', 'cancel', BTN_CANCELAR, 'button', 'onclick="cancelarAccion(\'frm_cerrar_hallazgo\',\'?mod=' . $modulo . '&niv=' . $niv . '\');"');

        $form->writeForm();
        break;

    /**
     * Permite guardar la edición del gasto.
     */
    case 'saveResponder':
        $idHallazgo = $_REQUEST['idHallazgo'];
        $fechaRespuesta = $_REQUEST['txt_fecha'];
        $observacionRespuesta = $_REQUEST['txt_observacion'];
        $archivoRespuesta = $_FILES['file_archivo'];
		$actividad = $daoHallazgosPendientes->getHallazgosPendientesById($idHallazgo)->getActividad();
		
        $hallazgo = new CHallazgosPendientes($idHallazgo, NULL, NULL, $actividad, NULL, $fechaRespuesta, $observacionRespuesta, $archivoRespuesta);
		
        $r = $daoHallazgosPendientes->saveRespuestaHallazgosPendientes($hallazgo);
        $m = ERROR_CERRAR_HALLAZGO_PENDIENTE;
        if ($r == 'true') {
            $m = EXITO_CERRAR_HALLAZGO_PENDIENTE;
        }
        echo $html->generaAviso($m, "?mod=" . $modulo . "&niv=1");
        break;
}
?>
