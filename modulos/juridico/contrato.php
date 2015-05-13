<?php

/**
 * Modulo Plan de Accion
 * Maneja el modulo plan de accion en union con CPlanAccion, CPlanAccionData
 *
 * @see \CPlanAccion
 * @see \CPlanAccionData
 *
 * @package modulos
 * @subpackage hseq
 * @author SERTIC SAS
 * @version 2014.12.13
 * @copyright SERTIC SAS
 */
//no permite el acceso directo
defined('_VALID_PRY') or die('Restricted access');
$daoContrato = new CContratoData($db);
$daoBasicas = new CBasicaData($db);
$daoOtroSi = new COtroSiData($db);
$task = $_REQUEST['task'];
$modulo = $_REQUEST['mod'];
$niv = $_REQUEST['niv'];
if (empty($task)) {
    $task = 'list';
}

switch ($task) {

    /**
     * la variable list, permite hacer la carga la página con la lista de 
     * objetos contratos según los parámetros de entrada
     */
    case 'list':
        $form = new CHtmlForm();
        $form->setTitle(TITULO_CONTRATOS);
        $form->setAction("?mod=" . $modulo . "&niv=" . $niv);
        $form->setMethod("post");
        $form->writeForm();

        $dt = new CHtmlDataTable();
        $dt->setTitleTable(TITULO_CONTRATOS);
        $titulos = array(CONTRATO_NUMERO, CONTRATO_OBJETO,
            CONTRATO_VALOR, CONTRATO_ANTICIPO, CONTRATO_AMORTIZACION, 
			CONTRATO_POR_AMORTIZAR, CONTRATO_SUMA_ORDENES, CONTRATO_POR_EJECUTAR, 
			CONTRATO_NUMERO_ORDENES, CONTRATO_PLAZO, CONTRATO_FECHA_INICIO, CONTRATO_FECHA_FIN, 
            CONTRATO_SOPORTE, CONTRATO_MONEDA, CONTRATO_ESTADO);
        $contratos = $daoContrato->getContratos();
        $dt->setTitleRow($titulos);
        $dt->setDataRows($contratos);
        $dt->setEditLink("?mod=" . $modulo . "&niv=" . $niv . "&task=edit");
        $dt->setDeleteLink("?mod=" . $modulo . "&niv=" . $niv . "&task=delete");
        $dt->setSeeLink("?mod=" . $modulo . "&niv=" . $niv . "&task=see");
        $dt->setAddLink("?mod=" . $modulo . "&niv=" . $niv . "&task=add");
        $dt->setType(1);
        $pag_crit = "";
        $dt->setPag(1, $pag_crit);
        $dt->writeDataTable($niv);

        break;
    /**
     * la variable add, permite hacer la carga la página con las variables 
     * que componen el objeto plan accion @see \CPlanAccionData
     */
    case 'add':
        $form = new CHtmlForm();
        $form->setTitle(TITULO_AGREGAR_CONTRATO);
        $form->setId('frm_add_contrato_accion');
        $form->setAction('?mod=' . $modulo . '&niv=1&task=saveAdd');
        $form->setMethod('post');

        $form->addEtiqueta(CONTRATO_NUMERO);
        $form->addInputText('text', 'txt_numero', 'txt_numero', '50', '50', '', '', 'pattern="' . PATTERN_ALFANUMERICO . '" title="' . $html->traducirTildes(TITLE_ALFANUMERICO) . '" required');

        $form->addEtiqueta(CONTRATO_OBJETO);
        $form->addTextArea('textarea', 'txt_objeto', 'txt_objeto', 100, 5, '', '', ' title="' . $html->traducirTildes(TITLE_ALFANUMERICO) . '" required');

        $form->addEtiqueta(CONTRATO_VALOR);
        $form->addInputText('text', 'txt_valor', 'txt_valor', '50', '50', '', '', 'pattern="' . PATTERN_NUMEROS . '" title="' . $html->traducirTildes(TITLE_NUMEROS) . '" onkeyup="formatearNumero(this);" required');
		
		$form->addEtiqueta(CONTRATO_ANTICIPO);
        $form->addInputText('text', 'txt_anticipo', 'txt_anticipo', '50', '50', '', '', 'pattern="' . PATTERN_NUMEROS . '" title="' . $html->traducirTildes(TITLE_NUMEROS) . '" onkeyup="formatearNumero(this);" required');

        $form->addEtiqueta(CONTRATO_PLAZO);
        $form->addInputText('text', 'txt_plazo', 'txt_plazo', '50', '50', '', '', 'pattern="' . PATTERN_ALFANUMERICO . '" title="' . $html->traducirTildes(TITLE_ALFANUMERICO) . '" required');

        $form->addEtiqueta(CONTRATO_FECHA_INICIO);
        $form->addInputDate('date', 'txt_fecha_inicio', 'txt_fecha_inicio', '', '%Y-%m-%d', '16', '16', '', 'pattern="' . PATTERN_FECHA . '" title="' . $html->traducirTildes(TITLE_FECHA) . '" required');

        $form->addEtiqueta(CONTRATO_FECHA_FIN);
        $form->addInputDate('date', 'txt_fecha_limite', 'txt_fecha_limite', '', '%Y-%m-%d', '16', '16', '', 'pattern="' . PATTERN_FECHA . '" title="' . $html->traducirTildes(TITLE_FECHA) . '" required');

        $form->addEtiqueta(CONTRATO_SOPORTE);
        $form->addInputFile('file', 'file_soporte', 'file_soporte', '25', 'file', ' required');

        $monedas = $daoBasicas->getBasicas('monedas');
        $opciones = null;
        if (isset($monedas)) {
            foreach ($monedas as $moneda) {
                $opciones[count($opciones)] = array('value' => $moneda->getId(),
                    'texto' => $moneda->getDescripcion());
            }
        }

        $form->addEtiqueta(CONTRATO_MONEDA);
        $form->addSelect('select', 'sel_moneda', 'sel_moneda', $opciones, '', '', '', ' required');
        
        $form->addInputButton('submit', 'btn_enviar', 'btn_enviar', BTN_ACEPTAR, 'button', '');
        $form->addInputButton('button', 'cancel', 'cancel', BTN_CANCELAR, 'button', 'onclick="cancelarAccion(\'frm_add_contrato_accion\',\'?mod=' . $modulo . '&niv=' . $niv . '\');"');

        $form->writeForm();

        break;
    /**
     * la variable saveAdd, permite almacenar el objeto contrato en la 
     * base de datos @see \CContratoData
     */
    case 'saveAdd':
        $numero = $_REQUEST['txt_numero'];
        $objeto = $_REQUEST['txt_objeto'];
        $valor = str_replace(",", ".", str_replace(".", "", $_REQUEST['txt_valor']));
		$anticipo = str_replace(",", ".", str_replace(".", "", $_REQUEST['txt_anticipo']));
        $plazo = $_REQUEST['txt_plazo'];
        $fechaInicio = $_REQUEST['txt_fecha_inicio'];
        $fechaFin = $_REQUEST['txt_fecha_limite'];
        $soporte = $_FILES['file_soporte'];
        $moneda = $_REQUEST['sel_moneda'];

        $contrato = new CContrato(NULL, $numero, $objeto, $valor, $anticipo, $plazo, $fechaInicio, $fechaFin, $soporte, $moneda);

        $r = $daoContrato->insertContrato($contrato);
        $m = ERROR_AGREGAR_CONTRATO;
        if ($r == "true") {
            $m = EXITO_AGREGAR_CONTRATO;
        }
        echo $html->generaAviso($m, "?mod=" . $modulo . "&niv=1&task=list");

        break;

    /**
     * la variable delete, permite hacer la carga del objeto contrato 
     * y espera confirmacion de eliminarlo @see \CContratoData
     */
    case 'delete':
        $id_delete = $_REQUEST['id_element'];
        echo $html->generaAdvertencia(CONFIRMAR_BORRAR_CONTRATO, '?mod=' . $modulo . '&niv=1&task=confirmDelete&id_element=' . $id_delete, 'onclick=location.href=\'?mod=' . $modulo . '&niv=1\'');
        break;

    /**
     * la variable confirmDelete, permite eliminar el objeto contrato de la 
     * base de datos @see \CContratoData
     */
    case 'confirmDelete':
        $id_delete = $_REQUEST['id_element'];
        $r = $daoContrato->deleteContratoById($id_delete);
        $m = ERROR_BORRAR_CONTRATO;
        if ($r == 'true') {
            $m = EXITO_BORRAR_CONTRATO;
        }
        echo $html->generaAviso($m, "?mod=" . $modulo . "&niv=1&task=list");
        break;

    /**
     * la variable edit, permite hacer la carga del objeto plan accion y espera 
     * confirmacion de edicion @see \CPlanAccionData
     */
    case 'edit':
        $id_edit = $_REQUEST['id_element'];
        $contrato = $daoContrato->getContratoById($id_edit);
		$form = new CHtmlForm();
        $form->setTitle(TITULO_EDITAR_CONTRATO);
        $form->setId('frm_add_contrato_accion');
        $form->setAction('?mod=' . $modulo . '&niv=1&task=saveEdit&id_element=' . $id_edit);
        $form->setMethod('post');

        $form->addEtiqueta(CONTRATO_NUMERO);
        $form->addInputText('text', 'txt_numero', 'txt_numero', '50', '50', $contrato->getNumero(), '', 'pattern="' . PATTERN_ALFANUMERICO . '" title="' . $html->traducirTildes(TITLE_ALFANUMERICO) . '" required');

        $form->addEtiqueta(CONTRATO_OBJETO);
        $form->addTextArea('textarea', 'txt_objeto', 'txt_objeto', 100, 5, $contrato->getObjeto(), '', ' title="' . $html->traducirTildes(TITLE_ALFANUMERICO) . '" required');

        $form->addEtiqueta(CONTRATO_VALOR);
        $form->addInputText('text', 'txt_valor', 'txt_valor', '50', '50', $contrato->getValor(), '', 'pattern="' . PATTERN_NUMEROS . '" title="' . $html->traducirTildes(TITLE_NUMEROS) . '" onkeyup="formatearNumero(this);" required');

		$form->addEtiqueta(CONTRATO_ANTICIPO);
        $form->addInputText('text', 'txt_anticipo', 'txt_anticipo', '50', '50', $contrato->getAnticipo(), '', 'pattern="' . PATTERN_NUMEROS . '" title="' . $html->traducirTildes(TITLE_NUMEROS) . '" onkeyup="formatearNumero(this);" required');
		
        $form->addEtiqueta(CONTRATO_PLAZO);
        $form->addInputText('text', 'txt_plazo', 'txt_plazo', '50', '50', $contrato->getPlazo(), '', 'pattern="' . PATTERN_ALFANUMERICO . '" title="' . $html->traducirTildes(TITLE_ALFANUMERICO) . '" required');

        $form->addEtiqueta(CONTRATO_FECHA_INICIO);
        $form->addInputDate('date', 'txt_fecha_inicio', 'txt_fecha_inicio', $contrato->getFechaInicio(), '%Y-%m-%d', '16', '16', '', 'pattern="' . PATTERN_FECHA . '" title="' . $html->traducirTildes(TITLE_FECHA) . '" required');

        $form->addEtiqueta(CONTRATO_FECHA_FIN);
        $form->addInputDate('date', 'txt_fecha_limite', 'txt_fecha_limite', $contrato->getFechaFin(), '%Y-%m-%d', '16', '16', '', 'pattern="' . PATTERN_FECHA . '" title="' . $html->traducirTildes(TITLE_FECHA) . '" required');

        $form->addEtiqueta(CONTRATO_SOPORTE);
        $form->addInputFile('file', 'file_soporte', 'file_soporte', '25', 'file', '');

        $monedas = $daoBasicas->getBasicas('monedas');
        $opciones = null;
        if (isset($monedas)) {
            foreach ($monedas as $moneda) {
                $opciones[count($opciones)] = array('value' => $moneda->getId(),
                    'texto' => $moneda->getDescripcion());
            }
        }

        $form->addEtiqueta(CONTRATO_MONEDA);
        $form->addSelect('select', 'sel_moneda', 'sel_moneda', $opciones, '', $contrato->getMoneda(), '', ' required');

        $form->addInputButton('submit', 'btn_enviar', 'btn_enviar', BTN_ACEPTAR, 'button', '');
        $form->addInputButton('button', 'cancel', 'cancel', BTN_CANCELAR, 'button', 'onclick="cancelarAccion(\'frm_add_contrato_accion\',\'?mod=' . $modulo . '&niv=' . $niv . '\');"');

        $form->writeForm();

        break;

    /**
     * la variable saveEdit, permite actualizar el objeto plan accion en la base 
     * de datos @see \CPlanAccionData
     */
    case 'saveEdit':
        $idContrato = $_REQUEST['id_element'];
        $numero = $_REQUEST['txt_numero'];
        $objeto = $_REQUEST['txt_objeto'];
        $valor = str_replace(",", ".", str_replace(".", "", $_REQUEST['txt_valor']));
        $anticipo = str_replace(",", ".", str_replace(".", "", $_REQUEST['txt_anticipo']));
		$plazo = $_REQUEST['txt_plazo'];
        $fechaInicio = $_REQUEST['txt_fecha_inicio'];
        $fechaFin = $_REQUEST['txt_fecha_limite'];
        $soporte = $_FILES['file_soporte'];
        $moneda = $_REQUEST['sel_moneda'];

        $contrato = new CContrato($idContrato, $numero, $objeto, $valor, $anticipo, $plazo, $fechaInicio, $fechaFin, $soporte, $moneda);

        $r = $daoContrato->updateContrato($contrato);
        $m = ERROR_EDITAR_CONTRATO;
        if ($r == 'true') {
            $m = EXITO_EDITAR_CONTRATO;
        }
        echo $html->generaAviso($m, "?mod=" . $modulo . "&niv=1&task=list");

        break;

    /**
     * la variable see, permite ver el detalle de un plan de accion accediendo
     * a la clase @see \CActividadPlanAccionData
     */
    case 'see':
        $idContrato = $_REQUEST['id_element'];
        $form = new CHtmlForm();
        $form->setOptions('autoClean', false);
        $form->setTitle(TITULO_OTRO_SI);
        $form->setAction("?mod=" . $modulo . "&niv=" . $niv);
        $form->addInputButton('submit', 'ok', 'ok', BTN_ATRAS, 'button', '');
        $form->setMethod("post");
        $form->writeForm();

        $dt = new CHtmlDataTable();
        $dt->setTitleTable(TITULO_OTRO_SI); 
        $titulos = array(OTRO_SI_DESCRIPCION, OTRO_SI_VALOR, OTRO_SI_FECHA, OTRO_SI_OBSERVACION, OTRO_SI_SOPORTE);
        $criterio = "idContrato = $idContrato";
        $contratos = $daoOtroSi->getOtrosSi($criterio);
        $dt->setTitleRow($titulos);
        $dt->setDataRows($contratos);
        $dt->setEditLink("?mod=$modulo&niv=$niv&task=editOtroSi&idContrato=$idContrato");
        $dt->setDeleteLink("?mod=$modulo&niv=$niv&task=deleteOtroSi&idContrato=$idContrato");
        $dt->setAddLink("?mod=$modulo&niv=$niv&task=addOtroSi&idContrato=$idContrato");
        $dt->setType(1);
        $pag_crit = "";
        $dt->setPag(1, $pag_crit);
        $dt->setFormatRow(array(null, array(2, ',', '.'), null));
        $dt->setSumColumns(array(2));
        $dt->writeDataTable($niv);
        break;
    
    case 'addOtroSi':
        $idContrato = $_REQUEST['idContrato'];
        $form = new CHtmlForm();
        $form->setTitle(TITULO_AGREGAR_OTRO_SI);
        $form->setId('frm_add_contrato_accion');
        $form->setAction('?mod=' . $modulo . '&niv=1&task=saveAddOtroSi&idContrato='.$idContrato);
        $form->setMethod('post');

        $form->addEtiqueta(OTRO_SI_DESCRIPCION);
        $form->addTextArea('textarea', 'txt_descripcion', 'txt_descripcion', 100, 5, '', '', ' title="' . $html->traducirTildes(TITLE_ALFANUMERICO) . '"');

        $form->addEtiqueta(OTRO_SI_VALOR);
        $form->addInputText('text', 'txt_valor', 'txt_valor', '50', '50', '', '', 'pattern="' . PATTERN_NUMEROS . '" title="' . $html->traducirTildes(TITLE_NUMEROS) . '" onkeyup="formatearNumero(this);" ');

        $form->addEtiqueta(OTRO_SI_FECHA);
        $form->addInputDate('date', 'txt_fecha', 'txt_fecha', '', '%Y-%m-%d', '16', '16', '', 'pattern="' . PATTERN_FECHA . '" title="' . $html->traducirTildes(TITLE_FECHA) . '" ');

        $form->addEtiqueta(OTRO_SI_OBSERVACION);
        $form->addTextArea('textarea', 'txt_observacion', 'txt_observacion', 100, 5, '', '', ' title="' . $html->traducirTildes(TITLE_ALFANUMERICO) . '" required');
        
        $form->addEtiqueta(OTRO_SI_SOPORTE);
        $form->addInputFile('file', 'file_soporte', 'file_soporte', '25', 'file', ' required');
        
        $form->addInputButton('submit', 'btn_enviar', 'btn_enviar', BTN_ACEPTAR, 'button', '');
        $form->addInputButton('button', 'cancel', 'cancel', BTN_CANCELAR, 'button', 'onclick="cancelarAccion(\'frm_add_contrato_accion\',\'?mod=' . $modulo . '&niv=' . $niv . '&id_element=' . $idContrato . '&task=see\');"');

        $form->writeForm();
        break;
    
    /**
     * la variable saveAdd, permite almacenar el objeto otro si en la 
     * base de datos @see \COtroSiData
     */
    case 'saveAddOtroSi':
        $idContrato = $_REQUEST['idContrato'];
        $descripcion = $_REQUEST['txt_descripcion'];
        $valor = str_replace(",", ".", str_replace(".", "", $_REQUEST['txt_valor']));
        $fecha = $_REQUEST['txt_fecha'];
        $soporte = $_FILES['file_soporte'];
        $observaciones = $_REQUEST['txt_observacion'];

        $otroSi = new COtroSi(NULL, $descripcion, $valor, $fecha, $soporte, $observaciones, $idContrato);

        $r = $daoOtroSi->insertOtroSi($otroSi);
        $m = ERROR_AGREGAR_OTRO_SI;
        if ($r == "true") {
            $m = EXITO_AGREGAR_OTRO_SI;
        }
        echo $html->generaAviso($m, "?mod=$modulo&niv=1&task=see&id_element=$idContrato");

        break;
        
    /**
     * la variable delete, permite hacer la carga del objeto otro si 
     * y espera confirmacion de eliminarlo @see \COtroSiData
     */
    case 'deleteOtroSi':
        $idContrato = $_REQUEST['idContrato'];
        $id_delete = $_REQUEST['id_element'];
        echo $html->generaAdvertencia(CONFIRMAR_BORRAR_OTRO_SI, '?mod=' . $modulo . '&niv=1&task=confirmDeleteOtroSi&id_element=' . $id_delete . '&idContrato='.$idContrato, 'onclick=location.href=\'?mod=' . $modulo . '&niv=1&task=see&id_element='. $idContrato.'\'');
        break;

    /**
     * la variable confirmDelete, permite eliminar el objeto otro si de la 
     * base de datos @see \COtroSiData
     */
    case 'confirmDeleteOtroSi':
        $idContrato = $_REQUEST['idContrato'];
        $id_delete = $_REQUEST['id_element'];
        $r = $daoOtroSi->deleteOtroSiById($id_delete);
        $m = ERROR_BORRAR_OTRO_SI_CONTRATO;
        if ($r == 'true') {
            $m = EXITO_BORRAR_OTRO_SI_CONTRATO;
        }
        echo $html->generaAviso($m, "?mod=" . $modulo . "&niv=1&task=see&id_element=". $idContrato);
        break;
        
    case 'editOtroSi':
        $idOtroSi = $_REQUEST['id_element'];
        $otroSi = $daoOtroSi->getOtrosSiById($idOtroSi);
        $idContrato = $_REQUEST['idContrato'];
        $form = new CHtmlForm();
        $form->setTitle(TITULO_EDITAR_OTRO_SI);
        $form->setId('frm_add_contrato_accion');
        $form->setAction('?mod=' . $modulo . '&niv=1&task=saveEditOtroSi&idContrato='.$idContrato."&id_element=".$idOtroSi);
        $form->setMethod('post');

        $form->addEtiqueta(OTRO_SI_DESCRIPCION);
        $form->addTextArea('textarea', 'txt_descripcion', 'txt_descripcion', 100, 5, $otroSi->getDescripcion(), '', ' title="' . $html->traducirTildes(TITLE_ALFANUMERICO) . '"');

        $form->addEtiqueta(OTRO_SI_VALOR);
        $form->addInputText('text', 'txt_valor', 'txt_valor', '50', '50', $otroSi->getValor(), '', 'pattern="' . PATTERN_NUMEROS . '" title="' . $html->traducirTildes(TITLE_NUMEROS) . '" onkeyup="formatearNumero(this);" ');

        $form->addEtiqueta(OTRO_SI_FECHA);
        $form->addInputDate('date', 'txt_fecha', 'txt_fecha', $otroSi->getFecha(), '%Y-%m-%d', '16', '16', '', 'pattern="' . PATTERN_FECHA . '" title="' . $html->traducirTildes(TITLE_FECHA) . '" ');

        $form->addEtiqueta(OTRO_SI_OBSERVACION);
        $form->addTextArea('textarea', 'txt_observacion', 'txt_observacion', 100, 5, $otroSi->getObservaciones(), '', ' title="' . $html->traducirTildes(TITLE_ALFANUMERICO) . '" required');
        
        $form->addEtiqueta(OTRO_SI_SOPORTE);
        $form->addInputFile('file', 'file_soporte', 'file_soporte', '25', 'file', '');
        
        $form->addInputButton('submit', 'btn_enviar', 'btn_enviar', BTN_ACEPTAR, 'button', '');
        $form->addInputButton('button', 'cancel', 'cancel', BTN_CANCELAR, 'button', 'onclick="cancelarAccion(\'frm_add_contrato_accion\',\'?mod=' . $modulo . '&niv=' . $niv . '&id_element=' . $idContrato . '&task=see\');"');

        $form->writeForm();
        break;
    
    /**
     * la variable saveAdd, permite almacenar el objeto otro si en la 
     * base de datos @see \COtroSiData
     */
    case 'saveEditOtroSi':
        $idOtroSi = $_REQUEST['id_element'];
        $idContrato = $_REQUEST['idContrato'];
        $descripcion = $_REQUEST['txt_descripcion'];
        $valor = str_replace(",", ".", str_replace(".", "", $_REQUEST['txt_valor']));
        $fecha = $_REQUEST['txt_fecha'];
        $soporte = $_FILES['file_soporte'];
        $observaciones = $_REQUEST['txt_observacion'];

        $otroSi = new COtroSi($idOtroSi, $descripcion, $valor, $fecha, $soporte, $observaciones, $idContrato);

        $r = $daoOtroSi->updateOtroSi($otroSi);
        $m = ERROR_EDITAR_OTRO_SI;
        if ($r == "true") {
            $m = EXITO_EDITAR_OTRO_SI;
        }
        echo $html->generaAviso($m, "?mod=$modulo&niv=1&task=see&id_element=$idContrato");

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

