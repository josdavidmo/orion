<?php

/**
 * Modulo Actividades Bitacora
 * Maneja el modulo actividades bitacora en union con 
 * CBitacoraData, CBitacora, CActividadBitacoraData, CActividadBitacora 
 *
 * @see \CBitacoraData
 * @see \CBitacora
 * @see \CActividadBitacora
 * @see \CActividadBitacoraData
 *
 * @package modulos
 * @subpackage usuarios
 * @author SERTIC SAS
 * @version 2014.11.02
 * @copyright SERTIC SAS
 */
//no permite el acceso directo
defined('_VALID_PRY') or die('Restricted access');
$daoUsuarios = new CUserData($db);
$daoBitacora = new CBitacoraData($db);
$daoActividadBitacora = new CActividadBitacoraData($db);
$daoRelacionTransporte = new CRelacionTransporteData($db);
$daoBasicas = new CBasicaData($db);
$daoRegistroFotografico = new CRegistroFotograficoData($db);
$daoHallazgosPendientes = new CHallazgosPendientesData($db);
$daoGastos = new CGastoData($db);
$daoAnticipo = new CAnticipoData($db);
$ejeData = new CGeneradorEjecucionData($db);
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
        $usuario = $daoUsuarios->getUserById($id_usuario);
        $form = new CHtmlForm();
        $form->setTitle(TITULO_BITACORA_PERSONAL . " " . $usuario['usu_nombre'] . " " . $usuario['usu_apellido']);
        $form->setMethod('post');
        $form->setOptions('autoClean', false);
        $form->setAction('?mod=' . $modulo . '&niv=1&task=sync');
        //$form->addInputButton('submit', 'sync', 'sync', BTN_SINCRONIZAR, 'button', '');
        $form->writeForm();
        $dt = new CHtmlDataTable();
        $dt->setTitleTable(TITULO_BITACORA_PERSONAL . " " . $usuario['usu_nombre'] . " " . $usuario['usu_apellido']);
        $titulos = array(BITACORA_MUNICIPIO, BITACORA_ACTIVIDAD, BITACORA_FECHA_INICIO, BITACORA_FECHA_FIN, BITACORA_ESTADO);
        $bitacoras = $daoBitacora->getBitacorasByUsuario($usuario['usu_id']);
        $dt->setTitleRow($titulos);
        $dt->setDataRows($bitacoras);
        $dt->setSeeLink("?mod=" . $modulo . "&niv=" . $niv . "&task=see");
        $dt->setType(1);
        $pag_crit = "";
        $dt->setPag(1, $pag_crit);
        $dt->writeDataTable($niv);

        break;
    
    case 'sync':
        $form = new CHtmlForm();
        $form->setId('frm_sync');
        $form->setMethod('post');
        $form->writeForm();
        $data = null;
        $cont=0;
        
        $bitacoras = $daoBitacora->getBitacorasSincronizacion($id_usuario);
        $actividades = $daoActividadBitacora->getActividadesSincronizacion($id_usuario,$html);
        $registrosFotograficos = $daoRegistroFotografico->getRegistroFotograficoSincronizacion($id_usuario,$html);
        $gastos = $daoGastos->getGastosSincronizacion($id_usuario,$html);
        $hallazgos = $daoHallazgosPendientes->getHallazgosSincronizacion($id_usuario,$html);
        
        $data = new arrayOfBitacora($bitacoras,$actividades,null,$hallazgos,$registrosFotograficos,$gastos);
        $client = new SoapClient(DIRECCION_WEB_SERVICE_SINCRONIZACION);
        // Paramters in PHP are passed via associative arrays
        $password= $ejeData->consultarPassUsuario($id_usuario);        
        $params = array("user"=>$id_usuario, "pass"=>$password, "data"=>$data);
        $cbitacora=null;
        $result = $client->sicronizarBitacora($params);
        $paquete = $result->return;
        $bits=$paquete->bitacoras;
        $anticipos=$paquete->anticipos;
        
        if (count($bits) == 1) {
            $cbitacora = new CBitacora($bits->idBitacora, $bits->idUsuario, $bits->idBeneficiario, $bits->descripcionActividad, $bits->fechaInicio, $bits->fechaFin);
            $daoBitacora->insertBitacoraSync($cbitacora);
        } else {
            for ($i = 0; $i < count($bits); $i++) {
                $cbitacora = new CBitacora($bits[$i]->idBitacora, $bits[$i]->idUsuario, $bits[$i]->idBeneficiario, $bits[$i]->descripcionActividad, $bits[$i]->fechaInicio, $bits[$i]->fechaFin);

                $daoBitacora->insertBitacoraSync($cbitacora);
            }
        }

        if (count($anticipos) == 1) {
            $cAnticipo = new CAnticipo($anticipos->idAnticipo, $anticipos->fecha, $anticipos->valor, $anticipos->idBitacora);
            $daoAnticipo->insertAnticipo($cAnticipo);
        } else {
            for ($i = 0; $i < count($anticipos); $i++) {
                $cAnticipo = new CAnticipo($anticipos[$i]->idAnticipo, $anticipos[$i]->fecha, $anticipos[$i]->valor, $anticipos[$i]->idBitacora);
                $daoAnticipo->insertAnticipo($cAnticipo);
            }
        }
        
        for($i=0;$i<count($actividades);$i++){
            $daoBitacora->setSync("actividad", "idActividad", $actividades[$i]->getIdActividad(), 0);
        }
        for($i=0;$i<count($registrosFotograficos);$i++){
            $daoBitacora->setSync("registrofotografico", "idRegistroFotografico", 
                    $registrosFotograficos[$i]->getIdRegistroFotografico(), 0);
        }
        for($i=0;$i<count($gastos);$i++){
            $daoBitacora->setSync("gastos_actividad", "idGastosActividad", $gastos[$i]->getIdGastosActividad(), 0);
        }
        for($i=0;$i<count($hallazgos);$i++){
            $daoBitacora->setSync("hallazgospendientes", "idHallazgosPendientes", $hallazgos[$i]->getIdHallazgosPendientes(), 0);
        }
        //$mens= "" . $result->return;
//        if(!strpos($mens,'error')|| !strpos($mens,'Error')){
//            $ejeData->setSyncEncuesta($id_element, 0);
//        }
        echo $html->generaAviso("Se agregaron ".count($paquete->bitacoras)." nuevos objetivos", "?mod=" . $modulo . "&niv=" . $niv . "&task=list");
        break;

    /**
     * la variable list, permite hacer la carga la página con la lista de 
     * objetos productos según los parámetros de entrada
     */
    case 'see':
        $idBitacora = $_REQUEST['id_element'];
        $form = new CHtmlForm();
        $form->setTitle(TITULO_ACTIVIDADES_BITACORA);
        $form->setMethod('post');
        $form->setOptions('autoClean', false);
        $form->setAction('?mod=' . $modulo . '&niv=' . $niv);
        $form->addInputButton('submit', 'ok', 'ok', BTN_ATRAS, 'button', '');
        $form->writeForm();

        $dt = new CHtmlDataTable();
        $dt->setTitleTable(TITULO_ACTIVIDADES_BITACORA);
        $titulos = array(BITACORA_ACTIVIDAD_FECHA, BITACORA_ACTIVIDAD_EJECUTADA_DESCRIPCION, TITULO_ACTIVIDADES_BITACORA_HALLAZGOS_PENDIENTES, TITULO_ACTIVIDADES_BITACORA_ESTADO);
        $actividades = $daoActividadBitacora->getActividadByBitacora($idBitacora);
        $dt->setTitleRow($titulos);
        $dt->setDataRows($actividades);
        $dt->setSeeLink("?mod=" . $modulo . "&niv=" . $niv . "&task=seeActividad&idBitacora=" . $idBitacora);
        $dt->setEditLink("?mod=" . $modulo . "&niv=" . $niv . "&task=edit&idBitacora=" . $idBitacora);
        $dt->setDeleteLink("?mod=" . $modulo . "&niv=" . $niv . "&task=delete&idBitacora=" . $idBitacora);
        $dt->setAddLink("?mod=" . $modulo . "&niv=" . $niv . "&task=add&idBitacora=" . $idBitacora);
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
        $dt->setType(1);
        $dt->setFormatRow(array(null, array(2, ',', '.')));
        $dt->setSumColumns(array(2));
        $pag_crit = "";
        $dt->setPag(1, $pag_crit);
        $dt->writeDataTable($niv);

        break;

    /**
     * la variable add, permite hacer la carga la página con las variables 
     * que componen el objeto actividad bitacora @see \CActividadBitacora
     */
    case 'add':
        $form = new CHtmlForm();
        $id = $_REQUEST['idBitacora'];
        $form->setTitle(TITULO_AGREGAR_ACTIVIDAD_BITACORA);
        $form->setId('frm_add_actividad');
        $form->setAction('?mod=' . $modulo . '&niv=1&task=saveAdd&idBitacora=' . $id);
        $form->setMethod('post');
        $form->setClassEtiquetas('td_label');
        $form->setTableId('tb_add_actividad');

        $estadosSalud = $daoBasicas->getBasicas('estadosalud');
        $opciones = null;
        $opcionesCumplimiento = null;
        if (isset($estadosSalud)) {
            foreach ($estadosSalud as $estadoSalud) {
                $opciones[count($opciones)] = array('value' => $estadoSalud->getId(),
                    'texto' => $estadoSalud->getDescripcion());
            }
        }
        $opcionesCumplimiento[count($opcionesCumplimiento)] = array('value' => "Si", 'texto' => "Si");
        $opcionesCumplimiento[count($opcionesCumplimiento)] = array('value' => "No", 'texto' => "No");
        $opcionesCumplimiento[count($opcionesCumplimiento)] = array('value' => "No Aplica", 'texto' => "No Aplica");

        $form->addEtiqueta(BITACORA_ACTIVIDAD_ESTADO_SALUD);
        $form->addSelect('select', 'sel_estado', 'sel_estado', $opciones, '', '', '', ' required');

        $form->addEtiqueta(BITACORA_ACTIVIDAD_FECHA_INICIO);
        $form->addInputDate('date', 'date_inicio', 'date_inicio', '', '%Y-%m-%d', 18, 18, 'pattern="' . PATTERN_FECHA . '" title="' . $html->traducirTildes(TITLE_FECHA) . '" required', '');

        $form->addEtiqueta(BITACORA_ACTIVIDAD_HORA_INICIO);
        $form->addInputText('time', 'time_hora_inicio', 'time_hora_inicio', 5, '', '', '', '" required');

        $form->addEtiqueta(BITACORA_ACTIVIDAD_FECHA_FIN);
        $form->addInputDate('date', 'date_fin', 'date_fin', '', '%Y-%m-%d', 18, 18, 'pattern="' . PATTERN_FECHA . '" title="' . $html->traducirTildes(TITLE_FECHA) . '" required', '');

        $form->addEtiqueta(BITACORA_ACTIVIDAD_HORA_FIN);
        $form->addInputText('time', 'time_hora_fin', 'time_hora_fin', 5, '', '', '', '" required');

        $form->addEtiqueta(BITACORA_ACTIVIDAD_EJECUTADA_DESCRIPCION);
        $form->addTextArea('textarea', 'txt_actividad', 'txt_actividad', '100', '5', '', '', ' title="' . $html->traducirTildes(TITLE_ALFANUMERICO) . '" required');

        $form->addEtiqueta(BITACORA_ACTIVIDAD_CONDICIONES_TOPOLOGICAS);
        $form->addTextArea('textarea', 'txt_topologicas', 'txt_topologicas', '100', '5', '', '', ' title="' . $html->traducirTildes(TITLE_ALFANUMERICO) . '" required');

        $form->addEtiqueta(BITACORA_ACTIVIDAD_CONDICIONES_CLIMATICAS);
        $form->addTextArea('textarea', 'txt_climaticas', 'txt_climaticas', '100', '5', '', '', ' title="' . $html->traducirTildes(TITLE_ALFANUMERICO) . '" required');

        $form->addEtiqueta(BITACORA_ACTIVIDAD_OBSERVACIONES);
        $form->addTextArea('textarea', 'txt_observaciones', 'txt_observaciones', '100', '5', '', '', ' title="' . $html->traducirTildes(TITLE_ALFANUMERICO) . '" required');

        $form->addEtiqueta(BITACORA_ACTIVIDAD_NUMERO_CUADRILLAS);
        $form->addInputText('text', 'txt_numero_cuadrillas', 'txt_numero_cuadrillas', 5, '', '', '', 'pattern="' . PATTERN_NUMEROS . '" title="' . $html->traducirTildes(TITLE_NUMEROS) . '" ');

        $form->addEtiqueta(BITACORA_ACTIVIDAD_TOTAL_PERSONAS);
        $form->addInputText('text', 'txt_total_personas', 'txt_total_personas', 5, '', '', '', 'pattern="' . PATTERN_NUMEROS . '" title="' . $html->traducirTildes(TITLE_NUMEROS) . '" ');

        $form->addEtiqueta(BITACORA_ACTIVIDAD_TOTAL_PERSONAS_CONTRATADAS);
        $form->addInputText('text', 'txt_total_personas_contratadas', 'txt_total_personas_contratadas', 5, '', '', '', 'pattern="' . PATTERN_NUMEROS . '" title="' . $html->traducirTildes(TITLE_NUMEROS) . '" ');

        $form->addEtiqueta(BITACORA_ACTIVIDAD_CUMPLIMIENTO_PARAFISCALES);
        $form->addSelect('select', 'sel_cumplimiento_parafiscales', 'sel_cumplimiento_parafiscales', $opcionesCumplimiento, '', '', '', ' ');

        $form->addEtiqueta(BITACORA_ACTIVIDAD_CUMPLIMIENTO_SENALIZACION);
        $form->addSelect('select', 'sel_cumplimiento_senalizacion', 'sel_cumplimiento_senalizacion', $opcionesCumplimiento, '', '', '', ' ');

        $form->addEtiqueta(BITACORA_ACTIVIDAD_CUMPLIMIENTO_EPP);
        $form->addSelect('select', 'sel_cumplimiento_epp', 'sel_cumplimiento_epp', $opcionesCumplimiento, '', '', '', ' ');

        $form->addEtiqueta(BITACORA_ACTIVIDAD_CUMPLIMIENTO_CERTIFICACIONES);
        $form->addSelect('select', 'sel_cumplimiento_certificaciones', 'sel_cumplimiento_certificaciones', $opcionesCumplimiento, '', '', '', ' ');

        $form->addInputButton('submit', 'btn_enviar', 'btn_enviar', BTN_ACEPTAR, 'button', '');
        $form->addInputButton('button', 'cancel', 'cancel', BTN_CANCELAR, 'button', 'onclick="cancelarAccion(\'frm_add_actividad\',\'?mod=' . $modulo . '&niv=' . $niv . '&id_element=' . $id . '&task=see\');"');

        $form->writeForm();

        break;
    /**
     * la variable saveAdd, permite almacenar el objeto actividadbitacora en la 
     * base de datos @see \CActividadBitacora
     */
    case 'saveAdd':
        $estadoSalud = $_REQUEST['sel_estado'];
        $descripcionActividadesEjecutadas = $_REQUEST['txt_actividad'];
        $condicionesClimaticas = $_REQUEST['txt_climaticas'];
        $condicionesTopologicas = $_REQUEST['txt_topologicas'];
        $observaciones = $_REQUEST['txt_observaciones'];
        $bitacora = $_REQUEST['idBitacora'];
        $numCuadrillas = $_REQUEST['txt_numero_cuadrillas'];

        if (($numCuadrillas == "")) {
            $numCuadrillas = "null";
        }
        $totalPersonas = $_REQUEST['txt_total_personas'];
        if (($totalPersonas == "")) {
            $totalPersonas = "null";
        }
        $totalPersonasContratadas = $_REQUEST['txt_total_personas_contratadas'];
        if (($totalPersonasContratadas == "")) {
            $totalPersonasContratadas = "null";
        }
        $cumplimientoParafiscales = $_REQUEST['sel_cumplimiento_parafiscales'];
        if (($cumplimientoParafiscales == "-1")) {
            $cumplimientoParafiscales = "null";
        } else {
            $cumplimientoParafiscales = "'$cumplimientoParafiscales'";
        }
        $cumplimientoSenalizacion = $_REQUEST['sel_cumplimiento_senalizacion'];
        if (($cumplimientoSenalizacion == "-1")) {
            $cumplimientoSenalizacion = "null";
        } else {
            $cumplimientoSenalizacion = "'$cumplimientoSenalizacion'";
        }
        $cumplimientoEpp = $_REQUEST['sel_cumplimiento_epp'];
        if (($cumplimientoEpp == "-1")) {
            $cumplimientoEpp = "null";
        } else {
            $cumplimientoEpp = "'$cumplimientoEpp'";
        }
        $cumplimientoCertificacion = $_REQUEST['sel_cumplimiento_certificaciones'];
        if (($cumplimientoCertificacion == "-1")) {
            $cumplimientoCertificacion = "null";
        } else {
            $cumplimientoCertificacion = "'$cumplimientoCertificacion'";
        }


        $fecha = $_REQUEST['date_inicio'] . " " . $_REQUEST['time_hora_inicio'];
        $fechaFin = $_REQUEST['date_fin'] . " " . $_REQUEST['time_hora_fin'];

        $actividad = new CActividadBitacora(NULL, $bitacora, $fecha, $fechaFin, $descripcionActividadesEjecutadas, $condicionesTopologicas, $condicionesClimaticas, $observaciones, $estadoSalud, $numCuadrillas, $totalPersonas, $totalPersonasContratadas, $cumplimientoParafiscales, $cumplimientoSenalizacion, $cumplimientoEpp, $cumplimientoCertificacion);

        $r = $daoActividadBitacora->insertActividad($actividad);
        $m = ERROR_AGREGAR_ACTIVIDAD_BITACORA;
        if ($r == 'true') {
            $m = EXITO_AGREGAR_ACTIVIDAD_BITACORA;
        }
        echo $html->generaAviso($m, "?mod=" . $modulo . "&niv=1&task=see&id_element=" . $bitacora);

        break;
    /**
     * la variable delete, permite hacer la carga del objeto bitacora 
     * y espera confirmacion de eliminarlo @see \CBitacora
     */
    case 'delete':
        $id_delete = $_REQUEST['id_element'];
        $idBitacora = $_REQUEST['idBitacora'];
        echo $html->generaAdvertencia(CONFIRMAR_BORRAR_ACTIVIDAD_BITACORA, '?mod=' . $modulo . '&niv=1&task=confirmDelete&id_element=' . $id_delete . '&idBitacora=' . $idBitacora, 'onclick=location.href=\'?mod=' . $modulo . '&niv=1&id_element=' . $idBitacora . '&task=see\'');
        break;
    /**
     * la variable confirmDelete, permite eliminar el objeto registro producto 
     * de la base de datos @see \CRegistroInversion
     */
    case 'confirmDelete':
        $idBitacora = $_REQUEST['idBitacora'];
        $id_delete = $_REQUEST['id_element'];
        $r = $daoActividadBitacora->deleteActividadById($id_delete);
        $m = ERROR_BORRAR_ACTIVIDAD_BITACORA;
        if ($r == 'true') {
            $m = EXITO_BORRAR_ACTIVIDAD_BITACORA;
        }
        echo $html->generaAviso($m, "?mod=" . $modulo . "&niv=1&task=see&id_element=" . $idBitacora);
        break;

    /**
     * la variable edit, permite hacer la carga del objeto actividad bitacora 
     * y espera confirmacion de edicion @see \CActividadBitacoraData
     */
    case 'edit':
        $id = $_REQUEST['idBitacora'];
        $id_edit = $_REQUEST['id_element'];
        $actividad = $daoActividadBitacora->getActividadById($id_edit);
        $form = new CHtmlForm();
        $form->setTitle(TITULO_EDITAR_ACTIVIDAD_BITACORA);
        $form->setId('frm_edit_actividad');
        $form->setAction('?mod=' . $modulo . '&niv=1&task=saveEdit&idBitacora=' . $id . "&id_element=" . $id_edit);
        $form->setMethod('post');
        $form->setClassEtiquetas('td_label');
        $form->setTableId('tb_edit_actividad');

        $estadosSalud = $daoBasicas->getBasicas('estadosalud');
        $opciones = null;
        if (isset($estadosSalud)) {
            foreach ($estadosSalud as $estadoSalud) {
                $opciones[count($opciones)] = array('value' => $estadoSalud->getId(),
                    'texto' => $estadoSalud->getDescripcion());
            }
        }

        $opcionesCumplimiento[count($opcionesCumplimiento)] = array('value' => "Si", 'texto' => "Si");
        $opcionesCumplimiento[count($opcionesCumplimiento)] = array('value' => "No", 'texto' => "No");
        $opcionesCumplimiento[count($opcionesCumplimiento)] = array('value' => "No Aplica", 'texto' => "No Aplica");
        
        $fechaInicio = preg_split("/[\s,]+/", $actividad->getFecha())[0];
        $horaInicio = preg_split("/[\s,]+/", $actividad->getFecha())[1];
        $fechaFin = preg_split("/[\s,]+/", $actividad->getFechaFin())[0];
        $horaFin = preg_split("/[\s,]+/", $actividad->getFechaFin())[1];

        $form->addEtiqueta(BITACORA_ACTIVIDAD_ESTADO_SALUD);
        $form->addSelect('select', 'sel_estado', 'sel_estado', $opciones, '', $actividad->getEstadoSalud(), '', ' required');

        $form->addEtiqueta(BITACORA_ACTIVIDAD_FECHA_INICIO);
        $form->addInputDate('date', 'date_inicio', 'date_inicio', $fechaInicio, '%Y-%m-%d', 18, 18, 'pattern="' . PATTERN_FECHA . '" title="' . $html->traducirTildes(TITLE_FECHA) . '" required', '');

        $form->addEtiqueta(BITACORA_ACTIVIDAD_HORA_INICIO);
        $form->addInputText('time', 'time_hora_inicio', 'time_hora_inicio', 5, '', $horaInicio, '', '" required');

        $form->addEtiqueta(BITACORA_ACTIVIDAD_FECHA_FIN);
        $form->addInputDate('date', 'date_fin', 'date_fin', $fechaFin, '%Y-%m-%d', 18, 18, 'pattern="' . PATTERN_FECHA . '" title="' . $html->traducirTildes(TITLE_FECHA) . '" required', '');

        $form->addEtiqueta(BITACORA_ACTIVIDAD_HORA_FIN);
        $form->addInputText('time', 'time_hora_fin', 'time_hora_fin', 5, '', $horaFin, '', ' required');

        $form->addEtiqueta(BITACORA_ACTIVIDAD_EJECUTADA_DESCRIPCION);
        $form->addTextArea('textarea', 'txt_actividad', 'txt_actividad', '100', '5', $actividad->getDescripcionActividadesEjecutadas(), '', ' title="' . $html->traducirTildes(TITLE_ALFANUMERICO) . '" required');

        $form->addEtiqueta(BITACORA_ACTIVIDAD_CONDICIONES_TOPOLOGICAS);
        $form->addTextArea('textarea', 'txt_topologicas', 'txt_topologicas', '100', '5', $actividad->getCondicionesTopologicas(), '', ' title="' . $html->traducirTildes(TITLE_ALFANUMERICO) . '" required');

        $form->addEtiqueta(BITACORA_ACTIVIDAD_CONDICIONES_CLIMATICAS);
        $form->addTextArea('textarea', 'txt_climaticas', 'txt_climaticas', '100', '5', $actividad->getCondicionesClimaticas(), '', ' title="' . $html->traducirTildes(TITLE_ALFANUMERICO) . '" required');

        $form->addEtiqueta(BITACORA_ACTIVIDAD_OBSERVACIONES);
        $form->addTextArea('textarea', 'txt_observaciones', 'txt_observaciones', '100', '5', $actividad->getObservaciones(), '', ' title="' . $html->traducirTildes(TITLE_ALFANUMERICO) . '" required');

        $form->addEtiqueta(BITACORA_ACTIVIDAD_NUMERO_CUADRILLAS);
        $form->addInputText('text', 'txt_numero_cuadrillas', 'txt_numero_cuadrillas', '5', '5', $actividad->getNumeroCuadrillas(), '', 'pattern="' . PATTERN_NUMEROS . '" title="' . $html->traducirTildes(TITLE_NUMEROS) . '" ');

        $form->addEtiqueta(BITACORA_ACTIVIDAD_TOTAL_PERSONAS);
        $form->addInputText('text', 'txt_total_personas', 'txt_total_personas', '5', '5', $actividad->getTotalPersonas(), '', 'pattern="' . PATTERN_NUMEROS . '" title="' . $html->traducirTildes(TITLE_NUMEROS) . '" ');

        $form->addEtiqueta(BITACORA_ACTIVIDAD_TOTAL_PERSONAS_CONTRATADAS);
        $form->addInputText('text', 'txt_total_personas_contratadas', 'txt_total_personas_contratadas', '5', '5', $actividad->getTotalPersonasContratadas(), '', 'pattern="' . PATTERN_NUMEROS . '" title="' . $html->traducirTildes(TITLE_NUMEROS) . '" ');

        $form->addEtiqueta(BITACORA_ACTIVIDAD_CUMPLIMIENTO_PARAFISCALES);
        $form->addSelect('select', 'sel_cumplimiento_parafiscales', 'sel_cumplimiento_parafiscales', $opcionesCumplimiento, '', $actividad->getCumplimientoParafiscales(), '', ' ');

        $form->addEtiqueta(BITACORA_ACTIVIDAD_CUMPLIMIENTO_SENALIZACION);
        $form->addSelect('select', 'sel_cumplimiento_senalizacion', 'sel_cumplimiento_senalizacion', $opcionesCumplimiento, '', $actividad->getCumplimientoSenalizacion(), '', ' ');

        $form->addEtiqueta(BITACORA_ACTIVIDAD_CUMPLIMIENTO_EPP);
        $form->addSelect('select', 'sel_cumplimiento_epp', 'sel_cumplimiento_epp', $opcionesCumplimiento, '', $actividad->getCumplimientoEpp(), '', ' ');

        $form->addEtiqueta(BITACORA_ACTIVIDAD_CUMPLIMIENTO_CERTIFICACIONES);
        $form->addSelect('select', 'sel_cumplimiento_certificaciones', 'sel_cumplimiento_certificaciones', $opcionesCumplimiento, '', $actividad->getCumplimientoCertificaciones(), '', ' ');

        $form->addInputButton('submit', 'btn_enviar', 'btn_enviar', BTN_ACEPTAR, 'button', '');
        $form->addInputButton('button', 'cancel', 'cancel', BTN_CANCELAR, 'button', 'onclick="cancelarAccion(\'frm_edit_actividad\',\'?mod=' . $modulo . '&niv=' . $niv . '&id_element=' . $id . '&task=see\');"');

        $form->writeForm();
        break;
    /**
     * la variable saveEdit, permite actualizar el actividad bitacora en la base 
     * de datos @see \CActividadBitacora
     */
    case 'saveEdit':
        $estadoSalud = $_REQUEST['sel_estado'];
        $descripcionActividadesEjecutadas = $_REQUEST['txt_actividad'];
        $condicionesClimaticas = $_REQUEST['txt_climaticas'];
        $condicionesTopologicas = $_REQUEST['txt_topologicas'];
        $observaciones = $_REQUEST['txt_observaciones'];
        $id_edit = $_REQUEST['id_element'];
        $bitacora = $_REQUEST['idBitacora'];

        $numCuadrillas = $_REQUEST['txt_numero_cuadrillas'];
        if (($numCuadrillas == "")) {
            $numCuadrillas = "null";
        }
        $totalPersonas = $_REQUEST['txt_total_personas'];
        if (($totalPersonas == "")) {
            $totalPersonas = "null";
        }
        $totalPersonasContratadas = $_REQUEST['txt_total_personas_contratadas'];
        if (($totalPersonasContratadas == "")) {
            $totalPersonasContratadas = "null";
        }
        $cumplimientoParafiscales = $_REQUEST['sel_cumplimiento_parafiscales'];
        if (($cumplimientoParafiscales == "-1")) {
            $cumplimientoParafiscales = "null";
        } else {
            $cumplimientoParafiscales = "'$cumplimientoParafiscales'";
        }
        $cumplimientoSenalizacion = $_REQUEST['sel_cumplimiento_senalizacion'];
        if (($cumplimientoSenalizacion == "-1")) {
            $cumplimientoSenalizacion = "null";
        } else {
            $cumplimientoSenalizacion = "'$cumplimientoSenalizacion'";
        }
        $cumplimientoEpp = $_REQUEST['sel_cumplimiento_epp'];
        if (($cumplimientoEpp == "-1")) {
            $cumplimientoEpp = "null";
        } else {
            $cumplimientoEpp = "'$cumplimientoEpp'";
        }
        $cumplimientoCertificacion = $_REQUEST['sel_cumplimiento_certificaciones'];
        if (($cumplimientoCertificacion == "-1")) {
            $cumplimientoCertificacion = "null";
        } else {
            $cumplimientoCertificacion = "'$cumplimientoCertificacion'";
        }
        $fecha = $_REQUEST['date_inicio'] . " " . $_REQUEST['time_hora_inicio'];
        $fechaFin = $_REQUEST['date_fin'] . " " . $_REQUEST['time_hora_fin'];

        $actividad = new CActividadBitacora($id_edit, $bitacora, $fecha, $fechaFin, $descripcionActividadesEjecutadas, $condicionesTopologicas, $condicionesClimaticas, $observaciones, $estadoSalud, $numCuadrillas, $totalPersonas, $totalPersonasContratadas, $cumplimientoParafiscales, $cumplimientoSenalizacion, $cumplimientoEpp, $cumplimientoCertificacion);


        $r = $daoActividadBitacora->updateActividad($actividad);
        $m = ERROR_EDITAR_ACTIVIDAD_BITACORA;
        if ($r == 'true') {
            $m = EXITO_EDITAR_ACTIVIDAD_BITACORA;
        }
        echo $html->generaAviso($m, "?mod=" . $modulo . "&niv=1&task=see&id_element=" . $bitacora);
        break;

    case 'seeActividad':
        $idActividad = $_REQUEST['id_element'];
        $idBitacora = $_REQUEST['idBitacora'];

        $form = new CHtmlForm();
        $form->setTitle(TITULO_ACTIVIDADES_BITACORA_HALLAZGOS_PENDIENTES);
        $form->setMethod('post');
        $form->setAction('?mod=' . $modulo . '&niv=' . $niv . '&task=see&id_element=' . $idBitacora);
        $form->addInputButton('submit', 'ok', 'ok', BTN_ATRAS, 'button', '');
        $form->setOptions('autoClean', false);
        $form->writeForm();

        $dt = new CHtmlDataTable();
        $dt->setTitleTable(TITULO_ACTIVIDADES_BITACORA_HALLAZGOS_PENDIENTES);
        $titulos = array(ACTIVIDADES_BITACORA_HALLAZGOS_PENDIENTES_CLASIFICACION, ACTIVIDADES_BITACORA_HALLAZGOS_PENDIENTES_OBSERVACION, BITACORA_ACTIVIDAD_GASTO_ARCHIVO);
        $hallazgosPendientes = $daoHallazgosPendientes->getHallazgosPendientesByActividad($idActividad);
        $dt->setTitleRow($titulos);
        $dt->setDataRows($hallazgosPendientes);
        $dt->setEditLink("?mod=" . $modulo . "&niv=" . $niv . "&task=editHallazgoPendiente&idBitacora=" . $idBitacora . "&idActividad=" . $idActividad);
        $dt->setDeleteLink("?mod=" . $modulo . "&niv=" . $niv . "&task=deleteHallazgoPendiente&idBitacora=" . $idBitacora . "&idActividad=" . $idActividad);
        $dt->setAddLink("?mod=" . $modulo . "&niv=" . $niv . "&task=addHallazgoPendiente&idBitacora=" . $idBitacora . "&idActividad=" . $idActividad);
        $dt->setType(1);
        $pag_crit = "";
        $dt->setPag(1, $pag_crit);
        $dt->writeDataTable($niv);

        $form = new CHtmlForm();
        $form->setTitle(TITULO_ACTIVIDADES_BITACORA_REGISTRO_FOTOGRAFICO);
        $form->setMethod('post');
        $form->setOptions('autoClean', false);
        $form->writeForm();

        $dt = new CHtmlDataTable();
        $dt->setTitleTable(TITULO_ACTIVIDADES_BITACORA_REGISTRO_FOTOGRAFICO);
        $titulos = array(ACTIVIDADES_BITACORA_REGISTRO_FOTOGRAFICO_DESCRIPCION, ACTIVIDADES_BITACORA_REGISTRO_FOTOGRAFICO_ARCHIVO);
        $registrosFotografico = $daoRegistroFotografico->getRegistroFotograficoByActividad($idActividad);
        $dt->setTitleRow($titulos);
        $dt->setDataRows($registrosFotografico);
        $dt->setEditLink("?mod=" . $modulo . "&niv=" . $niv . "&task=editPhoto&idBitacora=" . $idBitacora . "&idActividad=" . $idActividad);
        $dt->setDeleteLink("?mod=" . $modulo . "&niv=" . $niv . "&task=deletePhoto&idBitacora=" . $idBitacora . "&idActividad=" . $idActividad);
        $dt->setAddLink("?mod=" . $modulo . "&niv=" . $niv . "&task=addPhoto&idBitacora=" . $idBitacora . "&idActividad=" . $idActividad);
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
        $dt->setEditLink("?mod=" . $modulo . "&niv=" . $niv . "&task=editGasto&idBitacora=" . $idBitacora . "&idActividad=" . $idActividad);
        $dt->setDeleteLink("?mod=" . $modulo . "&niv=" . $niv . "&task=deleteGasto&idBitacora=" . $idBitacora . "&idActividad=" . $idActividad);
        $dt->setAddLink("?mod=" . $modulo . "&niv=" . $niv . "&task=addGasto&idBitacora=" . $idBitacora . "&idActividad=" . $idActividad);
        $dt->setType(1);
        $pag_crit = "";
        $dt->setPag(1, $pag_crit);
        $dt->setFormatRow(array(null, array(2, ',', '.'), null, null));
        $dt->setSumColumns(array(2));
        $dt->writeDataTable($niv);

        break;

    case 'addPhoto':
        $idActividad = $_REQUEST['idActividad'];
        $idBitacora = $_REQUEST['idBitacora'];
        $form = new CHtmlForm();
        $form->setTitle(TITULO_AGREGAR_ACTIVIDADES_BITACORA_REGISTRO_FOTOGRAFICO);
        $form->setId('frm_add_registro_fotografico');
        $form->setAction('?mod=' . $modulo . '&niv=1&task=saveAddPhoto&idActividad=' . $idActividad . '&idBitacora=' . $idBitacora);
        $form->setMethod('post');
        $form->setClassEtiquetas('td_label');

        $form->addEtiqueta(ACTIVIDADES_BITACORA_REGISTRO_FOTOGRAFICO_ARCHIVO);
        $form->addInputFile('file', 'file_archivo', 'file_archivo', '25', 'file', ' required');

        $form->addEtiqueta(ACTIVIDADES_BITACORA_REGISTRO_FOTOGRAFICO_DESCRIPCION);
        $form->addTextArea('textarea', 'txt_descripcion', 'txt_descripcion', 100, 5, '', '', ' title="' . $html->traducirTildes(TITLE_ALFANUMERICO) . '" required');

        $form->addInputButton('submit', 'ok', 'ok', BTN_ACEPTAR, 'button', '');
        $form->addInputButton('button', 'cancel', 'cancel', BTN_CANCELAR, 'button', 'onclick=location.href=\'?mod=' . $modulo . '&niv=1&task=seeActividad&id_element=' . $idActividad . '&idBitacora=' . $idBitacora . '\'');

        $form->writeForm();
        break;

    case 'saveAddPhoto':
        $idActividad = $_REQUEST['idActividad'];
        $idBitacora = $_REQUEST['idBitacora'];
        $archivo = $_FILES['file_archivo'];
        $descripcion = $_REQUEST['txt_descripcion'];

        $registroFotografico = new CRegistroFotografico(null, $archivo, $descripcion, $idActividad);

        $r = $daoRegistroFotografico->insertRegistroFotografico($registroFotografico);
        $m = ERROR_AGREGAR_ACTIVIDADES_BITACORA_REGISTRO_FOTOGRAFICO;
        if ($r == 'true') {
            $m = EXITO_AGREGAR_ACTIVIDADES_BITACORA_REGISTRO_FOTOGRAFICO;
        }
        echo $html->generaAviso($m, "?mod=" . $modulo . "&niv=1&task=seeActividad&id_element=" . $idActividad . "&idBitacora=" . $idBitacora);
        break;

    case 'editPhoto':
        $idGasto = $_REQUEST['id_element'];
        $gasto = $daoRegistroFotografico->getRegistroFotograficoById($idRegistroFotografico);
        $idActividad = $_REQUEST['idActividad'];
        $idBitacora = $_REQUEST['idBitacora'];
        $form = new CHtmlForm();
        $form->setTitle(TITULO_EDITAR_ACTIVIDADES_BITACORA_REGISTRO_FOTOGRAFICO);
        $form->setId('frm_edit_registro_fotografico');
        $form->setAction('?mod=' . $modulo . '&niv=1&task=saveEditPhoto&idActividad=' . $idActividad . '&idBitacora=' . $idBitacora . '&idRegistroFotografico=' . $idRegistroFotografico);
        $form->setMethod('post');
        $form->setClassEtiquetas('td_label');

        $form->addEtiqueta(ACTIVIDADES_BITACORA_REGISTRO_FOTOGRAFICO_ARCHIVO);
        $form->addInputFile('file', 'file_archivo', 'file_archivo', '25', 'file', '');

        $form->addEtiqueta(ACTIVIDADES_BITACORA_REGISTRO_FOTOGRAFICO_DESCRIPCION);
        $form->addTextArea('textarea', 'txt_descripcion', 'txt_descripcion', 100, 5, $registroFotografico->getDescripcion(), '', ' title="' . $html->traducirTildes(TITLE_ALFANUMERICO) . '" required');

        $form->addInputButton('submit', 'ok', 'ok', BTN_ACEPTAR, 'button', '');
        $form->addInputButton('button', 'cancel', 'cancel', BTN_CANCELAR, 'button', 'onclick=location.href=\'?mod=' . $modulo . '&niv=1&task=seeActividad&id_element=' . $idActividad . '&idBitacora=' . $idBitacora . '\'');

        $form->writeForm();
        break;

    case 'saveEditPhoto':
        $idRegistroFotografico = $_REQUEST['idRegistroFotografico'];
        $idActividad = $_REQUEST['idActividad'];
        $idBitacora = $_REQUEST['idBitacora'];
        $archivo = $_FILES['file_archivo'];
        $descripcion = $_REQUEST['txt_descripcion'];

        $registroFotografico = new CRegistroFotografico($idRegistroFotografico, $archivo, $descripcion, $idActividad);

        $r = $daoRegistroFotografico->updateRegistroFotografico($registroFotografico);
        $m = ERROR_EDITAR_ACTIVIDADES_BITACORA_REGISTRO_FOTOGRAFICO;
        if ($r == 'true') {
            $m = EXITO_EDITAR_ACTIVIDADES_BITACORA_REGISTRO_FOTOGRAFICO;
        }
        echo $html->generaAviso($m, "?mod=" . $modulo . "&niv=1&task=seeActividad&id_element=" . $idActividad . "&idBitacora=" . $idBitacora);
        break;

    /**
     * la variable delete, permite hacer la carga del objeto bitacora 
     * y espera confirmacion de eliminarlo @see \CBitacora
     */
    case 'deletePhoto':
        $idRegistroFotografico = $_REQUEST['id_element'];
        $idActividad = $_REQUEST['idActividad'];
        $idBitacora = $_REQUEST['idBitacora'];
        echo $html->generaAdvertencia(CONFIRMAR_BORRAR_REGISTRO_FOTOGRAFICO, '?mod=' . $modulo . '&niv=1&task=confirmDeletePhoto&idRegistroFotografico=' . $idRegistroFotografico . '&idBitacora=' . $idBitacora . '&idActividad=' . $idActividad, 'onclick=location.href=\'?mod=' . $modulo . '&niv=1&id_element=' . $idActividad . '&idBitacora=' . $idBitacora . '&task=seeActividad\'');
        break;

    /**
     * la variable confirmDelete, permite eliminar el objeto registro producto 
     * de la base de datos @see \CRegistroInversion
     */
    case 'confirmDeletePhoto':
        $idRegistroFotografico = $_REQUEST['idRegistroFotografico'];
        $idActividad = $_REQUEST['idActividad'];
        $idBitacora = $_REQUEST['idBitacora'];
        $r = $daoRegistroFotografico->deleteRegistroFotograficoById($idRegistroFotografico);
        $m = ERROR_BORRAR_ACTIVIDADES_BITACORA_REGISTRO_FOTOGRAFICO;
        if ($r == 'true') {
            $m = EXITO_BORRAR_ACTIVIDADES_BITACORA_REGISTRO_FOTOGRAFICO;
        }
        echo $html->generaAviso($m, "?mod=" . $modulo . "&niv=1&task=seeActividad&id_element=" . $idActividad . "&idBitacora=" . $idBitacora);
        break;

    case 'addHallazgoPendiente':
        $idActividad = $_REQUEST['idActividad'];
        $idBitacora = $_REQUEST['idBitacora'];

        $area = $_REQUEST['sel_area'];
        $criterio = "1";
        if ($area != null) {
            $criterio .= " AND idAreaHallazgo = " . $area;
        }
        $form = new CHtmlForm();
        $form->setTitle(TITULO_AGREGAR_ACTIVIDADES_BITACORA_HALLAZGOS_PENDIENTES);
        $form->setId('frm_add_hallazgo');
        $form->setAction('?mod=' . $modulo . '&niv=1&task=addHallazgoPendiente&idActividad=' . $idActividad . '&idBitacora=' . $idBitacora);
        $form->setMethod('post');
        $form->setClassEtiquetas('td_label');

        $form->addEtiqueta(ACTIVIDADES_BITACORA_HALLAZGOS_PENDIENTES_OBSERVACION);
        $form->addTextArea('textarea', 'txt_descripcion', 'txt_descripcion', 100, 5, $_REQUEST['txt_descripcion'], '', ' title="' . $html->traducirTildes(TITLE_ALFANUMERICO) . '" required');

        $clasificaciones = $daoBasicas->getBasicas('areashallazgospendientes');
        $opciones = null;
        if (isset($clasificaciones)) {
            foreach ($clasificaciones as $clasificacion) {
                $opciones[count($opciones)] = array('value' => $clasificacion->getId(),
                    'texto' => $clasificacion->getDescripcion());
            }
        }

        $form->addEtiqueta(ACTIVIDADES_BITACORA_HALLAZGOS_PENDIENTES_CLASIFICACION);
        $form->addSelect('select', 'sel_area', 'sel_area', $opciones, '', $area, '', 'onChange="submit();" required');

        $tipos = $daoBasicas->getBasicas('tipohallazgo', $criterio);
        $opciones = null;
        if (isset($tipos)) {
            foreach ($tipos as $tipo) {
                $opciones[count($opciones)] = array('value' => $tipo->getId(),
                    'texto' => $tipo->getDescripcion());
            }
        }

        $form->addEtiqueta(ACTIVIDADES_BITACORA_HALLAZGOS_PENDIENTES_CLASIFICACION);
        $form->addSelect('select', 'sel_tipo', 'sel_tipo', $opciones, '', '', '', ' required');

        $form->addEtiqueta(BITACORA_ACTIVIDAD_GASTO_ARCHIVO);
        $form->addInputFile('file', 'file_archivo', 'file_archivo', '25', 'file', '');

        $form->addInputButton('button', 'btn_add', 'btn_add', BTN_ACEPTAR, 'button', 'onclick="cancelarAccion(\'frm_add_hallazgo\',\'?mod=' . $modulo . '&niv=1&task=saveAddHallazgoPendiente&idActividad=' . $idActividad . '&idBitacora=' . $idBitacora . '\');"');
        $form->addInputButton('button', 'cancel', 'cancel', BTN_CANCELAR, 'button', 'onclick=location.href=\'?mod=' . $modulo . '&niv=1&task=seeActividad&id_element=' . $idActividad . '&idBitacora=' . $idBitacora . '\'');

        $form->writeForm();
        break;

    case 'saveAddHallazgoPendiente':
        $idActividad = $_REQUEST['idActividad'];
        $idBitacora = $_REQUEST['idBitacora'];
        $clasificacion = $_REQUEST['sel_tipo'];
        $observacion = $_REQUEST['txt_descripcion'];
        $archivo = $_FILES['file_archivo'];

        $hallazgoPendiente = new CHallazgosPendientes(null, $observacion, $clasificacion, $idActividad, $archivo);

        $r = $daoHallazgosPendientes->insertHallazgosPendientes($hallazgoPendiente);
        $m = ERROR_AGREGAR_ACTIVIDADES_BITACORA_HALLAZGOS_PENDIENTES;
        if ($r == 'true') {
            $m = EXITO_AGREGAR_ACTIVIDADES_BITACORA_HALLAZGOS_PENDIENTES;
        }
        echo $html->generaAviso($m, "?mod=" . $modulo . "&niv=1&task=seeActividad&id_element=" . $idActividad . "&idBitacora=" . $idBitacora);
        break;

    case 'editHallazgoPendiente':
        $idHallazgoPendiente = $_REQUEST['id_element'];
        $idActividad = $_REQUEST['idActividad'];
        $idBitacora = $_REQUEST['idBitacora'];
        $hallazgoPendiente = $daoHallazgosPendientes->getHallazgosPendientesById($idHallazgoPendiente);

        $daoBasicaRelacionada = new CBasicaRelacionadaData($db);


        $area = $_REQUEST['sel_area'];
        $criterio .= " idAreaHallazgo = " . $area;
        if ($area == NULL) {
            $area = $daoBasicaRelacionada->getBasicaById('tipohallazgo', 'idTipoHallazgo = ' . $hallazgoPendiente->getTipo());
            $area = $area->getTabla();
            $criterio = "1";
        }

        $descripcion = $_REQUEST['txt_descripcion'];
        if ($descripcion == NULL) {
            $descripcion = $hallazgoPendiente->getObservacion();
        }

        $tipoH = $_REQUEST['sel_tipo'];
        if ($tipoH == NULL) {
            $tipoH = $hallazgoPendiente->getTipo();
        }

        $form = new CHtmlForm();
        $form->setTitle(TITULO_EDITAR_ACTIVIDADES_BITACORA_HALLAZGOS_PENDIENTES);
        $form->setId('frm_edit_hallazgo');
        $form->setAction('?mod=' . $modulo . '&niv=1&task=editHallazgoPendiente&idActividad=' . $idActividad . '&idBitacora=' . $idBitacora . '&id_element=' . $idHallazgoPendiente);
        $form->setMethod('post');
        $form->setClassEtiquetas('td_label');

        $form->addEtiqueta(ACTIVIDADES_BITACORA_HALLAZGOS_PENDIENTES_OBSERVACION);
        $form->addTextArea('textarea', 'txt_descripcion', 'txt_descripcion', 100, 5, $descripcion, '', ' title="' . $html->traducirTildes(TITLE_ALFANUMERICO) . '" required');

        $clasificaciones = $daoBasicas->getBasicas('areashallazgospendientes');
        $opciones = null;
        if (isset($clasificaciones)) {
            foreach ($clasificaciones as $clasificacion) {
                $opciones[count($opciones)] = array('value' => $clasificacion->getId(),
                    'texto' => $clasificacion->getDescripcion());
            }
        }

        $form->addEtiqueta(ACTIVIDADES_BITACORA_HALLAZGOS_PENDIENTES_CLASIFICACION);
        $form->addSelect('select', 'sel_area', 'sel_area', $opciones, '', $area, '', 'onChange="submit();" required');

        $tipos = $daoBasicas->getBasicas('tipohallazgo', $criterio);
        $opciones = null;
        if (isset($tipos)) {
            foreach ($tipos as $tipo) {
                $opciones[count($opciones)] = array('value' => $tipo->getId(),
                    'texto' => $tipo->getDescripcion());
            }
        }

        $form->addEtiqueta(ACTIVIDADES_BITACORA_HALLAZGOS_PENDIENTES_CLASIFICACION);
        $form->addSelect('select', 'sel_tipo', 'sel_tipo', $opciones, '', $tipoH, '', ' required');

        $form->addEtiqueta(BITACORA_ACTIVIDAD_GASTO_ARCHIVO);
        $form->addInputFile('file', 'file_archivo', 'file_archivo', '25', 'file', '');

        $form->addInputButton('button', 'btn_add', 'btn_add', BTN_ACEPTAR, 'button', 'onclick="cancelarAccion(\'frm_edit_hallazgo\',\'?mod=' . $modulo . '&niv=1&task=saveEditHallazgoPendiente&idActividad=' . $idActividad . '&idBitacora=' . $idBitacora . '&idHallazgoPendiente=' . $idHallazgoPendiente . '\');"');
        $form->addInputButton('button', 'cancel', 'cancel', BTN_CANCELAR, 'button', 'onclick=location.href=\'?mod=' . $modulo . '&niv=1&task=seeActividad&id_element=' . $idActividad . '&idBitacora=' . $idBitacora . '\'');

        $form->writeForm();
        break;

    case 'saveEditHallazgoPendiente':
        $idHallazgoPendiente = $_REQUEST['idHallazgoPendiente'];
        $idActividad = $_REQUEST['idActividad'];
        $idBitacora = $_REQUEST['idBitacora'];
        $clasificacion = $_REQUEST['sel_tipo'];
        $observacion = $_REQUEST['txt_descripcion'];
        $archivo = $_FILES['file_archivo'];

        $hallazgoPendiente = new CHallazgosPendientes($idHallazgoPendiente, $observacion, $clasificacion, $idActividad, $archivo);

        $r = $daoHallazgosPendientes->updateHallazgosPendientes($hallazgoPendiente);
        $m = ERROR_EDITAR_ACTIVIDADES_BITACORA_HALLAZGOS_PENDIENTES;
        if ($r == 'true') {
            $m = EXITO_EDITAR_ACTIVIDADES_BITACORA_HALLAZGOS_PENDIENTES;
        }
        echo $html->generaAviso($m, "?mod=" . $modulo . "&niv=1&task=seeActividad&id_element=" . $idActividad . "&idBitacora=" . $idBitacora);
        break;

    /**
     * la variable delete, permite hacer la carga del objeto bitacora 
     * y espera confirmacion de eliminarlo @see \CBitacora
     */
    case 'deleteHallazgoPendiente':
        $idHallazgoPendiente = $_REQUEST['id_element'];
        $idActividad = $_REQUEST['idActividad'];
        $idBitacora = $_REQUEST['idBitacora'];
        echo $html->generaAdvertencia(CONFIRMAR_BORRAR_HALLAZGOS_PENDIENTES, '?mod=' . $modulo . '&niv=1&task=confirmDeleteHallazgoPendiente&idHallazgoPendiente=' . $idHallazgoPendiente . '&idBitacora=' . $idBitacora . '&idActividad=' . $idActividad, 'onclick=location.href=\'?mod=' . $modulo . '&niv=1&id_element=' . $idActividad . '&idBitacora=' . $idBitacora . '&task=seeActividad\'');
        break;

    /**
     * la variable confirmDelete, permite eliminar el objeto registro producto 
     * de la base de datos @see \CRegistroInversion
     */
    case 'confirmDeleteHallazgoPendiente':
        $idHallazgosPendientes = $_REQUEST['idHallazgoPendiente'];
        $idActividad = $_REQUEST['idActividad'];
        $idBitacora = $_REQUEST['idBitacora'];
        $r = $daoHallazgosPendientes->deleteHallazgosPendientesById($idHallazgosPendientes);
        $m = ERROR_BORRAR_ACTIVIDADES_BITACORA_HALLAZGOS_PENDIENTES;
        if ($r == 'true') {
            $m = EXITO_BORRAR_ACTIVIDADES_BITACORA_HALLAZGOS_PENDIENTES;
        }
        echo $html->generaAviso($m, "?mod=" . $modulo . "&niv=1&task=seeActividad&id_element=" . $idActividad . "&idBitacora=" . $idBitacora);
        break;

    case 'addGasto':
        $idActividad = $_REQUEST['idActividad'];
        $idBitacora = $_REQUEST['idBitacora'];
        $form = new CHtmlForm();
        $form->setTitle(TITULO_AGREGAR_ACTIVIDADES_BITACORA_GASTO);
        $form->setId('frm_add_registro_fotografico');
        $form->setAction('?mod=' . $modulo . '&niv=1&task=saveAddGasto&idActividad=' . $idActividad . '&idBitacora=' . $idBitacora);
        $form->setMethod('post');
        $form->setClassEtiquetas('td_label');

        $tiposActividad = $daoBasicas->getBasicas('tipo_gasto');
        $opciones = null;
        if (isset($tiposActividad)) {
            foreach ($tiposActividad as $tipoActividad) {
                $opciones[count($opciones)] = array('value' => $tipoActividad->getId(),
                    'texto' => $tipoActividad->getDescripcion());
            }
        }

        $form->addEtiqueta(BITACORA_ACTIVIDAD_GASTO_TIPO);
        $form->addSelect('select', 'sel_tipo', 'sel_tipo', $opciones, '', '', '', ' required');

        $form->addEtiqueta(BITACORA_ACTIVIDAD_GASTO_DESCRIPCION);
        $form->addTextArea('textarea', 'txt_descripcion', 'txt_descripcion', '100', '5', '', '', ' title="' . $html->traducirTildes(TITLE_ALFANUMERICO) . '" required');

        $form->addEtiqueta(BITACORA_ACTIVIDAD_GASTO_VALOR);
        $form->addInputText('text', 'txt_valor', 'txt_valor', '19', '19', '', '', ' pattern="' . PATTERN_NUMEROS_FINANCIEROS . '" title="' . $html->traducirTildes(TITLE_NUMEROS_FINANCIEROS) . '" onkeyup="formatearNumero(this);" required');

        $form->addEtiqueta(BITACORA_ACTIVIDAD_GASTO_ARCHIVO);
        $form->addInputFile('file', 'file_archivo', 'file_archivo', '25', 'file', '');

        $form->addInputButton('submit', 'ok', 'ok', BTN_ACEPTAR, 'button', '');
        $form->addInputButton('button', 'cancel', 'cancel', BTN_CANCELAR, 'button', 'onclick=location.href=\'?mod=' . $modulo . '&niv=1&task=seeActividad&id_element=' . $idActividad . '&idBitacora=' . $idBitacora . '\'');

        $form->writeForm();
        break;

    case 'saveAddGasto':
        $idActividad = $_REQUEST['idActividad'];
        $idBitacora = $_REQUEST['idBitacora'];
        $archivo = null;
        if (isset($_FILES['file_archivo'])) {
            $archivo = $_FILES['file_archivo'];
        }
        $descripcion = $_REQUEST['txt_descripcion'];
        $tipo = $_REQUEST['sel_tipo'];
        $valor = str_replace(".", "", $_REQUEST['txt_valor']);

        $gasto = new CGasto(NULL, $descripcion, $valor, $archivo, $tipo, $idActividad);

        $r = $daoGastos->insertGasto($gasto);
        $m = ERROR_AGREGAR_ACTIVIDADES_BITACORA_GASTO;
        if ($r == 'true') {
            $m = EXITO_AGREGAR_ACTIVIDADES_BITACORA_GASTO;
        }
        echo $html->generaAviso($m, "?mod=" . $modulo . "&niv=1&task=seeActividad&id_element=" . $idActividad . "&idBitacora=" . $idBitacora);
        break;

    case 'editGasto':
        $idActividad = $_REQUEST['idActividad'];
        $idBitacora = $_REQUEST['idBitacora'];
        $idGasto = $_REQUEST['id_element'];
        $gasto = $daoGastos->getGastoById($idGasto);
        if ($gasto->getEstado() == 1) {
            echo $html->generaAviso(BLOQUEAR_EDITAR_ACTIVIDADES_BITACORA_GASTO, "?mod=" . $modulo . "&niv=1&task=seeActividad&id_element=" . $idActividad . "&idBitacora=" . $idBitacora);
        } else {
            $form = new CHtmlForm();
            $form->setTitle(TITULO_EDITAR_ACTIVIDADES_BITACORA_GASTO);
            $form->setId('frm_edit_registro_fotografico');
            $form->setAction('?mod=' . $modulo . '&niv=1&task=saveEditGasto&idActividad=' . $idActividad . '&idBitacora=' . $idBitacora . '&idGasto=' . $idGasto);
            $form->setMethod('post');
            $form->setClassEtiquetas('td_label');

            $tiposActividad = $daoBasicas->getBasicas('tipo_gasto');
            $opciones = null;
            if (isset($tiposActividad)) {
                foreach ($tiposActividad as $tipoActividad) {
                    $opciones[count($opciones)] = array('value' => $tipoActividad->getId(),
                        'texto' => $tipoActividad->getDescripcion());
                }
            }

            $form->addEtiqueta(BITACORA_ACTIVIDAD_GASTO_TIPO);
            $form->addSelect('select', 'sel_tipo', 'sel_tipo', $opciones, '', $gasto->getTipo(), '', ' required');

            $form->addEtiqueta(BITACORA_ACTIVIDAD_GASTO_DESCRIPCION);
            $form->addTextArea('textarea', 'txt_descripcion', 'txt_descripcion', '100', '5', $gasto->getDescripcion(), '', ' title="' . $html->traducirTildes(TITLE_ALFANUMERICO) . '" required');

            $form->addEtiqueta(BITACORA_ACTIVIDAD_GASTO_VALOR);
            $form->addInputText('text', 'txt_valor', 'txt_valor', '19', '19', $gasto->getValor(), '', ' pattern="' . PATTERN_NUMEROS_FINANCIEROS . '" title="' . $html->traducirTildes(TITLE_NUMEROS_FINANCIEROS) . '" onkeyup="formatearNumero(this);" required');

            $form->addEtiqueta(BITACORA_ACTIVIDAD_GASTO_ARCHIVO);
            $form->addInputFile('file', 'file_archivo', 'file_archivo', '25', 'file', '');

            $form->addInputButton('submit', 'ok', 'ok', BTN_ACEPTAR, 'button', '');
            $form->addInputButton('button', 'cancel', 'cancel', BTN_CANCELAR, 'button', 'onclick=location.href=\'?mod=' . $modulo . '&niv=1&task=seeActividad&id_element=' . $idActividad . '&idBitacora=' . $idBitacora . '\'');

            $form->writeForm();
        }
        break;

    case 'saveEditGasto':
        $idGasto = $_REQUEST['idGasto'];
        $idActividad = $_REQUEST['idActividad'];
        $idBitacora = $_REQUEST['idBitacora'];
        $idActividad = $_REQUEST['idActividad'];
        $idBitacora = $_REQUEST['idBitacora'];
        $archivo = $_FILES['file_archivo'];
        $descripcion = $_REQUEST['txt_descripcion'];
        $tipo = $_REQUEST['sel_tipo'];
        $valor = str_replace(".", "", $_REQUEST['txt_valor']);

        $gasto = new CGasto($idGasto, $descripcion, $valor, $archivo, $tipo, $idActividad);

        $r = $daoGastos->updateGasto($gasto);
        $m = ERROR_EDITAR_ACTIVIDADES_BITACORA_GASTO;
        if ($r == 'true') {
            $m = EXITO_EDITAR_ACTIVIDADES_BITACORA_GASTO;
        }
        echo $html->generaAviso($m, "?mod=" . $modulo . "&niv=1&task=seeActividad&id_element=" . $idActividad . "&idBitacora=" . $idBitacora);
        break;

    /**
     * la variable delete, permite hacer la carga del objeto bitacora 
     * y espera confirmacion de eliminarlo @see \CBitacora
     */
    case 'deleteGasto':
        $id_delete = $_REQUEST['id_element'];
        $idActividad = $_REQUEST['idActividad'];
        $idBitacora = $_REQUEST['idBitacora'];
        $gasto = $daoGastos->getGastoById($id_delete);
        if ($gasto->getEstado() == 1) {
            echo $html->generaAviso(BLOQUEAR_EDITAR_ACTIVIDADES_BITACORA_GASTO, "?mod=" . $modulo . "&niv=1&task=seeActividad&id_element=" . $idActividad . "&idBitacora=" . $idBitacora);
        } else {
            echo $html->generaAdvertencia(CONFIRMAR_BORRAR_GASTO, '?mod=' . $modulo . '&niv=1&task=confirmDeleteGasto&id_delete=' . $id_delete . '&idBitacora=' . $idBitacora . '&idActividad=' . $idActividad, 'onclick=location.href=\'?mod=' . $modulo . '&niv=1&id_element=' . $idActividad . '&idBitacora=' . $idBitacora . '&task=seeActividad\'');
        }
        break;

    /**
     * la variable confirmDelete, permite eliminar el objeto registro producto 
     * de la base de datos @see \CRegistroInversion
     */
    case 'confirmDeleteGasto':
        $id = $_REQUEST['id_delete'];
        $idActividad = $_REQUEST['idActividad'];
        $idBitacora = $_REQUEST['idBitacora'];
        $r = $daoGastos->deleteGastosById($id);
        $m = ERROR_BORRAR_ACTIVIDADES_BITACORA_GASTO;
        if ($r == 'true') {
            $m = EXITO_BORRAR_ACTIVIDADES_BITACORA_GASTO;
        }
        echo $html->generaAviso($m, "?mod=" . $modulo . "&niv=1&task=seeActividad&id_element=" . $idActividad . "&idBitacora=" . $idBitacora);
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

