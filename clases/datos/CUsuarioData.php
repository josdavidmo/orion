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
 * Clase UsuarioData
 * Usada para la definicion de todas las funciones propias del objeto USUARIO
 *
 * @package  clases
 * @subpackage datos
 * @author Redcom Ltda
 * @version 2013.01.00
 * @copyright SERTIC - MINTICS
 */
Class CUserData {

    var $db = null;

    function CUserData($db) {
        $this->db = $db;
    }

    function opciones($us) {
        $sql = "select o.*,p.pxo_nivel
				from opcion o
				inner join perfil_x_opcion p on p.opc_id = o.opc_id
				inner join usuario u on p.per_id = u.per_id
				where u.usu_id = " . $us . "
				and o.opn_id in(0,1)
				order by o.opc_orden";

        $r = $this->db->ejecutarConsulta($sql);
        if ($r)
            return $r;
    }

    function getNivelOpcionByVariable($v) {
        $pre = "opc_variable = '" . $v . "'";
        $r = $this->db->recuperarCampo('opcion', 'opn_id', $pre);
        if ($r)
            return $r;
        else
            return -1;
    }

    function getPadreOpcionByVariable($v) {
        $pre = "opc_variable = '" . $v . "'";
        $r = $this->db->recuperarCampo('opcion', 'opc_padre_id', $pre);
        if ($r)
            return $r;
        else
            return -1;
    }

    function subopciones($us, $v, $operador) {
        $nivel = $this->getNivelOpcionByVariable($v);
        if ($nivel == 2) {
            $padre = $this->getPadreOpcionByVariable($v);
            $sql = "select o.*,p.pxo_nivel 
                                    from opcion o
                                    inner join perfil_x_opcion p on p.opc_id = o.opc_id
                                    inner join usuario u on p.per_id = u.per_id
                                    where u.usu_id = " . $us . "
                            and o.opc_padre_id = '" . $padre . "'
                                    and o.opn_id in(2)
                                    order by o.opc_orden";
        } else {
            $sql = "select o.*,p.pxo_nivel 
                                    from opcion o
                                    inner join perfil_x_opcion p on p.opc_id = o.opc_id
                                    inner join usuario u on p.per_id = u.per_id
                                    where u.usu_id = " . $us . "
                            and o.opc_padre_id = (select opc_id from opcion where opc_variable = '" . $v . "' and opn_id=1 and ope_id =" . $operador . ")
                                    and o.opn_id in(2)
                                    order by o.opc_orden";
        }
        $r = $this->db->ejecutarConsulta($sql);
        if ($r)
            return $r;
    }

    function getUserId($l, $p) {
        $pre = "usu_login = '" . $l . "' and usu_clave = md5('" . $p . "')";
        $r = $this->db->recuperarCampo('usuario', 'usu_id', $pre);
        if ($r)
            return $r;
        else
            return -1;
    }

    function getUserNivel($id, $task) {
        $pre = "u.usu_id = " . $id . " and o.opc_variable = '" . $task . "'";
        $tablas = "perfil_x_opcion p 
					inner join opcion o on o.opc_id = p.opc_id
					inner join usuario u on p.per_id = u.per_id";
        $r = $this->db->recuperarCampo($tablas, 'pxo_nivel', $pre);
        if ($r)
            return $r;
        else
            return -1;
    }

    function getUserPerfil($id) {
        $pre = "u.usu_id = " . $id;
        $tablas = "usuario u";
        $r = $this->db->recuperarCampo($tablas, 'per_id', $pre);
        if ($r)
            return $r;
        else
            return -1;
    }

    function getUserIdByLogin($l) {
        $pre = "usu_login = '" . $l . "'";
        $r = $this->db->recuperarCampo('usuario', 'usu_id', $pre);
        if ($r)
            return $r;
        else
            return -1;
    }

    function getCountUsersByLogin($l) {
        $pre = "usu_login = '" . $l . "'";
        $r = $this->db->recuperarCampo('usuario', 'count(1)', $pre);
        if ($r)
            return $r;
        else
            return -1;
    }

    function getUserNameById($id) {
        $pre = "usu_id = " . $id;
        $n = $this->db->recuperarCampo('usuario', 'usu_nombre', $pre);
        $a = $this->db->recuperarCampo('usuario', 'usu_apellido', $pre);
        $r = $n . " " . $a;
        if ($r)
            return $r;
        else
            return -1;
    }

    function getUserUltimoIngresoById($id) {
        $pre = "usu_id = " . $id;
        $f = $this->db->recuperarCampo('usuario', 'usu_fecha_ultimo_ingreso', $pre);
        if ($f)
            return $f;
        else
            return -1;
    }

    function getUserIpById($id) {
        $pre = "usu_id = " . $id;
        $f = $this->db->recuperarCampo('usuario', 'usu_ip', $pre);
        if ($f)
            return $f;
        else
            return -1;
    }

    function getUsers($criterio, $orden) {
        $users = null;
        $sql = "select * from usuario u, perfil p where p.per_id=u.per_id and " . $criterio . " order by " . $orden;
        $r = $this->db->ejecutarConsulta($sql);
        if ($r) {
            $cont = 0;
            while ($w = mysql_fetch_array($r)) {
                $users[$cont]['id'] = $w['usu_id'];
                $users[$cont]['login'] = $w['usu_login'];
                $users[$cont]['nombre'] = $w['usu_nombre'];
                $users[$cont]['apellido'] = $w['usu_apellido'];
                $users[$cont]['documento'] = $w['usu_documento'];
                $users[$cont]['telefono'] = $w['usu_telefono'];
                $users[$cont]['perfil'] = $w['per_nombre'];
                $users[$cont]['correo'] = $w['usu_correo_corporativo'];
                if ($w['usu_estado'] == 1)
                    $users[$cont]['estado'] = "Activo";
                else
                    $users[$cont]['estado'] = "Inactivo";
                $users[$cont]['fecha'] = $w['usu_fecha_ultimo_ingreso'];
                $cont++;
            }
        }
        return $users;
    }

    function getPersonal($criterio, $orden) {
        $users = null;
        $sql = "select * from usuario u, perfil p where p.per_id=u.per_id and " . $criterio . " and usu_estado = 1 order by " . $orden;
        $r = $this->db->ejecutarConsulta($sql);
        if ($r) {
            $cont = 0;
            while ($w = mysql_fetch_array($r)) {
                $users[$cont]['id'] = $w['usu_id'];
                $users[$cont]['nombre'] = $w['usu_nombre'];
                $users[$cont]['apellido'] = $w['usu_apellido'];
                $users[$cont]['documento'] = $w['usu_documento'];
                $users[$cont]['correo'] = $w['usu_correo_corporativo'];
                $cont++;
            }
        }
        return $users;
    }
    
    function getInformacionBasicaPersonal($criterio, $orden) {
        $users = null;
        $sql = "SELECT usu_id, "
                . "CONCAT(usu_nombre,' ',usu_apellido) as nombreUsuario, "
                . "usu_documento, usu_correo_corporativo "
                . "FROM usuario "
                . "WHERE " . $criterio . " and usu_estado = 1 order by " . $orden;
        $r = $this->db->ejecutarConsulta($sql);
        if ($r) {
            $cont = 0;
            while ($w = mysql_fetch_array($r)) {
                $users[$cont]['id'] = $w['usu_id'];
                $users[$cont]['nombre'] = $w['nombreUsuario'];
                $users[$cont]['documento'] = $w['usu_documento'];
                $users[$cont]['correo'] = $w['usu_correo_corporativo'];
                $cont++;
            }
        }
        return $users;
    }

    function getUsersExcel() {
        $users = null;
        $sql = "select * from usuario u, perfil p where p.per_id=u.per_id ";
        $r = $this->db->ejecutarConsulta($sql);
        if ($r) {
            $cont = 0;
            while ($w = mysql_fetch_array($r)) {
                $users[$cont]['id'] = $w['usu_id'];
                $users[$cont]['login'] = $w['usu_login'];
                $users[$cont]['nombre'] = $w['usu_nombre'];
                $users[$cont]['apellido'] = $w['usu_apellido'];
                $users[$cont]['documento'] = $w['usu_documento'];
                $users[$cont]['telefono'] = $w['usu_telefono'];
                $users[$cont]['correo'] = $w['usu_correo'];
                $users[$cont]['celular'] = $w['usu_celular'];
                $users[$cont]['correo'] = $w['usu_correo'];
                $users[$cont]['fecha_ultimo_ingreso'] = $w['usu_fecha_ultimo_ingreso'];
                $users[$cont]['fecha_ingreso'] = $w['usu_fecha_ingreso'];
                $users[$cont]['regional'] = $w['usu_regional'];
                $users[$cont]['cargo'] = $w['usu_cargo'];
                $users[$cont]['correo_corporativo'] = $w['usu_correo_corporativo'];
                $users[$cont]['cuenta_banco'] = $w['usu_cuenta_banco'];
                $users[$cont]['celular_corporativo'] = $w['usu_celular_corporativo'];
                $users[$cont]['ciudad'] = $w['usu_ciudad'];
                $users[$cont]['direccion'] = $w['usu_direccion'];
                $users[$cont]['fecha_nacimiento'] = $w['usu_fecha_nacimiento'];
                $users[$cont]['contacto_emergencia'] = $w['usu_contacto_emergencia'];
                $users[$cont]['fecha_aprobacion'] = $w['usu_fecha_aprobacion'];
                $users[$cont]['arl'] = $w['usu_arl'];
                $users[$cont]['eps'] = $w['usu_eps'];
                $users[$cont]['alergia'] = $w['usu_alergia'];
                $users[$cont]['antecedentes_enfermedad'] = $w['usu_antecedentes_enfermedad'];
                $users[$cont]['medicamentos'] = $w['usu_medicamentos'];
                $users[$cont]['rh'] = $w['usu_rh'];
                $users[$cont]['perfil'] = $w['per_nombre'];
                $users[$cont]['telefono_contacto'] = $w['usu_telefono_contacto'];
                if ($w['usu_estado'] == 1)
                    $users[$cont]['estado'] = "Activo";
                else
                    $users[$cont]['estado'] = "Inactivo";
                $users[$cont]['fecha'] = $w['usu_fecha_ultimo_ingreso'];

                $cont++;
            }
        }
        return $users;
    }

    function insertUser($CUsuario) {
        $tabla = "usuario";
        $campos = "usu_login,usu_clave,usu_nombre,usu_apellido,usu_documento,usu_telefono,usu_celular,usu_correo,per_id,usu_estado,"
                . "usu_rh,usu_fecha_ingreso,usu_regional,usu_cargo,usu_correo_corporativo,usu_cuenta_banco,"
                . "usu_celular_corporativo,usu_ciudad,usu_direccion,usu_fecha_nacimiento,usu_contacto_emergencia,usu_fecha_aprobacion,"
                . "usu_arl,usu_eps,usu_alergia,usu_antecedentes_enfermedad,usu_medicamentos,usu_telefono_contacto";
        $valores = "'" . $CUsuario->getlogin() . "',md5('" . $CUsuario->getPassword() . "'),
					'" . $CUsuario->getnombre() . "','" . $CUsuario->getapellido() . "',
					'" . $CUsuario->getdocumento() . "','" . $CUsuario->gettelefono() . "',
					'" . $CUsuario->getcelular() . "','" . $CUsuario->getcorreo() . "',
					'" . $CUsuario->getperfil() . "','" . $CUsuario->getEstado() . "','" .
                $CUsuario->getRh() . "','" . $CUsuario->getFecha_ingreso() . "','" .
                $CUsuario->getRegional() . "','" . $CUsuario->getCargo() . "','" .
                $CUsuario->getCorreo_corporativo() . "','" . $CUsuario->getCuenta_banco() . "','" .
                $CUsuario->getCelular_corporativo() . "','" . $CUsuario->getCiudad() . "','" .
                $CUsuario->getDireccion() . "','" . $CUsuario->getFecha_Nacimiento() . "','" .
                $CUsuario->getContacto_emergencia() . "','" . $CUsuario->getFecha_aprovacion() . "','" .
                $CUsuario->getArl() . "','" . $CUsuario->getEps() . "','" .
                $CUsuario->getAlergia() . "','" . $CUsuario->getAntecedentes_enfermedad() . "','" .
                $CUsuario->getMedicamentos() . "','" .
                $CUsuario->getTelefonoContacto() . "'";



        $r = $this->db->insertarRegistro($tabla, $campos, $valores);
        return $r;
    }

    function updateUser($CUsuario) {
        $tabla = 'usuario';
        if (isset($password) && $password != '') {
            $campos = array('usu_login', 'usu_clave', 'usu_nombre', 'usu_apellido', 'usu_documento', 'usu_telefono',
                'usu_celular', 'usu_correo', 'per_id', 'usu_estado', 'usu_rh', 'usu_fecha_ingreso', 'usu_regional', 'usu_cargo', 'usu_correo_corporativo', 'usu_cuenta_banco', '
                usu_celular_corporativo', 'usu_ciudad', 'usu_direccion', 'usu_fecha_nacimiento', 'usu_contacto_emergencia', 'usu_fecha_aprobacion', '
                usu_arl', 'usu_eps', 'usu_alergia', 'usu_antecedentes_enfermedad', 'usu_medicamentos', 'usu_telefono_contacto'
            );
            $valores = array("'" . $CUsuario->getlogin() . "'", "md5('" . $CUsuario->getpassword() . "')", "'" . $CUsuario->getNombre() . "'",
                "'" . $CUsuario->getapellido() . "'", "'" . $CUsuario->getDocumento() . "'", "'" . $CUsuario->gettelefono() . "'",
                "'" . $CUsuario->getcelular() . "'", "'" . $CUsuario->getcorreo() . "'", "'" . $CUsuario->getperfil() . "'",
                "'" . $CUsuario->getEstado() . "'", "'" .
                $CUsuario->getRh() . "'", "'" . $CUsuario->getFecha_ingreso() . "'", "'" .
                $CUsuario->getRegional() . "'", "'" . $CUsuario->getCargo() . "'", "'" .
                $CUsuario->getCorreo_corporativo() . "'", "'" . $CUsuario->getcuenta_banco() . "'", "'" .
                $CUsuario->getCelular_corporativo() . "'", "'" . $CUsuario->getCiudad() . "'", "'" .
                $CUsuario->getDireccion() . "'", "'" . $CUsuario->getFecha_Nacimiento() . "'", "'" .
                $CUsuario->getContacto_emergencia() . "'", "'" . $CUsuario->getFecha_aprovacion() . "'", "'" .
                $CUsuario->getArl() . "'", "'" . $CUsuario->getEps() . "'", "'" .
                $CUsuario->getAlergia() . "'", "'" . $CUsuario->getAntecedentes_enfermedad() . "'", "'" .
                $CUsuario->getMedicamentos() . "'", "'" .
                $CUsuario->getTelefonoContacto()
                . "'");
        } else {
            $campos = array('usu_login', 'usu_nombre', 'usu_clave', 'usu_apellido', 'usu_documento', 'usu_telefono', 'usu_celular',
                'usu_correo', 'per_id', 'usu_estado'
                , 'usu_rh', 'usu_fecha_ingreso', 'usu_regional', 'usu_cargo', 'usu_correo_corporativo', 'usu_cuenta_banco', '
                usu_celular_corporativo', 'usu_ciudad', 'usu_direccion', 'usu_fecha_nacimiento', 'usu_contacto_emergencia', 'usu_fecha_aprobacion', '
                usu_arl', 'usu_eps', 'usu_alergia', 'usu_antecedentes_enfermedad', 'usu_medicamentos', 'usu_telefono_contacto'
            );
            $valores = array("'" . $CUsuario->getlogin() . "'", "'" . $CUsuario->getNombre() . "'", "md5('" . $CUsuario->getlogin() . "')",
                "'" . $CUsuario->getapellido() . "'", "'" . $CUsuario->getDocumento() . "'", "'" . $CUsuario->gettelefono() . "'",
                "'" . $CUsuario->getcelular() . "'", "'" . $CUsuario->getcorreo() . "'", "'" . $CUsuario->getperfil() . "'",
                "'" . $CUsuario->getEstado() . "'", "'" .
                $CUsuario->getRh() . "'", "'" . $CUsuario->getFecha_ingreso() . "'", "'" .
                $CUsuario->getRegional() . "'", "'" . $CUsuario->getCargo() . "'", "'" .
                $CUsuario->getCorreo_corporativo() . "'", "'" . $CUsuario->getcuenta_banco() . "'", "'" .
                $CUsuario->getCelular_corporativo() . "'", "'" . $CUsuario->getCiudad() . "'", "'" .
                $CUsuario->getDireccion() . "'", "'" . $CUsuario->getFecha_Nacimiento() . "'", "'" .
                $CUsuario->getContacto_emergencia() . "'", "'" . $CUsuario->getFecha_aprovacion() . "'", "'" .
                $CUsuario->getArl() . "'", "'" . $CUsuario->getEps() . "'", "'" .
                $CUsuario->getAlergia() . "'", "'" . $CUsuario->getAntecedentes_enfermedad() . "'", "'" .
                $CUsuario->getMedicamentos() . "'", "'" .
                $CUsuario->getTelefonoContacto()
                . "'");
        }
        $condicion = "usu_id = " . $CUsuario->getId();
        $r = $this->db->actualizarRegistro($tabla, $campos, $valores, $condicion);
        return $r;
    }

    function updateUserFecha($id, $fecha) {
        $tabla = "usuario";
        $campos = array('usu_fecha_ultimo_ingreso');
        $valores = array("'" . $fecha . "'");

        $condicion = "usu_id = " . $id;
        $r = $this->db->actualizarRegistro($tabla, $campos, $valores, $condicion);
        return $r;
    }

    function deleteUser($id) {
        $tabla = "usuario";
        $predicado = "usu_id = " . $id;
        $r = $this->db->borrarRegistro($tabla, $predicado);
        return $r;
    }

    function deleteUserPerfiles($id) {
        $tabla = "perfil_x_opcion";
        $predicado = "usu_id = " . $id;
        $r = $this->db->borrarRegistro($tabla, $predicado);
        return $r;
    }

    function getUserById($id) {
        $sql = "select u.*,p.per_nombre 
				from usuario u 
				inner join perfil p on u.per_id = p.per_id 
				where u.usu_id = " . $id;
        $r = $this->db->recuperarResultado($this->db->ejecutarConsulta($sql));
        if ($r)
            return $r;
        else
            return -1;
    }

    function getUserControlById($id) {
        $pre = "usu_id = " . $id;
        $r = $this->db->recuperarCampo('documento_equipo', 'deq_controla_alarmas', $pre);
        if ($r)
            return $r;
        else
            return -1;
    }

    function loadOptionsForUser($id) {
        $sql = "select o.opc_id,o.opc_nombre,o.opn_id,u.usu_id,u.uxo_nivel
				from opcion o
				left join usuario_x_opcion u on u.opc_id = o.opc_id and u.usu_id = " . $id . "
				order by o.opc_orden";
        $r = $this->db->ejecutarConsulta($sql);
        return $r;
    }

    function getPerfiles($criterio, $orden) {
        $perfiles = null;
        $sql = "select * from perfil where " . $criterio . " order by " . $orden;
        $r = $this->db->ejecutarConsulta($sql);
        if ($r) {
            $cont = 0;
            while ($w = mysql_fetch_array($r)) {
                $perfiles[$cont]['id'] = $w['per_id'];
                $perfiles[$cont]['nombre'] = $w['per_nombre'];
                $cont++;
            }
        }
        return $perfiles;
    }

    function getTipoActividad() {
        $respuestas[0]['id'] = 1;
        $respuestas[0]['nombre'] = 'Activo';
        $respuestas[1]['id'] = 0;
        $respuestas[1]['nombre'] = 'Inactivo';
        return $respuestas;
    }

    function getTipoRespuesta() {
        $respuestas[0]['id'] = 1;
        $respuestas[0]['nombre'] = 'Si';
        $respuestas[1]['id'] = 2;
        $respuestas[1]['nombre'] = 'No';
        return $respuestas;
    }

    function updatePass($id, $password) {
        $tabla = "usuario";
        $campos = array('usu_clave');
        $valores = array("md5('" . $password . "')");
        $condicion = "usu_id = " . $id;
        $r = $this->db->actualizarRegistro($tabla, $campos, $valores, $condicion);
        return $r;
    }

    function getTipoBusqueda($criterio, $orden) {
        $respuestas = null;
        $sql = "select * from tipo_busqueda_alarmas where " . $criterio . " order by " . $orden;
        $r = $this->db->ejecutarConsulta($sql);
        if ($r) {
            $cont = 0;
            while ($w = mysql_fetch_array($r)) {
                $respuestas[$cont]['id'] = $w['tib_id'];
                $respuestas[$cont]['nombre'] = $w['tib_nombre'];
                $cont++;
            }
        }
        return $respuestas;
    }

}

?>
