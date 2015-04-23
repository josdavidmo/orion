<?php

/**
 * Clase Registro Fotografico Data Data
 * Usado para la conexion con la base de datos.
 * @package clases
 * @subpackage datos
 * @author SERTIC SAS
 * @version 2014.10.30
 * @copyright SERTIC SAS
 */
class CRegistroFotograficoData {

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
    function CRegistroFotograficoData($db) {
        $this->db = $db;
        $this->daoActividad = new CActividadBitacoraData($this->db);
        $this->daoBitacora = new CBitacoraData($this->db);
    }

    /**
     * Obtiene un registro fotografico dado su id.
     * @param type $idRegistroFotografico
     * @return string
     */
    function getRegistroFotograficoById($idRegistroFotografico) {
        $registroFotografico = null;
        $sql = "SELECT * "
                . "FROM registrofotografico "
                . "WHERE idRegistroFotografico = " . $idRegistroFotografico;
        $r = $this->db->ejecutarConsulta($sql);
        if ($r) {
            $w = mysql_fetch_array($r);
            $registroFotografico = new CRegistroFotografico($w['idRegistroFotografico'], $w['archivo'], $w['descripcionRegistroFotografico'], $w['IdActividad']);
        }
        return $registroFotografico;
    }

    /**
     * Obtiene los registros fotograficos almacenados en la base de datos.
     * @param type $idActividad
     * @return type
     */
    function getRegistroFotograficoByActividad($idActividad) {
        $registrosFotografico = null;
        $sql = "SELECT * "
                . "FROM registrofotografico "
                . "WHERE idActividad = '" . $idActividad."'";

        $actividad = $this->daoActividad->getActividadById($idActividad);
        $bitacora = $this->daoBitacora->getBitacoraById($actividad->getBitacora());

        $date = new DateTime($actividad->getFecha());
        $ruta = RUTA_DOCUMENTOS . "/" . RUTA_BITACORA . "/" . $bitacora->getUsuario() . "/" . $bitacora->getId() . "/" . $date->format('Y-m-d') . "/" . RUTA_REGISTRO_FOTOGRAFICO;

        $r = $this->db->ejecutarConsulta($sql);
        if ($r) {
            $cont = 0;
            while ($w = mysql_fetch_array($r)) {
                $registrosFotografico[$cont]['idRegistroFotografico'] = $w['idRegistroFotografico'];
                $registrosFotografico[$cont]['descripcionRegistroFotografico'] = $w['descripcionRegistroFotografico'];
                $registrosFotografico[$cont]['archivo'] = "<a href=" . $ruta . "/" . $w['archivo'] . ">" . $w['archivo'] . "</a>";
                $cont++;
            }
        }
        return $registrosFotografico;
    }

    /**
     * Inserta un registro fotografico en la base de datos.
     * @param \CRegistroFotografico $registroFotografico
     * @return type
     */
    public function insertRegistroFotografico($registroFotografico) {
        $r = 'false';
        $actividad = $this->daoActividad->getActividadById($registroFotografico->getActividad());
        $bitacora = $this->daoBitacora->getBitacoraById($actividad->getBitacora());

        $date = new DateTime($actividad->getFecha());
        $ruta = RUTA_BITACORA . "/" . $bitacora->getUsuario() . "/" . $bitacora->getId() . "/" . $date->format('Y-m-d') . "/" . RUTA_REGISTRO_FOTOGRAFICO;

        if ($this->db->guardarArchivo($registroFotografico->getArchivo(), $ruta)) {
            $tabla = "registrofotografico";
            $registroFotografico->setId($this->daoActividad->construirId($registroFotografico->getActividad(), $tabla, 'idRegistroFotografico'));
            $columnas = $this->db->getCampos($tabla);
            $campos = "";
            foreach ($columnas as $columna) {
                $campos .= $columna . ",";
            }
            $campos = substr($campos, 0, -1);
            $valores = "'" . $registroFotografico->getId() . "','"
                    . $registroFotografico->getDescripcion() . "','"
                    . $registroFotografico->getArchivo()['name'] . "','"
                    . $registroFotografico->getActividad() . "',1";
            $r = $this->db->insertarRegistro($tabla, $campos, $valores);
        }
        return $r;
    }

    /**
     * Actualiza una actividad bitacora de la base de datos.
     * @param \CRegistroFotografico $registroFotografico
     * @return type
     */
    public function updateRegistroFotografico($registroFotografico) {
        $r = true;
        $tabla = "registrofotografico";
        $condicion = "idRegistroFotografico = '" . $registroFotografico->getId() . "'";
        if ($registroFotografico->getArchivo()['name'] != "") {
            $actividad = $this->daoActividad->getActividadById($registroFotografico->getActividad());
            $bitacora = $this->daoBitacora->getBitacoraById($actividad->getBitacora());

            $date = new DateTime($actividad->getFecha());
            $ruta = RUTA_BITACORA . "/" . $bitacora->getUsuario() . "/" . $bitacora->getId() . "/" . $date->format('Y-m-d') . "/" . RUTA_REGISTRO_FOTOGRAFICO;

            $r = $this->db->guardarArchivo($registroFotografico->getArchivo(), $ruta);
            if ($r) {
                $campos = $this->db->getCampos($tabla);
                $valores = array("'" . $registroFotografico->getId() . "'",
                    "'" . $registroFotografico->getDescripcion() . "'",
                    "'" . $registroFotografico->getArchivo()['name'] . "'",
                    "'" . $registroFotografico->getActividad() . "'","1");
            }
        } else {
            $campos = array('descripcionRegistroFotografico',"sync");
            $valores = array("'" . $registroFotografico->getDescripcion() . "'","1");
        }
        $r = $this->db->actualizarRegistro($tabla, $campos, $valores, $condicion);
        return $r;
    }

    /**
     * Elimina un registro fotografico de la base de datos.
     * @param type $idRegistroFotografico
     * @return type
     */
    public function deleteRegistroFotograficoById($idRegistroFotografico) {
        $tabla = "registrofotografico";
        $predicado = "idRegistroFotografico = '" . $idRegistroFotografico . "'";
        $r = $this->db->borrarRegistro($tabla, $predicado);
        return $r;
    }
    
    function getRegistroFotograficoSincronizacion($usuario,$html) {
        $registrosFotograficos = null;
        $sql = "SELECT registrofotografico.* "
                . "FROM registrofotografico inner join actividad on registrofotografico.idActividad = actividad.idActividad "
                . "inner join bitacora on actividad.idBitacora = bitacora.idBitacora "
                . "WHERE registrofotografico.sync = 1 and bitacora.idUsuario = " . $usuario;
        $r = $this->db->ejecutarConsulta($sql);
        if ($r) {
            while ($w = mysql_fetch_array($r)) {
                $registrosFotograficos[count($registrosFotograficos)] = new registroFotografico(
                        $w['idRegistroFotografico'], $html->traducirTildes($w['descripcionRegistroFotografico']), $w['archivo'],
                        $w['IdActividad']);
            }
        }
        return $registrosFotograficos;
    }

}
