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
 * Clase Usuario
 *
 * @package  clases
 * @subpackage aplicacion
 * @author Redcom Ltda
 * @version 2013.01.00
 * @copyright SERTIC - MINTICS
 */
class CUsuario {

    var $id = null;
    var $login = null;
    var $password = null;
    var $nombre = null;
    var $apellido = null;
    var $documento = null;
    var $telefono = null;
    var $celular = null;
    var $correo = null;
    var $perfil = null;
    var $estado = null;
    var $fecha = null;
    var $ip = null;
    var $rh = null;
    var $fecha_ingreso = null;
    var $regional = null;
    var $cargo = null;
    var $correo_corporativo = null;
    var $cuenta_banco = null;
    var $celular_corporativo = null;
    var $ciudad = null;
    var $direccion = null;
    var $fecha_nacimiento = null;
    var $contacto_emergencia = null;
    var $fecha_aprovacion = null;
    var $arl = null;
    var $eps = null;
    var $alergia = null;
    var $antecedentes_enfermedad = null;
    var $medicamentos = null;
    var $telefonoContacto = null;
    var $du = null;

    /**
     * * Constructor de la clase CUsuarioData
     * */
    function CUsuario($id, $du) {
        $this->setId($id);
        $this->du = $du;
    }

    function setId($val) {
        $this->id = $val;
    }

    function setLogin($val) {
        $this->login = $val;
    }

    function setPassword($val) {
        $this->password = $val;
    }

    function setNombre($val) {
        $this->nombre = $val;
    }

    function setApellido($val) {
        $this->apellido = $val;
    }

    function setDocumento($val) {
        $this->documento = $val;
    }

    function setTelefono($val) {
        $this->telefono = $val;
    }

    function setCelular($val) {
        $this->celular = $val;
    }

    function setCorreo($val) {
        $this->correo = $val;
    }

    function setPerfil($val) {
        $this->perfil = $val;
    }

    function setEstado($val) {
        $this->estado = $val;
    }

    function setFecha($val) {
        $this->fecha = $val;
    }

    function setIp($val) {
        $this->ip = $val;
    }

    function getId() {
        return $this->id;
    }

    function getLogin() {
        return $this->login;
    }

    function getPassword() {
        return $this->password;
    }

    function getNombre() {
        return $this->nombre;
    }

    function getApellido() {
        return $this->apellido;
    }

    function getDocumento() {
        return $this->documento;
    }

    function getTelefono() {
        return $this->telefono;
    }

    function getCelular() {
        return $this->celular;
    }

    function getCorreo() {
        return $this->correo;
    }

    function getPerfil() {
        return $this->perfil;
    }

    function getEstado() {
        return $this->estado;
    }

    function getFecha() {
        return $this->fecha;
    }

    function getIp() {
        return $this->ip;
    }

    public function getRh() {
        return $this->rh;
    }

    public function getFecha_ingreso() {
        return $this->fecha_ingreso;
    }

    public function getRegional() {
        return $this->regional;
    }

    public function getCargo() {
        return $this->cargo;
    }

    public function getCorreo_corporativo() {
        return $this->correo_corporativo;
    }

    public function getCuenta_banco() {
        return $this->cuenta_banco;
    }

    public function getCelular_corporativo() {
        return $this->celular_corporativo;
    }

    public function getCiudad() {
        return $this->ciudad;
    }

    public function getDireccion() {
        return $this->direccion;
    }

    public function getFecha_nacimiento() {
        return $this->fecha_nacimiento;
    }

    public function getContacto_emergencia() {
        return $this->contacto_emergencia;
    }

    public function getFecha_aprovacion() {
        return $this->fecha_aprovacion;
    }

    public function getArl() {
        return $this->arl;
    }

    public function getEps() {
        return $this->eps;
    }

    public function getAlergia() {
        return $this->alergia;
    }

    public function getAntecedentes_enfermedad() {
        return $this->antecedentes_enfermedad;
    }

    public function getMedicamentos() {
        return $this->medicamentos;
    }

    public function setRh($rh) {
        $this->rh = $rh;
    }

    public function setFecha_ingreso($fecha_ingreso) {
        $this->fecha_ingreso = $fecha_ingreso;
    }

    public function setRegional($regional) {
        $this->regional = $regional;
    }

    public function setCargo($cargo) {
        $this->cargo = $cargo;
    }

    public function setCorreo_corporativo($correo_corporativo) {
        $this->correo_corporativo = $correo_corporativo;
    }

    public function setCuenta_banco($cuenta_banco) {
        $this->cuenta_banco = $cuenta_banco;
    }

    public function setCelular_corporativo($celular_corporativo) {
        $this->celular_corporativo = $celular_corporativo;
    }

    public function setCiudad($ciudad) {
        $this->ciudad = $ciudad;
    }

    public function setDireccion($direccion) {
        $this->direccion = $direccion;
    }

    public function setFecha_nacimiento($fecha_nacimiento) {
        $this->fecha_nacimiento = $fecha_nacimiento;
    }

    public function setContacto_emergencia($contacto_emergencia) {
        $this->contacto_emergencia = $contacto_emergencia;
    }

    public function setFecha_aprovacion($fecha_aprovacion) {
        $this->fecha_aprovacion = $fecha_aprovacion;
    }

    public function setArl($arl) {
        $this->arl = $arl;
    }

    public function setEps($eps) {
        $this->eps = $eps;
    }

    public function setAlergia($alergia) {
        $this->alergia = $alergia;
    }

    public function setAntecedentes_enfermedad($antecedentes_enfermedad) {
        $this->antecedentes_enfermedad = $antecedentes_enfermedad;
    }

    public function setMedicamentos($medicamentos) {
        $this->medicamentos = $medicamentos;
    }
    function getTelefonoContacto() {
        return $this->telefonoContacto;
    }

    function setTelefonoContacto($telefonoContacto) {
        $this->telefonoContacto = $telefonoContacto;
    }

        /**
     * carga los valores de un objeto USUARIO por su id para ser editados
     * */
    function loadUser() {
        $r = $this->du->getUserById($this->id);
        if ($r != -1) {
            $this->login = $r['usu_login'];
            $this->password = $r['usu_clave'];
            $this->nombre = $r['usu_nombre'];
            $this->apellido = $r['usu_apellido'];
            $this->documento = $r['usu_documento'];
            $this->telefono = $r['usu_telefono'];
            $this->celular = $r['usu_celular'];
            $this->correo = $r['usu_correo'];
            $this->perfil = $r['per_id'];
            $this->estado = $r['usu_estado'];
            $this->fecha = $r['usu_fecha_ultimo_ingreso'];
            $this->ip = $r['usu_ip'];
            $this->rh = $r['usu_rh'];
            $this->fecha_ingreso = $r['usu_fecha_ingreso'];
            $this->regional = $r['usu_regional'];
            $this->cargo = $r['usu_cargo'];
            $this->correo_corporativo = $r['usu_correo_corporativo'];
            $this->cuenta_banco = $r['usu_cuenta_banco'];
            $this->celular_corporativo = $r['usu_celular_corporativo'];
            $this->ciudad = $r['usu_ciudad'];
            $this->direccion = $r['usu_direccion'];
            $this->fecha_nacimiento = $r['usu_fecha_nacimiento'];
            $this->contacto_emergencia = $r['usu_contacto_emergencia'];
            $this->fecha_aprovacion = $r['usu_fecha_aprobacion'];
            $this->arl = $r['usu_arl'];
            $this->eps = $r['usu_eps'];
            $this->alergia = $r['usu_alergia'];
            $this->antecedentes_enfermedad = $r['usu_antecedentes_enfermedad'];
            $this->medicamentos = $r['usu_medicamentos'];
		 $this->telefonoContacto= $r['usu_telefono_contacto'];
        } else {
            $this->login = "";
            $this->password = "";
            $this->nombre = "";
            $this->apellido = "";
            $this->documento = "";
            $this->telefono = "";
            $this->celular = "";
            $this->correo = "";
            $this->perfil = "";
            $this->estado = "";
            $this->fecha = "";
            $this->ip = "";
            $this->rh = "";
            $this->fecha_ingreso = "";
            $this->regional = "";
            $this->cargo = "";
            $this->correo_corporativo = "";
            $this->cuenta_banco = "";
            $this->celular_corporativo = "";
            $this->ciudad = "";
            $this->direccion = "";
            $this->fecha_nacimiento = "";
            $this->contacto_emergencia = "";
            $this->fecha_aprovacion = "";
            $this->arl = "";
            $this->eps = "";
            $this->alergia = "";
            $this->antecedentes_enfermedad = "";
            $this->medicamentos = "";
		 $this->telefonoContacto= "";
        }
    }

    /**
     * * carga los valores de un objeto USUARIO por su id para ser visualizados
     * */
    function loadSeeUser() {
        $r = $this->du->getUserById($this->id);
        if ($r != -1) {
            $this->login = $r['usu_login'];
            $this->password = $r['usu_clave'];
            $this->nombre = $r['usu_nombre'];
            $this->apellido = $r['usu_apellido'];
            $this->documento = $r['usu_documento'];
            $this->telefono = $r['usu_telefono'];
            $this->celular = $r['usu_celular'];
            $this->correo = $r['usu_correo'];
            $this->perfil = $r['per_id'];
            $this->fecha = $r['usu_fecha_ultimo_ingreso'];
            $this->ip = $r['usu_ip'];
            $this->rh = $r['usu_rh'];
            $this->fecha_ingreso = $r['usu_fecha_ingreso'];
            $this->regional = $r['usu_regional'];
            $this->cargo = $r['usu_cargo'];
            $this->correo_corporativo = $r['usu_correo_corporativo'];
            $this->cuenta_banco = $r['usu_cuenta_banco'];
            $this->celular_corporativo = $r['usu_celular_corporativo'];
            $this->ciudad = $r['usu_ciudad'];
            $this->direccion = $r['usu_direccion'];
            $this->fecha_nacimiento = $r['usu_fecha_nacimiento'];
            $this->contacto_emergencia = $r['usu_contacto_emergencia'];
            $this->fecha_aprovacion = $r['usu_fecha_aprobacion'];
            $this->arl = $r['usu_arl'];
            $this->eps = $r['usu_eps'];
            $this->alergia = $r['usu_alergia'];
            $this->antecedentes_enfermedad = $r['usu_antecedentes_enfermedad'];
            $this->medicamentos = $r['usu_medicamentos'];
		 $this->telefonoContacto= $r['usu_telefono_contacto'];
            if ($r['usu_estado'] == 1)
                $this->estado = "Activo";
            else
                $this->estado = "Inactivo";
        } else {
            $this->login = "";
            $this->password = "";
            $this->nombre = "";
            $this->apellido = "";
            $this->documento = "";
            $this->telefono = "";
            $this->celular = "";
            $this->correo = "";
            $this->perfil = "";
            $this->estado = "";
            $this->fecha = "";
            $this->ip = "";
            $this->rh = "";
            $this->fecha_ingreso = "";
            $this->regional = "";
            $this->cargo = "";
            $this->correo_corporativo = "";
            $this->cuenta_banco = "";
            $this->celular_corporativo = "";
            $this->ciudad = "";
            $this->direccion = "";
            $this->fecha_nacimiento = "";
            $this->contacto_emergencia = "";
            $this->fecha_aprovacion = "";
            $this->arl = "";
            $this->eps = "";
            $this->alergia = "";
            $this->antecedentes_enfermedad = "";
            $this->medicamentos = "";
		 $this->telefonoContacto= "";
        }
    }

    /**
     * * almacena un objeto USUARIO y retorna un mensaje del resultado del proceso
     * */
    function saveNewUser() {
        $valid = $this->du->getUserIdByLogin($this->getlogin());
        if ($valid != -1) {
            $msg = USUARIO_EXISTENTE;
        }
        if ($valid == -1) {
            $r = $this->du->insertUser($this);
            if ($r == 'true') {
                $this->id = $this->du->getUserIdByLogin($this->login);
                $msg = USUARIO_AGREGADO;
            } else {
                $msg = ERROR_ADD_USER;
            }
        }
        return $msg;
    }

    /**
     * * elimina un objeto USUARIO y retorna un mensaje del resultado del proceso
     * */
    function deleteUser() {
        $r = $this->du->deleteUserPerfiles($this->id);
        $r = $this->du->deleteUser($this->id);
        if ($r == 'true') {
            $msg = USUARIO_BORRADO;
        } else {
            $msg = ERROR_DEL_USER;
        }
        return $msg;
    }

    /**
     * * actualiza un objeto USUARIO (incluido el password) y retorna un mensaje del resultado del proceso
     * */
    function saveEditUser() {
        $valid = $this->du->getCountUsersByLogin($this->getlogin());
        if ($valid > 1) {
            $msg = USUARIO_EXISTENTE;
        } else {
            $r = $this->du->updateUser($this);
            if ($r == 'true') {
                $msg = USUARIO_EDITADO;
            } else {
                $msg = ERROR_EDIT_USER;
            }
        }
        return $msg;
    }
    /**
     * * carga las opciones de un objeto USUARIO
     * */
    function loadOptionsForUser() {
        $r = $this->du->loadOptionsForUser($this->id);
        $opc = null;
        while ($row = mysql_fetch_array($r)) {
            if ($row['usu_id'] == $this->id)
                $indicador = 1;
            else
                $indicador = 0;
            $opc[count($opc)] = array('id' => $row['opc_id'],
                'nombre' => $row['opc_nombre'],
                'nivel' => $row['opc_nivel'],
                'indicador' => $indicador,
                'acceso' => $row['uxo_nivel']);
        }
        return $opc;
    }

    /**
     * * actualiza un objeto USUARIO (solo para el password - cambio de clave)
     * */
    function saveNewClave() {
        $id = $this->du->getUserId($this->login, $this->password);
        if ($id != -1) {
            $r = $this->du->updatePass($id, $this->nombre); //nombre=nuevoPass
            if ($r == 'true') {
                $msg = CLAVE_EDITADA;
            } else {
                $msg = CLAVE_NO_EDITADA;
            }
        } else
            $msg = CLAVE_NO_COINCIDE;
        return $msg;
    }

    /**
     * * actualiza las opciones de un objeto USUARIO
     * */
    function saveEditUserOptions($options) {
        $r = $this->du->deleteUserOptions($this->id);
        foreach ($options as $o) {
            $this->du->insertUserOption($this->id, $o['id'], $o['nivel']);
        }
    }

}

?>