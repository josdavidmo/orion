<?php

/**
 * Clase Hallazgos Pendientes Data
 * Usado para la conexion con la base de datos.
 * @package clases
 * @subpackage datos
 * @author SERTIC SAS
 * @version 2014.10.30
 * @copyright SERTIC SAS
 */
class CHallazgosPendientesData {

    /** Almacena la conexion con la base de datos. */
    var $db;
    /** Almacena el CActividadData. */
    var $daoActividad;
    /** Almacena el CBitacoraData. */
    var $daoBitacora;

    /**
     * Constructor de la clase.
     * @param type $db
     */
    function CHallazgosPendientesData($db) {
        $this->db = $db;
        $this->daoActividad = new CActividadBitacoraData($this->db);
        $this->daoBitacora = new CBitacoraData($this->db);
    }

    /**
     * Obtiene los hallazgos pendientes almacenados en la base de datos.
     * @return string
     */
    function getHallazgosPendientes($criterio = "1") {
        $criterio = str_replace("#", "'", $criterio);
        $hallazgosPendientes = null;
        $sql = "SELECT CONCAT(re.der_nombre,'-',de.dep_nombre,'-',mu.mun_nombre,'-',c.nombre,' ',be.nombre,' (',t.descripcionTipoBeneficiario,')') as beneficiario, "
                . "CONCAT(ar.descripcion,' ',ti.descripcionTipoHallazgo) as clasificacion, "
                . "CONCAT(usu_nombre,' ',usu_apellido) as usuario, a.idActividad, "
                . "DATE_FORMAT(a.fecha, '%Y-%m-%d') as fecha, h.* "
                . "FROM hallazgospendientes h "
                . "INNER JOIN tipohallazgo ti ON ti.idTipoHallazgo = h.idTipo "
                . "INNER JOIN areashallazgospendientes ar ON ti.idAreaHallazgo = ar.idAreaHallazgo "
                . "INNER JOIN actividad a ON a.idActividad = h.idActividad "
                . "INNER JOIN bitacora b ON b.idBitacora = a.idBitacora "
                . "INNER JOIN usuario u ON b.idUsuario = u.usu_id "
                . "INNER JOIN beneficiario be ON be.idBeneficiario = b.idBeneficiario "
                . "INNER JOIN tipobeneficiario t ON t.idTipoBeneficiario = be.idTipoBeneficiario "
                . "INNER JOIN centropoblado c ON c.idCentroPoblado = be.idCentroPoblado "
                . "INNER JOIN municipio mu ON mu.mun_id = c.mun_id "
                . "INNER JOIN departamento de ON de.dep_id = mu.dep_id "
                . "INNER JOIN departamento_region re ON re.der_id = de.der_id "
                . "WHERE " . $criterio;
        $r = $this->db->ejecutarConsulta($sql);
        if ($r) {
            $cont = 0;
            while ($w = mysql_fetch_array($r)) {
                $actividad = $this->daoActividad->getActividadById($w['idActividad']);
                $bitacora = $this->daoBitacora->getBitacoraById($actividad->getBitacora());

                $date = new DateTime($actividad->getFecha());
                $ruta = RUTA_DOCUMENTOS . "/" . RUTA_BITACORA . "/" . $bitacora->getUsuario() . "/" . $bitacora->getId() . "/" . $date->format('Y-m-d') . "/" . RUTA_HALLAZGOS;
                $hallazgosPendientes[$cont]['idHallazgosPendientes'] = $w['idHallazgosPendientes'];
                $hallazgosPendientes[$cont]['beneficiario'] = $w['beneficiario'];
                $hallazgosPendientes[$cont]['clasificacion'] = $w['clasificacion'];
                $hallazgosPendientes[$cont]['usuario'] = $w['usuario'];
                $hallazgosPendientes[$cont]['fechaInicio'] = $w['fecha'];
                $hallazgosPendientes[$cont]['observacion'] = $w['observacion'];
                $hallazgosPendientes[$cont]['archivo'] = "<a href='" . $ruta . "/" . $w['archivo'] . "'>" . $w['archivo'] . "</a>";
                $hallazgosPendientes[$cont]['fecha'] = $w['fechaRespuesta'];
                $hallazgosPendientes[$cont]['observacionRespuesta'] = $w['observacionRespuesta'];
                $hallazgosPendientes[$cont]['archivoRespuesta'] = "<a href='" . $ruta . "/" . $w['archivoRespuesta'] . "'>" . $w['archivoRespuesta'] . "</a>";
                $hallazgosPendientes[$cont]['estado'] = '<img src=./templates/img/ico/verde.gif> Cerrado';
                if ($w['fechaRespuesta'] == NULL) {
                    $hallazgosPendientes[$cont]['estado'] = '<img src=./templates/img/ico/rojo.gif> Abierto';
                }
                $cont++;
            }
        }
        return $hallazgosPendientes;
    }

    /**
     * Obtiene los hallazgos pendientes almacenados en la base de datos.
     * @return string
     */
    function getHallazgosPendientesExcel($criterio = "1") {
        $criterio = str_replace("#", "'", $criterio);
        $hallazgosPendientes = null;
        $sql = "SELECT re.der_nombre as region, de.dep_nombre as departamento, "
                . "mu.mun_nombre as municipio, c.nombre as centroPoblado, "
                . "be.nombre as beneficiario, t.descripcionTipoBeneficiario as tipoBeneficiario, "
                . "CONCAT(usu_nombre,' ',usu_apellido) as usuario, "
                . "DATE_FORMAT(a.fecha, '%Y-%m-%d') as fecha, a.descripcionActividadEjecutada as actividad, "
                . "ar.descripcion as area, ti.descripcionTipoHallazgo as clasificacion, "
                . "h.* "
                . "FROM hallazgospendientes h "
                . "INNER JOIN tipohallazgo ti ON ti.idTipoHallazgo = h.idTipo "
                . "INNER JOIN areashallazgospendientes ar ON ti.idAreaHallazgo = ar.idAreaHallazgo "
                . "INNER JOIN actividad a ON a.idActividad = h.idActividad "
                . "INNER JOIN bitacora b ON b.idBitacora = a.idBitacora "
                . "INNER JOIN usuario u ON b.idUsuario = u.usu_id "
                . "INNER JOIN beneficiario be ON be.idBeneficiario = b.idBeneficiario "
                . "INNER JOIN tipobeneficiario t ON t.idTipoBeneficiario = be.idTipoBeneficiario "
                . "INNER JOIN centropoblado c ON c.idCentroPoblado = be.idCentroPoblado "
                . "INNER JOIN municipio mu ON mu.mun_id = c.mun_id "
                . "INNER JOIN departamento de ON de.dep_id = mu.dep_id "
                . "INNER JOIN departamento_region re ON re.der_id = de.der_id "
                . "WHERE " . $criterio;
        $r = $this->db->ejecutarConsulta($sql);
        if ($r) {
            $cont = 0;
            while ($w = mysql_fetch_array($r)) {
                $hallazgosPendientes[$cont]['idHallazgosPendientes'] = $w['idHallazgosPendientes'];
                $hallazgosPendientes[$cont]['region'] = $w['region'];
                $hallazgosPendientes[$cont]['departamento'] = $w['departamento'];
                $hallazgosPendientes[$cont]['municipio'] = $w['municipio'];
                $hallazgosPendientes[$cont]['centroPoblado'] = $w['centroPoblado'];
                $hallazgosPendientes[$cont]['beneficiario'] = $w['beneficiario'];
                $hallazgosPendientes[$cont]['tipoBeneficiario'] = $w['tipoBeneficiario'];
                $hallazgosPendientes[$cont]['area'] = $w['area'];
                $hallazgosPendientes[$cont]['clasificacion'] = $w['clasificacion'];
                $hallazgosPendientes[$cont]['usuario'] = $w['usuario'];
                $hallazgosPendientes[$cont]['fechaInicio'] = $w['fecha'];
                $hallazgosPendientes[$cont]['actividad'] = $w['actividad'];
                $hallazgosPendientes[$cont]['observacion'] = $w['observacion'];
                $hallazgosPendientes[$cont]['archivo'] = "<a href='" . RUTA_DOCUMENTOS . "/historialBitacora/hallazgosPendientes/" . $w['idActividad'] . "/" . $w['archivo'] . "'>" . $w['archivo'] . "</a>";
                $hallazgosPendientes[$cont]['fechaRespuesta'] = $w['fechaRespuesta'];
                $hallazgosPendientes[$cont]['observacionRespuesta'] = $w['observacionRespuesta'];
                $hallazgosPendientes[$cont]['archivoRespuesta'] = "<a href='" . RUTA_DOCUMENTOS . "/historialBitacora/hallazgosPendientes/Respuestas/" . $w['idHallazgosPendientes'] . "/" . $w['archivoRespuesta'] . "'>" . $w['archivoRespuesta'] . "</a>";
                $hallazgosPendientes[$cont]['estado'] = '<img src=./templates/img/ico/verde.gif> Cerrado';
                if ($w['fechaRespuesta'] == NULL) {
                    $hallazgosPendientes[$cont]['estado'] = '<img src=./templates/img/ico/rojo.gif> Abierto';
                }
                $cont++;
            }
        }
        return $hallazgosPendientes;
    }

    /**
     * Obtiene un hallazgo pendiente dado el id del mismo.
     * @param type $idHallazgosPendientes
     * @return \CRegistroFotografico
     */
    function getHallazgosPendientesById($idHallazgosPendientes) {
        $hallazgoPendiente = null;
        $sql = "SELECT * "
                . "FROM hallazgospendientes "
                . "WHERE idHallazgosPendientes = '$idHallazgosPendientes'";
        $r = $this->db->ejecutarConsulta($sql);
        if ($r) {
            $w = mysql_fetch_array($r);
            $hallazgoPendiente = new CHallazgosPendientes($w['idHallazgosPendientes'], $w['observacion'], $w['idTipo'], $w['idActividad'], $w['archivo'], $w['fechaRespuesta'], $w['observacionRespuesta'], $w['archivoRespuesta']);
        }
        return $hallazgoPendiente;
    }

    /**
     * Obtiene los hallazgos pendientes almacenados en la base de datos.
     * @param type $idActividad
     * @return type
     */
    function getHallazgosPendientesByActividad($idActividad) {
        $hallazgosPendientes = null;
        $sql = "SELECT h.idHallazgosPendientes, h.observacion, "
                . "CONCAT(a.descripcion,' ',t.descripcionTipoHallazgo) as clasificacion, "
                . "h.archivo, h.idActividad "
                . "FROM hallazgospendientes h "
                . "INNER JOIN tipohallazgo t ON t.idTipoHallazgo = h.idTipo "
                . "INNER JOIN areashallazgospendientes a ON t.idAreaHallazgo = a.idAreaHallazgo "
                . "WHERE idActividad = '" . $idActividad."'";

        $actividad = $this->daoActividad->getActividadById($idActividad);
        $bitacora = $this->daoBitacora->getBitacoraById($actividad->getBitacora());

        $date = new DateTime($actividad->getFecha());
        $ruta = RUTA_DOCUMENTOS . "/" . RUTA_BITACORA . "/" . $bitacora->getUsuario() . "/" . $bitacora->getId() . "/" . $date->format('Y-m-d') . "/" . RUTA_HALLAZGOS;

        $r = $this->db->ejecutarConsulta($sql);
        if ($r) {
            $cont = 0;
            while ($w = mysql_fetch_array($r)) {
                $hallazgosPendientes[$cont]['idHallazgosPendientes'] = $w['idHallazgosPendientes'];
                $hallazgosPendientes[$cont]['clasificacion'] = $w['clasificacion'];
                $hallazgosPendientes[$cont]['observacion'] = $w['observacion'];
                $hallazgosPendientes[$cont]['archivo'] = "<a href='" . $ruta . "/" . $w['archivo'] . "'>" . $w['archivo'] . "</a>";
                $cont++;
            }
        }
        return $hallazgosPendientes;
    }

    /**
     * Inserta un hallazgo pendiente en la base de datos.
     * @param \CHallazgosPendientes $hallazgoPendiente
     * @return type
     */
    public function insertHallazgosPendientes($hallazgoPendiente) {
        $tabla = "hallazgospendientes";
        $hallazgoPendiente->setId($this->daoActividad->construirId($hallazgoPendiente->getActividad(), $tabla, 'idHallazgosPendientes'));
        $campos = "idHallazgosPendientes,observacion,idTipo,idActividad";
        $valores = "'" . $hallazgoPendiente->getId() . "','"
                . $hallazgoPendiente->getObservacion() . "','"
                . $hallazgoPendiente->getTipo() . "','"
                . $hallazgoPendiente->getActividad() . "'";
        if ($hallazgoPendiente->getArchivo()['name'] != null) {
            $actividad = $this->daoActividad->getActividadById($hallazgoPendiente->getActividad());
            $bitacora = $this->daoBitacora->getBitacoraById($actividad->getBitacora());

            $date = new DateTime($actividad->getFecha());
            $ruta = RUTA_BITACORA . "/" . $bitacora->getUsuario() . "/" . $bitacora->getId() . "/" . $date->format('Y-m-d') . "/" . RUTA_HALLAZGOS;

            $this->db->guardarArchivo($hallazgoPendiente->getArchivo(), $ruta);
            $campos .= ",archivo";
            $valores .= ",'" . $hallazgoPendiente->getArchivo()['name'] . "'";
        }
        $r = $this->db->insertarRegistro($tabla, $campos, $valores);
        return $r;
    }

    /**
     * Actualiza un hallazgo pendiente de la base de datos.
     * @param \CHallazgosPendientes $hallazgoPendiente
     * @return type
     */
    public function updateHallazgosPendientes($hallazgoPendiente) {
        $r = 'false';
        $tabla = "hallazgospendientes";
        $campos = array("observacion", "idTipo", "idActividad","sync");
        $valores = array("'" . $hallazgoPendiente->getObservacion() . "'",
            "'" . $hallazgoPendiente->getTipo() . "'",
            "'" . $hallazgoPendiente->getActividad() . "'","1");
        if ($hallazgoPendiente->getArchivo()['name'] != null) {
            $daoActividad = new CActividadBitacoraData($this->db);
            $daoBitacora = new CBitacoraData($this->db);

            $actividad = $this->daoActividad->getActividadById($hallazgoPendiente->getActividad());
            $bitacora = $this->daoBitacora->getBitacoraById($actividad->getBitacora());

            $date = new DateTime($actividad->getFecha());
            $ruta = RUTA_BITACORA . "/" . $bitacora->getUsuario() . "/" . $bitacora->getId() . "/" . $date->format('Y-m-d') . "/" . RUTA_HALLAZGOS;

            $this->db->guardarArchivo($hallazgoPendiente->getArchivo(), $ruta);
            $campos[count($campos)] = "archivo";
            $valores[count($valores)] = "'" . $hallazgoPendiente->getArchivo()['name'] . "'";
        }
        $condicion = "idHallazgosPendientes = '" . $hallazgoPendiente->getId() . "'";
        $r = $this->db->actualizarRegistro($tabla, $campos, $valores, $condicion);
        return $r;
    }

    /**
     * Elimina un registro de la base de datos.
     * @param type $idHallazgosPendientes
     * @return type
     */
    public function deleteHallazgosPendientesById($idHallazgosPendientes) {
        $tabla = "hallazgospendientes";
        $predicado = "idHallazgosPendientes = '$idHallazgosPendientes'";
        $r = $this->db->borrarRegistro($tabla, $predicado);
        return $r;
    }

    /**
     * Almacena las respuestas de un hallazgo pendiente.
     * @param \CHallazgosPendientes $hallazgoPendiente
     * @return type
     */
    public function saveRespuestaHallazgosPendientes($hallazgoPendiente) {
        $r = true;
        $tabla = "hallazgospendientes";
        $campos = array("fechaRespuesta", "observacionRespuesta");
        $valores = array("'" . $hallazgoPendiente->getFechaRespuesta() . "'",
            "'" . $hallazgoPendiente->getObservacionRespuesta() . "'");
        if ($hallazgoPendiente->getArchivoRespuesta()['name'] != null) {
            $daoActividad = new CActividadBitacoraData($this->db);
            $daoBitacora = new CBitacoraData($this->db);

            $actividad = $this->daoActividad->getActividadById($hallazgoPendiente->getActividad());
            $bitacora = $this->daoBitacora->getBitacoraById($actividad->getBitacora());

            $date = new DateTime($actividad->getFecha());
            $ruta = RUTA_BITACORA . "/" . $bitacora->getUsuario() . "/" . $bitacora->getId() . "/" . $date->format('Y-m-d') . "/" . RUTA_HALLAZGOS;
            $this->db->guardarArchivo($hallazgoPendiente->getArchivoRespuesta(), $ruta);
            $campos[count($campos)] = "archivoRespuesta";
            $valores[count($valores)] = "'" . $hallazgoPendiente->getArchivoRespuesta()['name'] . "'";
        }
        $condicion = "idHallazgosPendientes = '" . $hallazgoPendiente->getId() . "'";
        $r = $this->db->actualizarRegistro($tabla, $campos, $valores, $condicion);
        return $r;
    }
    
    function getHallazgosSincronizacion($usuario,$html){
        $hallazgosPendientes = null;
        $sql = "SELECT hallazgospendientes.* "
                . "FROM hallazgospendientes inner join actividad on hallazgospendientes.idActividad = actividad.idActividad "
                . "inner join bitacora on actividad.idBitacora = bitacora.idBitacora "
                . "WHERE hallazgospendientes.sync = 1 AND bitacora.idUsuario = " . $usuario;
        $r = $this->db->ejecutarConsulta($sql);
        if ($r) {
            $cont = 0;
            while ($w = mysql_fetch_array($r)) {
                $hallazgosPendientes[count($hallazgosPendientes)] = new hallazgo($w['idHallazgosPendientes'], 
                        $html->traducirTildes($w['observacion']), $w['archivo'], $w['idTipo'], $w['idActividad'],
                        $w['fechaRespuesta'], $html->traducirTildes($w['observacionRespuesta']), $w['archivoRespuesta']);
            }
        }
        return $hallazgosPendientes;
    }

}
