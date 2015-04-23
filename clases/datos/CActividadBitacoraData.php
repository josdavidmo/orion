<?php

/**
 * Clase Actividad Bitacora Data
 * Usado para la conexion con la base de datos.
 * @package clases
 * @subpackage aplicacion
 * @author SERTIC SAS
 * @version 2014.11.02
 * @copyright SERTIC
 */
class CActividadBitacoraData {

    /** Manejador de la base de datos. */
    var $db = null;

    /**
     * Constructor de la clase.
     * @param \CData $db
     */
    function CActividadBitacoraData($db) {
        $this->db = $db;
    }

    /**
     * Obtiene las actividades almacenadas en la bitacora de un usuario
     * @param type $idActividad
     * @return type
     */
    function getActividadById($idActividad) {
        $actividad = null;
        $sql = "SELECT * FROM actividad WHERE idActividad = '" . $idActividad."'";
        $r = $this->db->ejecutarConsulta($sql);
        if ($r) {
            $w = mysql_fetch_array($r);
            $actividad = new CActividadBitacora($w['idActividad'], $w['idBitacora'], $w['fecha'], $w['fechaFin'], $w['descripcionActividadEjecutada'], $w['condicionesTopologicas'], $w['condicionesClimaticas'], $w['observaciones'], $w['idEstadoSalud'], $w['numCuadrillas'], $w['totalPersonas'], $w['totalPersonasContratadas'], $w['cumplimientoParafiscales'], $w['cumplimientoSenalizacion'], $w['cumplimientoEPP'], $w['cumplimientoCertificaciones'], $w['estado']);
        }
        return $actividad;
    }

    /**
     * Obtiene las actividades almacenadas en la bitacora de un usuario
     * @param type $idBitacora
     * @return type
     */
    function getActividadByBitacora($idBitacora) {
        $actividades = null;
        $sql = "SELECT idActividad, fecha, descripcionActividadEjecutada, "
                . "(SELECT COUNT(*) FROM hallazgospendientes WHERE hallazgospendientes.idActividad = actividad.idActividad) as hallazgos,"
                . "estado "
                . "FROM actividad "
                . "INNER JOIN estadosalud ON estadosalud.idEstadoSalud = actividad.idEstadoSalud "
                . "WHERE idBitacora = '$idBitacora'";
        $r = $this->db->ejecutarConsulta($sql);
        if ($r) {
            $cont = 0;
            while ($w = mysql_fetch_array($r)) {
                $actividades[$cont]['idActividad'] = $w['idActividad'];
                $actividades[$cont]['fecha'] = $w['fecha'];
                $actividades[$cont]['descripcionActividadEjecutada'] = $w['descripcionActividadEjecutada'];
                $actividades[$cont]['hallazgos'] = $w['hallazgos'];
                $estado = $w['estado'];
                $actividades[$cont]['estado'] = '<img src=./templates/img/ico/verde.gif> Completado';
                if ($estado == 0) {
                    $actividades[$cont]['estado'] = '<img src=./templates/img/ico/rojo.gif> No Completado';
                }
                $cont++;
            }
        }
        return $actividades;
    }

    /**
     * Construye el id unico para la tabla seleccionada.
     * @param $idBitacora $actividad.
     * @return string
     */
    public function construirId($predecesor, $tabla, $columna) {
        $longitud = strlen($predecesor) + 2;
        $sql = "SELECT MAX(CAST(substring($columna, $longitud) AS UNSIGNED)) as lastId FROM $tabla where $columna like '$predecesor%'";
        //echo "<br>Consulta id: $sql<br>";
        $r = $this->db->ejecutarConsulta($sql);
        if ($r) {
            $w = mysql_fetch_array($r);
            $id = $predecesor."_".(str_replace("_","",$w['lastId'])+1);
        }
        return $id;
    }

    /**
     * Inserta una actividad en la base de datos.
     * @param \CActividadBitacora $actividad
     * @return type
     */
    public function insertActividad($actividad) {
        $tabla = "actividad";
        $actividad->setId($this->construirId($actividad->getBitacora(), $tabla, 'idActividad'));
        $columnas = $this->db->getCampos($tabla);
        unset($columnas[count($columnas) - 1]);
        $campos = "";
        foreach ($columnas as $columna) {
            $campos .= $columna . ",";
        }
        $campos = substr($campos, 0, -1);
        $valores = "'" . $actividad->getId() . "','"
                . $actividad->getBitacora() . "','"
                . $actividad->getFecha() . "','"
                . $actividad->getFechaFin() . "','"
                . $actividad->getDescripcionActividadesEjecutadas() . "','"
                . $actividad->getCondicionesClimaticas() . "','"
                . $actividad->getCondicionesTopologicas() . "','"
                . $actividad->getObservaciones() . "','"
                . $actividad->getEstadoSalud() . "',"
                . $actividad->getNumeroCuadrillas() . ","
                . $actividad->getTotalPersonas() . ","
                . $actividad->getTotalPersonasContratadas() . ","
                . $actividad->getCumplimientoParafiscales() . ","
                . $actividad->getCumplimientoSenalizacion() . ","
                . $actividad->getCumplimientoEpp() . ","
                . $actividad->getCumplimientoCertificaciones().",'1'";
        $r = $this->db->insertarRegistro($tabla, $campos, $valores);
        return $r;
    }

    /**
     * Actualiza una actividad bitacora de la base de datos.
     * @param \CActividadBitacora $actividad
     * @return type
     */
    public function updateActividad($actividad) {
        $r = true;
        $tabla = "actividad";
        $condicion = "idActividad = '" . $actividad->getId() . "'";
        $campos = $this->db->getCampos($tabla);
        unset($campos[count($campos) - 1]);
        $valores = array("'" . $actividad->getId() . "'",
            "'" . $actividad->getBitacora() . "'",
            "'" . $actividad->getFecha() . "'",
            "'" . $actividad->getFechaFin() . "'",
            "'" . $actividad->getDescripcionActividadesEjecutadas() . "'",
            "'" . $actividad->getCondicionesClimaticas() . "'",
            "'" . $actividad->getCondicionesTopologicas() . "'",
            "'" . $actividad->getObservaciones() . "'",
            "'" . $actividad->getEstadoSalud() . "'",
            $actividad->getNumeroCuadrillas(),
            $actividad->getTotalPersonas(),
            $actividad->getTotalPersonasContratadas(),
            $actividad->getCumplimientoParafiscales(),
            $actividad->getCumplimientoSenalizacion(),
            $actividad->getCumplimientoEpp(),
            $actividad->getCumplimientoCertificaciones(),'0');
        $r = $this->db->actualizarRegistro($tabla, $campos, $valores, $condicion);
        return $r;
    }

    /**
     * Elimina una actividad dado el su id.
     * @param type $idActividad
     * @return type
     */
    public function deleteActividadById($idActividad) {
        $tabla = "actividad";
        $predicado = "idActividad = '" . $idActividad. "'";
        $r = $this->db->borrarRegistro($tabla, $predicado);
        return $r;
    }

    /**
     * Cambia el estado de una actividad tomando el valor opuesto al mostrado.
     * @param type $idActividad
     * @return type
     */
    public function cambiarEstado($idActividad, $estado = "") {
        $r = 'false';
        $actividad = $this->getActividadById($idActividad);
        $tabla = "actividad";
        $columnas = $this->db->getCampos($tabla);
        $valores = array($estado);
        if ($estado == "") {
            $valores = array(0);
            if ($this->getAprobacionGastosByActividad($idActividad)) {
                if ($actividad->getEstado() == 0) {
                    $valores = array(1);
                }
                $r = 'true';
            }
        }
        $campos = array($columnas[count($columnas) - 1]);
        $condicion = $columnas[0] . " = " . $actividad->getId();
        $this->db->actualizarRegistro($tabla, $campos, $valores, $condicion);
        return $r;
    }

    /**
     * Obtiene los gastos aprobados por una actividad
     * @param type $idActividad
     * @return boolean
     */
    private function getAprobacionGastosByActividad($idActividad) {
        $sql = "SELECT SUM(estado)/COUNT(idGastosActividad) as estado "
                . "FROM gastos_actividad "
                . "WHERE idActividad = '$idActividad'";
        $r = $this->db->ejecutarConsulta($sql);
        if ($r) {
            $w = mysql_fetch_array($r);
            if ($w['estado'] == "1") {
                return true;
            } else {
                return false;
            }
        }
    }

    function getActividadesSincronizacion($usuario) {
        $actividades = null;
        $sql = "SELECT actividad.* FROM actividad inner join bitacora "
                . "on actividad.idBitacora = bitacora.idBitacora "
                . "WHERE actividad.sync = 1 AND bitacora.idUsuario = " . $usuario;
        $r = $this->db->ejecutarConsulta($sql);
        if ($r) {
            $cont = 0;
            while ($w = mysql_fetch_array($r)) {
                $actividades[count($actividades)] = new actividad($w['idActividad'], 
                        $w['idBitacora'], $w['fecha'], $w['fechaFin'], 
                        $w['descripcionActividadEjecutada'], $w['condicionesTopologicas'], 
                        $w['condicionesClimaticas'], $w['observaciones'], $w['idEstadoSalud'], 
                        $w['numCuadrillas'], $w['totalPersonas'], $w['totalPersonasContratadas'], 
                        $w['cumplimientoParafiscales'], $w['cumplimientoSenalizacion'], 
                        $w['cumplimientoEPP'], $w['cumplimientoCertificaciones'], $w['estado']);
            }
        }
        return $actividades;
    }
    

    
}
