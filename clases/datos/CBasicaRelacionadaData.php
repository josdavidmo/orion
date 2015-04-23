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
class CBasicaRelacionadaData {

    /** Manejador de la base de datos. */
    var $db = null;

    /**
     * Constructor de la clase.
     * @param type $db
     */
    function CBasicaRelacionadaData($db) {
        $this->db = $db;
    }

    /**
     * Obtiene un elemento de una tabla.
     * @param type $tabla
     * @return \CBasica
     */
    public function getBasicas($tabla, $condicion) {
        $basicas = null;
        $sql = "SELECT * FROM " . $tabla . " WHERE " . $condicion;
        $r = $this->db->ejecutarConsulta($sql);
        if ($r) {
            $cont = 0;
            while ($w = mysql_fetch_array($r)) {
                $basicas[$cont]['id'] = $w[0];
                $basicas[$cont]['descripcion'] = $w[1];
                $cont++;
            }
        }
        return $basicas;
    }

    /**
     * Obtiene una la informacion de un elemento dado su id.
     * @param type $tabla
     * @param type $condicion
     * @return \CBasicaRelacionada
     */
    public function getBasicaById($tabla, $condicion) {
        $basica = null;
        $sql = "SELECT * FROM " . $tabla . " WHERE " . $condicion;
        $r = $this->db->ejecutarConsulta($sql);
        if ($r) {
            $w = mysql_fetch_array($r);
            $basica = new CBasicaRelacionada($w[0], $w[1], $w[2]);
        }
        return $basica;
    }

    /**
     * Inserta una tabla basica en la base de datos relacionada
     * @param \CBasicaRelacionada $basica
     * @param type $tabla
     * @return type
     */
    public function insertBasicaRelacionada($basica, $tabla) {
        $columnas = $this->getCampos($tabla);
        $campos = null;
        foreach ($columnas as $columna) {
            $campos .= $columna . ",";
        }
        $campos = substr($campos, 0, -1);
        $valores = "'" . $basica->getId() . "','"
                . $basica->getDescripcion() . "','"
                . $basica->getTabla() . "'";
        $r = $this->db->insertarRegistro($tabla, $campos, $valores);
        return $r;
    }
    
    /**
     * Actualiza la informacion de una tabla basica.
     * @param \CBasicaRelacionada $basica
     * @param type $tabla
     * @return type
     */
    public function updateBasicaRelacionada($basica, $tabla) {
        $columnas = $this->getCampos($tabla);
        $campos = array($columnas[1] , $columnas[2]);
        $valores = array("'" . $basica->getDescripcion() . "'",
            "'" . $basica->getTabla() . "'");
        $condicion = $columnas[0] . " = " . $basica->getId();
        $r = $this->db->actualizarRegistro($tabla, $campos, $valores, $condicion);
        return $r;
    }
    
    /**
     * Elimina una tabla basica de la base de datos dado su id.
     * @param type $idTabla
     * @param type $tabla
     * @return type
     */
    public function deleteBasicaRelacionadaById($idTabla, $tabla) {
        $campo = $this->getCampos($tabla)[0];
        $predicado = $campo . " = " . $idTabla;
        $r = $this->db->borrarRegistro($tabla, $predicado);
        return $r;
    }

    /**
     * Obtiene las columnas de una tabla.
     * @param type $tabla
     * @return type
     */
    public function getCampos($tabla) {
        $sql = "SHOW COLUMNS FROM " . $tabla;
        $r = $this->db->ejecutarConsulta($sql);
        $columnas = null;
        if ($r) {
            $cont = 0;
            while ($w = mysql_fetch_array($r)) {
                $columnas[$cont] = $w[0];
                $cont++;
            }
        }
        return $columnas;
    }

}
