<?php

/**
 * Clase Plana bitacora para Stand Alone.
 * @package clases
 * @subpackage aplicacion
 * @author SERTIC SAS
 * @version 2015.01.20
 * @copyright SERTIC
 */
class bitacora {

    /** Almacena el id de la bitacora. */
    var $idBitacora;

    /** Almacena el usuario de la bitacora. */
    var $idUsuario;

    /** Almacena el beneficiario de la bitacora. */
    var $idBeneficiario;

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
    function bitacora($id, $usuario, $beneficiario, $descripcionActividad, $fechaInicio, $fechaFin) {
        $this->idBitacora = $id;
        $this->idUsuario = $usuario;
        $this->idBeneficiario = $beneficiario;
        $this->descripcionActividad = $descripcionActividad;
        $this->fechaInicio = $fechaInicio;
        $this->fechaFin = $fechaFin;
    }

    public function getId() {
        return $this->idBitacora;
    }

    public function getUsuario() {
        return $this->idUsuario;
    }

    public function getBeneficiario() {
        return $this->idBeneficiario;
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
        $this->idBitacora = $id;
    }

    public function setUsuario($usuario) {
        $this->idUsuario = $usuario;
    }

    public function setBeneficiario($beneficario) {
        $this->idBeneficiario = $beneficario;
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
