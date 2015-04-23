<?php

/**
 * Clase Plana Bitacora.
 * @package clases
 * @subpackage aplicacion
 * @author SERTIC SAS
 * @version 2015.01.20
 * @copyright SERTIC
 */
class CBitacora {
    
    /** Almacena el id de la bitacora. */
    var $id;
    /** Almacena el usuario de la bitacora. */
    var $usuario;
    /** Almacena el beneficiario de la bitacora. */
    var $beneficiario;
    /** Almacena el descripcion de la actividad. */
    var $descripcionActividad;
    /** Almacena la fecha inicio de la bitacora. */
    var $fechaInicio;
    /** Almacena la fecha fin la bitacora. */
    var $fechaFin;
    
    /**
     * Constructor para la clase CBitacora.
     * @param type $id
     * @param type $usuario
     * @param type $beneficiario
     * @param type $descripcionActividad
     * @param type $fechaInicio
     * @param type $fechaFin
     */
    function CBitacora($id, $usuario, $beneficiario, $descripcionActividad, 
                       $fechaInicio, $fechaFin) {
        $this->id = $id;
        $this->usuario = $usuario;
        $this->beneficiario = $beneficiario;
        $this->descripcionActividad = $descripcionActividad;
        $this->fechaInicio = $fechaInicio;
        $this->fechaFin = $fechaFin;
    }
    
    public function getId() {
        return $this->id;
    }

    public function getUsuario() {
        return $this->usuario;
    }

    public function getBeneficiario() {
        return $this->beneficiario;
    }

    public function getDescripcionActividad() {
        return $this->descripcionActividad;
    }

    public function getFechaInicio() {
        return $this->fechaInicio;
    }

    public function getFechaFin() {
        return $this->fechaFin;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function setUsuario($usuario) {
        $this->usuario = $usuario;
    }

    public function setBeneficiario($beneficario) {
        $this->beneficiario = $beneficario;
    }

    public function setDescripcionActividad($descripcionActividad) {
        $this->descripcionActividad = $descripcionActividad;
    }

    public function setFechaInicio($fechaInicio) {
        $this->fechaInicio = $fechaInicio;
    }

    public function setFechaFin($fechaFin) {
        $this->fechaFin = $fechaFin;
    }    
}
