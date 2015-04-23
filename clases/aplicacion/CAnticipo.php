<?php

/**
 * Clase Plana Anticipo.
 * @package clases
 * @subpackage aplicacion
 * @author SERTIC SAS
 * @version 2014.01.13
 * @copyright SERTIC
 */
class CAnticipo {
    
    /** Almacena el id del anticipo. */
    var $id;
    /** Almacena la fecha de realizacion del anticipo. */
    var $fecha;
    /** Almacena la fecha de la actividad plan de accion. */
    var $valor;
    /** Almacena el id de la bitacora asociada. */
    var $bitacora;
    
    /**
     * Constructor de la clase.
     * @param type $id
     * @param type $fecha
     * @param type $valor
     * @param type $bitacora
     */
    function CAnticipo($id, $fecha, $valor, $bitacora) {
        $this->id = $id;
        $this->fecha = $fecha;
        $this->valor = $valor;
        $this->bitacora = $bitacora;
    }
    
    function getId() {
        return $this->id;
    }

    function getFecha() {
        return $this->fecha;
    }

    function getValor() {
        return $this->valor;
    }

    function getBitacora() {
        return $this->bitacora;
    }

    function setId($id) {
        $this->id = $id;
    }

    function setFecha($fecha) {
        $this->fecha = $fecha;
    }

    function setValor($valor) {
        $this->valor = $valor;
    }

    function setBitacora($bitacora) {
        $this->bitacora = $bitacora;
    }

}
