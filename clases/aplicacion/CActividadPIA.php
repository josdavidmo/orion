<?php
/**
 * Clase plana que almacena los datos de una actividad del plan de la inversion 
 * del anticipo.
 * @author Brian Kings
 * @author Jose David Moreno Posada
 * @version 1.0
 * @since 08/08/2014
 */
class CActividadPIA {
    
    /** Almacena el id de una actividad. */
    var $id = null;
    /** Almacena la descripcion de una actividad. */
    var $descripcion = null;
    /** Almacena el monto de una actividad. */
    var $monto = null;
    
    /**
     * Constructor de la clase.
     * @param type $id
     * @param type $descripcion
     * @param type $monto
     */
    function CActividadPIA($id, $descripcion, $monto) {
        $this->id = $id;
        $this->descripcion = $descripcion;
        $this->monto = $monto;
    }
    
    /**
     * Obtiene el id de la actividad.
     * @return type
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Obtiene la descripcion de una actividad.
     * @return type
     */
    public function getDescripcion() {
        return $this->descripcion;
    }

    /**
     * Obtiene el monto de una actividad.
     * @return type
     */
    public function getMonto() {
        return $this->monto;
    }

    /**
     * Asigna el id de una actividad.
     * @param type $id
     */
    public function setId($id) {
        $this->id = $id;
    }

    /**
     * Asigna la descripcion de una actividad.
     * @param type $descripcion
     */
    public function setDescripcion($descripcion) {
        $this->descripcion = $descripcion;
    }

    /**
     * Asigna el monto de una actividad.
     * @param type $monto
     */
    public function setMonto($monto) {
        $this->monto = $monto;
    }
}
