<?php

/**
 * Modulo Parafiscales
 * Maneja el modulo parafiscales en union con CUsuario, CParafiscales, 
 * CEstadoParafiscales y CParafiscalesData
 *
 * @see \CParafiscales
 * @see \CUsuario
 * @see \CEstadoParafiscales
 * @see \CParafiscalesData
 *
 * @package modulos
 * @subpackage juridico
 * @author SERTIC SAS
 * @version 2014.09.14
 * @copyright SERTIC SAS
 */
//no permite el acceso directo
defined('_VALID_PRY') or die('Restricted access');
$daoParafiscales = new CParafiscalesData($db);
$operador = $_REQUEST['operador'];
$task = $_REQUEST['task'];
if (empty($task))
    $task = 'list';

switch ($task) {
    /**
     * La variable list, permite hacer la carga la página con la lista de 
     * objetos parafiscales según los parámetros de entrada.
     */
    case 'list':
        $periodo = $_REQUEST['txt_periodo'];
        $parafiscales = $daoParafiscales->getParafiscales();
        if($periodo != null){
            $parafiscales = $daoParafiscales->getParafiscalesByPeriodo($periodo);
        }
        
        $formFiltro = new CHtmlForm();
        $formFiltro->setTitle(TITULO_PARAFISCALES);
        $formFiltro->setAction('?mod=' . $modulo . '&niv='. $niv);
        $formFiltro->setMethod('post');
        $formFiltro->addEtiqueta(PERIODO_PARAFISCALES);
        $formFiltro->addInputText('month', 'txt_periodo', 'txt_periodo', '50', '50', 
                            '', '', '');
        $formFiltro->addInputButton('submit', 'ok', 'ok', BTN_ACEPTAR, 'button', '');
        $formFiltro->addInputButton('button', 'export', 'export', BTN_EXPORTAR, 
                              'button', 'onclick=location.href=\'modulos/juridico/parafiscales_en_excel.php?txt_periodo='.$periodo.'\'');
        $formFiltro->writeForm();

        $dt = new CHtmlDataTable();
        $dt->setTitleTable(TITULO_PARAFISCALES);
        $titulos = array(PERIODO_PARAFISCALES, 
                         FECHA_RADICACION_PARAFISCALES, 
                         COMUNICADO_ENTREGA_SOPORTES_PARAFISCALES,
                         EVALUACION_CONTENIDO_DOCUMENTO,
                         EVALUACION_REVISOR_FISCAL,
                         CONCEPTO_FINAL_PARAFISCALES,
                         USUARIO_PARAFISCALES,
                         FECHA_COMUNICADO_CONCEPTO_INTERVENTORIA_PARAFISCALES,
                         COMUNICADO_CONCEPTO_PARAFISCALES,
                         OBSERVACIONES_PARAFISCALES
                        );
        $dt->setDataRows($parafiscales);
        $dt->setTitleRow($titulos);
        $dt->setEditLink("?mod=" . $modulo . "&niv=" . $niv . "&task=edit");
        $dt->setDeleteLink("?mod=" . $modulo . "&niv=" . $niv . "&task=delete");
        $dt->setAddLink("?mod=" . $modulo . "&niv=" . $niv . "&task=add");
        $dt->setType(1);
        $pag_crit = "";
        $dt->setPag(1, $pag_crit);
        $dt->writeDataTable($niv);

        break;
    /**
     * la variable add, permite hacer la carga la página con las variables que 
     * componen el objeto parafiscales ver la clase @see \CParafiscales.
     */
    case 'add':
        $estadosParafiscal = $daoParafiscales->getEstadoParafiscales();
        $juridicos = $daoParafiscales->getUsuariosJuridico();
        ?>
        <datalist id="estadosParafiscal">
            <?php foreach ($estadosParafiscal as $estadoParafiscal) { ?>
            <option value="<?= $estadoParafiscal->getIdEstadoParasfiscales(); ?>"><?= $estadoParafiscal->getDescripcion(); ?></option>   
            <?php } ?>
        </datalist>
        <datalist id="usuariosJuridico">
            <?php foreach ($juridicos as $juridico) { ?>
            <option value="<?= $juridico->getId(); ?>"><?= $juridico->getApellido().' '.$juridico->getNombre(); ?></option>   
            <?php } ?>
        </datalist>
        <?php
        $form = new CHtmlForm();
        $form->setTitle(TITULO_AGREGAR_PARAFISCALES);
        $form->setId('frm_add_manual');
        $form->setAction('?mod=' . $modulo . '&niv=1&task=saveAdd');
        $form->setMethod('post');
        $form->setClassEtiquetas('td_label');

        $form->addEtiqueta(PERIODO_PARAFISCALES);
        $form->addInputText('month', 'txt_periodo', 'txt_periodo', '50', '50', 
                            '', '', 'required');
        
        $form->addEtiqueta(FECHA_RADICACION_PARAFISCALES);
        $form->addInputDate('date', 'txt_fecha_radicacion_parafiscales', 'txt_fecha_radicacion_parafiscales', '', '%Y-%m-%d', 
                            '16', '16', '', 'pattern="' . PATTERN_FECHA . '" title="' . $html->traducirTildes(TITLE_FECHA) . '" required');
        
        $form->addEtiqueta(COMUNICADO_ENTREGA_SOPORTES_PARAFISCALES);
        $form->addInputText('text', 'txt_comunicado_entrega_soportes', 'txt_comunicado_entrega_soportes', '50', '50', 
                            '', '', ' title="' . $html->traducirTildes(TITLE_ALFANUMERICO) . '" required');
        
        $form->addEtiqueta(EVALUACION_CONTENIDO_DOCUMENTO);
        $form->addInputText('text', 'txt_evaluacion_contenido', 'txt_evaluacion_contenido', '1', '1', 
                            '', '', 'list="estadosParafiscal" placeholder="Seleccione uno..." required');
        
        $form->addEtiqueta(EVALUACION_REVISOR_FISCAL);
        $form->addInputText('text', 'txt_evaluacion_revisor', 'txt_evaluacion_revisor', '1', '1', 
                            '', '', 'list="estadosParafiscal" placeholder="Seleccione uno..." required');
        
        $form->addEtiqueta(USUARIO_PARAFISCALES);
        $form->addInputText('text', 'txt_usuario', 'txt_usuario', '50', '50', 
                            '', '', 'list="usuariosJuridico" placeholder="Seleccione uno..." required');
        
        $form->addEtiqueta(FECHA_COMUNICADO_CONCEPTO_INTERVENTORIA_PARAFISCALES);
        $form->addInputDate('date', 'txt_fecha_concepto_interventoria', 'txt_fecha_concepto_interventoria', '', '%Y-%m-%d', 
                            '16', '16', '', 'pattern="' . PATTERN_FECHA . '" title="' . $html->traducirTildes(TITLE_FECHA) . '" required');
        
        $form->addEtiqueta(COMUNICADO_CONCEPTO_PARAFISCALES);
        $form->addInputText('text', 'txt_comunicado_concepto', 'txt_comunicado_concepto', '50', '50', 
                            '', '', ' title="' . $html->traducirTildes(TITLE_ALFANUMERICO) . '" required');
        
        $form->addEtiqueta(OBSERVACIONES_PARAFISCALES);
        $form->addTextArea('textarea', 'txt_observaciones', 'txt_observaciones', 
                           100, 5, '', '', 
                           ' title="' . $html->traducirTildes(TITLE_ALFANUMERICO) . '" required');
        

        $form->addInputButton('submit', 'ok', 'ok', BTN_ACEPTAR, 'button', '');
        $form->addInputButton('button', 'cancel', 'cancel', BTN_CANCELAR, 
                              'button', 'onclick=location.href=\'?mod=' . $modulo . '&niv=1\'');

        $form->writeForm();

        break;
    /**
     * la variable saveAdd, permite almacenar el objeto parafiscales en la 
     * base de datos, @see \CParafiscalesData
     */
    case 'saveAdd':
        $periodo = $_REQUEST['txt_periodo'].'-00';
        $fechaRealizacionComunicado = $_REQUEST['txt_fecha_radicacion_parafiscales'];
        $comunicadoEntregaSoportes = $_REQUEST['txt_comunicado_entrega_soportes'];
        $evaluacionContenidoDocumento = new CEstadoParafiscales($_REQUEST['txt_evaluacion_contenido'], NULL);
        $evaluacionRevisorFiscal = new CEstadoParafiscales($_REQUEST['txt_evaluacion_revisor'], NULL);
        $juridico = new CUsuario($_REQUEST['txt_usuario'], NULL);
        $fechaComunicadoInterventoria = $_REQUEST['txt_fecha_concepto_interventoria'];
        $comunicadoConceptoInterventoria = $_REQUEST['txt_comunicado_concepto'];
        $observaciones = $_REQUEST['txt_observaciones'];
        
        $parafiscal = new CParafiscales(NULL, 
                                        $periodo, 
                                        $fechaRealizacionComunicado, 
                                        $comunicadoEntregaSoportes, 
                                        $evaluacionContenidoDocumento, 
                                        $evaluacionRevisorFiscal, 
                                        $juridico, 
                                        $fechaComunicadoInterventoria, 
                                        $comunicadoConceptoInterventoria, 
                                        $observaciones);
        $r = $daoParafiscales->insertParafiscal($parafiscal);
        $m = ERROR_AGREGAR_PARAFISCALES;
        if($r == TRUE){
            $m = EXITO_AGREGAR_PARAFISCALES;
        }
        echo $html->generaAviso($m, "?mod=" . $modulo . "&niv=1&task=list");
        break;
    /**
     * la variable delete, permite hacer la carga del objeto parafiscales y 
     * espera confirmacion de eliminarlo @see \CParafiscales
     */
    case 'delete':
        $id_delete = $_REQUEST['id_element'];
        echo $html->generaAdvertencia(CONFIRMAR_BORRAR_PARAFISCALES, 
                                      '?mod=' . $modulo . '&niv=1&task=confirmDelete&id_element=' . $id_delete, 
                                      'onclick=location.href=\'?mod=' . $modulo . '&niv=1\'');
        break;
    /**
     * la variable confirmDelete, permite eliminar el objeto parafiscal de 
     * la base de datos @see \CParafiscales
     */
    case 'confirmDelete':
        $id_delete = $_REQUEST['id_element'];
        $r = $daoParafiscales->deleteParafiscalById($id_delete);
        $m = ERROR_BORRAR_PARAFISCALES;
        if($r == TRUE){
            $m = EXITO_BORRAR_PARAFISCALES;
        }
        echo $html->generaAviso($m, "?mod=" . $modulo . "&niv=1&task=list");
        break;
        
    /**
     * la variable edit, permite hacer la carga del objeto parafiscales y 
     * espera confirmacion de edicion @see \CParafiscalesData
     */
    case 'edit':
        $id_edit = $_REQUEST['id_element'];
        $parafiscal = $daoParafiscales->getParafiscalById($id_edit);
        $estadosParafiscal = $daoParafiscales->getEstadoParafiscales();
        $juridicos = $daoParafiscales->getUsuariosJuridico();
        ?>
        <datalist id="estadosParafiscal">
            <?php foreach ($estadosParafiscal as $estadoParafiscal) { ?>
            <option value="<?= $estadoParafiscal->getIdEstadoParasfiscales(); ?>"><?= $estadoParafiscal->getDescripcion(); ?></option>   
            <?php } ?>
        </datalist>
        <datalist id="usuariosJuridico">
            <?php foreach ($juridicos as $juridico) { ?>
            <option value="<?= $juridico->getId(); ?>"><?= $juridico->getApellido().' '.$juridico->getNombre(); ?></option>   
            <?php } ?>
        </datalist>
        <?php
        $form = new CHtmlForm();
        $form->setTitle(TITULO_EDITAR_PARAFISCALES);
        $form->setId('frm_add_manual');
        $form->setAction('?mod=' . $modulo . '&niv=1&task=saveEdit&id='.$id_edit);
        $form->setMethod('post');
        $form->setClassEtiquetas('td_label');

        $form->addEtiqueta(PERIODO_PARAFISCALES);
        $form->addInputText('month', 'txt_periodo', 'txt_periodo', '50', '50', 
                            $parafiscal->getPeriodo(), '', 'required');
        
        $form->addEtiqueta(FECHA_RADICACION_PARAFISCALES);
        $form->addInputDate('date', 'txt_fecha_radicacion_parafiscales', 'txt_fecha_radicacion_parafiscales', $parafiscal->getFechaRealizacionComunicado(), '%Y-%m-%d', 
                            '16', '16', '', 'pattern="' . PATTERN_FECHA . '" title="' . $html->traducirTildes(TITLE_FECHA) . '" required');
        
        $form->addEtiqueta(COMUNICADO_ENTREGA_SOPORTES_PARAFISCALES);
        $form->addInputText('text', 'txt_comunicado_entrega_soportes', 'txt_comunicado_entrega_soportes', '50', '50', 
                            $parafiscal->getComunicadoEntregaSoportes(), '', ' title="' . $html->traducirTildes(TITLE_ALFANUMERICO) . '" required');
        
        $form->addEtiqueta(EVALUACION_CONTENIDO_DOCUMENTO);
        $form->addInputText('text', 'txt_evaluacion_contenido', 'txt_evaluacion_contenido', '1', '1', 
                            $parafiscal->getEvaluacionContenidoDocumento()->getIdEstadoParasfiscales(), '', 'list="estadosParafiscal" placeholder="Seleccione uno..." required');
        
        $form->addEtiqueta(EVALUACION_REVISOR_FISCAL);
        $form->addInputText('text', 'txt_evaluacion_revisor', 'txt_evaluacion_revisor', '1', '1', 
                            $parafiscal->getEvaluacionRevisorFiscal()->getIdEstadoParasfiscales(), '', 'list="estadosParafiscal" placeholder="Seleccione uno..." required');
        
        $form->addEtiqueta(USUARIO_PARAFISCALES);
        $form->addInputText('text', 'txt_usuario', 'txt_usuario', '50', '50', 
                            $parafiscal->getUsuario()->getId(), '', 'list="usuariosJuridico" placeholder="Seleccione uno..." required');
        
        $form->addEtiqueta(FECHA_COMUNICADO_CONCEPTO_INTERVENTORIA_PARAFISCALES);
        $form->addInputDate('date', 'txt_fecha_concepto_interventoria', 'txt_fecha_concepto_interventoria', $parafiscal->getFechaComunicadoInterventoria(), '%Y-%m-%d', 
                            '16', '16', '', 'pattern="' . PATTERN_FECHA . '" title="' . $html->traducirTildes(TITLE_FECHA) . '" required');
        
        $form->addEtiqueta(COMUNICADO_CONCEPTO_PARAFISCALES);
        $form->addInputText('text', 'txt_comunicado_concepto', 'txt_comunicado_concepto', '50', '50', 
                            $parafiscal->getComunicadoConceptoInterventoria(), '', ' title="' . $html->traducirTildes(TITLE_ALFANUMERICO) . '" required');
        
        $form->addEtiqueta(OBSERVACIONES_PARAFISCALES);
        $form->addTextArea('textarea', 'txt_observaciones', 'txt_observaciones', 
                           100, 5, $parafiscal->getObservaciones(), '', 
                           ' title="' . $html->traducirTildes(TITLE_ALFANUMERICO) . '" required');
        

        $form->addInputButton('submit', 'ok', 'ok', BTN_ACEPTAR, 'button', '');
        $form->addInputButton('button', 'cancel', 'cancel', BTN_CANCELAR, 
                              'button', 'onclick=location.href=\'?mod=' . $modulo . '&niv=1\'');

        $form->writeForm();

        break;
    /**
     * la variable saveEdit, permite actualizar el objeto parafiscales en 
     * la base de datos @see \CParafiscales
     */
    case 'saveEdit':
        $idParafiscal = $_REQUEST['id'];
        $periodo = $_REQUEST['txt_periodo'].'-00';
        $fechaRealizacionComunicado = $_REQUEST['txt_fecha_radicacion_parafiscales'];
        $comunicadoEntregaSoportes = $_REQUEST['txt_comunicado_entrega_soportes'];
        $evaluacionContenidoDocumento = new CEstadoParafiscales($_REQUEST['txt_evaluacion_contenido'], NULL);
        $evaluacionRevisorFiscal = new CEstadoParafiscales($_REQUEST['txt_evaluacion_revisor'], NULL);
        $juridico = new CUsuario($_REQUEST['txt_usuario'], NULL);
        $fechaComunicadoInterventoria = $_REQUEST['txt_fecha_concepto_interventoria'];
        $comunicadoConceptoInterventoria = $_REQUEST['txt_comunicado_concepto'];
        $observaciones = $_REQUEST['txt_observaciones'];
        
        $parafiscal = new CParafiscales($idParafiscal, 
                                        $periodo, 
                                        $fechaRealizacionComunicado, 
                                        $comunicadoEntregaSoportes, 
                                        $evaluacionContenidoDocumento, 
                                        $evaluacionRevisorFiscal, 
                                        $juridico, 
                                        $fechaComunicadoInterventoria, 
                                        $comunicadoConceptoInterventoria, 
                                        $observaciones);
        $r = $daoParafiscales->updateParafiscal($parafiscal);
        $m = ERROR_EDITAR_PARAFISCALES;
        if($r == TRUE){
            $m = EXITO_EDITAR_PARAFISCALES;
        }
        echo $html->generaAviso($m, '?mod=' . $modulo . '&niv=1');
        break;
    /**
     * en caso de que la variable task no este definida carga la página en construcción
     */
    default:
        include('templates/html/under.html');

        break;
}
?>
