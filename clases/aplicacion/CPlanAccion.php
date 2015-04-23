<?php

/**
 * Clase Plana Plan de Accion.
 * @package clases
 * @subpackage aplicacion
 * @author SERTIC SAS
 * @version 2014.12.13
 * @copyright SERTIC
 */
class CPlanAccion {
    
    /** Almacena el id del plan de accion. */
    var $id;
    /** Almacena la descripcion del plan de accion. */
    var $descripcion;
    /** Almacena la fecha inicio del plan de accion. */
    var $fechaInicio;
    /** Almacena el consecutivo del plan de accion. */
    var $consecutivo;
    /** Almacena la fecha limite del plan de accion. */
    var $fechaLimite;
    /** Almacena el soporte del plan de accion. */
    var $soporte;
    /** Almacena el usuario del plan de accion. */
    var $usuario;
    /** Almacena la fuente del plan de accion. */
    var $fuente;
    
    /**
     * Constructor de la clase.
     * @param type $id
     * @param type $descripcion
     * @param type $fechaInicio
     * @param type $consecutivo
     * @param type $fechaLimite
     * @param type $soporte
     * @param type $usuario
     * @param type $fuente
     */
    function CPlanAccion($id, $descripcion, $fechaInicio, $consecutivo, 
                         $fechaLimite, $soporte, $usuario, $fuente) {
        $this->id = $id;
        $this->descripcion = $descripcion;
        $this->fechaInicio = $fechaInicio;
        $this->consecutivo = $consecutivo;
        $this->fechaLimite = $fechaLimite;
        $this->soporte = $soporte;
        $this->usuario = $usuario;
        $this->fuente = $fuente;
    }
    
    function getId() {
        return $this->id;
    }

    function getDescripcion() {
        return $this->descripcion;
    }

    function getFechaInicio() {
        return $this->fechaInicio;
    }

    function getConsecutivo() {
        return $this->consecutivo;
    }

    function getFechaLimite() {
        return $this->fechaLimite;
    }

    function getSoporte() {
        return $this->soporte;
    }

    function getUsuario() {
        return $this->usuario;
    }

    function getFuente() {
        return $this->fuente;
    }

    function setId($id) {
        $this->id = $id;
    }

    function setDescripcion($descripcion) {
        $this->descripcion = $descripcion;
    }

    function setFechaInicio($fechaInicio) {
        $this->fechaInicio = $fechaInicio;
    }

    function setConsecutivo($consecutivo) {
        $this->consecutivo = $consecutivo;
    }

    function setFechaLimite($fechaLimite) {
        $this->fechaLimite = $fechaLimite;
    }

    function setSoporte($soporte) {
        $this->soporte = $soporte;
    }

    function setUsuario($usuario) {
        $this->usuario = $usuario;
    }

    function setFuente($fuente) {
        $this->fuente = $fuente;
    }

}