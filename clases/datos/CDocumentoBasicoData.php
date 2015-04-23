<?php

/**
 * Clase Documento Basico Data
 * Usado para la conexion con la base de datos.
 * @package clases
 * @subpackage datos
 * @author SERTIC SAS
 * @version 2015.01.23
 * @copyright SERTIC SAS
 */
class CDocumentoBasicoData {

    /** Manejador de la base de datos. */
    var $db = null;

    /** Referencia a la tabla. */
    var $tabla;

    /** Referencia a la ruta. */
    var $ruta;

    /**
     * Constructor de la clase.
     * @param type $db
     */
    function CDocumentoBasicoData($db, $tabla, $ruta) {
        $this->db = $db;
        $this->tabla = $tabla;
        $this->ruta = $ruta;
    }

    /**
     * Obtiene los documentos basicos almacenados en la base de datos.
     * @param type $idElement
     * @return type
     */
    public function getDocumentoBasicoById($idElement) {
        $tabla = $this->tabla;
        $query = "SELECT * FROM $tabla WHERE id = $idElement";
        $documentoBasico = null;
        $r = $this->db->ejecutarConsulta($query);
        if ($r) {
            $w = mysql_fetch_array($r);
            $documentoBasico = new CDocumentoBasico($w['id'], $w['descripcion'], $w['archivo']);
        }
        return $documentoBasico;
    }

    /**
     * Obtiene los documentos basicos almacenados en la base de datos.
     * @param type $criterio
     * @return type
     */
    public function getDocumentoBasico($criterio = "1") {
        $tabla = $this->tabla;
        $query = "SELECT * FROM $tabla WHERE $criterio";
        $documentosBasicos = null;
        $r = $this->db->ejecutarConsulta($query);
        $ruta = RUTA_DOCUMENTOS . "/" . $this->ruta . "/";
        if ($r) {
            $cont = 0;
            while ($w = mysql_fetch_array($r)) {
                $documentosBasicos[$cont]['id'] = $w['id'];
                $documentosBasicos[$cont]['descripcion'] = $w['descripcion'];
                $ruta .= $w['archivo'];
                $documentosBasicos[$cont]['archivo'] = "<a href='$ruta' >" . $w['archivo'] . "</a>";
                $cont++;
            }
        }
        return $documentosBasicos;
    }

    /**
     * Inserta el documento basico.
     * @param \CDocumentoBasico $documentoBasico
     * @return type
     */
    public function insertDocumentoBasico($documentoBasico) {
        $r = 'false';
        $ruta = $this->ruta;
        if ($this->db->guardarArchivo($documentoBasico->getArchivo(), $ruta)) {
            $tabla = $this->tabla;
            $columnas = $this->db->getCampos($tabla);
            $campos = implode(",", $columnas);
            $valores = "NULL,'" . $documentoBasico->getDescripcion() . "','"
                    . $documentoBasico->getArchivo()['name'] . "'";
            $r = $this->db->insertarRegistro($tabla, $campos, $valores);
        }
        return $r;
    }

    /**
     * Elimina un documento basico de la base de datos.
     * @param type $idDocumentoBasico
     * @return type
     */
    public function deleteDocumentoBasicoById($idDocumentoBasico) {
        $tabla = $this->tabla;
        $columna = $this->db->getCampos($tabla);
        $predicado = $columna[0] . " = " . $idDocumentoBasico;
        $r = $this->db->borrarRegistro($tabla, $predicado);
        return $r;
    }

    /**
     * Actualiza un beneficiario de la base de datos.
     * @param \CDocumentoBasico $documentoBasico
     * @return type
     */
    public function updateDocumentoBasico($documentoBasico) {
        $r = true;
        $tabla = $this->tabla;
        $columnas = $this->db->getCampos($tabla);
        $valores = array("'".$documentoBasico->descripcion."'");
        $campos = array($columnas[1]);
        $condicion = $columnas[0] . " = " . $documentoBasico->getId();
        if ($documentoBasico->getArchivo()['name'] != "") {
            $ruta = $this->ruta;
            if ($this->db->guardarArchivo($documentoBasico->getArchivo(), $ruta)) {
                $campos[count($campos)] = $columnas[2];
                $valores[count($valores)] = "'" . $documentoBasico->getArchivo()['name'] . "'";
            }
        }
        $r = $this->db->actualizarRegistro($tabla, $campos, $valores, $condicion);
        return $r;
    }

}
