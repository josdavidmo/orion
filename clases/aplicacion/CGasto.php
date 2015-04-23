<?php

/**
 * Clase Plana Actividad Bitacora.
 * @package clases
 * @subpackage aplicacion
 * @author SERTIC SAS
 * @version 2014.12.10
 * @copyright SERTIC
 */
class CGasto {
    
    /** Almacena el id del gasto. */
    var $id;
    /** Almacena la descripcion de la actividad. */
    var $descripcion;
    /** Almacena el valor de la actividad. */
    var $valor;
    /** Almacena el archivo asociado a la actividad. */
    var $archivo;
    /** Almacena el tipo de gasto. */
    var $tipo;
    /** Almacena la actividad asociada al gasto. */
    var $actividad;
    /** Almacena el estado de un gasto, puede tomar los valores de 
     *  aprobado y no aprobado. */
    var $estado;
    
    /**
     * Constructor de la clase.
     * @param type $id
     * @param type $descripcion
     * @param type $valor
     * @param type $archivo
     * @param type $tipo
     * @param type $actividad
     * @param type $estado
     */
    function CGasto($id, $descripcion, $valor, $archivo, $tipo, $actividad, $estado = null) {
        $this->id = $id;
        $this->descripcion = $descripcion;
        $this->valor = $valor;
        $this->archivo = $archivo;
        $this->tipo = $tipo;
        $this->actividad = $actividad;
        $this->estado = $estado;
    }
    
    function getId() {
        return $this->id;
    }

    function getDescripcion() {
        return $this->descripcion;
    }

    function getValor() {
        return $this->valor;
    }

    function getArchivo() {
        return $this->archivo;
    }

    function getTipo() {
        return $this->tipo;
    }

    function getActividad() {
        return $this->actividad;
    }

    function setId($id) {
        $this->id = $id;
    }

    function setDescripcion($descripcion) {
        $this->descripcion = $descripcion;
    }

    function setValor($valor) {
        $this->valor = $valor;
    }

    function setArchivo($archivo) {
        $this->archivo = $archivo;
    }

    function setTipo($tipo) {
        $this->tipo = $tipo;
    }

    function setActividad($actividad) {
        $this->actividad = $actividad;
    }
    
    function getEstado() {
        return $this->estado;
    }

    function setEstado($estado) {
        $this->estado = $estado;
    }

}
