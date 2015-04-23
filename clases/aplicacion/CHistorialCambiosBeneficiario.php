<?php

/**
 * Clase Historial Cambios Beneficiario.
 * @package clases
 * @subpackage aplicacion
 * @author SERTIC SAS
 * @version 2014.10.26
 * @copyright SERTIC
 */
class CHistorialCambiosBeneficiario {
    
    var $idHistorialCambio;
    var $beneficiario1;
    var $beneficiario2;
    var $tipoCambioBeneficiario;
    var $fecha;
    var $soporte;
    var $observaciones;
    
    /**
     * Constructor de la clase.
     * @param type $idHistorialCambio
     * @param type $beneficiario1
     * @param type $beneficiario2
     * @param type $tipoCambioBeneficiario
     * @param type $fecha
     * @param type $soporte
     * @param type $observaciones
     */
    function CHistorialCambiosBeneficiario($idHistorialCambio, $beneficiario1, 
                                           $beneficiario2, 
                                           $tipoCambioBeneficiario, $fecha, 
                                           $soporte, $observaciones) {
        $this->idHistorialCambio = $idHistorialCambio;
        $this->beneficiario1 = $beneficiario1;
        $this->beneficiario2 = $beneficiario2;
        $this->tipoCambioBeneficiario = $tipoCambioBeneficiario;
        $this->fecha = $fecha;
        $this->soporte = $soporte;
        $this->observaciones = $observaciones;
    }
    
    function getIdHistorialCambio() {
        return $this->idHistorialCambio;
    }

    function getBeneficiario1() {
        return $this->beneficiario1;
    }

    function getBeneficiario2() {
        return $this->beneficiario2;
    }

    function getTipoCambioBeneficiario() {
        return $this->tipoCambioBeneficiario;
    }

    function getFecha() {
        return $this->fecha;
    }

    function getSoporte() {
        return $this->soporte;
    }

    function getObservaciones() {
        return $this->observaciones;
    }

    function setIdHistorialCambio($idHistorialCambio) {
        $this->idHistorialCambio = $idHistorialCambio;
    }

    function setBeneficiario1($beneficiario1) {
        $this->beneficiario1 = $beneficiario1;
    }

    function setBeneficiario2($beneficiario2) {
        $this->beneficiario2 = $beneficiario2;
    }

    function setTipoCambioBeneficiario($tipoCambioBeneficiario) {
        $this->tipoCambioBeneficiario = $tipoCambioBeneficiario;
    }

    function setFecha($fecha) {
        $this->fecha = $fecha;
    }

    function setSoporte($soporte) {
        $this->soporte = $soporte;
    }

    function setObservaciones($observaciones) {
        $this->observaciones = $observaciones;
    }

}
