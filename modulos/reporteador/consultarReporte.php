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
 * Modulo Incidencias
 * maneja el modulo INCIDENCIAS en union con CIncidencia y CIncidenciaData
 *
 * @see CIncidencia
 * @see CIncidenciaData
 *
 * @package  modulos
 * @subpackage incidencias
 * @author Redcom Ltda
 * @version 2014.09.07
 * @copyright SERTIC - MINTICS
 */

defined('_VALID_PRY') or die('Restricted access');

$conData = new CConsultaData($db);

if (empty($_REQUEST['task'])){
    $task = 'list';
}

switch($task){
    case 'list':
        $id = $_REQUEST['id_element'];
        $reporte = $conData->getReporteById($id);
        $consulta= unserialize($reporte['consulta']);
        
        $consulta->prepararConsulta();
        $titulos = $consulta->getTitulos();
		
        $form = new CHtmlForm();
        $form->setId('frm_consulta');
        $form->setMethod('post');
        $form->setOptions('autoClean', false);
        $form->setTitle($reporte['nombre']);
		$form->writeForm();
		
        $consulta->prepararConsulta();
        $elementos=$consulta->ejecutarConsulta($conData);       
        $dt = new CHtmlDataTable();
        $dt->setDataRows($elementos);
        $dt->setTitleRow($titulos);
        $dt->setType(1);
        $pag_crit = "";
        $dt->setPag(1, $pag_crit);
        $dt->setTitleTable(CONSULTA);
        $dt->writeDataTable($niv);
        break;
}
?>
