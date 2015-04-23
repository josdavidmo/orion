<?php
defined('_VALID_PRY') or die('Restricted access');
$niv = $_REQUEST['niv'];
$operador = OPERADOR_DEFECTO;
$ifiData = new CInformeFinancieroData($db);
$html = new CHtml();
$task = $_REQUEST['task'];

if (empty($task)) {
    $task = 'list';
}
switch ($task) {
    /**
     * La variable list, permite hacer la carga la página con la lista de 
     * objetos inventarioActividades según los parámetros de entrada.
     */
    case 'list':        
        $vigencia = $_REQUEST['txt_vigencia'];
        $descripcion = $_REQUEST['txt_descripcion'];
        $estado = $_REQUEST['sel_estado'];
        //-------------------------------criterios---------------------------
        $criterio = "";
        if (isset($vigencia) && $vigencia != '') {
            if ($criterio == "") {
                $criterio = "  ifi.ifi_vigencia = '" . $vigencia . "'";
            } else {
                $criterio .= " and ifi.ifi_vigencia = '" . $vigencia . "'";
            }
        }
        if (isset($descripcion) && $descripcion != '') {
            if ($criterio == "") {
                $criterio = " ifi.ifi_descripcion LIKE '" . $descripcion . "%'";
            } else {
                $criterio .= " and ifi.ifi_descripcion LIKE '" . $descripcion . "%'";
            }
        }
        if (isset($estado) && $estado != '-1') {
            if ($criterio == "") {
                $criterio = "  ifi.ife_id = '" . $estado . "'";
            } else {
                $criterio .= " and ifi.ife_id = '" . $estado . "'";
            }
        }
        if ($criterio == "") {
            $criterio = "1";
        }
        /*
         * Inicio formulario
         */
        $form = new CHtmlForm();
        $form->setTitle(TABLA_INFORME_FINANCIERO);
        $form->setId('frm_list_informe_financiero');
        $form->setMethod('post');
        $form->setClassEtiquetas('td_label');

        $form->addEtiqueta(INFORME_FINANCIERO_VIGENCIA);
        $form->addInputDate('date', 'txt_vigencia', 'txt_vigencia', $vigencia, '%Y', '16', '16', '', 'pattern="' . PATTERN_AÑO . '" title="' . $html->traducirTildes(TITLE_AÑO) . '" ');

        $form->addEtiqueta(INFORME_FINANCIERO_DESCRIPCION);
        $form->addTextArea('textarea', 'txt_descripcion', 'txt_descripcion', 30, 5, $descripcion, '', ' title="' . $html->traducirTildes(TITLE_ALFANUMERICO) . '" ');

        $form->addEtiqueta(INFORME_FINANCIERO_ESTADO);
        $opciones = null;
        $estados = $ifiData->getEstadosInformeFinanciero();
        if (isset($estados)) {
            foreach ($estados as $t) {
                $opciones[count($opciones)] = array('value' => $t['id'], 'texto' => $t['nombre']);
            }
        }
        $form->addSelect('select', 'sel_estado', 'sel_estado', $opciones, INFORME_FINANCIERO_ESTADO, $estado, '', '');
        $form->addInputButton('button', 'btn_consultar', 'btn_consultar', BTN_ACEPTAR, 'button', 'onClick=consultar_informe_financiero();');
        $form->addInputButton('button', 'btn_exportar', 'btn_exportar', INFORME_FINANCIERO_EXPORTAR, 'button', 'onClick=exportar_excel_informe_financiero();');
        $form->writeForm();


        $dataRows = $ifiData->getInformeFinanciero($criterio);
        //Inicio Tabla
        $dt = new CHtmlDataTable();
        $titulos = array(INFORME_FINANCIERO_NUMERO_PAGO, INFORME_FINANCIERO_VIGENCIA,
            INFORME_FINANCIERO_NUMERO_FACTURA, INFORME_FINANCIERO_FECHA_FACTURA,
            INFORME_FINANCIERO_NUMERO_RADICADO_MINISTERIO, INFORME_FINANCIERO_DOCUMENTO_SOPORTE,
            INFORME_FINANCIERO_DESCRIPCION, INFORME_FINANCIERO_VALOR_FACTURA,
            INFORME_FINANCIERO_AMORTIZACION, INFORME_FINANCIERO_SALDOP_CONTRATO,
            INFORME_FINANCIERO_SALDOP_AMORTIZACION, INFORME_FINANCIERO_ESTADO,
            INFORME_FINANCIERO_FECHA_PAGO, INFORME_FINANCIERO_OBSERVACIONES);
        $dt->setDataRows($dataRows);
        $dt->setTitleRow($titulos);
        $dt->setTitleTable(TABLA_INFORME_FINANCIERO);

        //OPCIONES DE GESTIÓN
        $dt->setEditLink("?mod=" . $modulo . "&niv=" . $niv . "&task=edit");
        $dt->setDeleteLink("?mod=" . $modulo . "&niv=" . $niv . "&task=delete");
        $dt->setAddLink("?mod=" . $modulo . "&niv=" . $niv . "&task=add");
        $dt->setType(1);
        $pag_crit = "";
        $dt->setPag(1, $pag_crit);
        $dt->setSumColumns(array(8, 9));
		$dt->setFormatRow(array(null,null,null,null,null,null,null,array(2,',','.'),array(2,',','.'),array(2,',','.'),array(2,',','.')));
        $dt->writeDataTable($niv);

        break;

    /*
     * Variable add, agregar un informe financiero
     */
    case 'add':
        $numero_pago = $_REQUEST['txt_numero_pago'];
        $vigencia = $_REQUEST['txt_vigencia'];
        $numero_factura = $_REQUEST['txt_numero_factura'];
        $fecha_factura = $_REQUEST['txt_fecha'];
        $numero_radicado_ministerio = $_REQUEST['txt_numero_radicado_ministerio'];
        $documento_soporte = $_FILES['documento_soporte'];
        $descripcion = $_REQUEST['txt_descripcion'];
        $valor_factura = $_REQUEST['txt_valor_factura'];
        $amortizacion = $_REQUEST['txt_amortizacion'];
        $observaciones = $_REQUEST['txt_observaciones'];
        $estado = $_REQUEST['sel_estado'];
        $fecha_pago = $_REQUEST['txt_fecha_pago'];
        $form = new CHtmlForm();
        $form->setTitle(TITULO_AGREGAR_INFORME_FINANCIERO);
        $form->setId('frm_add_informe_financiero');
        $form->setMethod('post');
        $form->setClassEtiquetas('td_label');
        $form->setAction('?mod=' . $modulo . '&niv=' . $niv . '&task=saveAdd'.'" onsubmit=" return validarSaldos('.$ifiData->getSaldop_contrato().','.$ifiData->getSaldop_amortizacion().');');
        $form->setTableId("addII");

        $form->addEtiqueta(INFORME_FINANCIERO_NUMERO_PAGO);
        $form->addInputText('text', 'txt_numero_pago', 'txt_numero_pago', 15, 12, $numero_pago, '', 'autofocus pattern="' . PATTERN_ALFANUMERICO . '" title="' . $html->traducirTildes(TITLE_ALFANUMERICO) . '" required');

        $form->addEtiqueta(INFORME_FINANCIERO_VIGENCIA);
        $form->addInputDate('date', 'txt_vigencia', 'txt_vigencia', $vigencia, '%Y', '16', '16', '', 'pattern="' . PATTERN_AÑO . '" title="' . $html->traducirTildes(TITLE_AÑO) . '" required');

        $form->addEtiqueta(INFORME_FINANCIERO_NUMERO_FACTURA);
        $form->addInputText('text', 'txt_numero_factura', 'txt_numero_factura', 15, 12, $numero_factura, '', 'pattern="' . PATTERN_ALFANUMERICO . '" title="' . $html->traducirTildes(TITLE_ALFANUMERICO) . '" required');

        $form->addEtiqueta(INFORME_FINANCIERO_FECHA_FACTURA);
        $form->addInputDate('date', 'txt_fecha', 'txt_fecha', $fecha_factura, '%Y-%m-%d', '16', '16', '', 'pattern="' . PATTERN_FECHA . '" title="' . $html->traducirTildes(TITLE_FECHA) . '" required');

        $form->addEtiqueta(INFORME_FINANCIERO_NUMERO_RADICADO_MINISTERIO);
        $form->addInputText('text', 'txt_numero_radicado_ministerio', 'txt_numero_radicado_ministerio', 15, 12, $numero_radicado_ministerio, '', 'pattern="' . PATTERN_ALFANUMERICO . '" title="' . $html->traducirTildes(TITLE_ALFANUMERICO) . '" required');

        $form->addEtiqueta(INFORME_FINANCIERO_DOCUMENTO_SOPORTE);
        $form->addInputFile('file', 'documento_soporte', 'documento_soporte', '25', 'file', ' required');

        $form->addEtiqueta(INFORME_FINANCIERO_DESCRIPCION);
        $form->addTextArea('textarea', 'txt_descripcion', 'txt_descripcion', 30, 5, $descripcion, '', ' title="' . $html->traducirTildes(TITLE_ALFANUMERICO) . '" required');

        $form->addEtiqueta(INFORME_FINANCIERO_VALOR_FACTURA);
        $form->addInputText('text', 'txt_valor_factura', 'txt_valor_factura', 15, 19, $valor_factura, '', 'onkeyup="formatearNumero(this);" pattern="' . PATTERN_NUMEROS_FINANCIEROS . '" title="' . $html->traducirTildes(TITLE_NUMEROS_FINANCIEROS) . '" required');

        $form->addEtiqueta(INFORME_FINANCIERO_AMORTIZACION);
        $form->addInputText('text', 'txt_amortizacion', 'txt_amortizacion', 15, 19, $amortizacion, '', 'onkeyup="formatearNumero(this);" pattern="' . PATTERN_NUMEROS_FINANCIEROS . '" title="' . $html->traducirTildes(TITLE_NUMEROS_FINANCIEROS) . '" required');

        $form->addEtiqueta(INFORME_FINANCIERO_ESTADO);
        $opciones = null;
        $estados = $ifiData->getEstadosInformeFinanciero();
        if (isset($estados)) {
            foreach ($estados as $t) {
                $opciones[count($opciones)] = array('value' => $t['id'], 'texto' => $t['nombre']);
            }
        }
        $form->addSelect('select', 'sel_estado', 'sel_estado', $opciones, INFORME_FINANCIERO_ESTADO, $estado, '', '');

        $form->addEtiqueta(INFORME_FINANCIERO_OBSERVACIONES);
        $form->addTextArea('textarea', 'txt_observaciones', 'txt_observaciones', 30, 5, $observaciones, '', ' title="' . $html->traducirTildes(TITLE_ALFANUMERICO) . '"');

        $form->addInputButton('submit', 'btn_consultar', 'btn_consultar', BTN_ACEPTAR, 'button', '');
        $form->addInputButton('button', 'btn_exportar', 'btn_exportar', BTN_CANCELAR, 'button', 'onClick=location.href="?mod='.$modulo.'&niv='.$niv.'"');

        $form->writeForm();
        break;
    case 'saveAdd':
        $numero_pago = $_REQUEST['txt_numero_pago'];
        $vigencia = $_REQUEST['txt_vigencia'];
        $numero_factura = $_REQUEST['txt_numero_factura'];
        $fecha_factura = $_REQUEST['txt_fecha'];
        $numero_radicado_ministerio = $_REQUEST['txt_numero_radicado_ministerio'];
        $documento_soporte = $_FILES['documento_soporte'];
        $descripcion = $_REQUEST['txt_descripcion'];
        $valor_factura = $_REQUEST['txt_valor_factura'];
        $amortizacion = $_REQUEST['txt_amortizacion'];
        $observaciones = $_REQUEST['txt_observaciones'];
        $estado = $_REQUEST['sel_estado'];
        $fecha_pago = $_REQUEST['txt_fecha_pago'];
        $informeFinanciero = new CInformeFinanciero('', $ifiData);
        $informeFinanciero->setNumero_pago($numero_pago);
        $informeFinanciero->setVigencia($vigencia);
        $informeFinanciero->setNumero_factura($numero_factura);
        $informeFinanciero->setFecha_factura($fecha_factura);
        $informeFinanciero->setNumero_radicado_ministerio($numero_radicado_ministerio);
        $informeFinanciero->setDocumento_soporte($documento_soporte);
        $informeFinanciero->setDescripcion($descripcion);
        $informeFinanciero->setValor_factura($valor_factura);
        $informeFinanciero->setAmortizacion($amortizacion);
        $informeFinanciero->setObservaciones($observaciones);
        $informeFinanciero->setEstado($estado);
        $informeFinanciero->setFecha_pago($fecha_pago);
        
        $mens = $informeFinanciero->saveInformeFinanciero($documento_soporte);
        echo $html->generaAviso($mens, "?mod=" . $modulo . "&niv=1&task=list");
        break;

    case 'delete':
        $id_ifi = $_REQUEST['id_element'];
        echo $html->generaAdvertencia(INFORME_FINANCIERO_MSG_BORRADO, '?mod=' . $modulo . '&niv='
                . $niv . '&task=confirmDelete&id_element=' . $id_ifi, '"onClick=location.href="?mod='.$modulo.'&niv='.$niv);
        break;
    case 'confirmDelete':
        $id_ifi = $_REQUEST['id_element'];
        $informeFinanciero = new CInformeFinanciero($id_ifi, $ifiData);
        $informeFinanciero->loadInformeFinanciero();
        $mens = $informeFinanciero->deletInformeFinanciero();
        echo $html->generaAviso($mens, "?mod=" . $modulo . "&niv=1&task=list");
        break;
    case 'edit':
        $id_ifi = $_REQUEST['id_element'];

        $informeFinanciero = new CInformeFinanciero($id_ifi, $ifiData);
        $informeFinanciero->loadInformeFinanciero();
        $estados = $ifiData->getEstadosInformeFinanciero();
        if (isset($_REQUEST['txt_numero_pago'])) {
            $numero_pago = $_REQUEST['txt_numero_pago'];
        } else {
            $numero_pago = $informeFinanciero->getNumero_pago();
        }

        if (isset($_REQUEST['txt_vigencia'])) {
            $vigencia = $_REQUEST['txt_vigencia'];
        } else {
            $vigencia = $informeFinanciero->getVigencia();
        }

        if (isset($_REQUEST['txt_numero_factura'])) {
            $numero_factura = $_REQUEST['txt_numero_factura'];
        } else {
            $numero_factura = $informeFinanciero->getNumero_factura();
        }

        if (isset($_REQUEST['txt_fecha'])) {
            $fecha_factura = $_REQUEST['txt_fecha'];
        } else {
            $fecha_factura = $informeFinanciero->getFecha_factura();
        }

        if (isset($_REQUEST['txt_numero_radicado_ministerio'])) {
            $numero_radicado_ministerio = $_REQUEST['txt_numero_radicado_ministerio'];
        } else {
            $numero_radicado_ministerio = $informeFinanciero->getNumero_radicado_ministerio();
        }

        if (isset($_REQUEST['txt_descripcion'])) {
            $descripcion = $_REQUEST['txt_descripcion'];
        } else {
            $descripcion = $informeFinanciero->getDescripcion();
        }

        if (isset($_REQUEST['txt_valor_factura'])) {
            $valor_factura = $_REQUEST['txt_valor_factura'];
        } else {
            $valor_factura = $informeFinanciero->getValor_factura();
        }

        if (isset($_REQUEST['txt_amortizacion'])) {
            $amortizacion = $_REQUEST['txt_amortizacion'];
        } else {
            $amortizacion = $informeFinanciero->getAmortizacion();
        }

        if (isset($_REQUEST['txt_observaciones'])) {
            $observaciones = $_REQUEST['txt_observaciones'];
        } else {
            $observaciones = $informeFinanciero->getObservaciones();
        }

        if (isset($_REQUEST['sel_estado'])) {
            $estado = $_REQUEST['sel_estado'];
        } else {
            $estado = $informeFinanciero->getEstado();
        }

        if (isset($_REQUEST['txt_fecha_pago'])) {
            $fecha_pago = $_REQUEST['txt_fecha_pago'];
        } else {
            $fecha_pago = $informeFinanciero->getFecha_pago();
        }
        $documento_soporte_anterior = $informeFinanciero->getDocumento_soporte();

        /*
         * Inicio formulario
         */
        $form = new CHtmlForm();
        $form->setTitle(TITULO_EDITAR_INFORME_FINANCIERO);
        $form->setId('frm_add_informe_financiero');
        $form->setMethod('post');
        $form->setClassEtiquetas('td_label');
        $form->setAction('?mod=' . $modulo . '&niv=' . $niv . '&task=saveEdit&id_element=' . $id_ifi.'" onsubmit=" return validarSaldos('.$ifiData->getSaldop_contrato().','.$ifiData->getSaldop_amortizacion().');');
        $form->setTableId("addII");

        $form->addEtiqueta(INFORME_FINANCIERO_NUMERO_PAGO);
        $form->addInputText('text', 'txt_numero_pago', 'txt_numero_pago', 15, 12, $numero_pago, '', 'autofocus pattern="' . PATTERN_ALFANUMERICO . '" title="' . $html->traducirTildes(TITLE_ALFANUMERICO) . '" required');

        $form->addEtiqueta(INFORME_FINANCIERO_VIGENCIA);
        $form->addInputDate('date', 'txt_vigencia', 'txt_vigencia', $vigencia, '%Y', '16', '16', '', 'pattern="' . PATTERN_AÑO . '" title="' . $html->traducirTildes(TITLE_AÑO) . '" required');

        $form->addEtiqueta(INFORME_FINANCIERO_NUMERO_FACTURA);
        $form->addInputText('text', 'txt_numero_factura', 'txt_numero_factura', 15, 12, $numero_factura, '', 'pattern="' . PATTERN_ALFANUMERICO . '" title="' . $html->traducirTildes(TITLE_ALFANUMERICO) . '" required');

        $form->addEtiqueta(INFORME_FINANCIERO_FECHA_FACTURA);
        $form->addInputDate('date', 'txt_fecha', 'txt_fecha', $fecha_factura, '%Y-%m-%d', '16', '16', '', 'pattern="' . PATTERN_FECHA . '" title="' . $html->traducirTildes(TITLE_FECHA) . '" required');

        $form->addEtiqueta(INFORME_FINANCIERO_NUMERO_RADICADO_MINISTERIO);
        $form->addInputText('text', 'txt_numero_radicado_ministerio', 'txt_numero_radicado_ministerio', 15, 12, $numero_radicado_ministerio, '', 'pattern="' . PATTERN_ALFANUMERICO . '" title="' . $html->traducirTildes(TITLE_ALFANUMERICO) . '" required');

        $form->addEtiqueta(INFORME_FINANCIERO_DOCUMENTO_SOPORTE);
        $form->addInputFile('file', 'documento_soporte', 'documento_soporte', '25', 'file', '');

        $form->addEtiqueta(INFORME_FINANCIERO_DESCRIPCION);
        $form->addTextArea('textarea', 'txt_descripcion', 'txt_descripcion', 30, 5, $descripcion, '', ' title="' . $html->traducirTildes(TITLE_ALFANUMERICO) . '" required');

        $form->addEtiqueta(INFORME_FINANCIERO_VALOR_FACTURA);
        $form->addInputText('text', 'txt_valor_factura', 'txt_valor_factura', 15, 19, $valor_factura, '', ' onkeyup="formatearNumero(this);"  pattern="' . PATTERN_NUMEROS_FINANCIEROS . '" title="' . $html->traducirTildes(TITLE_NUMEROS_FINANCIEROS) . '" required');

        $form->addEtiqueta(INFORME_FINANCIERO_AMORTIZACION);
        $form->addInputText('text', 'txt_amortizacion', 'txt_amortizacion', 15, 19, $amortizacion, '', 'onkeyup="formatearNumero(this);" pattern="' . PATTERN_NUMEROS_FINANCIEROS . '" title="' . $html->traducirTildes(TITLE_NUMEROS_FINANCIEROS) . '" required');

        $form->addEtiqueta(INFORME_FINANCIERO_ESTADO);
        $opciones = null;
        $estados = $ifiData->getEstadosInformeFinanciero();
        if (isset($estados)) {
            foreach ($estados as $t) {
                $opciones[count($opciones)] = array('value' => $t['id'], 'texto' => $t['nombre']);
            }
        }
        $form->addSelect('select', 'sel_estado', 'sel_estado', $opciones, INFORME_FINANCIERO_ESTADO, $estado, '', '');
        
        $form->addEtiqueta(INFORME_FINANCIERO_OBSERVACIONES);
        $form->addTextArea('textarea', 'txt_observaciones', 'txt_observaciones', 30, 5, $observaciones, '', 'title="' . $html->traducirTildes(TITLE_ALFANUMERICO) . '"');

        $form->addInputButton('submit', 'btn_consultar', 'btn_consultar', BTN_ACEPTAR, 'button','');
        $form->addInputButton('button', 'btn_exportar', 'btn_exportar', BTN_CANCELAR, 'button', 'onClick=location.href="?mod=informeFinanciero&niv=1"');

        $form->addInputText('hidden', 'documento_soporte_anterior', 'documento_soporte_anterior', '', '', $documento_soporte_anterior, '', '');
        $form->writeForm();
        if ($estado == '2') {
            ?><script>agregarFechaPago('<?php echo $form->getTableId(); ?>', '<?php echo $fecha_pago; ?>');</script><?php
        }
        break;
    /*
     * Almacenar los datos obtenidos del formulario de la variable edit
     */
    case 'saveEdit':
        $id_ifi = $_REQUEST['id_element'];
        $numero_pago = $_REQUEST['txt_numero_pago'];
        $vigencia = $_REQUEST['txt_vigencia'];
        $numero_factura = $_REQUEST['txt_numero_factura'];
        $fecha_factura = $_REQUEST['txt_fecha'];
        $numero_radicado_ministerio = $_REQUEST['txt_numero_radicado_ministerio'];
        $documento_soporte = $_FILES['documento_soporte'];
        $descripcion = $_REQUEST['txt_descripcion'];
        $valor_factura = $_REQUEST['txt_valor_factura'];
        $amortizacion = $_REQUEST['txt_amortizacion'];
        $observaciones = $_REQUEST['txt_observaciones'];
        $estado = $_REQUEST['sel_estado'];
        $fecha_pago = $_REQUEST['txt_fecha_pago'];
        
        $documento_soporte_anterior = $_REQUEST['documento_soporte_anterior'];
            
        $informeFinanciero = new CInformeFinanciero($id_ifi, $ifiData);
        $informeFinanciero->setNumero_pago($numero_pago);
        $informeFinanciero->setVigencia($vigencia);
        $informeFinanciero->setNumero_factura($numero_factura);
        $informeFinanciero->setFecha_factura($fecha_factura);
        $informeFinanciero->setNumero_radicado_ministerio($numero_radicado_ministerio);
        $informeFinanciero->setDocumento_soporte($documento_soporte);
        $informeFinanciero->setDescripcion($descripcion);
        $informeFinanciero->setValor_factura($valor_factura);
        $informeFinanciero->setAmortizacion($amortizacion);
        $informeFinanciero->setObservaciones($observaciones);
        $informeFinanciero->setEstado($estado);
        $informeFinanciero->setFecha_pago($fecha_pago);
        $mens = $informeFinanciero->saveEditInformeFinanciero($documento_soporte, $documento_soporte_anterior);
        echo $html->generaAviso($mens, "?mod=" . $modulo . "&niv=1&task=list");
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