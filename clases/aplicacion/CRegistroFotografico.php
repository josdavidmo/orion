<?php

/**
 * Clase Plana Registro Fotografico.
 * @package clases
 * @subpackage aplicacion
 * @author SERTIC SAS
 * @version 2014.10.30
 * @copyright SERTIC
 */
class CRegistroFotografico {
    
    /** Almacena el id del registro fotografico. */
    var $id;
    /** Almacena el archivo del registro fotografico. */
    var $archivo;
    /** Almacena la descripcion del registro fotografico. */
    var $descripcion;
    /** Almacena la actividad del registro fotografico. */
    var $actividad;
    
    /**
     * Constructor de la clase.
     * @param type $id
     * @param type $archivo
     * @param type $descripcion
     * @param type $actividad
     */
    function CRegistroFotografico($id, $archivo, $descripcion, $actividad) {
        $this->id = $id;
        $this->archivo = $archivo;
        $this->descripcion = $descripcion;
        $this->actividad = $actividad;
    }
    
    function getId() {
        return $this->id;
    }

    function getArchivo() {
        return $this->archivo;
    }

    function getDescripcion() {
        return $this->descripcion;
    }

    function getActividad() {
        return $this->actividad;
    }

    function setId($id) {
        $this->id = $id;
    }

    function setArchivo($archivo) {
        $this->archivo = $archivo;
    }

    function setDescripcion($descripcion) {
        $this->descripcion = $descripcion;
    }

    function setActividad($actividad) {
        $this->actividad = $actividad;
    }



    
}
