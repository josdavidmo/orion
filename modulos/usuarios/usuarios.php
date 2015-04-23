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
        $nombre = $_REQUEST['txt_nombre'];
        $criterio_list = "1";
        if($nombre != NULL){
            $criterio_list .= " AND CONCAT(usu_nombre,' ',usu_apellido) LIKE '%".$nombre."%'";
        }
        if ($id_usuario != 1) {
            $criterio_list .= " AND usu_id <> 1";
        } 
        
        
        $form = new CHtmlForm();   
        $form->setTitle("Usuarios");		
        $form->setId('frm_list_user');
        $form->setMethod('post');
        $form->setClassEtiquetas('td_label');
        $form->setAction('?mod=' . $modulo . '&niv=' . $niv);
        $form->setId('form_filtro');
        $form->addEtiqueta(USUARIO_NOMBRE);
        $form->addInputText('text', 'txt_nombre', 'txt_nombre', '19', '19', $nombre, '', ' pattern="' . PATTERN_ALFANUMERICO . '" title="' . $html->traducirTildes(TITLE_ALFANUMERICO) . '"');
        $form->addInputButton('submit', 'btn_enviar', 'btn_enviar', BTN_ACEPTAR, 'button', '');
        $form->addInputButton('button', 'cancel', 'cancel', BTN_EXPORTAR, 'button', 'onClick=location.href="modulos/usuarios/usuarios_en_excel.php"');
        $form->writeForm();
        
        $usuarios = $du->getUsers($criterio_list, 'usu_login');
        $dt = new CHtmlDataTable();
        $titulos = array(USUARIO_LOGIN, USUARIO_NOMBRE, USUARIO_APELLIDO, USUARIO_DOCUMENTO, 
                         USUARIO_TELEFONO, USUARIO_PERFIL, USUARIO_CORREO, USUARIO_ESTADO, USUARIO_FECHA);
        $dt->setDataRows($usuarios);
        $dt->setTitleRow($titulos);

        $dt->setTitleTable(TABLA_USUARIOS);

        $dt->setSeeLink("?mod=" . $modulo . "&niv=" . $nivel . "&task=see");
        $dt->setEditLink("?mod=" . $modulo . "&niv=" . $nivel . "&task=edit");
        $dt->setDeleteLink("?mod=" . $modulo . "&niv=" . $nivel . "&task=delete");

        $dt->setAddLink("?mod=" . $modulo . "&niv=" . $nivel . "&task=add");

        $dt->setType(1);
        $pag_crit = "";
        $dt->setPag(1, $pag_crit);
        $dt->writeDataTable($niv);

        break;
    /**
     * la variable add, permite hacer la carga la página con las variables que componen el objeto USUARIO, ver la clase CUsuario
     */
    case 'add':
        $form = new CHtmlForm();
        $form->setTitle(AGREGAR_USUARIO);
        $form->setId('frm_add_user');
        $form->setMethod('post');
        $form->setClassEtiquetas('td_label');
        $form->setAction('?mod=' . $modulo . '&niv=' . $niv . '&task=saveAdd');
                
        $form->addEtiqueta(USUARIO_LOGIN);
        $form->addInputText('text', 'txt_login', 'txt_login', '15', '15', '', '', 'autofocus pattern="' . PATTERN_ALFANUMERICO . '" title="' . $html->traducirTildes(TITLE_ALFANUMERICO) . '" required');
        
        $form->addEtiqueta(USUARIO_PASSWORD);
        $form->addInputText('password', 'txt_password', 'txt_password', '15', '15', '', '', 'pattern="' . PATTERN_ALFANUMERICO . '" title="' . $html->traducirTildes(TITLE_ALFANUMERICO) . '" ');
        
        $perfiles = $du->getPerfiles('1', 'per_nombre');
        $opciones = null;
        foreach ($perfiles as $s) {
            $opciones[count($opciones)] = array('value' => $s['id'], 'texto' => $s['nombre']);
        }
        $form->addEtiqueta(USUARIO_PERFIL);
        $form->addSelect('select', 'sel_perfil', 'sel_perfil', $opciones, USUARIO_PERFIL, $perfil, '', '');
        
        $form->addEtiqueta(USUARIO_DOCUMENTO);
        $form->addInputText('text', 'txt_documento', 'txt_documento', '15', '20', '', '', 'onkeyup="formatearNumero(this);" pattern="' . PATTERN_NUMEROS_FINANCIEROS . '" title="' . $html->traducirTildes(TITLE_NUMEROS_FINANCIEROS) . '" required');
        
        $form->addEtiqueta(USUARIO_NOMBRE);
        $form->addInputText('text', 'txt_nombre', 'txt_nombre', '20', '60', '', '', 'pattern="'.PATTERN_PALABRAS_ESPACIOS.'"title="'.$html->traducirTildes(TITLE_PALABRAS_ESPACIOS).'" required');
        
        $form->addEtiqueta(USUARIO_APELLIDO);
        $form->addInputText('text', 'txt_apellido', 'txt_apellido', '20', '60', '', '', 'pattern="'.PATTERN_PALABRAS_ESPACIOS.'"title="'.$html->traducirTildes(TITLE_PALABRAS_ESPACIOS).'" required');
        
        $form->addEtiqueta(USUARIO_RH);
        $form->addInputText('text', 'txt_rh', 'txt_rh', '20', '3', '', '', 'title="'.$html->traducirTildes('Digite su RH').'" ');
        
        $form->addEtiqueta(USUARIO_FECHA_INGRESO);
        $form->addInputDate('date', 'txt_fecha_ingreso', 'txt_fecha_ingreso', '', '%Y-%m-%d', '16', '16', '', 'pattern="' . PATTERN_FECHA . '" title="' . $html->traducirTildes(TITLE_FECHA) . '" readonly ');

        $form->addEtiqueta(USUARIO_REGIONAL);
        $form->addInputText('text', 'txt_regional', 'txt_regional', '20', '60', '', '', 'pattern="'.PATTERN_PALABRAS_ESPACIOS.'"title="'.$html->traducirTildes(TITLE_PALABRAS_ESPACIOS).'" ');
        
        $form->addEtiqueta(USUARIO_CARGO);
        $form->addInputText('text', 'txt_cargo', 'txt_cargo', '20', '60', '', '', 'pattern="'.PATTERN_PALABRAS_ESPACIOS.'"title="'.$html->traducirTildes(TITLE_PALABRAS_ESPACIOS).'" ');
        
        $form->addEtiqueta(USUARIO_CORREO_CORPORATIVO);
        $form->addInputText('text', 'txt_correo_corporativo', 'txt_correo_corporativo', '20', '60', '', '', 'pattern="'.PATTERN_EMAIL.'" title="' . $html->traducirTildes(TITLE_EMAIL) . '" ');
        
        $form->addEtiqueta(USUARIO_CUENTA_BANCO);
        $form->addInputText('text', 'txt_cuenta_banco', 'txt_cuenta_banco', '20', '20', '', '', 'pattern="'.PATTERN_NUMEROS.'"title="'.$html->traducirTildes(TITLE_NUMEROS).'" ');
        
        $form->addEtiqueta(USUARIO_CELULAR);
        $form->addInputText('text', 'txt_celular', 'txt_celular', '15', '10', '', '', 'pattern="' . PATTERN_NUMEROS . '" title="' . $html->traducirTildes(TITLE_NUMEROS) . '" ');
        
        $form->addEtiqueta(USUARIO_CELULAR_CORPORATIVO);
        $form->addInputText('text', 'txt_celular_corporativo', 'txt_celular_corporativo', '20', '10', '', '', 'pattern="'.PATTERN_NUMEROS.'"title="'.$html->traducirTildes(TITLE_NUMEROS).'" ');
        
        $form->addEtiqueta(USUARIO_TELEFONO);
        $form->addInputText('text', 'txt_telefono', 'txt_telefono', '15', '10', '', '', 'pattern="' . PATTERN_NUMEROS . '" title="' . $html->traducirTildes(TITLE_NUMEROS) . '" ');
                
        $form->addEtiqueta(USUARIO_CORREO);
        $form->addInputText('text', 'txt_correo', 'txt_correo', '30', '200', '', '', 'pattern="'.PATTERN_EMAIL.'" title="' . $html->traducirTildes(TITLE_EMAIL) . '" ');
        
        $form->addEtiqueta(USUARIO_CIUDAD);
        $form->addInputText('text', 'txt_ciudad', 'txt_ciudad', '20', '200', '', '', 'pattern="'.PATTERN_PALABRAS_ESPACIOS.'"title="'.$html->traducirTildes(TITLE_PALABRAS_ESPACIOS).'" ');
        
        $form->addEtiqueta(USUARIO_DIRECCION);
        $form->addInputText('text', 'txt_direccion', 'txt_direccion', '20', '200', '', '', ' ');
        
        $form->addEtiqueta(USUARIO_FECHA_DE_NACIMIENTO);
        $form->addInputDate('date', 'txt_fecha_nacimiento', 'txt_fecha_nacimiento', '', '%Y-%m-%d', '16', '16', '', 'pattern="' . PATTERN_FECHA . '" title="' . $html->traducirTildes(TITLE_FECHA) . '" readonly ');

        $form->addEtiqueta(USUARIO_CONTACTO_EMERGENCIA);
        $form->addInputText('text', 'txt_contacto_emergencia', 'txt_contacto_emergencia', '20', '200', '', '', 'pattern="'.PATTERN_PALABRAS_ESPACIOS.'"title="'.$html->traducirTildes(TITLE_PALABRAS_ESPACIOS).'" ');
        
        $form->addEtiqueta(USUARIO_CONTACTO_EMERGENCIA_CELULAR);
        $form->addInputText('text', 'txt_celular_contacto', 'txt_celular_contacto', '20', '10', '', '', 'pattern="'.PATTERN_NUMEROS.'"title="'.$html->traducirTildes(TITLE_NUMEROS).'" ');
        
        $form->addEtiqueta(USUARIO_FECHA_APROBACION);
        $form->addInputDate('date', 'txt_fecha_aprovacion', 'txt_fecha_aprovacion', '', '%Y-%m-%d', '16', '16', '', 'pattern="' . PATTERN_FECHA . '" title="' . $html->traducirTildes(TITLE_FECHA) . '" readonly ');

        $form->addEtiqueta(USUARIO_ARL);
        $form->addInputText('text', 'txt_arl', 'txt_arl', '20', '200', '', '', 'pattern="'.PATTERN_PALABRAS_ESPACIOS.'"title="'.$html->traducirTildes(TITLE_PALABRAS_ESPACIOS).'" ');
             
        $form->addEtiqueta(USUARIO_EPS);
        $form->addInputText('text', 'txt_eps', 'txt_eps', '20', '200', '', '', 'pattern="'.PATTERN_PALABRAS_ESPACIOS.'"title="'.$html->traducirTildes(TITLE_PALABRAS_ESPACIOS).'" ');
             
        $form->addEtiqueta(USUARIO_ALERGIA);
        $form->addInputText('text', 'txt_alergia', 'txt_alergia', '20', '400', '', '', 'pattern="'.PATTERN_PALABRAS_ESPACIOS.'"title="'.$html->traducirTildes(TITLE_PALABRAS_ESPACIOS).'" ');
             
        $form->addEtiqueta(USUARIO_ANTECEDENTES_ENFERMEDAD);
        $form->addInputText('text', 'txt_antecedentes_enfermedad', 'txt_antecedentes_enfermedad', '20', '400', '', '', 'pattern="'.PATTERN_PALABRAS_ESPACIOS.'"title="'.$html->traducirTildes(TITLE_PALABRAS_ESPACIOS).'" ');
             
        $form->addEtiqueta(USUARIO_MEDICAMENTOS);
        $form->addInputText('text', 'txt_medicamentos', 'txt_medicamentos', '20', '400', '', '', ' title="'.$html->traducirTildes(TITLE_PALABRAS_ESPACIOS).'" ');
        
        $estados = $du->getTipoActividad();
        $opciones = null;
        foreach ($estados as $s) {
            $opciones[count($opciones)] = array('value' => $s['id'], 'texto' => $s['nombre']);
        }

        $form->addEtiqueta(USUARIO_ESTADO);
        $form->addSelect('select', 'sel_estado', 'sel_estado', $opciones, USUARIO_ESTADO, $estado, '', '');
        
        $form->addInputButton('submit', 'ok', 'ok', BTN_ACEPTAR, 'button', '');
        $form->addInputButton('button', 'cancel', 'cancel', BTN_CANCELAR, 'button', 'onClick=location.href="?mod='.$modulo.'&niv='.$niv.'"');
        
        $form->writeForm();

        break;
    /**
     * la variable saveAdd, permite almacenar el objeto USUARIO en la base de datos, ver la clase CUsuarioData
     */
    case 'saveAdd':
        $login = $_POST['txt_login'];
        $password = $_POST['txt_password'];
        $documento = $_POST['txt_documento'];
        $nombre = $_POST['txt_nombre'];
        $apellido = $_POST['txt_apellido'];
        $telefono = $_POST['txt_telefono'];
        $celular = $_POST['txt_celular'];
        $correo = $_POST['txt_correo'];
        $perfil = $_POST['sel_perfil'];
        $estado = $_POST['sel_estado'];
        $rh = $_POST['txt_rh'];
        $fecha_ingreso = $_POST['txt_fecha_ingreso'];
        $regional = $_POST['txt_regional'];
        $cargo = $_POST['txt_cargo'];
        $correo_corporativo = $_POST['txt_correo_corporativo'];
        $cuenta_banco = $_POST['txt_cuenta_banco'];
        $celular_corporativo = $_POST['txt_celular_corporativo'];
        $ciudad = $_POST['txt_ciudad'];
        $direccion = $_POST['txt_direccion'];
        $fecha_nacimiento = $_POST['txt_fecha_nacimiento'];
        $contacto_emergencia = $_POST['txt_contacto_emergencia'];
        $fecha_aprovacion = $_POST['txt_fecha_aprovacion'];
        $arl = $_POST['txt_arl'];
        $eps = $_POST['txt_eps'];
        $alergia = $_POST['txt_alergia'];
        $antecedentes_enfermedad = $_POST['txt_antecedentes_enfermedad'];
        $medicamentos = $_POST['txt_medicamentos'];
        $fecha = date("Y-m-d");
        $telefonoContacto=$_POST['txt_celular_contacto'];
        $usuario = new CUsuario('', $du);

        $usuario->setLogin($login);
        $usuario->setPassword($password);
        $usuario->setNombre($nombre);
        $usuario->setApellido($apellido);
        $usuario->setRh($rh);
        $usuario->setDocumento($documento);
        $usuario->setTelefono($telefono);
        $usuario->setCelular($celular);
        $usuario->setCorreo($correo);
        $usuario->setPerfil($perfil);
        $usuario->setEstado($estado);
        $usuario->setFecha($fecha);
        $usuario->setFecha_ingreso($fecha_ingreso);
        $usuario->setRegional($regional);
        $usuario->setCargo($cargo);
        $usuario->setCorreo_corporativo($correo_corporativo);
        $usuario->setCuenta_banco($cuenta_banco);
        $usuario->setCelular_corporativo($celular_corporativo);
        $usuario->setCiudad($ciudad);
        $usuario->setDireccion($direccion);
        $usuario->setFecha_nacimiento($fecha_nacimiento);
        $usuario->setContacto_emergencia($contacto_emergencia);
        $usuario->setFecha_aprovacion($fecha_aprovacion);
        $usuario->setArl($arl);
        $usuario->setEps($eps);
        $usuario->setAlergia($alergia);
        $usuario->setAntecedentes_enfermedad($antecedentes_enfermedad);
        $usuario->setMedicamentos($medicamentos);
        $usuario->setTelefonoContacto($telefonoContacto);
        $m = $usuario->saveNewUser();

        echo $html->generaAviso($m, '?mod=usuarios&niv=1');

        break;
    /**
     * la variable delete, permite hacer la carga del objeto USUARIO y espera confirmacion de eliminarlo, ver la clase CUsuario
     */
    case 'delete':
        $id_delete = $_REQUEST['id_element'];
        $usuario = new CUsuario($id_delete, $du);

        $form = new CHtmlForm();
        $form->setId('frm_delet_user');
        $form->setMethod('post');

        $form->addInputText('hidden', 'id_element', 'id_element', '15', '15', $usuario->getId(), '', '');

        $form->writeForm();

        echo $html->generaAdvertencia(USUARIO_MSG_BORRADO, '?mod=usuarios&niv=1&task=confirmDelete&id_element=' . $id_delete, "cancelarAccion('frm_delet_user','?mod=usuarios&niv=1')");

        break;
    /**
     * la variable confirmDelete, permite eliminar el objeto USUARIO de la base de datos, ver la clase CUsuarioData
     */
    case 'confirmDelete':
        $id_delete = $_REQUEST['id_element'];
        $usuario = new CUsuario($id_delete, $du);
        $m = $usuario->deleteUser();

        echo $html->generaAviso($m, '?mod=usuarios&niv=1');

        break;
    /**
     * la variable edit, permite hacer la carga del objeto USUARIO y espera confirmacion de edicion, ver la clase CUsuario
     */
    case 'edit':
        $id_edit = $_REQUEST['id_element'];
        $usuario = new CUsuario($id_edit, $du);
        $usuario->loadUser();
        
        $form = new CHtmlForm();
        $form->setTitle(EDITAR_USUARIO);
        $form->setId('frm_add_user');
        $form->setMethod('post');
        $form->setClassEtiquetas('td_label');
        $form->setAction('?mod=' . $modulo . '&niv=' . $niv . '&task=saveEdit&id_element='.$id_edit);
                
        $form->addEtiqueta(USUARIO_LOGIN);
        $form->addInputText('text', 'txt_login', 'txt_login', '15', '15', $usuario->getLogin(), '', 'autofocus pattern="' . PATTERN_ALFANUMERICO . '" title="' . $html->traducirTildes(TITLE_ALFANUMERICO) . '" required');
        
        $form->addEtiqueta(USUARIO_PASSWORD);
        $form->addInputText('password', 'txt_password', 'txt_password', '15', '15', '', '', 'pattern="' . PATTERN_ALFANUMERICO . '" title="' . $html->traducirTildes(TITLE_ALFANUMERICO) . '" ');
        
        $perfiles = $du->getPerfiles('1', 'per_nombre');
        $opciones = null;
        foreach ($perfiles as $s) {
            $opciones[count($opciones)] = array('value' => $s['id'], 'texto' => $s['nombre']);
        }
        $form->addEtiqueta(USUARIO_PERFIL);
        $form->addSelect('select', 'sel_perfil', 'sel_perfil', $opciones, USUARIO_PERFIL, $usuario->getPerfil(), '', '');
        
        $form->addEtiqueta(USUARIO_DOCUMENTO);
        $form->addInputText('text', 'txt_documento', 'txt_documento', '15', '20', $usuario->getDocumento(), '', 'onkeyup="formatearNumero(this);" pattern="' . PATTERN_NUMEROS_FINANCIEROS . '" title="' . $html->traducirTildes(TITLE_NUMEROS_FINANCIEROS) . '" required');
        
        $form->addEtiqueta(USUARIO_NOMBRE);
        $form->addInputText('text', 'txt_nombre', 'txt_nombre', '20', '60', $usuario->getNombre(), '', 'pattern="'.PATTERN_PALABRAS_ESPACIOS.'"title="'.$html->traducirTildes(TITLE_PALABRAS_ESPACIOS).'" required');
        
        $form->addEtiqueta(USUARIO_APELLIDO);
        $form->addInputText('text', 'txt_apellido', 'txt_apellido', '20', '60', $usuario->getApellido(), '', 'pattern="'.PATTERN_PALABRAS_ESPACIOS.'"title="'.$html->traducirTildes(TITLE_PALABRAS_ESPACIOS).'" required');
        
        $form->addEtiqueta(USUARIO_RH);
        $form->addInputText('text', 'txt_rh', 'txt_rh', '20', '3', $usuario->getRh(), '', ' title="'.$html->traducirTildes('Digite su RH').'" ');
        
        $form->addEtiqueta(USUARIO_FECHA_INGRESO);
        $form->addInputDate('date', 'txt_fecha_ingreso', 'txt_fecha_ingreso', $usuario->getFecha_ingreso(), '%Y-%m-%d', '16', '16', '', 'pattern="' . PATTERN_FECHA . '" title="' . $html->traducirTildes(TITLE_FECHA) . '" readonly ');

        $form->addEtiqueta(USUARIO_REGIONAL);
        $form->addInputText('text', 'txt_regional', 'txt_regional', '20', '60', $usuario->getRegional(), '', 'pattern="'.PATTERN_PALABRAS_ESPACIOS.'"title="'.$html->traducirTildes(TITLE_PALABRAS_ESPACIOS).'" ');
        
        $form->addEtiqueta(USUARIO_CARGO);
        $form->addInputText('text', 'txt_cargo', 'txt_cargo', '20', '60', $usuario->getCargo(), '', 'pattern="'.PATTERN_PALABRAS_ESPACIOS.'"title="'.$html->traducirTildes(TITLE_PALABRAS_ESPACIOS).'" ');
        
        $form->addEtiqueta(USUARIO_CORREO_CORPORATIVO);
        $form->addInputText('text', 'txt_correo_corporativo', 'txt_correo_corporativo', '20', '60', $usuario->getCorreo_corporativo(), '', 'pattern="'.PATTERN_EMAIL.'" title="' . $html->traducirTildes(TITLE_EMAIL) . '" ');
        
        $form->addEtiqueta(USUARIO_CUENTA_BANCO);
        $form->addInputText('text', 'txt_cuenta_banco', 'txt_cuenta_banco', '20', '20', $usuario->getCuenta_banco(), '', 'pattern="'.PATTERN_NUMEROS.'"title="'.$html->traducirTildes(TITLE_NUMEROS).'" ');
        
        $form->addEtiqueta(USUARIO_CELULAR);
        $form->addInputText('text', 'txt_celular', 'txt_celular', '15', '10', $usuario->getCelular(), '', 'pattern="' . PATTERN_NUMEROS . '" title="' . $html->traducirTildes(TITLE_NUMEROS) . '" ');
        
        $form->addEtiqueta(USUARIO_CELULAR_CORPORATIVO);
        $form->addInputText('text', 'txt_celular_corporativo', 'txt_celular_corporativo', '20', '10', $usuario->getCelular_corporativo(), '', 'pattern="'.PATTERN_NUMEROS.'"title="'.$html->traducirTildes(TITLE_NUMEROS).'" ');
        
        $form->addEtiqueta(USUARIO_TELEFONO);
        $form->addInputText('text', 'txt_telefono', 'txt_telefono', '15', '10', $usuario->getTelefono(), '', 'pattern="' . PATTERN_NUMEROS . '" title="' . $html->traducirTildes(TITLE_NUMEROS) . '" ');
                
        $form->addEtiqueta(USUARIO_CORREO);
        $form->addInputText('text', 'txt_correo', 'txt_correo', '30', '200', $usuario->getCorreo(), '', 'pattern="'.PATTERN_EMAIL.'" title="' . $html->traducirTildes(TITLE_EMAIL) . '" ');
        
        $form->addEtiqueta(USUARIO_CIUDAD);
        $form->addInputText('text', 'txt_ciudad', 'txt_ciudad', '20', '200', $usuario->getCiudad(), '', 'pattern="'.PATTERN_PALABRAS_ESPACIOS.'"title="'.$html->traducirTildes(TITLE_PALABRAS_ESPACIOS).'" ');
        
        $form->addEtiqueta(USUARIO_DIRECCION);
        $form->addInputText('text', 'txt_direccion', 'txt_direccion', '20', '200', $usuario->getDireccion(), '', ' ');
        
        $form->addEtiqueta(USUARIO_FECHA_DE_NACIMIENTO);
        $form->addInputDate('date', 'txt_fecha_nacimiento', 'txt_fecha_nacimiento', $usuario->getFecha_nacimiento(), '%Y-%m-%d', '16', '16', '', 'pattern="' . PATTERN_FECHA . '" title="' . $html->traducirTildes(TITLE_FECHA) . '" readonly ');

        $form->addEtiqueta(USUARIO_CONTACTO_EMERGENCIA);
        $form->addInputText('text', 'txt_contacto_emergencia', 'txt_contacto_emergencia', '20', '200', $usuario->getContacto_emergencia(), '', 'pattern="'.PATTERN_PALABRAS_ESPACIOS.'"title="'.$html->traducirTildes(TITLE_PALABRAS_ESPACIOS).'" ');
        
        $form->addEtiqueta(USUARIO_CONTACTO_EMERGENCIA_CELULAR);
        $form->addInputText('text', 'txt_celular_contacto', 'txt_celular_contacto', '20', '10', $usuario->getTelefonoContacto(), '', 'pattern="'.PATTERN_NUMEROS.'"title="'.$html->traducirTildes(TITLE_NUMEROS).'"  ');
        
        $form->addEtiqueta(USUARIO_FECHA_APROBACION);
        $form->addInputDate('date', 'txt_fecha_aprovacion', 'txt_fecha_aprovacion', $usuario->getFecha_aprovacion(), '%Y-%m-%d', '16', '16', '', 'pattern="' . PATTERN_FECHA . '" title="' . $html->traducirTildes(TITLE_FECHA) . '" readonly ');

        $form->addEtiqueta(USUARIO_ARL);
        $form->addInputText('text', 'txt_arl', 'txt_arl', '20', '200', $usuario->getArl(), '', 'pattern="'.PATTERN_PALABRAS_ESPACIOS.'"title="'.$html->traducirTildes(TITLE_PALABRAS_ESPACIOS).'" ');
             
        $form->addEtiqueta(USUARIO_EPS);
        $form->addInputText('text', 'txt_eps', 'txt_eps', '20', '200', $usuario->getEps(), '', 'pattern="'.PATTERN_PALABRAS_ESPACIOS.'"title="'.$html->traducirTildes(TITLE_PALABRAS_ESPACIOS).'" ');
             
        $form->addEtiqueta(USUARIO_ALERGIA);
        $form->addInputText('text', 'txt_alergia', 'txt_alergia', '20', '400', $usuario->getAlergia(), '', 'pattern="'.PATTERN_PALABRAS_ESPACIOS.'"title="'.$html->traducirTildes(TITLE_PALABRAS_ESPACIOS).'" ');
             
        $form->addEtiqueta(USUARIO_ANTECEDENTES_ENFERMEDAD);
        $form->addInputText('text', 'txt_antecedentes_enfermedad', 'txt_antecedentes_enfermedad', '20', '400', $usuario->getAntecedentes_enfermedad(), '', 'pattern="'.PATTERN_PALABRAS_ESPACIOS.'"title="'.$html->traducirTildes(TITLE_PALABRAS_ESPACIOS).'" ');
             
        $form->addEtiqueta(USUARIO_MEDICAMENTOS);
        $form->addInputText('text', 'txt_medicamentos', 'txt_medicamentos', '20', '400', $usuario->getMedicamentos(), '', ' title="'.$html->traducirTildes(TITLE_PALABRAS_ESPACIOS).'" ');
        
        $estados = $du->getTipoActividad();
        $opciones = null;
        foreach ($estados as $s) {
            $opciones[count($opciones)] = array('value' => $s['id'], 'texto' => $s['nombre']);
        }

        $form->addEtiqueta(USUARIO_ESTADO);
        $form->addSelect('select', 'sel_estado', 'sel_estado', $opciones, USUARIO_ESTADO, $usuario->getEstado(), '', '');
              
        $form->addInputButton('submit', 'ok', 'ok', BTN_ACEPTAR, 'button', '');
        $form->addInputButton('button', 'cancel', 'cancel', BTN_CANCELAR, 'button', 'onClick=location.href="?mod='.$modulo.'&niv='.$niv.'"');

        $form->writeForm();

        

        break;
    /**
     * la variable saveEdit, permite actualizar el objeto USUARIO en la base de datos, ver la clase CUsuarioData
     */
    case 'saveEdit':
        $id = $_REQUEST ['id_element'];
        $login = $_POST['txt_login'];
        $password = $_POST['txt_password'];
        $documento = $_POST['txt_documento'];
        $nombre = $_POST['txt_nombre'];
        $apellido = $_POST['txt_apellido'];
        $telefono = $_POST['txt_telefono'];
        $celular = $_POST['txt_celular'];
        $correo = $_POST['txt_correo'];
        $perfil = $_POST['sel_perfil'];
        $estado = $_POST['sel_estado'];
        $rh = $_POST['txt_rh'];
        $fecha_ingreso = $_POST['txt_fecha_ingreso'];
        $regional = $_POST['txt_regional'];
        $cargo = $_POST['txt_cargo'];
        $correo_corporativo = $_POST['txt_correo_corporativo'];
        $cuenta_banco = $_POST['txt_cuenta_banco'];
        $celular_corporativo = $_POST['txt_celular_corporativo'];
        $ciudad = $_POST['txt_ciudad'];
        $direccion = $_POST['txt_direccion'];
        $fecha_nacimiento = $_POST['txt_fecha_nacimiento'];
        $contacto_emergencia = $_POST['txt_contacto_emergencia'];
        $fecha_aprovacion = $_POST['txt_fecha_aprovacion'];
        $arl = $_POST['txt_arl'];
        $eps = $_POST['txt_eps'];
        $alergia = $_POST['txt_alergia'];
        $antecedentes_enfermedad = $_POST['txt_antecedentes_enfermedad'];
        $medicamentos = $_POST['txt_medicamentos'];
        $telefonoContacto=$_POST['txt_celular_contacto'];
        $usuario = new CUsuario($id, $du);
        
        $usuario->setLogin($login);
        $usuario->setPassword($password);
        $usuario->setNombre($nombre);
        $usuario->setApellido($apellido);
        $usuario->setRh($rh);
        $usuario->setDocumento($documento);
        $usuario->setTelefono($telefono);
        $usuario->setCelular($celular);
        $usuario->setCorreo($correo);
        $usuario->setPerfil($perfil);
        $usuario->setEstado($estado);
        $usuario->setFecha($fecha);
        $usuario->setFecha_ingreso($fecha_ingreso);
        $usuario->setRegional($regional);
        $usuario->setCargo($cargo);
        $usuario->setCorreo_corporativo($correo_corporativo);
        $usuario->setCuenta_banco($cuenta_banco);
        $usuario->setCelular_corporativo($celular_corporativo);
        $usuario->setCiudad($ciudad);
        $usuario->setDireccion($direccion);
        $usuario->setFecha_nacimiento($fecha_nacimiento);
        $usuario->setContacto_emergencia($contacto_emergencia);
        $usuario->setFecha_aprovacion($fecha_aprovacion);
        $usuario->setArl($arl);
        $usuario->setEps($eps);
        $usuario->setAlergia($alergia);
        $usuario->setAntecedentes_enfermedad($antecedentes_enfermedad);
        $usuario->setMedicamentos($medicamentos);
        $usuario->setTelefonoContacto($telefonoContacto);
        $m = $usuario->saveEditUser();
        echo $html->generaAviso($m, '?mod=usuarios&niv=1');

        break;
    /**
     * la variable see, permite hacer la carga del objeto USUARIO para ver sus variables, ver la clase CUsuario
     */
    case 'see':
        $id_edit = $_REQUEST['id_element'];
        $usuario = new CUsuario($id_edit, $du);
        $usuario->loadUser();
        
        $form = new CHtmlForm();
        $form->setTitle($usuario->getNombre().' '.$usuario->getApellido());
        $form->setId('frm_see_usuario');
        $form->setMethod('post');
        $form->setClassEtiquetas('td_label');
        $form->setAction('?mod=' . $modulo . '&niv=' . $niv);
        $form->setOptions('autoClean', false);
                
        $form->addEtiqueta(USUARIO_LOGIN);
        $form->addInputText('text', 'txt_login', 'txt_login', '15', '15', $usuario->getLogin(), '', 'autofocus pattern="' . PATTERN_ALFANUMERICO . '" title="' . $html->traducirTildes(TITLE_ALFANUMERICO) . '"  readonly ');
        
        $form->addEtiqueta(USUARIO_PASSWORD);
        $form->addInputText('password', 'txt_password', 'txt_password', '15', '15', '', '', 'pattern="' . PATTERN_ALFANUMERICO . '" title="' . $html->traducirTildes(TITLE_ALFANUMERICO) . '"  readonly ');
        
        $perfiles = $du->getPerfiles('1', 'per_nombre');
        $opciones = null;
        foreach ($perfiles as $s) {
            $opciones[count($opciones)] = array('value' => $s['id'], 'texto' => $s['nombre']);
        }
        $form->addEtiqueta(USUARIO_PERFIL);
        $form->addSelect('select', 'sel_perfil', 'sel_perfil', $opciones, USUARIO_PERFIL, $usuario->getPerfil(), '', '  readonly ');
        
        $form->addEtiqueta(USUARIO_DOCUMENTO);
        $form->addInputText('text', 'txt_documento', 'txt_documento', '15', '20', $usuario->getDocumento(), '', 'onkeyup="formatearNumero(this);" pattern="' . PATTERN_NUMEROS_FINANCIEROS . '" title="' . $html->traducirTildes(TITLE_NUMEROS_FINANCIEROS) . '"  readonly ');
        
        $form->addEtiqueta(USUARIO_NOMBRE);
        $form->addInputText('text', 'txt_nombre', 'txt_nombre', '20', '60', $usuario->getNombre(), '', 'pattern="'.PATTERN_PALABRAS_ESPACIOS.'"title="'.$html->traducirTildes(TITLE_PALABRAS_ESPACIOS).'"  readonly ');
        
        $form->addEtiqueta(USUARIO_APELLIDO);
        $form->addInputText('text', 'txt_apellido', 'txt_apellido', '20', '60', $usuario->getApellido(), '', 'pattern="'.PATTERN_PALABRAS_ESPACIOS.'"title="'.$html->traducirTildes(TITLE_PALABRAS_ESPACIOS).'"  readonly ');
        
        $form->addEtiqueta(USUARIO_RH);
        $form->addInputText('text', 'txt_rh', 'txt_rh', '20', '3', $usuario->getRh(), '', ' title="'.$html->traducirTildes('Digite su RH').'"  readonly ');
        
        $form->addEtiqueta(USUARIO_FECHA_INGRESO);
        $form->addInputDate('date', 'txt_fecha_ingreso', 'txt_fecha_ingreso', $usuario->getFecha_ingreso(), '%Y-%m-%d', '16', '16', '', 'pattern="' . PATTERN_FECHA . '" title="' . $html->traducirTildes(TITLE_FECHA) . '"   readonly ');

        $form->addEtiqueta(USUARIO_REGIONAL);
        $form->addInputText('text', 'txt_regional', 'txt_regional', '20', '60', $usuario->getRegional(), '', 'pattern="'.PATTERN_PALABRAS_ESPACIOS.'"title="'.$html->traducirTildes(TITLE_PALABRAS_ESPACIOS).'"  readonly ');
        
        $form->addEtiqueta(USUARIO_CARGO);
        $form->addInputText('text', 'txt_cargo', 'txt_cargo', '20', '60', $usuario->getCargo(), '', 'pattern="'.PATTERN_PALABRAS_ESPACIOS.'"title="'.$html->traducirTildes(TITLE_PALABRAS_ESPACIOS).'"  readonly ');
        
        $form->addEtiqueta(USUARIO_CORREO_CORPORATIVO);
        $form->addInputText('text', 'txt_correo_corporativo', 'txt_correo_corporativo', '20', '60', $usuario->getCorreo_corporativo(), '', 'pattern="'.PATTERN_EMAIL.'" title="' . $html->traducirTildes(TITLE_EMAIL) . '"  readonly ');
        
        $form->addEtiqueta(USUARIO_CUENTA_BANCO);
        $form->addInputText('text', 'txt_cuenta_banco', 'txt_cuenta_banco', '20', '20', $usuario->getCuenta_banco(), '', 'pattern="'.PATTERN_NUMEROS.'"title="'.$html->traducirTildes(TITLE_NUMEROS).'"  readonly ');
        
        $form->addEtiqueta(USUARIO_CELULAR);
        $form->addInputText('text', 'txt_celular', 'txt_celular', '15', '10', $usuario->getCelular(), '', 'pattern="' . PATTERN_NUMEROS . '" title="' . $html->traducirTildes(TITLE_NUMEROS) . '"  readonly ');
        
        $form->addEtiqueta(USUARIO_CELULAR_CORPORATIVO);
        $form->addInputText('text', 'txt_celular_corporativo', 'txt_celular_corporativo', '20', '10', $usuario->getCelular_corporativo(), '', 'pattern="'.PATTERN_NUMEROS.'"title="'.$html->traducirTildes(TITLE_NUMEROS).'"  readonly ');
        
        $form->addEtiqueta(USUARIO_TELEFONO);
        $form->addInputText('text', 'txt_telefono', 'txt_telefono', '15', '10', $usuario->getTelefono(), '', 'pattern="' . PATTERN_NUMEROS . '" title="' . $html->traducirTildes(TITLE_NUMEROS) . '"  readonly ');
                
        $form->addEtiqueta(USUARIO_CORREO);
        $form->addInputText('text', 'txt_correo', 'txt_correo', '30', '200', $usuario->getCorreo(), '', 'pattern="'.PATTERN_EMAIL.'" title="' . $html->traducirTildes(TITLE_EMAIL) . '"  readonly ');
        
        $form->addEtiqueta(USUARIO_CIUDAD);
        $form->addInputText('text', 'txt_ciudad', 'txt_ciudad', '20', '200', $usuario->getCiudad(), '', 'pattern="'.PATTERN_PALABRAS_ESPACIOS.'"title="'.$html->traducirTildes(TITLE_PALABRAS_ESPACIOS).'"  readonly ');
        
        $form->addEtiqueta(USUARIO_DIRECCION);
        $form->addInputText('text', 'txt_direccion', 'txt_direccion', '20', '200', $usuario->getDireccion(), '', ' readonly  ');
        
        $form->addEtiqueta(USUARIO_FECHA_DE_NACIMIENTO);
        $form->addInputDate('date', 'txt_fecha_nacimiento', 'txt_fecha_nacimiento', $usuario->getFecha_nacimiento(), '%Y-%m-%d', '16', '16', '', ' readonly pattern="' . PATTERN_FECHA . '" title="' . $html->traducirTildes(TITLE_FECHA) . '" readonly ');

        $form->addEtiqueta(USUARIO_CONTACTO_EMERGENCIA);
        $form->addInputText('text', 'txt_contacto_emergencia', 'txt_contacto_emergencia', '20', '200', $usuario->getContacto_emergencia(), '', ' readonly pattern="'.PATTERN_PALABRAS_ESPACIOS.'"title="'.$html->traducirTildes(TITLE_PALABRAS_ESPACIOS).'"  readonly ');
        
        $form->addEtiqueta(USUARIO_CONTACTO_EMERGENCIA_CELULAR);
        $form->addInputText('text', 'txt_celular_contacto', 'txt_celular_contacto', '20', '10', $usuario->getTelefonoContacto(), '', 'pattern="'.PATTERN_NUMEROS.'"title="'.$html->traducirTildes(TITLE_NUMEROS).'" readonly ');

        $form->addEtiqueta(USUARIO_FECHA_APROBACION);
        $form->addInputDate('date', 'txt_fecha_aprovacion', 'txt_fecha_aprovacion', $usuario->getFecha_aprovacion(), '%Y-%m-%d', '16', '16', '', ' readonly pattern="' . PATTERN_FECHA . '" title="' . $html->traducirTildes(TITLE_FECHA) . '" readonly ');

        $form->addEtiqueta(USUARIO_ARL);
        $form->addInputText('text', 'txt_arl', 'txt_arl', '20', '200', $usuario->getArl(), '', ' readonly pattern="'.PATTERN_PALABRAS_ESPACIOS.'"title="'.$html->traducirTildes(TITLE_PALABRAS_ESPACIOS).'"  readonly ');
             
        $form->addEtiqueta(USUARIO_EPS);
        $form->addInputText('text', 'txt_eps', 'txt_eps', '20', '200', $usuario->getEps(), '', ' readonly pattern="'.PATTERN_PALABRAS_ESPACIOS.'"title="'.$html->traducirTildes(TITLE_PALABRAS_ESPACIOS).'"  readonly ');
             
        $form->addEtiqueta(USUARIO_ALERGIA);
        $form->addInputText('text', 'txt_alergia', 'txt_alergia', '20', '400', $usuario->getAlergia(), '', 'pattern="'.PATTERN_PALABRAS_ESPACIOS.'"title="'.$html->traducirTildes(TITLE_PALABRAS_ESPACIOS).'"  readonly ');
             
        $form->addEtiqueta(USUARIO_ANTECEDENTES_ENFERMEDAD);
        $form->addInputText('text', 'txt_antecedentes_enfermedad', 'txt_antecedentes_enfermedad', '20', '400', $usuario->getAntecedentes_enfermedad(), '', ' readonly  readonly pattern="'.PATTERN_PALABRAS_ESPACIOS.'"title="'.$html->traducirTildes(TITLE_PALABRAS_ESPACIOS).'" ');
             
        $form->addEtiqueta(USUARIO_MEDICAMENTOS);
        $form->addInputText('text', 'txt_medicamentos', 'txt_medicamentos', '20', '400', $usuario->getMedicamentos(), '', ' readonly title="'.$html->traducirTildes(TITLE_PALABRAS_ESPACIOS).'" ');
        
        $estados = $du->getTipoActividad();
        $opciones = null;
        foreach ($estados as $s) {
            $opciones[count($opciones)] = array('value' => $s['id'], 'texto' => $s['nombre']);
        }

        $form->addEtiqueta(USUARIO_ESTADO);
        $form->addSelect('select', 'sel_estado', 'sel_estado', $opciones, USUARIO_ESTADO, $usuario->getEstado(), '', ' readonly ');
              
        
        $form->addInputButton('button', 'cancel', 'cancel', BTN_ATRAS, 'button', 'onClick=location.href="?mod='.$modulo.'&niv='.$niv.'"');

        $form->writeForm();


        break;

// *******************************************************O P C I O N E S ****************************************************************

    /**
     * la variable editOption, permite hacer la carga de las opciones del objeto USUARIO y espera confirmacion de edicion, ver la clase CUsuario
     */
    case 'editOption':
        $id_edit = $_REQUEST['id_element'];
        $usuario = new CUsuario($id_edit, $du);
        $usuario->loadUser();

        $subelementos = null;

        $opc = $usuario->loadOptionsForUser();

        foreach ($opc as $o) {
            $clase = null;
            switch ($o['nivel']) {
                case 0:
                    $clase = 'opcUno';
                    break;
                case 1:
                    $clase = 'opcDos';
                    break;
                default:
                    $clase = 'opcTres';
                    break;
            }
            if ($o['indicador'] == 1)
                $checked = "checked";
            else
                $checked = "";
            if ($o['acceso'] == 1)
                $sub_check = "checked";
            else
                $sub_check = "";
            if ($o['indicador'] == 1)
                $sub_check .= "";
            else
                $sub_check = " disabled";
            $dependientes[0] = array('id' => $o['id'], 'texto' => NIVEL_ADMIN, 'checked' => $sub_check);
            if ($o['acceso'] == 2)
                $sub_check = "checked";
            else
                $sub_check = "";
            if ($o['indicador'] == 1)
                $sub_check .= "";
            else
                $sub_check = " disabled";
            $dependientes[1] = array('id' => $o['id'], 'texto' => NIVEL_SOLO_LECTURA, 'checked' => $sub_check);
            $subelementos[count($subelementos)] = array('value' => $o['id'],
                'texto' => $o['nombre'],
                'clase' => $clase,
                'checked' => $checked,
                'dependientes' => $dependientes,
                'clase_dep' => 'opcDep',
                'events' => 'onClick = habilitarDependiente(this);');
        }

        $form = new CHtmlForm();
        $form->setTitle(EDITAR_USUARIO_OPCIONES);
        $form->setId('frm_edit_options');
        $form->setMethod('post');
        $form->setClassEtiquetas('td_label');

        $form->addInputText('hidden', 'txt_id', 'txt_id', '15', '15', $usuario->getId(), '', '');

        $form->addEtiqueta(USUARIO_LOGIN);
        $form->addInputText('text', 'txt_login', 'txt_login', '15', '15', $usuario->getLogin(), '', 'onkeypress="ocultarDiv(\'error_login\');" readOnly');
        $form->addError('error_login', ERROR_LOGIN);

        $form->addEtiqueta('Opciones');
        $form->addExtendedCheckBox('extendedCheckbox', 'chk_opciones', 'chk_opciones', $subelementos, '', '', '');
        $form->addError('error_opciones', '');

        $form->addInputButton('button', 'ok', 'ok', BTN_ACEPTAR, 'button', 'onclick="validar_edit_options();"');
        $form->addInputButton('button', 'cancel', 'cancel', BTN_CANCELAR, 'button', 'onclick="cancelarAccion(\'frm_edit_options\',\'?mod=usuarios&niv=1\');"');

        $form->writeForm();

        break;
    /**
     * la variable saveOptions, permite actualizar las opciones del objeto USUARIO en la base de datos, ver la clase CUsuarioData
     */
    case 'saveOptions':
        $id_usu = $_POST['txt_id'];
        $login_usu = $_POST['txt_login'];
        $usuario = new CUsuario($id_usu, $du);
        $usuario->loadUser();

        $opc = $usuario->loadOptionsForUser();

        $options = null;

        foreach ($opc as $o) {
            $var_chk = "chk_opciones_" . $o['id'];
            $var_rdo = "radio_" . $o['id'];

            if (isset($_POST[$var_chk])) {
                if ($_POST[$var_rdo] == '0') {
                    $nivel_opc = 1;
                } else {
                    $nivel_opc = 2;
                }
                $options[count($options)] = array('id' => $_POST[$var_chk], 'nivel' => $nivel_opc);
            }
        }

        $usuario->saveEditUserOptions($options);
        echo $html->generaAviso(MSG_OPCIONES_EDITADAS . " " . $usuario->getLogin(), '?mod=usuarios&niv=1');

        break;
    /**
     * la variable editClave, permite hacer la carga de la clave del objeto USUARIO y espera confirmacion de edicion
     */
    case 'editClave':
        $form = new CHtmlForm();
        $form->setTitle(CAMBIAR_CLAVE);

        $form->setId('frm_cambiar_clave');
        $form->setMethod('post');
        $form->setClassEtiquetas('td_label');

        $form->addEtiqueta(USUARIO_PASSWORD_ANTERIOR);
        $form->addInputText('password', 'txt_password', 'txt_password', '15', '15', '', '', 'onkeypress="ocultarDiv(\'error_password\');"');
        $form->addError('error_password', ERROR_PASSWORD);

        $form->addEtiqueta(USUARIO_NUEVO_PASSWORD);
        $form->addInputText('password', 'txt_nuevo_password', 'txt_nuevo_password', '15', '15', '', '', 'onkeypress="ocultarDiv(\'error_nuevo_password\');"');
        $form->addError('error_nuevo_password', EEROR_USUARIO_NUEVO_PASSWORD);

        $form->addInputButton('button', 'ok', 'ok', BTN_ACEPTAR, 'button', 'onclick="validar_cambiar_clave();"');
        $form->addInputButton('button', 'cancel', 'cancel', BTN_CANCELAR, 'button', 'onclick="cancelarAccion(\'frm_cambiar_clave\',\'?mod=home&niv=1\');"');

        $form->writeForm();

        break;
    /**
     * la variable saveOptions, permite actualizar la clave del objeto USUARIO en la base de datos, ver la clase CUsuarioData
     */
    case 'saveEditClave':

        $login = $_SESSION["usuario_sesion_pry"];
        $password = $_POST['txt_password'];
        $nuevo_password = $_POST['txt_nuevo_password'];

        $usuario = new CUsuario('', $du);
        $usuario->setLogin($login);
        $usuario->setPassword($password);
        $usuario->setNombre($nuevo_password);

        $m = $usuario->saveNewClave();

        echo $html->generaAviso($m, '?mod=usuarios?niv=1&task=cambiarClave');

        break;
    /**
     * en caso de que la variable task no este definida carga la página en construcción
     */
    default:
        include('templates/html/under.html');

        break;
}
?>