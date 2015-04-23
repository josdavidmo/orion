<?php

/**
 * PNCAV
 *
 * <ul>
 * <li> Redcom Ltda <www.redcom.com.co></li>
 * <li> Proyecto PNCAV</li>
 * </ul>
 */
/**
 * Modulo Compromisos
 * maneja el modulo COMPROMISOS en union con CCompromiso y CCompromisoData
 *
 * @see CPoliza
 * @see CPolizaData
 *
 * @package  modulos
 * @subpackage jurídico
 * @author Redcom Ltda
 * @version 2014.09.05
 * @copyright SERTIC - MINTICS
 */

defined('_VALID_PRY') or die('Restricted access');

$docData = new CPolizaData($db);

$task = $_REQUEST['task'];
if (empty($task))
    $task = 'list';


switch ($task){
    
    case 'list':
        
        if(isset($_REQUEST['txt_numerocontrato'])){
            $numero_contrato=$_REQUEST['txt_numerocontrato'];
            if($numero_contrato!=""){
                $consultaContrato=" AND pol_numero_contrato LIKE '$numero_contrato%'";
            }
        }else{
            $consultaContrato="";
        }
        if(isset($_REQUEST['txt_objeto'])){
            $objeto=$_REQUEST['txt_objeto'];
            if($objeto!=""){
                $consultaObjeto=" AND pol_objeto LIKE '$objeto%'";
            }
        }else{
            $consultaObjeto="";
        }
        if(isset($_REQUEST['txt_numero_poliza'])){
            $poliza=$_REQUEST['txt_numero_poliza'];
            if($poliza!=""){
                $consultaPoliza=" AND pol_numero_poliza LIKE '$poliza%'";
            }
        }else{
            $consultaPoliza="";
        }
        if(isset($_REQUEST['txt_tomador'])){
            $tomador=$_REQUEST['txt_tomador'];
            if($tomador!=""){
                $consultaTomador=" AND pol_tomador LIKE '$tomador%'";
            }
        }else{
            $consultaTomador="";
        }
        $criterio = "1".$consultaContrato.$consultaObjeto.$consultaPoliza.$consultaTomador;
        
        
        $form = new CHtmlForm();
        $form->setClassEtiquetas('td_label');
        $form->setTitle(TABLA_POLIZAS);
        $form->setId('frm_agregar_poliza');
        $form->setMethod('post');
        $form->setAction('?mod=' . $modulo . '&niv=' . $niv . '&task=list');
        
        $form->addEtiqueta(CAMPO_NUMERO_CONTRATO);
        $form->addInputText('text', 'txt_numerocontrato', 'txt_numerocontrato', 15, 19, $numero_contrato, '', 'pattern="' . 
                PATTERN_ALFANUMERICO . '" title="' . $html->traducirTildes(TITLE_ALFANUMERICO) . '" ');
        
        $form->addEtiqueta(CAMPO_OBJETO);
        $form->addInputText('text', 'txt_objeto', 'txt_objeto', 15, 19, $objeto, '', 'pattern="' . 
                PATTERN_ALFANUMERICO . '" title="' . $html->traducirTildes(TITLE_ALFANUMERICO) . '" ');
        
        $form->addEtiqueta(CAMPO_NUMERO_POLIZA);
        $form->addInputText('text', 'txt_numero_poliza', 'txt_numero_poliza', 15, 19, $poliza, '', 'pattern="' . 
                PATTERN_ALFANUMERICO . '" title="' . $html->traducirTildes(TITLE_ALFANUMERICO) . '" ');
        
        $form->addEtiqueta(CAMPO_TOMADOR);
        $form->addInputText('text', 'txt_tomador', 'txt_tomador', 15, 19, $tomador, '', 'pattern="' . 
                PATTERN_ALFANUMERICO . '" title="' . $html->traducirTildes(TITLE_NUMEROS) . '" ');
        
        $form->addInputButton('submit', 'ok', 'ok', BTN_ACEPTAR, 'button', '');
        
        $form->writeForm();
        
        
        $elementos = $docData->getPolizas($criterio, "polizainterventoria");
        $dt = new CHtmlDataTable();
        $titulos = array(
                        CAMPO_NUMERO_CONTRATO,
                        CAMPO_OBJETO,
                        CAMPO_PLAZO,
                        CAMPO_FECHA_SUSCRIPCION,
                        CAMPO_CONTRATANTE,
                        CAMPO_CONTRATISTA,
                        CAMPO_NUMERO_POLIZA,
                        CAMPO_ASEGURADORA,
                        CAMPO_TOMADOR,
                        CAMPO_ASEGURADO,
                        CAMPO_BENEFICIARIO,
                        CAMPO_AMPARO,
                        CAMPO_PORCENTAJE,
                        CAMPO_VALOR_ASEGURADO,
                        CAMPO_VIGENCIA_INICIO,
                        CAMPO_VIGENCIA_FIN,
                        CAMPO_OBSERVACIONES,
                        CAMPO_ARCHIVO_INCIDENCIA);
        $dt->setDataRows($elementos);
        $dt->setTitleRow($titulos);

        $dt->setTitleTable(TABLA_POLIZAS);
		$dt->setFormatRow(array(null,null,null,null,null,null,null,null,null,null,null,null,null,array(0, ',', '.'),null,null,null,null));
        $dt->setSumColumns(array(14));
        
        $dt->setAddLink("?mod=" . $modulo . "&niv=" . $niv . "&task=add");
        $dt->setDeleteLink("?mod=" . $modulo . "&niv=" . $niv . "&task=delete");
        $dt->setEditLink("?mod=" . $modulo . "&niv=" . $niv . "&task=edit");
        $dt->setType(1);
        $dt->setPag(1);
        $dt->writeDataTable($niv);
        
        break;
    
    case 'add':
        $form = new CHtmlForm();
        $form->setClassEtiquetas('td_label');
        $form->setTitle(TITULO_AGREGAR_POLIZA);
        $form->setId('frm_agregar_poliza');
        $form->setMethod('post');
        $form->setAction('?mod=' . $modulo . '&niv=' . $niv . '&task=saveAdd');
        
        $form->addEtiqueta(CAMPO_NUMERO_CONTRATO);
        $form->addInputText('text', 'txt_numerocontrato', 'txt_numerocontrato', 15, 19, '', '', 'pattern="' . 
                PATTERN_ALFANUMERICO . '" title="' . $html->traducirTildes(TITLE_ALFANUMERICO) . '" required');
        
        $form->addEtiqueta(CAMPO_OBJETO);
        $form->addTextArea('textarea', 'txt_objeto', 'txt_objeto', 15, 19, '', '', 'pattern="' . 
                PATTERN_ALFANUMERICO . '" title="' . $html->traducirTildes(TITLE_ALFANUMERICO) . '" required');
        
        $form->addEtiqueta(CAMPO_PLAZO);
        $form->addInputText('text', 'txt_plazo', 'txt_plazo', 15, 19, '', '', 'pattern="' . 
                PATTERN_ALFANUMERICO . '" title="' . $html->traducirTildes(TITLE_ALFANUMERICO) . '" required');
        
        $form->addEtiqueta(CAMPO_FECHA_SUSCRIPCION);
        $form->addInputDate('date', 'date_suscripcion', 'date_suscripcion', '', '%Y-%m-%d', '22', '22', '', 'pattern="' . 
                PATTERN_FECHA . '" title="' . $html->traducirTildes(TITLE_FECHA) . '" required');
        
        $form->addEtiqueta(CAMPO_CONTRATANTE);
        $form->addInputText('text', 'txt_contratante', 'txt_contratante', 15, 19, '', '', 'pattern="' . 
                PATTERN_ALFANUMERICO . '" title="' . $html->traducirTildes(TITLE_ALFANUMERICO) . '" required');
        
        $form->addEtiqueta(CAMPO_CONTRATISTA);
        $form->addInputText('text', 'txt_contratista', 'txt_contratista', 15, 19, '', '', 'pattern="' . 
                PATTERN_ALFANUMERICO . '" title="' . $html->traducirTildes(TITLE_ALFANUMERICO) . '" required');
        
        $form->addEtiqueta(CAMPO_NUMERO_POLIZA);
        $form->addInputText('text', 'txt_numero_poliza', 'txt_numero_poliza', 15, 19, '', '', 'pattern="' . 
                PATTERN_ALFANUMERICO . '" title="' . $html->traducirTildes(TITLE_ALFANUMERICO) . '" required');
        
        $form->addEtiqueta(CAMPO_ASEGURADORA);
        $form->addInputText('text', 'txt_aseguradora', 'txt_aseguradora', 15, 19, '', '', 'pattern="' . 
                PATTERN_ALFANUMERICO . '" title="' . $html->traducirTildes(TITLE_ALFANUMERICO) . '" required');
        
        $form->addEtiqueta(CAMPO_TOMADOR);
        $form->addInputText('text', 'txt_tomador', 'txt_tomador', 15, 19, '', '', 'pattern="' . 
                PATTERN_ALFANUMERICO . '" title="' . $html->traducirTildes(TITLE_ALFANUMERICO) . '" required');
        
        $form->addEtiqueta(CAMPO_ASEGURADO);
        $form->addInputText('text', 'txt_asegurado', 'txt_asegurado', 15, 19, '', '', 'pattern="' . 
                PATTERN_ALFANUMERICO . '" title="' . $html->traducirTildes(TITLE_ALFANUMERICO) . '" required');
        
        $form->addEtiqueta(CAMPO_BENEFICIARIO);
        $form->addInputText('text', 'txt_beneficiario', 'txt_beneficiario', 15, 19, '', '', 'pattern="' . 
                PATTERN_ALFANUMERICO . '" title="' . $html->traducirTildes(TITLE_ALFANUMERICO) . '" required');
        
        $form->addEtiqueta(CAMPO_AMPARO);
        $form->addInputText('text', 'txt_amparo', 'txt_amparo', 15, 19, '', '', 'pattern="' . 
                PATTERN_ALFANUMERICO . '" title="' . $html->traducirTildes(TITLE_ALFANUMERICO) . '" required');
        
        $form->addEtiqueta(CAMPO_PORCENTAJE);
        $form->addInputText('text', 'txt_porcentaje', 'txt_porcentaje', 15, 19, '', '', 'pattern="' . 
                PATTERN_NUMEROS_FINANCIEROS. '" title="' . $html->traducirTildes(TITLE_NUMEROS_FINANCIEROS) . '" required');
        //onkeyup="formatearNumero(this);" 
        $form->addEtiqueta(CAMPO_VALOR_ASEGURADO);
        $form->addInputText('text', 'txt_valor_asegurado', 'txt_valor_asegurado', 15, 19, '', '', ' pattern="' . 
                PATTERN_NUMEROS_FINANCIEROS . '" title="' . $html->traducirTildes(TITLE_NUMEROS) . '" required onkeyup="formatearNumero(this);"');
        
        $form->addEtiqueta(CAMPO_VIGENCIA_INICIO);
        $form->addInputDate('date', 'date_inicio', 'date_inicio', '', '%Y-%m-%d', '22', '22', '', 'pattern="' . 
                PATTERN_FECHA . '" title="' . $html->traducirTildes(TITLE_FECHA) . '" required');
        
        $form->addEtiqueta(CAMPO_VIGENCIA_FIN);
        $form->addInputDate('date', 'date_fin', 'date_fin', '', '%Y-%m-%d', '22', '22', '', 'pattern="' . 
                PATTERN_FECHA . '" title="' . $html->traducirTildes(TITLE_FECHA) . '" required');

        $form->addEtiqueta(CAMPO_OBSERVACIONES);
        $form->addTextArea('textarea', 'txt_observaciones', 'txt_observaciones', 15, 19, '', '', 'pattern="' . 
                PATTERN_ALFANUMERICO . '" title="' . $html->traducirTildes(TITLE_ALFANUMERICO) . '"');
        
        $form->addEtiqueta(CAMPO_ARCHIVO_INCIDENCIA);
        $form->addInputFile('file', 'file_archivo', 'file_archivo', 15,'', 'pattern="' . 
                PATTERN_ALFANUMERICO . '" title="' . $html->traducirTildes(TITLE_ALFANUMERICO) . '"');
        
        $form->addInputButton('submit', 'ok', 'ok', BOTON_INSERTAR, 'button', '');
        $form->addInputButton('button', 'cancelar', 'cancelar', BOTON_CANCELAR, 'button', 'onClick=location.href="?mod=' . $modulo . '&niv=' . $niv . '"');
        $form->writeForm();
        
        break;
    
    case 'saveAdd':
        $numeroContrato     = $_REQUEST['txt_numerocontrato'];
        $objeto             = $_REQUEST['txt_objeto'];
        $plazo              = $_REQUEST['txt_plazo'];
        $fecha_suscripcion  = $_REQUEST['date_suscripcion'];
        $contratante        = $_REQUEST['txt_contratante'];
        $contratista        = $_REQUEST['txt_contratista'];
        $numero_poliza      = $_REQUEST['txt_numero_poliza'];
        $aseguradora        = $_REQUEST['txt_aseguradora'];
        $tomador            = $_REQUEST['txt_tomador'];
        $asegurado          = $_REQUEST['txt_asegurado'];
        $beneficiario       = $_REQUEST['txt_beneficiario'];
        $amparo             = $_REQUEST['txt_amparo'];
        $porcentaje         = $_REQUEST['txt_porcentaje']/100;
        $valor_asegurado    = $_REQUEST['txt_valor_asegurado'];
		$valor_asegurado 	= str_replace(".", "", $valor_asegurado);
        $inicio             = $_REQUEST['date_inicio'];
        $fin                = $_REQUEST['date_fin'];
        $observaciones      = $_REQUEST['txt_observaciones'];
        $archivo            = $_FILES['file_archivo'];
        
        $poliza = new CPoliza('',$docData);
        $poliza->setNumContrato($numeroContrato);
        $poliza->setObjeto($objeto);
        $poliza->setPlazo($plazo);
        $poliza->setFechaSuscripcion($fecha_suscripcion);
        $poliza->setContratante($contratante);
        $poliza->setContratista($contratista);
        $poliza->setNumeroPoliza($numero_poliza);
        $poliza->setAseguradora($aseguradora);
        $poliza->setTomador($tomador);
        $poliza->setAsegurado($asegurado);
        $poliza->setBeneficiario($beneficiario);
        $poliza->setAmparo($amparo);
        $poliza->setPorcentaje($porcentaje);
        $poliza->setValorAsegurado($valor_asegurado);
        $poliza->setVigenciaInicio($inicio);
        $poliza->setVigenciaFin($fin);
        $poliza->setObservacionesPoliza($observaciones);
        $poliza->setArchivoPoliza($archivo);
        $mesg=$poliza->saveNewPoliza("polizainterventoria");
        
        echo $html->generaAviso($mesg, "?mod=" . $modulo . "&niv=" . $niv . "&task=list");
        
        break;
    
    case 'edit':
        
        $id = $_REQUEST['id_element'];
        
        $poliza = new CPoliza($id, $docData);
        $poliza->loadPoliza("polizainterventoria");
        
        $numero_contrato=   $poliza->getNumContrato();
        $objeto=            $poliza->getObjeto();
        $plazo=             $poliza->getPlazo();
        $fecha_suscripcion= $poliza->getFechaSuscripcion();
        $contratante=       $poliza->getContratante();
        $contratista=       $poliza->getContratista();
        $numero_poliza=     $poliza->getNumeroPoliza();
        $aseguradora=       $poliza->getAseguradora();
        $tomador=           $poliza->getTomador();
        $asegurado=         $poliza->getAsegurado();
        $beneficiario=      $poliza->getBeneficiario();
        $amparo=            $poliza->getAmparo();
        $porcentaje=        $poliza->getPorcentajePoliza()*100;
        $valor_asegurado=   $poliza->getValorAsegurado();
        $inicio=            $poliza->getVigenciaInicio();
        $fin=               $poliza->getVigenciaFin();
        $observaciones=     $poliza->getObservacionesPoliza();
        
        $form = new CHtmlForm();
        $form->setClassEtiquetas('td_label');
        $form->setTitle(TITULO_EDITAR_POLIZA);
        $form->setId('frm_editar_poliza');
        $form->setMethod('post');
        $form->setAction('?mod=' . $modulo . '&niv=' . $niv . '&task=saveEdit');
        $form->addInputText('hidden', 'id_element', 'id_element', '', '', $id, '', '');
        $form->addEtiqueta(CAMPO_NUMERO_CONTRATO);
        $form->addInputText('text', 'txt_numerocontrato', 'txt_numerocontrato', 15, 19, $numero_contrato, '', 'pattern="' . 
                PATTERN_ALFANUMERICO . '" title="' . $html->traducirTildes(TITLE_ALFANUMERICO) . '" required');
        
        $form->addEtiqueta(CAMPO_OBJETO);
        $form->addTextArea('textarea', 'txt_objeto', 'txt_objeto', 15, 19, $objeto, '', 'pattern="' . 
                PATTERN_ALFANUMERICO . '" title="' . $html->traducirTildes(TITLE_ALFANUMERICO) . '" required');
        
        $form->addEtiqueta(CAMPO_PLAZO);
        $form->addInputText('text', 'txt_plazo', 'txt_plazo', 15, 19, $plazo, '', 'pattern="' . 
                PATTERN_ALFANUMERICO . '" title="' . $html->traducirTildes(TITLE_ALFANUMERICO) . '" required');
        
        $form->addEtiqueta(CAMPO_FECHA_SUSCRIPCION);
        $form->addInputDate('date', 'date_suscripcion', 'date_suscripcion', $fecha_suscripcion, '%Y-%m-%d', '22', '22', '', 'pattern="' . 
                PATTERN_FECHA . '" title="' . $html->traducirTildes(TITLE_FECHA) . '" required');
        
        $form->addEtiqueta(CAMPO_CONTRATANTE);
        $form->addInputText('text', 'txt_contratante', 'txt_contratante', 15, 19, $contratante, '', 'pattern="' . 
                PATTERN_ALFANUMERICO . '" title="' . $html->traducirTildes(TITLE_ALFANUMERICO) . '" required');
        
        $form->addEtiqueta(CAMPO_CONTRATISTA);
        $form->addInputText('text', 'txt_contratista', 'txt_contratista', 15, 19, $contratista, '', 'pattern="' . 
                PATTERN_ALFANUMERICO . '" title="' . $html->traducirTildes(TITLE_ALFANUMERICO) . '" required');
        
        $form->addEtiqueta(CAMPO_NUMERO_POLIZA);
        $form->addInputText('text', 'txt_numero_poliza', 'txt_numero_poliza', 15, 19, $numero_poliza, '', 'pattern="' . 
                PATTERN_ALFANUMERICO . '" title="' . $html->traducirTildes(TITLE_ALFANUMERICO) . '" required');
        
        $form->addEtiqueta(CAMPO_ASEGURADORA);
        $form->addInputText('text', 'txt_aseguradora', 'txt_aseguradora', 15, 19, $aseguradora, '', 'pattern="' . 
                PATTERN_ALFANUMERICO . '" title="' . $html->traducirTildes(TITLE_ALFANUMERICO) . '" required');
        
        $form->addEtiqueta(CAMPO_TOMADOR);
        $form->addInputText('text', 'txt_tomador', 'txt_tomador', 15, 19, $tomador, '', 'pattern="' . 
                PATTERN_ALFANUMERICO. '" title="' . $html->traducirTildes(TITLE_ALFANUMERICO) . '" required');
        
        $form->addEtiqueta(CAMPO_ASEGURADO);
        $form->addInputText('text', 'txt_asegurado', 'txt_asegurado', 15, 19, $asegurado, '', 'pattern="' . 
                PATTERN_ALFANUMERICO. '" title="' . $html->traducirTildes(TITLE_ALFANUMERICO) . '" required');
        
        $form->addEtiqueta(CAMPO_BENEFICIARIO);
        $form->addInputText('text', 'txt_beneficiario', 'txt_beneficiario', 15, 19, $beneficiario, '', 'pattern="' . 
                PATTERN_ALFANUMERICO. '" title="' . $html->traducirTildes(TITLE_ALFANUMERICO) . '" required');
        
        $form->addEtiqueta(CAMPO_AMPARO);
        $form->addInputText('text', 'txt_amparo', 'txt_amparo', 15, 19, $amparo, '', 'pattern="' . 
                PATTERN_ALFANUMERICO . '" title="' . $html->traducirTildes(TITLE_ALFANUMERICO) . '" required');
        
        $form->addEtiqueta(CAMPO_PORCENTAJE);
        $form->addInputText('text', 'txt_porcentaje', 'txt_porcentaje', 15, 19, $porcentaje, '', 'pattern="' . 
                PATTERN_NUMEROS_FINANCIEROS. '" title="' . $html->traducirTildes(TITLE_NUMEROS_FINANCIEROS) . '" required');
        //onkeyup="formatearNumero(this);"
        $form->addEtiqueta(CAMPO_VALOR_ASEGURADO);
        $form->addInputText('text', 'txt_valor_asegurado', 'txt_valor_asegurado', 15, 19, $valor_asegurado, '', ' pattern="' . 
                PATTERN_NUMEROS_FINANCIEROS . '" title="' . $html->traducirTildes(TITLE_NUMEROS_FINANCIEROS) . '" required onkeyup="formatearNumero(this);"');
        
        $form->addEtiqueta(CAMPO_VIGENCIA_INICIO);
        $form->addInputDate('date', 'date_inicio', 'date_inicio', $inicio, '%Y-%m-%d', '22', '22', '', 'pattern="' . 
                PATTERN_FECHA . '" title="' . $html->traducirTildes(TITLE_FECHA) . '" required');
        
        $form->addEtiqueta(CAMPO_VIGENCIA_FIN);
        $form->addInputDate('date', 'date_fin', 'date_fin', $fin, '%Y-%m-%d', '22', '22', '', 'pattern="' . 
                PATTERN_FECHA . '" title="' . $html->traducirTildes(TITLE_FECHA) . '" required');

        $form->addEtiqueta(CAMPO_OBSERVACIONES);
        $form->addTextArea('textarea', 'txt_observaciones', 'txt_observaciones', 15, 19, $observaciones, '', 'pattern="' . 
                PATTERN_ALFANUMERICO . '" title="' . $html->traducirTildes(TITLE_ALFANUMERICO) . '"');
        
        $form->addEtiqueta(CAMPO_ARCHIVO_INCIDENCIA);
        $form->addInputFile('file', 'file_archivo', 'file_archivo', 15,'', 'pattern="' . 
                PATTERN_ALFANUMERICO . '" title="' . $html->traducirTildes(TITLE_ALFANUMERICO) . '"');
        
        $form->addInputButton('submit', 'ok', 'ok', BOTON_EDITAR, 'button', '');
        $form->addInputButton('button', 'cancelar', 'cancelar', BOTON_CANCELAR, 'button', 'onClick=location.href="?mod=' . $modulo . '&niv=' . $niv . '"');
        $form->writeForm();
        break;
    
    case 'saveEdit':
        $id                 = $_REQUEST['id_element'];
        $numeroContrato     = $_REQUEST['txt_numerocontrato'];
        $objeto             = $_REQUEST['txt_objeto'];
        $plazo              = $_REQUEST['txt_plazo'];
        $fecha_suscripcion  = $_REQUEST['date_suscripcion'];
        $contratante        = $_REQUEST['txt_contratante'];
        $contratista        = $_REQUEST['txt_contratista'];
        $numero_poliza      = $_REQUEST['txt_numero_poliza'];
        $aseguradora        = $_REQUEST['txt_aseguradora'];
        $tomador            = $_REQUEST['txt_tomador'];
        $asegurado          = $_REQUEST['txt_asegurado'];
        $beneficiario       = $_REQUEST['txt_beneficiario'];
        $amparo             = $_REQUEST['txt_amparo'];
        $porcentaje         = $_REQUEST['txt_porcentaje']/100;
        $valor_asegurado    = $_REQUEST['txt_valor_asegurado'];
		$valor_asegurado 	= str_replace(".", "", $valor_asegurado);
        $inicio             = $_REQUEST['date_inicio'];
        $fin                = $_REQUEST['date_fin'];
        $observaciones      = $_REQUEST['txt_observaciones'];
        $archivo            = $_FILES['file_archivo'];
        
        $poliza = new CPoliza($id,$docData);
        $poliza->setNumContrato($numeroContrato);
        $poliza->setObjeto($objeto);
        $poliza->setPlazo($plazo);
        $poliza->setFechaSuscripcion($fecha_suscripcion);
        $poliza->setContratante($contratante);
        $poliza->setContratista($contratista);
        $poliza->setNumeroPoliza($numero_poliza);
        $poliza->setAseguradora($aseguradora);
        $poliza->setTomador($tomador);
        $poliza->setAsegurado($asegurado);
        $poliza->setBeneficiario($beneficiario);
        $poliza->setAmparo($amparo);
        $poliza->setPorcentaje($porcentaje);
        $poliza->setValorAsegurado($valor_asegurado);
        $poliza->setVigenciaInicio($inicio);
        $poliza->setVigenciaFin($fin);
        $poliza->setObservacionesPoliza($observaciones);
        $poliza->setArchivoPoliza($archivo);
        $mesg=$poliza->updatePoliza("polizainterventoria");
        echo $html->generaAviso($mesg, "?mod=" . $modulo . "&niv=" . $niv . "&task=list");
        break;
    
    case 'delete':
        $id = $_REQUEST['id_element'];
        echo $html->generaAdvertencia(MENSAJE_ELIMINAR_POLIZA, '?mod=' . $modulo . '&niv='
                . $niv . '&task=confirmDelete&id_element=' . $id, '"onClick=location.href="?mod='.$modulo.'&niv='.$niv);
        break;
    
    case 'confirmDelete':
        $id = $_REQUEST['id_element'];
        $poliza = new CPoliza($id, $docData);
        $poliza->loadPoliza("polizainterventoria");
        $mens = $poliza->deletePoliza("polizainterventoria");
        echo $html->generaAviso($mens, "?mod=" . $modulo . "&niv=1&task=list");
        break;
    default:
        include('templates/html/under.html');
    
}


?>
