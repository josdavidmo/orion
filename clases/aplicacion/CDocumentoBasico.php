<?php

/**
 * Clase Plana Documento Basico.
 * @package clases
 * @subpackage aplicacion
 * @author SERTIC SAS
 * @version 2015.04.23
 * @copyright SERTIC
 */
class CDocumentoBasico {
    
    /** Almacena la referencia a un archivo. */
    var $id;
    /** Almacena la referencia a una descripcion. */
    var $descripcion;
    /** Almacena el archivo correspondiente. */
    var $archivo;
    
    /**
     * Constructor de la clase.
     * @param type $id
     * @param type $descripcion
     * @param type $archivo
     */
    function CDocumentoBasico($id, $descripcion, $archivo) {
        $this->id = $id;
        $this->descripcion = $descripcion;
        $this->archivo = $archivo;
    }
    
    function getId() {
        return $this->id;
    }

    function getDescripcion() {
        return $this->descripcion;
    }

    function getArchivo() {
        return $this->archivo;
    }

    function setId($id) {
        $this->id = $id;
    }

    function setDescripcion($descripcion) {
        $this->descripcion = $descripcion;
    }

    function setArchivo($archivo) {
        $this->archivo = $archivo;
    }
    
}
