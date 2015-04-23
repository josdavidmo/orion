<?php

/**
 * Clase Plana Bodega.
 * @package clases
 * @subpackage aplicacion
 * @author SERTIC SAS
 * @version 2014.09.20
 * @copyright SERTIC
 */
class CCentroPoblado {
    
    /** Almacena el id del Centro Poblado. */
    var $idCentroPoblado = null;
    /** Almacena el codigo dane del Centro Poblado. */
    var $codigoDane = null;
    /** Almacena el nombre del Centro Poblado. */
    var $nombre = null;
    /** Almacena el municipio del Centro Poblado. */
    var $municipio = null;
    
    /**
     * Constructor de la clase.
     * @param type $idCentroPoblado
     * @param type $codigoDane
     * @param type $nombre
     * @param type $municipio
     */
    function CCentroPoblado($idCentroPoblado, $codigoDane, $nombre, 
                            $municipio) {
        $this->idCentroPoblado = $idCentroPoblado;
        $this->codigoDane = $codigoDane;
        $this->nombre = $nombre;
        $this->municipio = $municipio;
    }
    
    public function getIdCentroPoblado() {
        return $this->idCentroPoblado;
    }

    public function getCodigoDane() {
        return $this->codigoDane;
    }

    public function getNombre() {
        return $this->nombre;
    }

    public function getMunicipio() {
        return $this->municipio;
    }

    public function setIdCentroPoblado($idCentroPoblado) {
        $this->idCentroPoblado = $idCentroPoblado;
    }

    public function setCodigoDane($codigoDane) {
        $this->codigoDane = $codigoDane;
    }

    public function setNombre($nombre) {
        $this->nombre = $nombre;
    }

    public function setMunicipio($municipio) {
        $this->municipio = $municipio;
    }
}
