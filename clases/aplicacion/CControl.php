<?php

/**
 * Clase Control.
 * @package clases
 * @subpackage aplicacion
 * @author SERTIC SAS
 * @version 2014.11.14
 * @copyright SERTIC
 */
class CControl {
    
    /** Almacena el id del control. */
    var $id;
    /** Almacena el responsable del control. */
    var $obligaciones;
    /** Almacena el responsable del control. */
    var $responsable;
    /** Almacena la verificacion del control. */
    var $verificacion;
    /** Almacena el numero documento contractual del control. */
    var $numeroDocumentoContractual;
    
    /**
     * Constructor de la clase.
     * @param type $id
     * @param type $obligaciones
     * @param type $responsable
     * @param type $verificacion
     * @param type $numeroDocumentoContractual
     */
    public function CControl($id, $obligaciones, $responsable, $verificacion, 
                             $numeroDocumentoContractual) {
        $this->id = $id;
        $this->obligaciones = $obligaciones;
        $this->responsable = $responsable;
        $this->verificacion = $verificacion;
        $this->numeroDocumentoContractual = $numeroDocumentoContractual;
    }
    
    public function getId() {
        return $this->id;
    }

    public function getObligaciones() {
        return $this->obligaciones;
    }

    public function getResponsable() {
        return $this->responsable;
    }

    public function getVerificacion() {
        return $this->verificacion;
    }

    public function getNumeroDocumentoContractual() {
        return $this->numeroDocumentoContractual;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function setObligaciones($obligaciones) {
        $this->obligaciones = $obligaciones;
    }

    public function setResponsable($responsable) {
        $this->responsable = $responsable;
    }

    public function setVerificacion($verificacion) {
        $this->verificacion = $verificacion;
    }

    public function setNumeroDocumentoContractual($numeroDocumentoContractual) {
        $this->numeroDocumentoContractual = $numeroDocumentoContractual;
    }

}
