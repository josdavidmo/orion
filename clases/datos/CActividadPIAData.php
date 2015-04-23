<?php

/**
 * DAO para el modulo actividades plan de inversion del anticipo.
 * 
 * @author Brian Kings
 * @author Jose David Moreno Posada
 * @version 1.0
 * @since 08/08/2014
 */
class CActividadPIAData {

    /** Almacena el acceso a la base de datos. */
    var $db = null;

    /**
     * Constructor de la clase.
     * @param \CData $db
     */
    function CActividadPIAData($db) {
        $this->db = $db;
    }

    /**
     * Obtiene las actividades de plan de inversión del anticipo según el 
     * criterio dado.
     * @param type $criterio
     * @return type
     */
    function getActividadPIA($criterio) {
        $sql = "SELECT * FROM actividadpia";
        if ($criterio) {
            $sql = "SELECT * FROM actividadpia WHERE ".$criterio;
        }
        $actividades = null;
        $r = $this->db->ejecutarConsulta($sql);
        if ($r) {
            $cont = 0;
            while ($w = mysql_fetch_array($r)) {
                $actividades[$cont]['id_element'] = $w['act_id'];
                $actividades[$cont]['descripcion'] = $w['act_descripcion'];
                $actividades[$cont]['monto'] = $w['act_monto'];
                $cont++;
            }
        }
        return $actividades;
    }

    /**
     * Asigna las actividades de plan de inversion del anticipo.
     * @param \CActividadPIA $actividad
     * @return boolean
     */
    function insertActividadPIA($actividad) {
        $tabla = 'actividadpia';
        $campos = 'act_descripcion, act_monto';
        $valores = "'" . $actividad->getDescripcion() . "','" . $actividad->getMonto() . "'";
        $r = $this->db->insertarRegistro($tabla, $campos, $valores);
        return $r;
    }

    /**
     * Elimina una actividad de la tabla actividadpia dado el id de la actividad.
     * @param type $idActividad
     * @return type
     */
    function deleteActividadPIA($idActividad) {
        $tabla = "actividadpia";
        $predicado = "act_id = " . $idActividad;
        $r = $this->db->borrarRegistro($tabla, $predicado);
        return $r;
    }
    
    /**
     * Obtiene una actividad dado el id de la misma.
     * @param type $idActividad
     * @return \CActividadPIA
     */
    function getActividadPIAById($idActividad) {
        $sql = "SELECT * FROM actividadpia WHERE act_id = " . $idActividad;
        $r = $this->db->ejecutarConsulta($sql);
        $actividad = null;
        if ($r){
           $w = mysql_fetch_array($r);
           $actividad = new CActividadPIA($w['act_id'], 
                                          $w['act_descripcion'], 
                                          number_format(($w['act_monto']), 0, ',', '.'));
        }
        return $actividad;
    }

    /**
     * Actualiza su actividad dado su id y los nuevos valores.
     * @param type $id
     * @param \CActividadPIA $actividad
     * @return type
     */
    function updateActividadPIA($id, $actividad) {
        $tabla = 'actividadpia';
        $campos = array('act_descripcion', ' act_monto');
        $valores = array("'" . $actividad->getDescripcion() . "'", 
                         "'" . $actividad->getMonto() . "'");
        $condicion = " act_id = " . $id;
        $r = $this->db->actualizarRegistro($tabla, $campos, $valores, $condicion);
        return $r;
    }

}
