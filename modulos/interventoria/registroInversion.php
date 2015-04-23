<?php

defined('_VALID_PRY') or die('Restricted access');
$niv = $_REQUEST['niv'];
$operador = OPERADOR_DEFECTO;
$rinData = new CRegistroInversionData($db);

$task = $_REQUEST['task'];

if (empty($task)) {
    $task = 'list';
}
switch ($task) {
    /**
     * La variable list, permite hacer la carga la página con la lista de 
     * objetos registroInversion según los parámetros de entrada.
     */
    case 'list':
        $fecha_inicio = $_REQUEST['txt_fecha_inicio'];
        $fecha_fin = $_REQUEST['txt_fecha_fin'];
        $actividad = $_REQUEST['txt_actividad'];
        $proveedor = $_REQUEST['txt_proveedor'];
        //-------------------------------criterios---------------------------
        $criterio = "";
        if (isset($fecha_inicio) && $fecha_inicio != '' && $fecha_inicio != '0000-00-00') {
            if (!isset($fecha_fin) || $fecha_fin == '' || $fecha_fin == '0000-00-00') {
                if ($criterio == "") {
                    $criterio = " (rin.rin_fecha >= '" . $fecha_inicio . "')";
                } else {
                    $criterio .= " and rin.rin_fecha >= '" . $fecha_inicio . "'";
                }
            } else {
                if ($criterio == "") {
                    $criterio = "( rin.rin_fecha between '" . $fecha_inicio .
                            "' and '" . $fecha_fin . "')";
                    ;
                } else {
                    $criterio .= " and rin.rin_fecha between '" . $fecha_inicio .
                            "' and '" . $fecha_fin . "')";
                    ;
                }
            }
        }
        if (isset($fecha_fin) && $fecha_fin != '' && $fecha_fin != '0000-00-00') {
            if (!isset($fecha_inicio) || $fecha_inicio == '' || $fecha_inicio == '0000-00-00') {
                if ($criterio == "") {
                    $criterio = "( rin.rin_fecha <= '" . $fecha_fin . "')";
                } else {
                    $criterio .= " and rin.rin_fecha <= '" . $fecha_fin . "')";
                }
            }
        }
        if (isset($actividad) && $actividad != -1 && $actividad != '') {
            if ($criterio == "") {
                $criterio = "  rin.act_id = " . $actividad;
            } else {
                $criterio .= " and rin.act_id = " . $actividad;
            }
        }
        if (isset($proveedor) && $proveedor != -1 && $proveedor != '') {
            if ($criterio == "") {
                $criterio = " rin.id_prove = " . $proveedor;
            } else {
                $criterio .= " and rin.id_prove = " . $proveedor;
            }
        }
        if ($criterio == "") {
            $criterio = "1";
        }
        /*
         * Inicio formulario
         */
        $form = new CHtmlForm();
        $form->setTitle(TABLA_REGISTRO_INVERSION);
        $form->setId('frm_list_registro_inversion');
        $form->setMethod('post');
        $form->setClassEtiquetas('td_label');

        $form->addEtiqueta(REGISTRO_INVERSION_FECHA_INICIO);
        $form->addInputDate('date', 'txt_fecha_inicio', 'txt_fecha_inicio', $fecha_inicio, '%Y-%m-%d', '16', '16', '', 'pattern="' . PATTERN_FECHA . '" title="' . $html->traducirTildes(TITLE_FECHA) . '" ');

        $form->addEtiqueta(REGISTRO_INVERSION_FECHA_FIN);
        $form->addInputDate('date', 'txt_fecha_fin', 'txt_fecha_fin', $fecha_fin, '%Y-%m-%d', '16', '16', '', 'pattern="' . PATTERN_FECHA . '" title="' . $html->traducirTildes(TITLE_FECHA) . '" ');

        //Seccion actividades
        $form->addEtiqueta(REGISTRO_INVERSION_ACTIVIDAD);
        $opciones = null;
        $activities = $rinData->getActividades();
        if (isset($activities)) {
            foreach ($activities as $t) {
                $opciones[count($opciones)] = array('value' => $t['id'], 'texto' => $t['nombre']);
            }
        }
        $form->addSelect('select', 'txt_actividad', 'txt_actividad', $opciones, REGISTRO_INVERSION_ACTIVIDAD, $actividad, '', '');

        //Seccion pROVEDORES
        $form->addEtiqueta(REGISTRO_INVERSION_PROVEEDOR);
        $opciones = null;
        $proveedores = $rinData->getProveedores();
        if (isset($proveedores)) {
            foreach ($proveedores as $t) {
                $opciones[count($opciones)] = array('value' => $t['id'], 'texto' => $t['nombre']);
            }
        }
        $form->addSelect('select', 'txt_proveedor', 'txt_proveedor', $opciones, REGISTRO_INVERSION_PROVEEDOR, $proveedor, '', '');


        $form->addInputButton('button', 'btn_consultar', 'btn_consultar', BTN_ACEPTAR, 'button', 'onClick=consultar_registro_inversion();');
        $form->addInputButton('button', 'btn_exportar', 'btn_exportar', REGISTRO_INVERSION_EXPORTAR, 'button', 'onClick=exportar_excel_registro_inversion();');

        $form->writeForm();
        //Carga filtro de actividades
        $dataRows = $rinData->getRegistroInversion($criterio);
        //Inicio Tabla
        $dt = new CHtmlDataTable();
        $titulos = array(REGISTRO_INVERSION_ACTIVIDAD, REGISTRO_INVERSION_FECHA, REGISTRO_INVERSION_PROVEEDOR,
            REGISTRO_INVERSION_NUMERO_DOCUMENTO, REGISTRO_INVERSION_VALOR, REGISTRO_INVERSION_OBSERVACIONES,
            REGISTRO_INVERSION_DOCUMENTO_SOPORTE);
        $dt->setDataRows($dataRows);
        $dt->setTitleRow($titulos);
        $dt->setTitleTable(TABLA_REGISTRO_INVERSION);

        //OPCIONES DE GESTIÓN
        $dt->setEditLink("?mod=" . $modulo . "&niv=" . $niv . "&task=edit");
        $dt->setDeleteLink("?mod=" . $modulo . "&niv=" . $niv . "&task=delete");
        $dt->setAddLink("?mod=" . $modulo . "&niv=" . $niv . "&task=add");
        $dt->setSumColumns(array(5));
		$dt->setFormatRow(array(null,null,null,null,array(2,',','.'),null,null));
        $dt->setType(1);
        $pag_crit = "";
        $dt->setPag(1, $pag_crit);
        $dt->writeDataTable($niv);

        break;

    /*
     * Variable agregar, agregar un inventario de actividades
     */
    case 'add':

        $actividad = $_REQUEST['txt_actividad'];
        $fecha = $_REQUEST['txt_fecha'];
        $proveedor = $_REQUEST['txt_proveedor'];
        $numero_documento = $_REQUEST['txt_numero_documento'];
        $valor = $_REQUEST['txt_valor'];
        $observaciones = $_REQUEST['txt_observaciones'];
        $documento_soporte = $_FILES['documento_soporte'];
        /*
         * Inicio formulario
         */
        $form = new CHtmlForm();
        $ventana = new CHtmlVentanas();
        $form->setTitle(TITULO_AGREGAR_REGISTRO_INVERSION);
        $form->setId('frm_add_registro_inversion');
        $form->setMethod('post');
        $form->setClassEtiquetas('td_label');
        $form->setAction('?mod=' . $modulo . '&niv=' . $niv . '&task=saveAdd');

        //Seccion actividades
        $form->addEtiqueta(REGISTRO_INVERSION_ACTIVIDAD);
        $opciones = null;
        $activities = $rinData->getActividades();
        if (isset($activities)) {
            foreach ($activities as $t) {
                $opciones[count($opciones)] = array('value' => $t['id'], 'texto' => $t['nombre']);
            }
        }
        $form->addSelect('select', 'txt_actividad', 'txt_actividad', $opciones, REGISTRO_INVERSION_ACTIVIDAD, $actividad, '', '');
        
        $form->addEtiqueta(REGISTRO_INVERSION_FECHA);
        $form->addInputDate('date', 'txt_fecha', 'txt_fecha', $fecha, '%Y-%m-%d', '16', '16', '', 'pattern="' . PATTERN_FECHA . '" title="' . $html->traducirTildes(TITLE_FECHA) . '" required');

        $form->addEtiqueta(REGISTRO_INVERSION_PROVEEDOR);
        $opciones = null;
        $proveedores = $rinData->getProveedores();
        if (isset($proveedores)) {
            foreach ($proveedores as $t) {
                $opciones[count($opciones)] = array('value' => $t['id'], 'texto' => $t['nombre']);
            }
        }
        $form->addSelect('select', 'txt_proveedor', 'txt_proveedor', $opciones, REGISTRO_INVERSION_PROVEEDOR, $proveedor, '', '');

        $form->addEtiqueta(REGISTRO_INVERSION_NUMERO_DOCUMENTO);
        $form->addInputText('text', 'txt_numero_documento', 'txt_numero_documento', 12, 12, $numero_documento, '', 'pattern="' . PATTERN_ALFANUMERICO . '" title="' . $html->traducirTildes(TITLE_ALFANUMERICO) . '" required');

        $form->addEtiqueta(REGISTRO_INVERSION_VALOR);
        $form->addInputText('text', 'txt_valor', 'txt_valor', 15, 19, $valor, '', 'onkeyup="formatearNumero(this);" pattern="' . PATTERN_NUMEROS_FINANCIEROS . '" title="' . $html->traducirTildes(TITLE_NUMEROS_FINANCIEROS) . '" required');

        $form->addEtiqueta(REGISTRO_INVERSION_OBSERVACIONES);
        $form->addTextArea('textarea', 'txt_observaciones', 'txt_observaciones', 30, 5, $observaciones, '', ' pattern="{0,500} "title="' . $html->traducirTildes(TITLE_ALFANUMERICO) . '" ');

        $form->addEtiqueta(REGISTRO_INVERSION_DOCUMENTO_SOPORTE);
        $form->addInputFile('file', 'documento_soporte', 'documento_soporte', '25', 'file', 'required');

        $form->addInputButton('submit', 'btn_consultar', 'btn_consultar', BTN_ACEPTAR, 'button', '');
        $form->addInputButton('button', 'btn_exportar', 'btn_exportar', BTN_CANCELAR, 'button', 'onClick=location.href="?mod=' . $modulo . '&niv=' . $niv . '"');

        $form->writeForm();
        break;

    case 'saveAdd':

        $actividad = $_REQUEST['txt_actividad'];
        $fecha = $_REQUEST['txt_fecha'];
        $proveedor = $_REQUEST['txt_proveedor'];
        $numero_documento = $_REQUEST['txt_numero_documento'];
        $valor = $_REQUEST['txt_valor'];
        $observaciones = $_REQUEST['txt_observaciones'];
        $documento_soporte = $_FILES['documento_soporte'];

        $actividades = new CRegistroInversion('', $rinData);
        $actividades->setActividad($actividad);
        $actividades->setFecha($fecha);
        $actividades->setProveedor($proveedor);
        $actividades->setNumeroDocumento($numero_documento);
        $actividades->setValor($valor);
        $actividades->setObservaciones($observaciones);
        $actividades->setDocumentoSoporte($documento_soporte);
        
        $mens = $actividades->saveRegistroInversion($documento_soporte);
        echo $html->generaAviso($mens, "?mod=" . $modulo . "&niv=".$niv."&task=list");

        break;
    case 'delete':
        $id_rin = $_REQUEST['id_element'];
        echo $html->generaAdvertencia(REGISTRO_INVERSION_MSG_BORRADO, '?mod=' . $modulo . '&niv='.$niv.'&task=confirmDelete&id_element=' . $id_rin,
                '"onClick=location.href="?mod='.$modulo.'&niv='.$niv);
        break;
    case 'confirmDelete':
        $id_rin = $_REQUEST['id_element'];
        $actividades = new CRegistroInversion($id_rin, $rinData);
        $actividades->loadRegistroInversion();
        $mens = $actividades->deletRegistroInversion($actividades->getDocumentoSoporte());
        echo $html->generaAviso($mens, "?mod=" . $modulo . "&task=list&niv=1");
        break;
    case 'edit':
        $id_rin = $_REQUEST['id_element'];

        $actividades = new CRegistroInversion($id_rin, $rinData);
        $actividades->loadRegistroInversion();

        if (!isset($_REQUEST['txt_actividad']) || $_REQUEST['txt_actividad'] <= 0) {
            $actividad = $actividades->getActividad();
        } else {
            $actividad = $_REQUEST['txt_actividad'];
        }
        $fecha = $actividades->getFecha();
        if (!isset($_REQUEST['txt_proveedor']) || $_REQUEST['txt_proveedor'] <= 0) {
            $proveedor = $actividades->getProveedor();
        } else {
            $proveedor = $_REQUEST['txt_proveedor'];
        }
        $numero_documento = $actividades->getNumeroDocumento();
        $valor = $actividades->getValor();
        $observaciones = $actividades->getObservaciones();
        $documento_soporte_anterior = $actividades->getDocumentoSoporte();
        /*
         * Inicio formulario
         */
        $form = new CHtmlForm();
        $form->setTitle(TITULO_EDITAR_REGISTRO_INVERSION);
        $form->setId('frm_edit_registro_inversion');
        $form->setMethod('post');
        $form->setClassEtiquetas('td_label');
        $form->setAction('?mod=' . $modulo . '&niv=' . $niv . '&task=saveEdit&id_element=' . $id_rin);

        //Seccion actividades
        $form->addEtiqueta(REGISTRO_INVERSION_ACTIVIDAD);
        $opciones = null;
        $activities = $rinData->getActividades();
        if (isset($activities)) {
            foreach ($activities as $t) {
                $opciones[count($opciones)] = array('value' => $t['id'], 'texto' => $t['nombre']);
            }
        }
        $form->addSelect('select', 'txt_actividad', 'txt_actividad', $opciones, REGISTRO_INVERSION_ACTIVIDAD, $actividad, '', '');
        
        $form->addEtiqueta(REGISTRO_INVERSION_FECHA);
        $form->addInputDate('date', 'txt_fecha', 'txt_fecha', $fecha, '%Y-%m-%d', '16', '16', '', 'pattern="' . PATTERN_FECHA . '" title="' . $html->traducirTildes(TITLE_FECHA) . '" required');

        $form->addEtiqueta(REGISTRO_INVERSION_PROVEEDOR);
        $opciones = null;
        $opciones = null;
        $proveedores = $rinData->getProveedores();
        if (isset($proveedores)) {
            foreach ($proveedores as $t) {
                $opciones[count($opciones)] = array('value' => $t['id'], 'texto' => $t['nombre']);
            }
        }
        $form->addSelect('select', 'txt_proveedor', 'txt_proveedor', $opciones, REGISTRO_INVERSION_PROVEEDOR, $proveedor, '', '');

        $form->addEtiqueta(REGISTRO_INVERSION_NUMERO_DOCUMENTO);
        $form->addInputText('text', 'txt_numero_documento', 'txt_numero_documento', 12, 12, $numero_documento, '', 'pattern="' . PATTERN_ALFANUMERICO . '" title="' . $html->traducirTildes(TITLE_ALFANUMERICO) . '" required');

        $form->addEtiqueta(REGISTRO_INVERSION_VALOR);
        $form->addInputText('text', 'txt_valor', 'txt_valor', 15, 19, $valor, '', 'onkeyup="formatearNumero(this);" pattern="' . PATTERN_NUMEROS_FINANCIEROS . '" title="' . $html->traducirTildes(TITLE_NUMEROS_FINANCIEROS) . '" required');

        $form->addEtiqueta(REGISTRO_INVERSION_OBSERVACIONES);
        $form->addTextArea('textarea', 'txt_observaciones', 'txt_observaciones', 30, 5, $observaciones, '', ' pattern="{0,500} "title="' . $html->traducirTildes(TITLE_ALFANUMERICO) . '" ');

        $form->addEtiqueta(REGISTRO_INVERSION_DOCUMENTO_SOPORTE);
        $form->addInputFile('file', 'documento_soporte', 'documento_soporte', '25', 'file', '');
        
        $form->addInputText('hidden', 'documento_soporte_anterior', 'documento_soporte_anterior', '', '', $documento_soporte_anterior, '', '');
        $form->addInputButton('submit', 'btn_consultar', 'btn_consultar', BTN_ACEPTAR, 'button', '');
        $form->addInputButton('button', 'btn_exportar', 'btn_exportar', BTN_CANCELAR, 'button', 'onClick=location.href="?mod=' . $modulo . '&niv=' . $niv . '"');

        $form->writeForm();
        break;
    case 'saveEdit':
        $id_rin = $_REQUEST['id_element'];
        $actividad = $_REQUEST['txt_actividad'];
        $fecha = $_REQUEST['txt_fecha'];
        $proveedor = $_REQUEST['txt_proveedor'];
        $numero_documento = $_REQUEST['txt_numero_documento'];
        $valor = $_REQUEST['txt_valor'];
        $observaciones = $_REQUEST['txt_observaciones'];
        $documento_soporte = $_FILES['documento_soporte'];

        $documento_soporte_anterior = $_REQUEST['documento_soporte_anterior'];

        $actividades = new CRegistroInversion($id_rin, $rinData);
        $actividades->loadRegistroInversion();
        $actividades->setActividad($actividad);
        $actividades->setFecha($fecha);
        $actividades->setProveedor($proveedor);
        $actividades->setNumeroDocumento($numero_documento);
        $actividades->setValor($valor);
        $actividades->setObservaciones($observaciones);
        
        $mens = $actividades->saveEditRegistroInversion($documento_soporte, $documento_soporte_anterior);
        echo $html->generaAviso($mens, "?mod=" . $modulo . "&task=list&niv=".$niv);
        break;
    /**
     * Cuando la variable task no esta
     * definida carga la página en construcción
     */
    default:
        include('templates/html/under.html');
        break;
}
?>
