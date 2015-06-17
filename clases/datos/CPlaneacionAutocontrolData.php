<?php

/**
 * Clase Planeacion Autocontrol Data
 * Usado para la conexion con la base de datos.
 * @package clases
 * @subpackage datos
 * @author SERTIC SAS
 * @version 2014.11.14
 * @copyright SERTIC SAS
 */
class CPlaneacionAutocontrolData {

    /** Manejador de la base de datos. */
    var $db = null;

    /**
     * Constructor de la clase.
     * @param type $db
     */
    public function CPlaneacionAutocontrolData($db) {
        $this->db = $db;
    }

    /**
     * Obtiene la planeacion autocontrol dado el id del mismo.
     * @return \CPlaneacionAutocontrol
     */
    public function getPlaneacionAutocontrolById($idAutocontrol) {
        $autocontrol = null;
        $sql = "SELECT * "
                . "FROM planeacionautocontrol "
                . "WHERE idPlaneacionAutocontrol = " . $idAutocontrol;
        $r = $this->db->ejecutarConsulta($sql);
        if ($r) {
            $w = mysql_fetch_array($r);
            $autocontrol = new CPlaneacionAutocontrol($w['idPlaneacionAutocontrol'], $w['objetivos'], $w['idResponsable'], $w['IdResponsablePNC']);
        }
        return $autocontrol;
    }

    /**
     * Obtiene la planeacion control dado el id del mismo.
     * @return \CControl
     */
    public function getControlById($idControl) {
        $autocontrol = null;
        $sql = "SELECT * "
                . "FROM planeacioncontrol "
                . "WHERE idPlaneacionControl = " . $idControl;
        $r = $this->db->ejecutarConsulta($sql);
        if ($r) {
            $w = mysql_fetch_array($r);
            $control = new CControl($w['idPlaneacionControl'], $w['obligaciones'], $w['idResposanble'], $w['verificacion'], $w['numeroDocumentoContractual']);
        }
        return $control;
    }

    /**
     * Obtiene la planeacion del autocontrol dado el responsable.
     * @param type $idResponsable
     * @return type
     */
    public function getPlaneacionAutocontrolByResponsable($idResponsable) {
        $planeacionAutocontrol = null;
        $sql = "SELECT p.idPlaneacionAutocontrol, p.objetivos, "
                . "CONCAT(u.usu_nombre,' ',u.usu_apellido) as nombre "
                . "FROM planeacionautocontrol p, usuario u "
                . "WHERE p.idResponsable = " . $idResponsable . " AND "
                . "p.IdResponsablePNC = u.usu_id";
        $r = $this->db->ejecutarConsulta($sql);
        if ($r) {
            $cont = 0;
            while ($w = mysql_fetch_array($r)) {
                $planeacionAutocontrol[$cont]['idPlaneacionAutocontrol'] = $w['idPlaneacionAutocontrol'];
                $planeacionAutocontrol[$cont]['objetivos'] = $w['objetivos'];
                $planeacionAutocontrol[$cont]['responsablePNC'] = $w['nombre'];
                $cont++;
            }
        }
        return $planeacionAutocontrol;
    }

    /**
     * Obtiene la planeacion del autocontrol dado el responsable.
     * @param type $idResponsable
     * @return type
     */
    public function getPlaneacionControl($criterio = "1") {
        $planeacionAutocontrol = null;
        $sql = "SELECT p.idPlaneacionControl, p.obligaciones, p.verificacion, "
                . "p.numeroDocumentoContractual, "
                . "CONCAT(usu_nombre,' ', usu_apellido) as usuario, "
                . "(SELECT GROUP_CONCAT(descripcion SEPARATOR '\n ') FROM criteriosaceptacioncontrol c WHERE c.idPlaneacionControl = p.idPlaneacionControl) as criteriosAceptacion, "
                . "(SELECT GROUP_CONCAT(descripcion SEPARATOR '\n ') FROM metodologiacontrol m WHERE m.idPlaneacionControl = p.idPlaneacionControl) as metodologia, "
                . "(SELECT GROUP_CONCAT(descripcion SEPARATOR '\n ') FROM registrocontrol r WHERE r.idPlaneacionControl = p.idPlaneacionControl) as registro "
                . "FROM pncav2.planeacioncontrol p "
                . "INNER JOIN usuario ON idResponsable = usu_id "
                . "WHERE $criterio";
        $r = $this->db->ejecutarConsulta($sql);
        if ($r) {
            $cont = 0;
            while ($w = mysql_fetch_array($r)) {
                $planeacionAutocontrol[$cont]['idPlaneacionControl'] = $w['idPlaneacionControl'];
                $planeacionAutocontrol[$cont]['obligaciones'] = $w['obligaciones'];
                $planeacionAutocontrol[$cont]['verificacion'] = $w['verificacion'];
                $planeacionAutocontrol[$cont]['numeroDocumentoContractual'] = $w['numeroDocumentoContractual'];
                $planeacionAutocontrol[$cont]['usuario'] = $w['usuario'];
                $planeacionAutocontrol[$cont]['criteriosAceptacion'] = $w['criteriosAceptacion'];
                $planeacionAutocontrol[$cont]['metodologia'] = $w['metodologia'];
                $planeacionAutocontrol[$cont]['registro'] = $w['registro'];
                $cont++;
            }
        }
        return $planeacionAutocontrol;
    }
    
    /**
     * Obtiene la planeacion del autocontrol dado el responsable.
     * @return type
     */
    public function getPlaneacionAutocontrol($criterio = "1") {
        $planeacionAutocontrol = null;
        $sql = "SELECT idActividades, objetivos, descripcion, " 
                . "CONCAT(u.usu_nombre,' ', u.usu_apellido) as responsable, " 
                . "CONCAT(r.usu_nombre,' ', r.usu_apellido) as responsablePNC, "
                . "(SELECT GROUP_CONCAT(descripcion SEPARATOR '\n ') FROM fuentedatos f WHERE f.idActividad = a.idActividades) as fuentedatos, "
                . "(SELECT GROUP_CONCAT(descripcion SEPARATOR '\n ') FROM registro r WHERE r.idActividad = a.idActividades) as registro "
                . "FROM actividadescontrol a " 
                . "INNER JOIN planeacionautocontrol p ON p.idPlaneacionAutocontrol = a.idPlaneacionAutocontrol "
                . "INNER JOIN usuario u ON u.usu_id = p.idResponsable "
                . "INNER JOIN usuario r ON r.usu_id = p.idResponsablePNC "
                . "WHERE $criterio";
        $r = $this->db->ejecutarConsulta($sql);
        if ($r) {
            $cont = 0;
            while ($w = mysql_fetch_array($r)) {
                $planeacionAutocontrol[$cont]['idActividades'] = $w['idActividades'];
                $planeacionAutocontrol[$cont]['objetivo'] = $w['objetivos'];
                $planeacionAutocontrol[$cont]['actividad'] = $w['descripcion'];
                $planeacionAutocontrol[$cont]['responsable'] = $w['responsable'];
                $planeacionAutocontrol[$cont]['responsablePNC'] = $w['responsablePNC'];
                $planeacionAutocontrol[$cont]['fuentedatos'] = $w['fuentedatos'];
                $planeacionAutocontrol[$cont]['registro'] = $w['registro'];
                $cont++;
            }
        }
        return $planeacionAutocontrol;
    }

    /**
     * Obtiene la planeacion del autocontrol dado el responsable.
     * @param type $idResponsable
     * @return type
     */
    public function getPlaneacionControlByResponsable($idResponsable) {
        $planeacionAutocontrol = null;
        $sql = "SELECT * "
                . "FROM planeacioncontrol "
                . "WHERE idResponsable = " . $idResponsable;
        $r = $this->db->ejecutarConsulta($sql);
        if ($r) {
            $cont = 0;
            while ($w = mysql_fetch_array($r)) {
                $planeacionAutocontrol[$cont]['idPlaneacionControl'] = $w[0];
                $planeacionAutocontrol[$cont]['obligaciones'] = $w[1];
                $planeacionAutocontrol[$cont]['verificacion'] = $w[2];
                $planeacionAutocontrol[$cont]['numeroDocumentoContractual'] = $w[3];
                $cont++;
            }
        }
        return $planeacionAutocontrol;
    }

    /**
     * Obtiene la planeacion del autocontrol dado el responsable pnc.
     * @param type $idResponsablePNC
     * @return type
     */
    public function getPlaneacionAutocontrolByResponsablePNC($idResponsablePNC) {
        $planeacionAutocontrol = null;
        $sql = "SELECT p.idPlaneacionAutocontrol, p.objetivos, "
                . "CONCAT(u.usu_nombre,' ',u.usu_apellido) as nombre, "
                . "CONCAT(r.usu_nombre,' ',r.usu_apellido) as responsable "
                . "FROM planeacionautocontrol p, usuario u, usuario r "
                . "WHERE p.idResponsablePNC = " . $idResponsablePNC . " AND "
                . "p.IdResponsablePNC = u.usu_id AND "
                . "p.idResponsable = r.usu_id";
        $r = $this->db->ejecutarConsulta($sql);
        if ($r) {
            $cont = 0;
            while ($w = mysql_fetch_array($r)) {
                $planeacionAutocontrol[$cont]['idPlaneacionAutocontrol'] = $w['idPlaneacionAutocontrol'];
                $planeacionAutocontrol[$cont]['objetivos'] = $w['objetivos'];
                $planeacionAutocontrol[$cont]['responsablePNC'] = $w['nombre'];      
                $planeacionAutocontrol[$cont]['responsable'] = $w['responsable'];
                $cont++;
            }
        }
        return $planeacionAutocontrol;
    }

    /**
     * Obtiene las observaciones de un actividad.
     * @param type $idActividad
     * @return type
     */
    public function getObservacionesByIdActividad($idActividad) {
        $observaciones = null;
        $sql = "SELECT o.idObservacionAutocontrol, "
                . "DATE_FORMAT(o.periodo, '%b-%y') as periodo, "
                . "o.descripcion, t.descripcion as estado "
                . "FROM observacionautocontrol o, tipoobservacion t "
                . "WHERE o.idActividad = " . $idActividad . " AND "
                . "o.estado = t.idtipoObservacion ORDER BY o.periodo ASC";
        $r = $this->db->ejecutarConsulta($sql);
        if ($r) {   
            $cont = 0;
            while ($w = mysql_fetch_array($r)) {
                $observaciones[$cont]['idObservacionAutocontrol'] = $w['idObservacionAutocontrol'];
                $observaciones[$cont]['periodo'] = $w['periodo'];
                $observaciones[$cont]['descripcion'] = $w['descripcion'];
                $observaciones[$cont]['estado'] = $w['estado'];
                $cont++;
            }
        }
        return $observaciones;
    }

    /**
     * Obtiene las observaciones de un autocontrol.
     * @param type $idControl
     * @return type
     */
    public function getObservacionesByIdControl($idControl) {
        $observaciones = null;
        $sql = "SELECT o.idObservacionAutocontrol, "
                . "DATE_FORMAT(o.periodo, '%b-%y') as periodo, "
                . "o.descripcion, t.descripcion as estado "
                . "FROM observacionautocontrol o, tipoobservacion t "
                . "WHERE o.idControl = $idControl AND "
                . "o.estado = t.idtipoObservacion ORDER BY o.periodo ASC";
        $r = $this->db->ejecutarConsulta($sql);
        if ($r) {
            $cont = 0;
            while ($w = mysql_fetch_array($r)) {
                $observaciones[$cont]['idObservacionAutocontrol'] = $w['idObservacionAutocontrol'];
                $observaciones[$cont]['periodo'] = $w['periodo'];
                $observaciones[$cont]['descripcion'] = $w['descripcion'];
                $observaciones[$cont]['estado'] = $w['estado'];
                $cont++;
            }
        }
        return $observaciones;
    }
    
    /**
     * Obtiene las observaciones de un autocontrol.
     * @param type $idActividad
     * @return type
     */
    public function getObservacionesByIdAutocontrol($idActividad) {
        $observaciones = null;
        $sql = "SELECT o.idObservacionAutocontrol, "
                . "DATE_FORMAT(o.periodo, '%b-%y') as periodo, "
                . "o.descripcion, t.descripcion as estado "
                . "FROM observacionautocontrol o, tipoobservacion t "
                . "WHERE o.idActividad = $idActividad AND "
                . "o.estado = t.idtipoObservacion ORDER BY o.periodo ASC";
        $r = $this->db->ejecutarConsulta($sql);
        if ($r) {
            $cont = 0;
            while ($w = mysql_fetch_array($r)) {
                $observaciones[$cont]['idObservacionAutocontrol'] = $w['idObservacionAutocontrol'];
                $observaciones[$cont]['periodo'] = $w['periodo'];
                $observaciones[$cont]['descripcion'] = $w['descripcion'];
                $observaciones[$cont]['estado'] = $w['estado'];
                $cont++;
            }
        }
        return $observaciones;
    }

    public function getFechaAndCantidad($control = TRUE) {
        $cantidad = null;
        $query = "SELECT MIN(periodo) as fechaMinima, "
                  . "(12 * (YEAR(NOW()) - YEAR(MIN(periodo))) + MONTH(NOW()) - MONTH(MIN(periodo))) as meses "
                  . "FROM observacionautocontrol b";
        if($control){
            $query .= " WHERE idControl IS NOT NULL"; 
        } else {
            $query .= " WHERE idActividad IS NOT NULL";
        }
        $r = $this->db->ejecutarConsulta($query);
        if ($r) {
            $w = mysql_fetch_array($r);
            $cantidad['fechaMinima'] = $w['fechaMinima'];
            $cantidad['meses'] = $w['meses'];
        }
        return $cantidad;
    }

    /**
     * Obtiene una observación dado su id.
     * @param type $idObservacion
     * @return \CObservaciones
     */
    public function getObservacionesById($idObservacion) {
        $observacion = null;
        $sql = "SELECT * "
                . "FROM observacionautocontrol "
                . "WHERE idObservacionAutocontrol = " . $idObservacion;
        $r = $this->db->ejecutarConsulta($sql);
        if ($r) {
            while ($w = mysql_fetch_array($r)) {
                $observacion = new CObservaciones($w[0], substr($w[1], 0, -3), $w[2], $w[3], $w[4], $w[5]);
            }
        }
        return $observacion;
    }

    /**
     * Inserta una planeacion autocontrol en la base de datos
     * @param \CPlaneacionAutocontrol $autocontrol
     * @return type
     */
    public function insertAutocontrol($autocontrol) {
        $tabla = "planeacionautocontrol";
        $campos = "objetivos,idResponsable,idResponsablePNC";
        $valores = "'" . $autocontrol->getObjetivos() . "','"
                . $autocontrol->getResponsable() . "','"
                . $autocontrol->getResponsablePNC() . "'";
        $r = $this->db->insertarRegistro($tabla, $campos, $valores);
        return $r;
    }

    /**
     * Inserta una planeacion control en la base de datos
     * @param \CControl $control
     * @return type
     */
    public function insertControl($control) {
        $tabla = "planeacioncontrol";
        $campos = "obligaciones,verificacion,numeroDocumentoContractual,idResponsable";
        $valores = "'" . $control->getObligaciones() . "','"
                . $control->getVerificacion() . "','"
                . $control->getNumeroDocumentoContractual() . "','"
                . $control->getResponsable() . "'";
        $r = $this->db->insertarRegistro($tabla, $campos, $valores);
        return $r;
    }

    /**
     * Inserta una observacion en la base de datos.
     * @param \CObservaciones $observacion
     * @return type
     */
    public function insertObservacion($observacion) {
        $tabla = "observacionautocontrol";
        $campos = "periodo,descripcion,idActividad,estado,idControl";
        $valores = "'" . $observacion->getPeriodo() . "','"
                . $observacion->getDescripcion() . "','"
                . $observacion->getAutocontrol() . "','"
                . $observacion->getEstado() . "','"
                . $observacion->getControl() . "'";
        $r = $this->db->insertarRegistro($tabla, $campos, $valores);
        return $r;
    }

    /**
     * Actualiza un autocontrol de la base de datos.
     * @param \CPlaneacionAutocontrol $autocontrol
     * @return type
     */
    public function updateAutocontrol($autocontrol) {
        $tabla = "planeacionautocontrol";
        $campos = array('objetivos', 'idResponsable', 'idResponsablePNC');
        $valores = array("'" . $autocontrol->getObjetivos() . "'",
            "'" . $autocontrol->getResponsable() . "'",
            "'" . $autocontrol->getResponsablePNC() . "'");
        $condicion = "idPlaneacionAutocontrol = " . $autocontrol->getId();
        $r = $this->db->actualizarRegistro($tabla, $campos, $valores, $condicion);
        return $r;
    }

    /**
     * Actualiza un autocontrol de la base de datos.
     * @param \CControl $control
     * @return type
     */
    public function updateControl($control) {
        $tabla = "planeacioncontrol";
        $campos = array('obligaciones', 'verificacion', 'numeroDocumentoContractual');
        $valores = array("'" . $control->getObligaciones() . "'",
            "'" . $control->getVerificacion() . "'",
            "'" . $control->getNumeroDocumentoContractual() . "'");
        $condicion = "idPlaneacionControl = " . $control->getId();
        $r = $this->db->actualizarRegistro($tabla, $campos, $valores, $condicion);
        return $r;
    }

    /**
     * Actualiza una observacion de la base de datos.
     * @param \CObservaciones $observacion
     * @return type
     */
    public function updateObservacion($observacion) {
        $tabla = "observacionautocontrol";
        $campos = array('periodo', 'descripcion', 'estado');
        $valores = array("'" . $observacion->getPeriodo() . "'",
            "'" . $observacion->getDescripcion() . "'",
            "'" . $observacion->getEstado() . "'");
        $condicion = "idObservacionAutocontrol = " . $observacion->getId();
        $r = $this->db->actualizarRegistro($tabla, $campos, $valores, $condicion);
        return $r;
    }

    /**
     * Elimina un autocontrol dado su id.
     * @param type $idAutocontrol
     * @return type
     */
    public function deleteAutocontrolById($idAutocontrol) {
        $tabla = "planeacionautocontrol";
        $predicado = "idPlaneacionAutocontrol = " . $idAutocontrol;
        $r = $this->db->borrarRegistro($tabla, $predicado);
        return $r;
    }

    /**
     * Elimina un control dado su id.
     * @param type $idControl
     * @return type
     */
    public function deleteControlById($idControl) {
        $tabla = "planeacioncontrol";
        $predicado = "idPlaneacionControl = " . $idControl;
        $r = $this->db->borrarRegistro($tabla, $predicado);
        return $r;
    }

    /**
     * Elimina una observacion dado su id.
     * @param type $idObservacion
     * @return type
     */
    public function deleteObservacionById($idObservacion) {
        $tabla = "observacionautocontrol";
        $predicado = "idObservacionAutocontrol = " . $idObservacion;
        $r = $this->db->borrarRegistro($tabla, $predicado);
        return $r;
    }

}
