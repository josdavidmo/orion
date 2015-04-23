<?php

/**
 * Clase Básica Data
 * Usado para la conexion con la base de datos.
 * @package clases
 * @subpackage datos
 * @author SERTIC SAS
 * @version 2014.09.12
 * @copyright SERTIC SAS
 */
class CBasicaData {

    /** Manejador de la base de datos. */
    var $db = null;

    /**
     * Constructor de la clase.
     * @param type $db
     */
    function CBasicaData($db) {
        $this->db = $db;
    }

    /**
     * Obtiene un elemento de una tabla.
     * @param type $tabla
     * @return \CBasica
     */
    public function getBasicas($tabla, $criterio = "1") {
        $basicas = null;
        $sql = "SELECT * FROM " . $tabla . " WHERE " . $criterio;
        $r = $this->db->ejecutarConsulta($sql);
        if ($r) {
            $cont = 0;
            while ($w = mysql_fetch_array($r)) {
                $basica = new CBasica($w[0], $w[1]);
                $basicas[$cont] = $basica;
                $cont++;
            }
        }
        return $basicas;
    }

    /**
     * Obtiene un objeto de tipo basico
     * @param type $tabla
     * @param type $condicion
     * @return \CBasica
     */
    public function getBasicasById($tabla, $condicion) {
        $basica = null;
        $sql = "SELECT * FROM " . $tabla . " WHERE " . $condicion;
        $r = $this->db->ejecutarConsulta($sql);
        if ($r) {
            $w = mysql_fetch_array($r);
            $basica = new CBasica($w[0], $w[1]);
        }
        return $basica;
    }

    /**
     * Obtiene el id de un registro dado la descripcion.
     * @param type $tabla
     * @param type $descripcion
     * @return \CBasica
     */
    public function getIdBasicasByDescripcion($tabla, $descripcion) {
        $basica = null;
        $campos = $this->db->getCampos($tabla);
        $sql = "SELECT " . $campos[0] 
                . " FROM " . $tabla 
                . " WHERE LOWER(" . $campos[1] . ") = LOWER('" . $descripcion . "')";
        $r = $this->db->ejecutarConsulta($sql);
        if ($r) {
            $w = mysql_fetch_array($r);
            $basica = new CBasica($w[0], $w[1]);
        }
        return $basica;
    }

}
