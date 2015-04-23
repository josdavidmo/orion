<?php

/**
 * 
 *
 * <ul>
 * <li> Redcom Ltda <www.redcom.com.co></li>
 * <li> Proyecto PNCAV</li>
 * </ul>
 */
/**
 * Modulo Usuarios
 * maneja el modulo USUARIOS en union con CUsuario y CUsuarioData
 *
 * @see CUsuario
 * @see CUsuarioData

 * @package  modulos
 * @subpackage usuarios
 * @author Redcom Ltda
 * @version 2013.01.00
 * @copyright SERTIC - MINTICS
 */
//no permite el acceso directo
defined('_VALID_PRY') or die('Restricted access');
$task = $_REQUEST['task'];
if (empty($task))
    $task = 'list';

switch ($task) {
    /**
     * la variable list, permite hacer la carga la página con la lista de objetos USUARIO según los parámetros de entrada
     */
    case 'list':
        $form= new CHtmlForm();   
		$form->setTitle("Personal PNCAV");		
        $form->setId('frm_list_user');
        $form->setMethod('post');
        $form->setClassEtiquetas('td_label');   
        $form->setOptions('autoClean', false);
        //$form->addInputButton('button', 'cancel', 'cancel', BTN_EXPORTAR, 'button', 'onClick=location.href="modulos/usuarios/usuarios_en_excel.php"');
        $form->writeForm();
        if ($id_usuario != 1) {
            $criterio_list = 'usu_id <> 1';
        } else {
            $criterio_list = '1';
        }
        $usuarios = $du->getPersonal($criterio_list, 'usu_login');
        $dt = new CHtmlDataTable();
        $titulos = array(USUARIO_NOMBRE, USUARIO_APELLIDO, USUARIO_DOCUMENTO, 
						 USUARIO_CORREO);
        $dt->setDataRows($usuarios);
        $dt->setTitleRow($titulos);

        $dt->setTitleTable(TABLA_USUARIOS);

        $dt->setType(1);
        $pag_crit = "";
        $dt->setPag(1, $pag_crit);
        $dt->writeDataTable($niv);

        break;
    
    /**
     * en caso de que la variable task no este definida carga la página en construcción
     */
    default:
        include('templates/html/under.html');

        break;
}
?>