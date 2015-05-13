<?php
defined('_VALID_PRY') or die('Restricted access');
$tipoEncuesta = 0;
$operador = OPERADOR_DEFECTO;
$planData = new CGeneradorPlaneacionData($db);
$ejeData = new CGeneradorEjecucionData($db);
$daoInstrumentos = new CInstrumentoData($db);

$task = $_REQUEST['task'];
$nivel = $ejeData->consultarNivelUsuario($id_usuario);
$ref = $_REQUEST['ref'];
if (empty($task)) {
    $task = 'pre-list';
}
switch ($task) {
    /*
     * la variable pre-list muestra las planeaciones existentes actualmente con
     *  la posibilidad de realizar un filtro en ellas
     */
    case 'pre-list':

        $region = $_REQUEST['txt_region'];
        $departamento = $_REQUEST['txt_departamento'];
        $municipio = $_REQUEST['txt_municipio'];
        $estado = $_REQUEST['txt_estado'];
        $consecutivo_encuesta = $_REQUEST['txt_consecutivo_encuesta'];
        $instrumento = $_REQUEST['txt_instrumento'];
        //-------------------------------criterios---------------------------
        $criterio = " 1";

        if (isset($region) && $region != -1 && $region != '') {
            $criterio .= " and r.der_id = " . $region;
        }
        if (isset($departamento) && $departamento != -1 && $departamento != '') {
            $criterio .= " and d.dep_id = " . $departamento;
        }
        if (isset($municipio) && $municipio != -1 && $municipio != '') {
            $criterio .= " and m.mun_id = " . $municipio;
        }
        if (isset($estado) && $estado != -1 && $estado != '') {
            
            switch ($estado) {
                
                case "1":
                    $criterio .= " AND (SELECT count(e.enc_id) FROM generador_encuesta e WHERE e.pla_id = g.pla_id AND e.ees_id)/(SELECT count(e.enc_id) FROM generador_encuesta e WHERE e.pla_id = g.pla_id) = 1";
                    break;

                case "2":
                    $criterio .= " AND g.pla_fecha_fin > NOW() AND (SELECT count(e.enc_id) FROM generador_encuesta e WHERE e.pla_id = g.pla_id AND e.ees_id)/(SELECT count(e.enc_id) FROM generador_encuesta e WHERE e.pla_id = g.pla_id) != 1";
                    break;

                case "3":
                    $criterio .= " AND g.pla_fecha_fin < NOW() AND (SELECT count(e.enc_id) FROM generador_encuesta e WHERE e.pla_id = g.pla_id AND e.ees_id)/(SELECT count(e.enc_id) FROM generador_encuesta e WHERE e.pla_id = g.pla_id) != 1";
                    break;

                default:
                    break;
            }
        }
        if (isset($instrumento) && $instrumento != -1 && $instrumento != '') {
            $criterio .= " and i.idInstrumento = " . $instrumento;
        }
        if (isset($ref) && $ref != -1 && $ref != '') {
            $criterio .= " and i.idEncabezado = " . $ref;
        }
        if ($nivel != 1 && $id_usuario != 64) {
            $criterio .=" and u.usu_id=$id_usuario";
        }

        //Inicio Formulario
        $form = new CHtmlForm();
        $form->setTitle(TABLA_EJECUCION);
        $form->setId('frm_list_ejecucion');
        $form->setMethod('post');
        $form->setClassEtiquetas('td_label');

        //Regiones
        $opciones = null;
        $form->addEtiqueta(PLANEACION_REGION);
        $regiones = $planData->getRegiones(' der_nombre');

        if (isset($regiones)) {
            foreach ($regiones as $t) {
                $opciones[count($opciones)] = array('value' => $t['id'], 'texto' => $t['nombre']);
            }
        }
        $form->addSelect('select', 'txt_region', 'txt_region', $opciones, PLANEACION_REGION, $region, '', 'onChange=submit();');


        //Departamento
        $opciones = null;
        $form->addEtiqueta(PLANEACION_DEPARTAMENTO);
        $departamentos = $planData->getDepartamento($region, ' dep_nombre');
        if (isset($departamentos)) {
            foreach ($departamentos as $t) {
                $opciones[count($opciones)] = array('value' => $t['id'], 'texto' => $t['nombre']);
            }
        }
        $form->addSelect('select', 'txt_departamento', 'txt_departamento', $opciones, PLANEACION_DEPARTAMENTO, $departamento, '', 'onChange=submit();');

        //Municipio
        $form->addEtiqueta(PLANEACION_MUNICIPIO);
        $opciones = null;
        $municipios = $planData->getMunicipio($departamento, ' mun_nombre');
        if (isset($municipios)) {
            foreach ($municipios as $t) {
                $opciones[count($opciones)] = array('value' => $t['id'], 'texto' => $t['nombre']);
            }
        }
        $form->addSelect('select', 'txt_municipio', 'txt_municipio', $opciones, PLANEACION_MUNICIPIO, $municipio, '', 'onChange=submit();');
        //Estado
        $form->addEtiqueta(EJECUCION_ESTADO);
        $opciones = null;
        $encuesta_estados = $ejeData->getEncuestaEstados();
        if (isset($encuesta_estados)) {
            foreach ($encuesta_estados as $t) {
                $opciones[count($opciones)] = array('value' => $t['id'], 'texto' => $t['nombre']);
            }
        }
        $form->addSelect('select', 'txt_estado', 'txt_estado', $opciones, EJECUCION_ESTADO, $estado, '', 'onChange=submit();');

        $form->addEtiqueta(INSTRUMENTO);
        $opciones = null;
		if($ref != ""){
			$ejes = $planData->getInstrumentos(' idInstrumento', "idEncabezado = $ref");
		} else {
			$ejes = $planData->getInstrumentos(' idInstrumento', "1");
		}
        if (isset($ejes)) {
            foreach ($ejes as $t) {
                $opciones[count($opciones)] = array('value' => $t['id'], 'texto' => $t['nombre']);
            }
        }

        $form->addSelect('select', 'txt_instrumento', 'txt_instrumento', $opciones, INSTRUMENTO, $instrumento, '', 'onChange=submit();');

        $titulos = array(PLANEACION_REGION, PLANEACION_DEPARTAMENTO, PLANEACION_MUNICIPIO, CENTRO_POBLADO_BENEFICIARIO, NOMBRE_BENEFICIARIO,
            INSTRUMENTO, PLANEACION_NUMERO_ENCUESTAS, PLANEACION_FECHA_INICIO, PLANEACION_FECHA_FIN,
            PLANEACION_USUARIO, EJECUCION_ESTADO, EJECUCION_PORCENTAJE);
        $planeaciones = $ejeData->getEjecucion($criterio, false);

        if ($nivel == 1) {
            $form->addInputButton('button', 'btn_sincronizar', 'btn_sincronizar', BTN_SINCRONIZAR, 'button', 'onClick=location.href="?mod=' . $modulo . '&niv=' . $niv . "&task=syncEncuestaGeneral\"");
        }
        $form->writeForm();

        //Carga filtro de planeaciones

        $dt = new CHtmlDataTable();

        $dt->setDataRows($planeaciones);
        $dt->setTitleRow($titulos);
        $dt->setTitleTable(TABLA_EJECUCION);

        //OPCIONES DE GESTIÓN
        $dt->setSeeLink("?mod=" . $modulo . "&niv=" . $nivel . "&task=list&r=" . $region . "&dep=" . $departamento . "&mun=" . $municipio . "&est=" . $estado . "&ref=" . $ref);

        $dt->setType(1);
        $pag_crit = "";
        $dt->setPag(1, $pag_crit);
        $dt->writeDataTable($niv);
        
        $criterio = "1";
        if($ref != NULL){
            $criterio .= " AND i.idEncabezado = $ref";
        }
        $ejecucion_resumen = $ejeData->getResumenPlaneacion($criterio);
        $dtr = new CHtmlDataTable();
        $titulos_resumen = array(INSTRUMENTO, PLANEACION_NUMERO_ENCUESTAS, PLANEACION_NUMERO_EJECUTADO, PLANEACION_PORCENTAJE);
        $dtr->setTitleTable(TABLA_RESUMEN_EJECUCION);
        $dtr->setDataRows($ejecucion_resumen);
        $dtr->setTitleRow($titulos_resumen);

        $dtr->setType(1);
        $dtr->setPag(1, $pag_crit);
        $dtr->writeDataTable($niv);
        break;

    case 'list':
        $pla_id = $_REQUEST['id_element'];

        $region = $_REQUEST['r'];
        $departamento = $_REQUEST['dep'];
        $municipio = $_REQUEST['mun'];
        $estado = $_REQUEST['est'];

        $criterio = "(enc.pla_id = " . $pla_id . ")";

        $form = new CHtmlForm();
        $form->setId('frm_list_ejecucion');
        $form->setMethod('post');
        $form->setClassEtiquetas('td_label');
        $form->setMethod("POST");
        $form->setAction("?mod=$modulo&niv=$niv&ref=$ref");
        $form->setOptions('autoClean', false);

        $form->addInputText('hidden', 'txt_region', 'txt_region', '', '', $region, '', '', '');
        $form->addInputText('hidden', 'txt_departamento', 'txt_departamento', '', '', $departamento, '', '', '');
        $form->addInputText('hidden', 'txt_municipio', 'txt_municipio', '', '', $municipio, '', '', '');
        $form->addInputText('hidden', 'txt_estado', 'txt_estado', '', '', $estado, '', '', '');

        $form->addInputButton('submit', 'btn_atras', 'btn_atras', BTN_ATRAS, 'button', '');
        $form->writeForm();

        $ejecuciones_tb = $ejeData->getEncuestas($criterio);
        $dt = new CHtmlDataTable();
        $titulos_ejecucion = array(EJECUCION_CONSECUTIVO_ENCUESTA,
            EJECUCION_FECHA, EJECUCION_CC, EJECUCION_MCI,
            EJECUCION_RF, EJECUCION_VI, EJECUCION_RI,
            EJECUCION_MEI, EJECUCION_ESTADO);
        $dt->setDataRows($ejecuciones_tb);
        $dt->setTitleRow($titulos_ejecucion);
        $dt->setTitleTable(TABLA_EJECUCION);

        $otros = array('link' => "?mod=" . $modulo . "&niv=" . $niv . "&task=addEncuesta&idPlaneacion=" . $pla_id . "&ref=" . $ref, 'img' => 'hcalc.png', 'alt' => ALT_AGREGAR_ENCUESTA);
        $dt->addOtrosLink($otros);

        if ($niv == 1) {
            //OPCIONES DE GESTIÓN
            //Las opciones de gestión corresponden a Agregar, Editar, Borrar, Editar Encuesta y Borrar Encuesta.
            $dt->setDigitalizationLink("?mod=" . $modulo . "&niv=" . $nivel . "&task=add&pla=$pla_id");
            $dt->setEditLink("?mod=" . $modulo . "&niv=" . $nivel . "&task=edit&pla=$pla_id");
            $otros = array('link' => "?mod=" . $modulo . "&niv=" . $niv . "&task=deleteEncuesta&pla=" . $pla_id, 'img' => 'delete.png', 'alt' => ALT_AGREGAR_ENCUESTA);
            $dt->addOtrosLink($otros);
            $otros = array('link' => "?mod=" . $modulo . "&niv=" . $niv . "&task=syncEncuesta&pla=" . $pla_id, 'img' => 'cubrimiento.gif', 'alt' => ALT_SYNC_ENCUESTA);
            $dt->addOtrosLink($otros);
        }


        $dt->setType(1);
        $pag_crit = "";
        $dt->setPag(1, $pag_crit);
        $dt->writeDataTable(1);
        break;

    case 'syncPlaneacion':
        $form = new CHtmlForm();
        $form->setId('frm_sync_planeacion');
        $form->setMethod('post');
        $form->writeForm();
        $contEnc = 0;
        $errores = 0;
        $planeacionesSync = $ejeData->getSyncPlaneaciones($id_usuario);

        $client = new SoapClient(DIRECCION_WEB_SERVICE_SINCRONIZACION, array('encoding' => 'ISO-8859-1'));
        // Paramters in PHP are passed via associative arrays
        $password = $ejeData->consultarPassUsuario($id_usuario);
        $params = array("user" => $id_usuario, "pass" => $password, "data" => $planeacionesSync);

        $result = $client->sincronizarPlaneacion($params);
        $planeacionSync = $result->planeaciones;
        $ejecucionSync = $result->encuestas;
        $cont = 0;
        while ($cont < count($planeacionSync)) {
            $plaId = $planeacionSync[$cont]->pla_id;
            $beneficiario = $planeacionSync[$cont]->ben_id;
            $instrumento = $planeacionSync[$cont]->ins_id;
            $inicio = $planeacionSync[$cont]->pla_fecha_inicio;
            $fin = $planeacionSync[$cont]->pla_fecha_fin;
            $numero = $planeacionSync[$cont]->pla_numero_encuestas;
            $usuario = $planeacionSync[$cont]->usu_id;
            $municipio = $planeacionSync[$cont]->mun_id;

            $planData->insertPlaneacionSync($beneficiario, $instrumento, $inicio, $fin, $numero, $usuario);
        }
        $cont = 0;
        while ($cont < count($ejecucionSync)) {
            $enc_id = $ejecucionSync[$cont]->enc_id;
            $pla_id = $ejecucionSync[$cont]->pla_id;
            $enc_consecutivo = $ejecucionSync[$cont]->enc_consecutivo;
            $usu_id = $ejecucionSync[$cont]->usu_id;

            $ejeData->insertEjecucionSync($enc_id, $enc_consecutivo, $pla_id, $usu_id);
        }

        $mens = "Se sincronizaron " . (count($planeacionSync)) . " planeaciones con " . (count($planeacionSync)) . " encuestas";
        echo $html->generaAviso($mens, "?mod=" . $modulo . "&niv=" . $niv . "&task=pre-list");

        break;

    case 'syncEncuestaGeneral':
        $form = new CHtmlForm();
        $form->setId('frm_sync_enc_general');
        $form->setMethod('post');
        $form->writeForm();
        $contEnc = 0;
        $errores = 0;
        $encuestasSync = $ejeData->getSyncEncuestas(1);
        while ($contEnc < count($encuestasSync)) {
            $cont = 0;
            $data = null;
            $respuestas = $ejeData->getRespuestas($encuestasSync[$contEnc]['encuesta']);
            while ($cont < count($respuestas)) {
                $data[$cont] = new respuesta($respuestas[$cont]['pregunta'], $respuestas[$cont]['encuesta'], ($respuestas[$cont]['respuesta']));
                $cont++;
            }
            $client = new SoapClient(DIRECCION_WEB_SERVICE_SINCRONIZACION, array('encoding' => 'ISO-8859-1'));
            // Paramters in PHP are passed via associative arrays
            $password = $ejeData->consultarPassUsuario($id_usuario);
            $params = array("user" => $id_usuario, "pass" => $password, "data" => $data);

            $result = $client->sicronizar($params);
            if (!strpos($mens, 'error') || !strpos($mens, 'Error')) {
                $ejeData->setSyncEncuesta($encuestasSync[$contEnc]['encuesta'], 0);
            } else {
                $errores++;
            }
            $contEnc++;
        }
        $mens = "Se sincronizaron " . (count($encuestasSync) - $errores) . " encuestas";
        echo $html->generaAviso($mens, "?mod=" . $modulo . "&niv=" . $niv . "&task=pre-list");

        break;

    case 'syncEncuesta':
        $id_element = $_REQUEST['id_element'];
        $planeacion = $_REQUEST['pla'];
        $form = new CHtmlForm();
        $form->setId('frm_sync_encuesta');
        $form->setMethod('post');
        $form->writeForm();
        $data = null;
        $cont = 0;
        $respuestas = $ejeData->getRespuestas($id_element);
        while ($cont < count($respuestas)) {
            $data[$cont] = new respuesta($respuestas[$cont]['pregunta'], $respuestas[$cont]['encuesta'], ($respuestas[$cont]['respuesta']));
            $cont++;
        }
        $client = new SoapClient(DIRECCION_WEB_SERVICE_SINCRONIZACION, array('encoding' => 'ISO-8859-1'));
        // Paramters in PHP are passed via associative arrays
        $password = $ejeData->consultarPassUsuario($id_usuario);
        $params = array("user" => $id_usuario, "pass" => $password, "data" => $data);

        $result = $client->sicronizar($params);
        $mens = "" . $result->return;
        if (!strpos($mens, 'error') || !strpos($mens, 'Error')) {
            $ejeData->setSyncEncuesta($id_element, 0);
        }
        echo $html->generaAviso($mens, "?mod=" . $modulo . "&niv=" . $niv . "&task=list&pla=$planeacion&id_element=" . $planeacion);
        break;


    case 'deleteEncuesta':
        $id_element = $_REQUEST['id_element'];
        $planeacion = $_REQUEST['pla'];
        $form = new CHtmlForm();
        $form->setId('frm_delet_ejecucion');
        $form->setMethod('post');

        $form->writeForm();
        echo $html->generaAdvertencia(EJECUCION_MSG_BORRADO_DATOS, '?mod=' . $modulo . '&niv='
                . $niv . '&task=confirmDeleteEncuesta&id_element=' . $id_element . "&pla=$planeacion", 'onClick=location.href="?mod=' . $modulo . '&niv=' . $niv . "&task=list&id_element=$planeacion\"");
        break;

    case 'confirmDeleteEncuesta':
        $id_delete = $_REQUEST['id_element'];
        $planeacion = $_REQUEST['pla'];
        $ejeData->borrarRespuestas($id_delete);
        $mens = $ejeData->setEstadoEncuesta($id_delete, '2');
        echo $html->generaAviso($mens, "?mod=" . $modulo . "&niv=" . $niv . "&task=list&pla=$planeacion&id_element=" . $id_delete);
        break;

    case 'edit':
        $id_element = $_REQUEST['id_element'];
        $planeacion = $_REQUEST['pla'];
        $encuesta = new CEncuesta($id_element, $ejeData);
        $encuesta->loadEncuesta();
        $archivo_anterior = $encuesta->getDocumento_soporte();
        $fecha = $encuesta->getFecha();
        $cc = $encuesta->getCc();
        $mci = $encuesta->getMci();
        $rf = $encuesta->getRf();
        $vi = $encuesta->getVi();
        $ri = $encuesta->getRi();
        $mei = $encuesta->getMei();
        $usuario = $encuesta->getUsuario();
        $estado = $encuesta->getEstado();
        if ($archivo_anterior == '') {
            echo $html->generaAviso(ERORR_EJECUCION_ENCUESTA_EDITADA, "?mod=" . $modulo . "&niv=" . $niv . "&task=list&id_element=" . $planeacion);
        } else {
            $form = new CHtmlForm();
            $form->setTitle(EJECUCION_EDITAR);
            $form->setId('frm_edit_ejecucion');
            $form->setMethod('post');
            $form->setClassEtiquetas('td_label');
            $form->setAction("?mod=$modulo&niv=$niv&task=saveEdit&eje=$id_element&pla=$planeacion");
            //File
            $form->addEtiqueta(EJECUCION_DOCUMENTO_SOPORTE);
            $form->addInputFile('file', 'file_documento_soporte', 'file_documento_soporte', 25, 'file', 'onChange="ocultarDiv(\'error_documento_soporte\');"');
            $form->addError('error_documento_soporte', ERROR_EJECUCION_DOCUMENTO_SOPORTE);

            $form->addEtiqueta(EJECUCION_FECHA);
            $form->addInputDate('date', 'txt_fecha', 'txt_fecha', $fecha, '%Y-%m-%d', '16', '16', '', 'onChange="ocultarDiv(\'error_fecha\');"');
            $form->addError('error_fecha', ERROR_PLANEACION_FECHA);
            //ecc
            $form->addEtiqueta(EJECUCION_CC);
            $opciones = null;
            $resultado = $ejeData->getCuestionarioCompletoOptions();
            if (isset($resultado)) {
                foreach ($resultado as $t) {
                    $opciones[count($opciones)] = array('value' => $t['id'], 'texto' => $t['nombre']);
                }
            }

            $form->addSelect('select', 'txt_cc', 'txt_cc', $opciones, EJECUCION_CC, $cc, '', 'onChange="motivo_onchange(\'error_cc\',\'txt_cc\',\'label_3\',\'txt_mci\');"');
            $form->addError('error_cc', ERROR_EJECUCION_CC);

            $form->addEtiqueta(EJECUCION_MCI);
            $form->addInputText('text', 'txt_mci', 'txt_mci', 40, 500, $mci, '', 'onChange="ocultarDiv(\'error_mci\');"');
            $form->addError('error_mci', ERROR_EJECUCION_MCI);
            //ERF
            $form->addEtiqueta(EJECUCION_RF);
            $opciones = null;
            $resultado = $ejeData->getResultadoFinalOptions();
            if (isset($resultado)) {
                foreach ($resultado as $t) {
                    $opciones[count($opciones)] = array('value' => $t['id'], 'texto' => $t['nombre']);
                }
            }
            $form->addSelect('select', 'txt_rf', 'txt_rf', $opciones, EJECUCION_RF, $rf, '', 'onChange="ocultarDiv(\'error_rf\');"');
            $form->addError('error_rf', ERROR_EJECUCION_RF);
            //EVI
            $form->addEtiqueta(EJECUCION_VI);
            $opciones = null;
            $resultado = $ejeData->getValidacionInspeccionOptions();
            if (isset($resultado)) {
                foreach ($resultado as $t) {
                    $opciones[count($opciones)] = array('value' => $t['id'], 'texto' => $t['nombre']);
                }
            }
            $form->addSelect('select', 'txt_vi', 'txt_vi', $opciones, EJECUCION_VI, $vi, '', 'onChange="ocultarDiv(\'error_vi\');"');
            $form->addError('error_vi', ERROR_EJECUCION_VI);
            //ERI
            $form->addEtiqueta(EJECUCION_RI);
            $opciones = null;
            $resultado = $ejeData->getResultadoInspeccionOptions();
            if (isset($resultado)) {
                foreach ($resultado as $t) {
                    $opciones[count($opciones)] = array('value' => $t['id'], 'texto' => $t['nombre']);
                }
            }
            $form->addSelect('select', 'txt_ri', 'txt_ri', $opciones, EJECUCION_RI, $ri, '', 'onChange="motivo_onchange(\'error_ri\',\'txt_ri\',\'label_7\',\'txt_mei\');"');
            $form->addError('error_ri', ERROR_EJECUCION_RI);

            $form->addEtiqueta(EJECUCION_MEI);
            $form->addInputText('text', 'txt_mei', 'txt_mei', 40, 500, $mei, '', 'onChange="ocultarDiv(\'error_mei\');"');
            $form->addError('error_mei', ERROR_EJECUCION_MEI);


            $form->addEtiqueta(PLANEACION_USUARIO);
            $opciones = null;
            $usuarios = $planData->getUsuarios('usu_nombre');
            if (isset($usuarios)) {
                foreach ($usuarios as $t) {
                    $opciones[count($opciones)] = array('value' => $t['id'], 'texto' => $t['nombre']);
                }
            }
            $form->addSelect('select', 'txt_usuario', 'txt_usuario', $opciones, PLANEACION_USUARIO, $usuario, '', 'onChange="ocultarDiv(\'error_usuario\');"');
            $form->addError('error_usuario', ERROR_PLANEACION_USUARIO);

            $form->addInputText('hidden', 'archivo_anterior', 'archivo_anterior', '', '', $archivo_anterior, '', '');
            //Botones Formulario
            $form->addInputButton('submit', 'btn_add', 'btn_add', BTN_ACEPTAR, 'button', '');
            $form->addInputButton('button', 'btn_consultar', 'btn_consultar', BTN_CANCELAR, 'button', 'onClick=location.href="?mod=' . $modulo . '&niv=' . $niv . "&task=list&id_element=$planeacion\"");
            $form->writeForm();
            //dinamismo en el formulario
            ?>
            <script>
                motivo_carga();
            </script>
            <?php
        }
        break;

    case 'add':
        $id_element = $_REQUEST['id_element'];
        $planeacion = $_REQUEST['pla'];
        $encuesta = new CEncuesta($id_element, $ejeData);
        $encuesta->loadEncuesta();
        if ($encuesta->getDocumento_soporte() != '') {
            echo $html->generaAviso(ERORR_EJECUCION_ENCUESTA_AGREGADA, "?mod=" . $modulo . "&niv=" . $niv . "&task=list&id_element=" . $planeacion);
        } else {
            $form = new CHtmlForm();
            $form->setId('frm_add_ejecucion');
            $form->setMethod('post');
            $form->setClassEtiquetas('td_label');
            $form->setAction("?mod=$modulo&niv=$niv&task=saveEdit&eje=$id_element&pla=$planeacion");
            //File
            $form->addEtiqueta(EJECUCION_DOCUMENTO_SOPORTE);
            $form->addInputFile('file', 'file_documento_soporte', 'file_documento_soporte', 25, 'file', 'onChange="ocultarDiv(\'error_documento_soporte\');"');
            $form->addError('error_documento_soporte', ERROR_EJECUCION_DOCUMENTO_SOPORTE);
            //Botones Formulario
            $form->addInputButton('submit', 'btn_add', 'btn_add', BTN_AGREGAR, '', '');
            $form->addInputButton('button', 'btn_consultar', 'btn_consultar', BTN_CANCELAR, 'button', 'onClick=location.href="?mod=' . $modulo . '&niv=' . $niv . "&task=list&id_element=$planeacion\"");
            $form->writeForm();
        }
        break;

    case 'saveEdit':
        $id_element = $_REQUEST['eje'];
        $pla = $_REQUEST['pla'];
        $archivo = $_FILES['file_documento_soporte'];
        $fecha = $_REQUEST['txt_fecha'];
        $cc = $_REQUEST['txt_cc'];
        $mci = $_REQUEST['txt_mci'];
        $rf = $_REQUEST['txt_rf'];
        $vi = $_REQUEST['txt_vi'];
        $ri = $_REQUEST['txt_ri'];
        $mei = $_REQUEST['txt_mei'];
        $usuario = $_REQUEST['txt_usuario'];

        $encuesta = new CEncuesta($id_element, $ejeData);
        $encuesta->loadEncuesta();
        //$encuesta->setConsecutivo($cosecutivo);
        $encuesta->setFecha($fecha);
        $encuesta->setCc($cc);
        $encuesta->setMci($mci);
        $encuesta->setRf($rf);
        $encuesta->setVi($vi);
        $encuesta->setRi($ri);
        $encuesta->setMei($mei);
        $encuesta->setUsuario($usuario);
        $encuesta->saveRDM();
        $m = $encuesta->saveEditEncuesta($archivo, '', $encuesta->getId());

        echo $html->generaAviso($m, "?mod=" . $modulo . "&niv=" . $niv . "&task=list&id_element=" . $pla);
        break;

    case 'addEncuesta':
        $idPlaneacion = $_REQUEST['idPlaneacion'];
        $idEncuesta = $_REQUEST['id_element'];
        $idPreguntas = "";
        $pagina = $_REQUEST['pagina'];
        $idInstrumento = $ejeData->getInstrumento($idEncuesta);
        $instrumento = $daoInstrumentos->getInstrumentoById($idInstrumento);
        $secciones = $daoInstrumentos->getSecciones($instrumento);
        $numeroSecciones = count($secciones);
        $seccionActual = 0;
        if (isset($_REQUEST['seccionActual'])) {
            $seccionActual = $_REQUEST['seccionActual'];
        }
        if (isset($_REQUEST['idPreguntasR'])) {
            $idPreguntasR = split(",", $_REQUEST['idPreguntasR']);
            $numeroPreguntas = $_REQUEST['numeroPreguntas'];
            $respuestas = null;
            for ($i = 0; $i < $numeroPreguntas; $i++) {
                $respuestaMarcada = "";
                if (isset($_FILES['pregunta' . $i])) {
                    $archivo = $_FILES['pregunta' . $i];
                    $respuestaMarcada = $archivo["name"];
                    if ($respuestaMarcada != "" && $respuestaMarcada != null) {
                        $respuesta = new CRespuesta($idPreguntasR[$i], $idEncuesta, $respuestaMarcada);
                        $daoInstrumentos->insertRespuesta($respuesta);
                        $ruta = $idPlaneacion . "/" . $idEncuesta . "/";
                        $daoInstrumentos->guardarArchivo($archivo, $ruta);
                    }
                } else if (isset($_REQUEST['pregunta' . $i])) {
                    $respuestaMarcada = $_REQUEST['pregunta' . $i];
                    $respuesta = new CRespuesta($idPreguntasR[$i], $idEncuesta, $respuestaMarcada);
                    $daoInstrumentos->insertRespuesta($respuesta);
                } else {
                    $pregunta = $daoInstrumentos->getPreguntaById($idPreguntasR[$i]);
                    $maximoRespuestas = count(split(",", $pregunta->getOpcionRespuesta()));
                    for ($j = 0; $j < $maximoRespuestas; $j++) {
                        if (isset($_REQUEST['pregunta' . $i . '_' . $j])) {
                            $respuestaMarcada .= $_REQUEST['pregunta' . $i . '_' . $j] . ",";
                        }
                    }
                    if ($respuestaMarcada != "") {
                        $respuestaMarcada = substr($respuestaMarcada, 0, -1);
                        $respuesta = new CRespuesta($idPreguntasR[$i], $idEncuesta, $respuestaMarcada);
                        $daoInstrumentos->insertRespuesta($respuesta);
                    }
                }
            }
        }
        if ($seccionActual == count($secciones)) {
            $ejeData->completarEncuesta($idEncuesta);
            echo $html->generaAviso(EJECUCION_MNS_AGREGACION, "?mod=" . $modulo . "&niv=" . $niv . "&task=list&id_element=" . $idPlaneacion . "&ref=$ref");
        } else {
            $preguntas = $daoInstrumentos->getPreguntas($secciones[$seccionActual]);
            $numeroPreguntas = $daoInstrumentos->getNumeroPreguntasByInstrumento($idInstrumento);
            $numeroRespuestas = $daoInstrumentos->getNumeroRespuestasByEncuesta($idEncuesta);
            $porcentajeEjecucion = ($numeroRespuestas / $numeroPreguntas) * 100;
            $style = "progress-bar-success";
            if ($porcentajeEjecucion <= 25) {
                $style = "progress-bar-danger";
            } else if (25 < $porcentajeEjecucion && $porcentajeEjecucion <= 50) {
                $style = "progress-bar-warning";
            } else if (50 < $porcentajeEjecucion && $porcentajeEjecucion <= 75) {
                $style = "progress-bar-info";
            }
            $numeroPaginas = ceil($numeroSecciones / PAGINAS);
            ?>
            <h1><?= $instrumento->getCodigo() . " " . $instrumento->getNombreInstrumento(); ?></h1>
            <div class="progress progress-striped">
                <div class="progress-bar <?= $style ?>" role="progressbar" aria-valuenow="<?= $porcentajeEjecucion ?>"
                     aria-valuemin="0" aria-valuemax="100" style="width: <?= $porcentajeEjecucion ?>%;">
                    <span class="sr-only"><?= $porcentajeEjecucion ?> completado</span>
                </div>
            </div>
            <nav>
                <ul class="pagination pagination-lg">
                    <li>
                        <a href="?mod=genEjecucion&task=addEncuesta&seccionActual=0&id_element=<?= $idEncuesta ?>&niv=<?= $niv ?>&pagina=0&idPlaneacion=<?= $idPlaneacion ?>&ref=<?= $ref ?>" aria-label="Previous">
                            <span aria-hidden="true">&laquo;</span>
                        </a>
                    </li>
                    <?php for ($j = 0; $j < $numeroPaginas; $j++) { ?>
                        <?php if ($j == $pagina) { ?>
                            <li class="active">
                            <?php } else { ?>
                            <li>
                            <?php } ?>
                            <a href="?mod=genEjecucion&task=addEncuesta&seccionActual=<?= ($j * PAGINAS) ?>&id_element=<?= $idEncuesta ?>&niv=<?= $niv ?>&pagina=<?= ($j) ?>&idPlaneacion=<?= $idPlaneacion ?>&ref=<?= $ref ?>"><?= ($j + 1) ?></a>
                        </li>
                    <?php } ?>
                    <li>
                        <a href="?mod=genEjecucion&task=addEncuesta&seccionActual=<?= ($numeroSecciones - 1) ?>&id_element=<?= $idEncuesta ?>&niv=<?= $niv ?>&pagina=<?= ($numeroPaginas - 1) ?>&idPlaneacion=<?= $idPlaneacion ?>&ref=<?= $ref ?>" aria-label="Next">
                            <span aria-hidden="true">&raquo;</span>
                        </a>
                    </li>
                </ul>
            </nav>
            <ul class="nav nav-tabs nav-justified">
                <?php
                $maximo = PAGINAS;
                if ($maximo * $numeroPaginas > $numeroSecciones && $pagina == ($numeroPaginas - 1)) {
                    $maximo = PAGINAS - ($numeroPaginas * PAGINAS - $numeroSecciones);
                }
                for ($i = 0; $i < $maximo; $i++) {
                    $numeroSeccion = (PAGINAS * $pagina) + $i;
                    $seccion = $secciones[$numeroSeccion];
                    ?>
                    <li <?php
                    if ($seccionActual == $numeroSeccion) {
                        echo "class=active style='background: rgba(54, 25, 25, .5)'";
                    }
                    ?>>
                        <a href="?mod=<?= $modulo ?>&niv=<?= $niv ?>&task=addEncuesta&seccionActual=<?= $numeroSeccion ?>&id_element=<?= $idEncuesta ?>&idPlaneacion=<?= $idPlaneacion ?>&pagina=<?= ($pagina) ?>&ref=<?= $ref ?>">
                            <?php
                            if ($seccionActual == $numeroSeccion) {
                                echo "<strong>";
                            }
                            echo $seccion->getNumero() . ". " . $seccion->getNombreSeccion();
                            if ($seccionActual == $numeroSeccion) {
                                echo "</strong>";
                            }
                            ?>
                        </a>
                    </li>      
                <?php } ?>
            </ul>
            <?php
            $nuevaPagina = $pagina;
            if (($seccionActual + 1) % PAGINAS == 0) {
                $nuevaPagina = $pagina + 1;
            }
            ?>
            <form id="frm_add_encuesta" enctype="multipart/form-data" method="post" action="?mod=<?= $modulo ?>&niv=<?= $niv ?>&task=addEncuesta&id_element=<?= $idEncuesta ?>&seccionActual=<?= ($seccionActual + 1) ?>&idPlaneacion=<?= $idPlaneacion ?>&pagina=<?= $nuevaPagina ?>&ref=<?= $ref ?>">
                <?php
                $j = 0;
                foreach ($preguntas as $pregunta) {
                    $respuesta = $daoInstrumentos->getRespuestaByIdPreguntaAndIdEncuesta($idEncuesta, $pregunta->getIdPregunta());
                    $idPreguntas .= $pregunta->getIdPregunta() . ",";
                    ?>
                    <fieldset>
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h3 class="panel-title">
                                    <strong><?= $pregunta->getNumero() . "."; ?></strong>
                                    <?= $pregunta->getEnunciado(); ?>
                                </h3>
                            </div>
                            <div class="panel-body">
                                <div class="input-group">
                                    <?php if ($pregunta->getTipoPregunta() == 0) { ?>
                                        <?php $input = $daoInstrumentos->construirInput($pregunta, $j, $respuesta); ?>  
                                        <?php
                                        $tipo = split(',', $pregunta->getDescripcion())[0];
                                        if ($tipo == '6' && $respuesta->getRespuesta() != NULL) {
                                            $input = str_replace("required", "", $input);
                                            $resultado = "<span class='input-group-addon'><a href='././soportes/soporteEncuestas/" . $idPlaneacion . "/" . $id_add . "/" . $respuesta->getRespuesta() . "'>" . $respuesta->getRespuesta() . "</a></span>";
                                            echo $input;
                                            echo $resultado;
                                        } else {
                                            echo $input;
                                        }
                                        ?>
                                        <?php
                                    } else {
                                        $opcionesRespuesta = split(",", $pregunta->getOpcionRespuesta());
                                        $entro = false;
                                        ?>
                                        <div class="row">
                                            <?php if ($pregunta->getTipoPregunta() == 4) { ?>
                                                <div class="col-lg-12">
                                                    <div class="input-group">
                                                        <span class="input-group-addon">Respuesta Pregunta <?= $pregunta->getNumero() ?>.</span>
                                                        <select id="pregunta<?= $j ?>" name="pregunta<?= $j ?>" class="form-control" <?php
                                                        if ($pregunta->isRequerido()) {
                                                            echo 'required';
                                                        }
                                                        ?>>
                                                            <option value="">Seleccione uno</option>
                                                            <?php for ($i = 0; $i < count($opcionesRespuesta); $i++) { ?>
                                                                <option value="<?= $opcionesRespuesta[$i] ?>" <?php
                                                                if ($respuesta->getRespuesta() == $opcionesRespuesta[$i]) {
                                                                    echo "selected";
                                                                }
                                                                ?>><?= $opcionesRespuesta[$i] ?></option>
                                                                    <?php } ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            <?php } else if ($pregunta->getTipoPregunta() != 7) { ?>
                                                <?php for ($i = 0; $i < count($opcionesRespuesta); $i++) { ?>
                                                    <div class="col-lg-6">
                                                        <div class="input-group">
                                                            <span class="input-group-addon">
                                                                <?php if ($pregunta->getTipoPregunta() == 1) { ?>
                                                                    <input type="radio" id="pregunta<?= $j ?>" name="pregunta<?= $j ?>" value="<?= $opcionesRespuesta[$i] ?>" <?php
                                                                    if ($pregunta->isRequerido() && !$entro) {
                                                                        echo "required";
                                                                        $entro = true;
                                                                    }
                                                                    if ($opcionesRespuesta[$i] == $respuesta->getRespuesta()) {
                                                                        echo " checked";
                                                                    }
                                                                    ?>>
                                                                       <?php } else { ?>
                                                                    <input id="pregunta<?= $j . "_" . $i ?>" name="pregunta<?= $j . "_" . $i ?>" type="checkbox" value="<?= $opcionesRespuesta[$i] ?>" <?php
                                                                    $respuestas = split(",", $respuesta->getRespuesta());
                                                                    for ($k = 0; $k < count($respuestas); $k++) {
                                                                        if ($respuestas[$k] == $opcionesRespuesta[$i]) {
                                                                            echo " checked";
                                                                        }
                                                                    }
                                                                    ?>>
                                                                       <?php } ?>
                                                            </span>
                                                            <input type="text" class="form-control" value="<?= $opcionesRespuesta[$i] ?>" readonly>
                                                        </div>
                                                    </div>
                                                <?php } ?>
                                            <?php } else { ?>
                                                <script>
                                                    evaluadorExpresiones = new Evaluar();
                                                    preguntas = new Array();

                                                    window.onload = function () {
                                                        determinarCampos();
                                                        agregarEvento();
                                                    };

                                                    function determinarCampos() {
                                                        var expresion = document.getElementById("expresion").value;
                                                        for (var i = 0; i < <?= count($preguntas) ?>; i++) {
                                                            var expresionR = replaceAll('p' + (i + 1), String.fromCharCode(i + 97), expresion);
                                                            if (expresionR !== expresion) {
                                                                preguntas.push(i);
                                                            }
                                                            expresion = expresionR;
                                                        }
                                                    }

                                                    function agregarEvento() {
                                                        for (var i = 0; i < preguntas.length; i++) {
                                                            var id = "pregunta" + preguntas[i];
                                                            document.getElementById(id).onkeyup = function () {
                                                                evaluar();
                                                            };
                                                        }
                                                    }

                                                    function replaceAll(find, replace, str) {
                                                        return str.replace(new RegExp(find, 'g'), replace);
                                                    }

                                                    function evaluar() {
                                                        var expresion = document.getElementById("expresion").value;
                                                        for (var i = 0; i < preguntas.length; i++) {
                                                            expresion = replaceAll('p' + (preguntas[i] + 1), String.fromCharCode(preguntas[i] + 97), expresion);
                                                        }
                                                        var transformado = evaluadorExpresiones.TransformaExpresion(expresion);
                                                        var chequeoSintaxis = evaluadorExpresiones.EvaluaSintaxis(transformado);
                                                        if (chequeoSintaxis === 0) {
                                                            var exprNegativos = evaluadorExpresiones.ArreglaNegativos(transformado);
                                                            evaluadorExpresiones.Analizar(exprNegativos);
                                                            for (var i = 0; i < preguntas.length; i++) {
                                                                var value = document.getElementById('pregunta' + preguntas[i]).value;
                                                                evaluadorExpresiones.ValorVariable(String.fromCharCode(preguntas[i] + 97), Number(value));
                                                            }
                                                            var resultado = evaluadorExpresiones.Calcular();
                                                            document.getElementById("resultado").innerHTML = "Resultado = " + resultado;
                                                            document.getElementById("pregunta<?= $j ?>").value = resultado;
                                                        } else {
                                                            var mensaje = evaluadorExpresiones.MensajeSintaxis(chequeoSintaxis);
                                                            document.getElementById("resultado").innerHTML = "Resultado = " + mensaje;
                                                        }
                                                    }
                                                </script>
                                                <div class="col-lg-12">
                                                    <div class="input-group">
                                                        <span class="input-group-addon">Respuesta Pregunta <?= $pregunta->getNumero() ?>.</span>
                                                        <input id="expresion" type="text" class="form-control" value="<?= $opcionesRespuesta[0] ?>" readonly>
                                                        <input type="hidden" name="pregunta<?= $j ?>" id="pregunta<?= $j ?>" value="<?= $respuesta->getRespuesta() ?>">
                                                        <span class='input-group-addon'>
                                                            <div id="resultado">
                                                                Resultado = <?= $respuesta->getRespuesta() ?>
                                                            </div>
                                                        </span>
                                                    </div>
                                                </div>
                                            <?php } ?>
                                        </div>
                                    <?php } ?>
                                    <?php $j++; ?>
                                </div>
                            </div>
                        </div>
                    </fieldset>
                    <?php
                }
                $idPreguntas = substr($idPreguntas, 0, -1);
                ?>
                <input type="hidden" value="<?= $idPreguntas ?>" name="idPreguntasR" id="idPreguntasR"/>
                <input type="hidden" value="<?= $idEncuesta ?>" name="id_element" id="id_element"/>
                <input type="hidden" value="<?= count($preguntas) ?>" name="numeroPreguntas" id="numeroPreguntas"/>
                <input type="button" value="<?= BTN_ATRAS; ?>" onclick="location.href = '?mod=<?= $modulo ?>&niv=<?= $niv ?>&id_element=<?= $idPlaneacion ?>&task=list&ref=<?= $ref ?>'">
                <input type="submit" value=<?= BTN_CONTINUAR; ?>>
            </form>

            <?php
        }
        break;
}
?>