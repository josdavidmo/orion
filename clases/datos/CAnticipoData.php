<?php

/**
 * Clase Anticipo
 * Usado para la conexion con la base de datos.
 * @package clases
 * @subpackage datos
 * @author SERTIC SAS
 * @version 2014.01.13
 * @copyright SERTIC SAS
 */
class CAnticipoData {

    /** Almacena la conexion de la clase. */
    var $db = null;

    /**
     * Constructor de la clase.
     * @param \CData $db
     */
    function CAnticipoData($db) {
        $this->db = $db;
    }

    /**
     * Obtiene un anticipo el id del mismo.
     * @param type $idAnticipo
     * @return \CAnticipo
     */
    function getAnticipoById($idAnticipo) {
        $anticipo = null;
        $sql = "SELECT * FROM anticipos_bitacora WHERE idAnticipo = " . $idAnticipo;
        $r = $this->db->ejecutarConsulta($sql);
        if ($r) {
            $w = mysql_fetch_array($r);
            $anticipo = new CAnticipo($w['idAnticipo'], $w['fecha'], number_format(($w['valor']), 0, ',', '.'), $w['idBitacora']);
        }
        return $anticipo;
    }

    /**
     * Obtiene los anticipos asociados a una bitacora.
     * @param type $idBitacora
     * @return string
     */
    function getAnticiposByBitacora($idBitacora) {
        $anticipos = null;
        $sql = "SELECT * FROM anticipos_bitacora "
                . "WHERE idBitacora = " . $idBitacora;
        $r = $this->db->ejecutarConsulta($sql);
        if ($r) {
            $cont = 0;
            while ($w = mysql_fetch_array($r)) {
                $anticipos[$cont]['idAnticipo'] = $w['idAnticipo'];
                $anticipos[$cont]['fecha'] = $w['fecha'];
                $anticipos[$cont]['valor'] = $w['valor'];
                $cont++;
            }
        }
        return $anticipos;
    }

    /**
     * Inserta un anticipo en la base de datos.
     * @param \CAnticipo $anticipo
     * @return type
     */
    public function insertAnticipo($anticipo) {
        $tabla = "anticipos_bitacora";
        $columnas = $this->db->getCampos($tabla);
        $campos = "";
        foreach ($columnas as $columna) {
            $campos .= $columna . ",";
        }
        $campos = substr($campos, 0, -1);
        $valores = "'" . $anticipo->getId() . "','"
                . $anticipo->getFecha() . "','"
                . $anticipo->getValor() . "','"
                . $anticipo->getBitacora() . "',1";
        $r = $this->db->insertarRegistro($tabla, $campos, $valores);
        return $r;
    }

    /**
     * Elimina un anticipo dado su id.
     * @param type $idAnticipo
     * @return type
     */
    public function deleteAnticipoById($idAnticipo) {
        $tabla = "anticipos_bitacora";
        $predicado = "idAnticipo = " . $idAnticipo;
        $r = $this->db->borrarRegistro($tabla, $predicado);
        return $r;
    }

    /**
     * Actualiza un anticipo de la base de datos.
     * @param \CAnticipo $anticipo
     * @return type
     */
    public function updateAnticipo($anticipo) {
        $tabla = "anticipos_bitacora";
        $campos = $this->db->getCampos($tabla);
        $condicion = $campos[0] . " = " . $anticipo->getId();
        $valores = array("'" . $anticipo->getId() . "'",
            "'" . $anticipo->getFecha() . "'",
            "'" . $anticipo->getValor() . "'",
            "'" . $anticipo->getBitacora() . "'","1");
        $r = $this->db->actualizarRegistro($tabla, $campos, $valores, $condicion);
        return $r;
    }

}
