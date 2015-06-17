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
        $sql = "SELECT * FROM actividad WHERE idActividad = '" . $idActividad . "'";
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
     * Obtiene las actividades almacenadas en la bitacora de un usuario
     * @param type $idBitacora
     * @return type
     */
    function getActividadesSincronizar($idUsuario) {
        $actividades = null;
        $sql = "SELECT * FROM actividad a "
                . "INNER JOIN bitacora b ON a.idBitacora = b.idBitacora "
                . "WHERE a.sync AND b.idUsuario = $idUsuario";
        $r = $this->db->ejecutarConsulta($sql);
        if ($r) {
            $cont = 0;
            while ($w = mysql_fetch_array($r)) {
                $actividades[$cont]['idActividad'] = $w['idActividad'];
                $actividades[$cont]['idBitacora'] = $w['idBitacora'];
                $actividades[$cont]['fecha'] = $w['fecha'];
                $actividades[$cont]['fechaFin'] = $w['fechaFin'];
                $actividades[$cont]['descripcionActividadEjecutada'] = $w['descripcionActividadEjecutada'];
                $actividades[$cont]['condicionesClimaticas'] = $w['condicionesClimaticas'];
                $actividades[$cont]['condicionesTopologicas'] = $w['condicionesTopologicas'];
                $actividades[$cont]['observaciones'] = $w['observaciones'];
                $actividades[$cont]['idEstadoSalud'] = $w['idEstadoSalud'];
                $actividades[$cont]['numCuadrillas'] = $w['numCuadrillas'];
                $actividades[$cont]['totalPersonas'] = $w['totalPersonas'];
                $actividades[$cont]['totalPersonasContratadas'] = $w['totalPersonasContratadas'];
                $actividades[$cont]['cumplimientoParafiscales'] = $w['cumplimientoParafiscales'];
                $actividades[$cont]['cumplimientoSenalizacion'] = $w['cumplimientoSenalizacion'];
                $actividades[$cont]['cumplimientoEPP'] = $w['cumplimientoEPP'];
                $actividades[$cont]['cumplimientoCertificaciones'] = $w['cumplimientoCertificaciones'];
                $actividades[$cont]['estado'] = $w['estado'];
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
            $id = $predecesor . "_" . (str_replace("_", "", $w['lastId']) + 1);
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
                . $actividad->getCumplimientoCertificaciones() . ",'0','1'";
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
            $actividad->getCumplimientoCertificaciones(), '0', '1');
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
        $predicado = "idActividad = '" . $idActividad . "'";
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

    public function enviarActividades($idUsuario) {
        $r = 'true';
        $actividades = $this->getActividadesSincronizar($idUsuario);
        if (count($actividades) != 0) {
            require_once "./clases/nusoap-0.9.5/lib/nusoap.php";
            $cliente = new nusoap_client(DIRECCION_WEB_SERVICE_SINCRONIZACION);
            $error = $cliente->getError();
            if ($error) {
                $r = SERVIDOR_NO_DISPONIBLE;
            } else {
                $totalActividades = count($actividades);
                $exitosas = 0;
                foreach ($actividades as $actividad) {
                    $param = array("idActividad" => $actividad['idActividad'], 
                                   "idBitacora" => $actividad['idBitacora'], 
                                   "fecha" => $actividad['fecha'], 
                                   "fechaFin" => $actividad['fechaFin'], 
                                   "descripcionActividadEjecutada" => utf8_decode($actividad['descripcionActividadEjecutada']), 
                                   "condicionesClimaticas" => utf8_decode($actividad['condicionesClimaticas']), 
                                   "condicionesTopologicas" => utf8_decode($actividad['condicionesTopologicas']), 
                                   "observaciones" => utf8_decode($actividad['observaciones']), 
                                   "idEstadoSalud" => $actividad['idEstadoSalud'], 
                                   "numCuadrillas" => $actividad['numCuadrillas'], 
                                   "totalPersonas" => $actividad['totalPersonas'], 
                                   "totalPersonasContratadas" => $actividad['totalPersonasContratadas'], 
                                   "cumplimientoParafiscales" => $actividad['cumplimientoParafiscales'], 
                                   "cumplimientoSenalizacion" => $actividad['cumplimientoSenalizacion'], 
                                   "cumplimientoEPP" => $actividad['cumplimientoEPP'], 
                                   "cumplimientoCertificaciones" => $actividad['cumplimientoCertificaciones'], 
                                   "estado" => $actividad['estado']);
                    $result = $cliente->call("insertarActividad", $param);
                    if ($cliente->fault) {
                        $r = NO_EXISTE_SINCRONIZACION;
                    } else {
                        $error = $cliente->getError();
                        if ($error) {
                            $r = ERROR_CONEXION;
                        } else {
                            if ($result) {
                                $exitosas++;
                                $this->setSyncActividad($actividad['idActividad'], 0);
                            }
                        }
                    }
                }
                if (($exitosas / $totalActividades) == 1) {
                    $r = $exitosas . " " . SINCRONIZACION_RECIBIDA;
                } else {
                    $r = SINCRONIZACION_INCOMPLETA;
                }
            }
        } else {
            $r = NO_SINCRONIZAR;
        }
        return $r;
    }

    function setSyncActividad($id, $valor) {
        $tabla = "actividad";
        $campos = array('sync');
        $valores = array($valor);
        $condicion = " idActividad = '$id'";
        $r = $this->db->actualizarRegistro($tabla, $campos, $valores, $condicion);
        return $r;
    }

}
