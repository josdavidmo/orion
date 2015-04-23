<?php

/**
 * Clase Plan de Accion Data
 * Usado para la conexion con la base de datos.
 * @package clases
 * @subpackage datos
 * @author SERTIC SAS
 * @version 2014.12.13
 * @copyright SERTIC SAS
 */
class CPlanAccionData {

    /** Almacena la conexion de la clase. */
    var $db = null;

    /**
     * Constructor de la clase.
     * @param \CData $db
     */
    function CPlanAccionData($db) {
        $this->db = $db;
    }

    /**
     * Obtiene un plan de accion dado el id del mismo.
     * @param type $idPlanAccion
     * @return \CPlanAccion
     */
    function getPlanAccionById($idPlanAccion) {
        $planAccion = null;
        $sql = "SELECT * FROM planes_accion WHERE idPlanAccion = " . $idPlanAccion;
        $r = $this->db->ejecutarConsulta($sql);
        if ($r) {
            $w = mysql_fetch_array($r);
            $planAccion = new CPlanAccion($w['idPlanAccion'], $w['descripcion'], $w['fechaInicio'], $w['consecutivo'], $w['fechaLimite'], $w['soporte'], $w['idUsuario'], $w['idFuente']);
        }
        return $planAccion;
    }

    /**
     * Obtiene todos los planes de accion almacenados en la tabla plan de 
     * accion.
     * @param type $condicion
     * @return \CPlanAccion
     */
    function getPlanesAccion($condicion = "1") {
        $planesAccion = null;
        $sql = "SELECT idPlanAccion, descripcion, fechaInicio, consecutivo, "
                . "fechaLimite, soporte, descripcionFuentePlanAccion, "
                . "(SELECT sum(isnull(fechaCumplimiento))/count(idActividadPlanAccion) "
                . "FROM actividad_plan_accion "
                . "WHERE actividad_plan_accion.idPlanAccion = planes_accion.idPlanAccion) as estado, "
                . "CONCAT(usu_nombre,' ', usu_apellido) as usuario "
                . "FROM planes_accion "
                . "INNER JOIN usuario ON usu_id = idUsuario "
                . "INNER JOIN fuente_plan_accion ON idFuentePlanAccion = idFuente "
                . "WHERE " . $condicion;
        $r = $this->db->ejecutarConsulta($sql);
        if ($r) {
            $cont = 0;
            while ($w = mysql_fetch_array($r)) {
                $planesAccion[$cont]['idPlanAccion'] = $w['idPlanAccion'];
                $planesAccion[$cont]['descripcion'] = $w['descripcion'];
                $planesAccion[$cont]['fechaInicio'] = $w['fechaInicio'];
                $planesAccion[$cont]['consecutivo'] = $w['consecutivo'];
                $planesAccion[$cont]['fechaLimite'] = $w['fechaLimite'];
                $planesAccion[$cont]['soporte'] = "<a href='" . RUTA_DOCUMENTOS . "/planesAccion/" . $w['soporte'] . "' >" . $w['soporte'] . "</a>";
                $planesAccion[$cont]['usuario'] = $w['usuario'];
                $planesAccion[$cont]['descripcionFuentePlanAccion'] = $w['descripcionFuentePlanAccion'];
                if ($w['estado'] == "0") {
                    $planesAccion[$cont]['imagen'] = '<img src=./templates/img/ico/verde.gif> Cerrado';
                } else {
                    date_default_timezone_set('America/Bogota');
                    $fechaActual = date("Y-m-d");
                    if ($fechaActual < $w['fechaLimite']) {
                        $planesAccion[$cont]['imagen'] = '<img src=./templates/img/ico/amarillo.gif> Vigente';
                    } else {
                        $planesAccion[$cont]['imagen'] = '<img src=./templates/img/ico/rojo.gif> Vencido';
                    }
                }
                $cont++;
            }
        }
        return $planesAccion;
    }

    /**
     * Obtiene los usuarios almacenados en el sistema.
     * @return \CBasica
     */
    function getUsuarios() {
        $usuarios = null;
        $sql = "SELECT  usu_id, "
                . "CONCAT(usu_nombre,'  ',usu_apellido) AS usu_nombre "
                . "FROM usuario order by usu_nombre";
        $r = $this->db->ejecutarConsulta($sql);
        if ($r) {
            $cont = 0;
            while ($w = mysql_fetch_array($r)) {
                $usuarios[$cont] = new CBasica($w['usu_id'], $w['usu_nombre']);
                $cont++;
            }
        }
        return $usuarios;
    }

    /**
     * Obtiene un plan de accion de la base de datos.
     * @param \CPlanAccion $planAccion
     * @return type
     */
    public function insertPlanAccion($planAccion) {
        $r = "false";
        $ruta = "planesAccion";
        if ($this->db->guardarArchivo($planAccion->getSoporte(), $ruta)) {
            $tabla = "planes_accion";
            $columnas = $this->db->getCampos($tabla);
            $campos = "";
            foreach ($columnas as $columna) {
                $campos .= $columna . ",";
            }
            $campos = substr($campos, 0, -1);
            $valores = "'" . $planAccion->getId() . "','"
                    . $planAccion->getDescripcion() . "','"
                    . $planAccion->getFechaInicio() . "','"
                    . $planAccion->getConsecutivo() . "','"
                    . $planAccion->getFechaLimite() . "','"
                    . $planAccion->getSoporte()['name'] . "','"
                    . $planAccion->getUsuario() . "','"
                    . $planAccion->getFuente() . "'";
            $r = $this->db->insertarRegistro($tabla, $campos, $valores);
        }
        return $r;
    }

    /**
     * Elimina una plan accion dado el su id.
     * @param type $idPlanAccion
     * @return type
     */
    public function deletePlanAccionById($idPlanAccion) {
        $tabla = "planes_accion";
        $predicado = "idPlanAccion = " . $idPlanAccion;
        $r = $this->db->borrarRegistro($tabla, $predicado);
        return $r;
    }

    /**
     * Actualiza una actividad bitacora de la base de datos.
     * @param \CPlanAccion $planAccion
     * @return type
     */
    public function updatePlanAccion($planAccion) {
        $tabla = "planes_accion";
        $campos = $this->db->getCampos($tabla);
        $condicion = $campos[0] . " = " . $planAccion->getId();
        $valores = NULL;
        if ($planAccion->getSoporte()['name'] != "") {
            $ruta = "planesAccion";
            if ($this->db->guardarArchivo($planAccion->getSoporte(), $ruta)) {
                $valores = array("'" . $planAccion->getId() . "'",
                    "'" . $planAccion->getDescripcion() . "'",
                    "'" . $planAccion->getFechaInicio() . "'",
                    "'" . $planAccion->getConsecutivo() . "'",
                    "'" . $planAccion->getFechaLimite() . "'",
                    "'" . $planAccion->getSoporte()['name'] . "'",
                    "'" . $planAccion->getUsuario() . "'",
                    "'" . $planAccion->getFuente() . "'");
            }
        } else {
            unset($campos[5]);
            $valores = array("'" . $planAccion->getId() . "'",
                "'" . $planAccion->getDescripcion() . "'",
                "'" . $planAccion->getFechaInicio() . "'",
                "'" . $planAccion->getConsecutivo() . "'",
                "'" . $planAccion->getFechaLimite() . "'",
                "'" . $planAccion->getUsuario() . "'",
                "'" . $planAccion->getFuente() . "'");
        }
        $r = $this->db->actualizarRegistro($tabla, $campos, $valores, $condicion);
        return $r;
    }

}
