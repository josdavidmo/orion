<?php

/**
 * Clase Plana Basica.
 * @package clases
 * @subpackage aplicacion
 * @author SERTIC SAS
 * @version 2014.11.14
 * @copyright SERTIC
 */
class CBasicaRelacionada {
    
    /** Almacena el id de la tabla. */
    var $id;
    /** Almacena la descripcion de la tabla. */
    var $descripcion;
    /** Almacena la tabla con la que se encuentra relacionada la tabla. */
    var $tabla;
    
    /**
     * Constructo de la clase.
     * @param type $id
     * @param type $descripcion
     * @param type $tabla
     */
    function CBasicaRelacionada($id, $descripcion, $tabla) {
        $this->id = $id;
        $this->descripcion = $descripcion;
        $this->tabla = $tabla;
    }
    
    /**
     * Obtiene el id de la tabla.
     * @return type
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Obtiene la descripcion de la tabla.
     * @return type
     */
    public function getDescripcion() {
        return $this->descripcion;
    }

    /**
     * Obtiene la tabla de la tabla.
     * @return type
     */
    public function getTabla() {
        return $this->tabla;
    }

    /**
     * Asigna el id de la tabla.
     * @param type $id
     */
    public function setId($id) {
        $this->id = $id;
    }

    /**
     * Asigna la descripcion de la tabla.
     * @param type $descripcion
     */
    public function setDescripcion($descripcion) {
        $this->descripcion = $descripcion;
    }

    /**
     * Asigna la tabla de la tabla.
     * @param type $tabla
     */
    public function setTabla($tabla) {
        $this->tabla = $tabla;
    }

}
