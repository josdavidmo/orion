<?php

/**
 * Clase Actividad Plan de Accion Data
 * Usado para la conexion con la base de datos.
 * @package clases
 * @subpackage datos
 * @author SERTIC SAS
 * @version 2014.12.13
 * @copyright SERTIC SAS
 */
class CActividadPlanAccionData {

    /** Almacena la conexion con la base de datos. */
    var $db;

    /**
     * Constructor de la clase.
     * @param \CData $db
     */
    function CActividadPlanAccionData($db) {
        $this->db = $db;
    }

    /**
     * Constructor de la clase.
     * @param type $idActividad
     * @return \CActividadPlanAccion
     */
    function getActividadPlanAccionById($idActividad) {
        $actividad = null;
        $sql = "SELECT * FROM actividad_plan_accion "
                . "WHERE idActividadPlanAccion = " . $idActividad;
        $r = $this->db->ejecutarConsulta($sql);
        if ($r) {
            $w = mysql_fetch_array($r);
            $actividad = new CActividadPlanAccion($w['idActividadPlanAccion'], 
                                                  $w['descripcion'], 
                                                  $w['fecha'], 
                                                  $w['recursos'], 
                                                  $w['idPlanAccion'], 
                                                  $w['idUsuario'], 
                                                  $w['fechaCumplimiento'], 
                                                  $w['soporte']);
        }
        return $actividad;
    }

    /**
     * Obtiene todas las actividades plan de accion dado el id del plan de
     * accion.
     * @param type $idPlanAccion
     * @return type
     */
    function getActividadesByPlanAccion($idPlanAccion) {
        $actividades = null;
        $sql = "SELECT idActividadPlanAccion, descripcion, fecha, recursos, "
                . "fechaCumplimiento, soporte, "
                . "CONCAT(usu_nombre, ' ', usu_apellido) as usuario "
                . "FROM actividad_plan_accion "
                . "INNER JOIN usuario on idUsuario = usu_id "
                . "WHERE idPlanAccion = " . $idPlanAccion;
        $r = $this->db->ejecutarConsulta($sql);
        if ($r) {
            $cont = 0;
            while ($w = mysql_fetch_array($r)) {
                $actividades[$cont]['idActividadPlanAccion'] = $w['idActividadPlanAccion'];
                $actividades[$cont]['descripcion'] = $w['descripcion'];
                $actividades[$cont]['fecha'] = $w['fecha'];
                $actividades[$cont]['recursos'] = $w['recursos'];
                $actividades[$cont]['fechaCumplimiento'] = $w['fechaCumplimiento'];
                $actividades[$cont]['soporte'] = "<a href='" . RUTA_DOCUMENTOS . "/planesAccion/actividades/" . $w['soporte'] . "' >" . $w['soporte'] . "</a>";
                $actividades[$cont]['usuario'] = $w['usuario'];
                $actividades[$cont]['imagen'] = '<img src=./templates/img/ico/verde.gif> Cerrado';
                if($w['fechaCumplimiento'] == ""){
                    $actividades[$cont]['imagen'] = '<img src=./templates/img/ico/rojo.gif> Abierto';
                }
                $cont++;
            }
        }
        return $actividades;
    }

    /**
     * Inserta una actividad en la base de datos.
     * @param \CActividadPlanAccion $actividad
     * @return type
     */
    public function insertActividad($actividad) {
        $tabla = "actividad_plan_accion";
        $columnas = $this->db->getCampos($tabla);
        $campos = "";
        foreach ($columnas as $columna) {
            $campos .= $columna . ",";
        }
        $campos = substr($campos, 0, -1);
        $valores = "'" . $actividad->getId() . "','"
                . $actividad->getDescripcion() . "','"
                . $actividad->getFecha() . "','"
                . $actividad->getRecursos() . "','"
                . $actividad->getFechaCumplimiento() . "','"
                . $actividad->getSoporte() . "','"
                . $actividad->getPlanAccion() . "','"
                . $actividad->getUsuario() . "'";
        $r = $this->db->insertarRegistro($tabla, $campos, $valores);
        return $r;
    }
    
    /**
     * Elimina una actividad plan accion dado el su id.
     * @param type $idActividad
     * @return type
     */
    public function deletePlanAccionById($idActividad) {
        $tabla = "actividad_plan_accion";
        $predicado = "idActividadPlanAccion = " . $idActividad;
        $r = $this->db->borrarRegistro($tabla, $predicado);
        return $r;
    }
    
    /**
     * Actualizar una actividad de la base de datos.
     * @param \CActividadPlanAccion $actividad
     * @return type
     */
    public function updatePlanAccion($actividad) {
        $tabla = "actividad_plan_accion";
        $campos = $this->db->getCampos($tabla);
        $condicion = $campos[0] . " = " . $actividad->getId();
        $valores = array("'" . $actividad->getId() . "'",
                    "'" . $actividad->getDescripcion() . "'",
                    "'" . $actividad->getFecha() . "'",
                    "'" . $actividad->getRecursos() . "'",
                    "'" . $actividad->getFechaCumplimiento() . "'",
                    "'" . $actividad->getSoporte() . "'",
                    "'" . $actividad->getPlanAccion() . "'",
                    "'" . $actividad->getUsuario() . "'");
        $r = $this->db->actualizarRegistro($tabla, $campos, $valores, $condicion);
        return $r;
    }
    
    /**
     * Actualiza una actividad de la base de datos.
     * @param \CActividadPlanAccion $actividad
     * @return type
     */
    public function answerPlanAccion($actividad) {
        $tabla = "actividad_plan_accion";
        $campos = array("fechaCumplimiento", "soporte");
        $condicion = "idActividadPlanAccion = " . $actividad->getId();
        $valores = array("'" . $actividad->getFechaCumplimiento() . "'",
                    "'" . $actividad->getSoporte()['name'] . "'");
        $ruta = "planesAccion/actividades";
        if($this->db->guardarArchivo($actividad->getSoporte(), $ruta)){
            $r = $this->db->actualizarRegistro($tabla, $campos, $valores, $condicion);
        }
        return $r;
    }
    

}
