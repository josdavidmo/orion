<?php

/**
 * Clase Plana Otro Si.
 * @package clases
 * @subpackage aplicacion
 * @author SERTIC SAS
 * @version 2015.04.06
 * @copyright SERTIC
 */
class COtroSi {
    
    /** Almacena el id del otro si. */
    var $id;
    /** Alamacena la descripcion del otro si. */
    var $descripcion;
    /** Almacena el valor del otro si. */
    var $valor;
    /** Almacena la fecha del otro si. */
    var $fecha;
    /** Almacena el documento soporte del otro si. */
    var $documentoSoporte;
    /** Almacena las observaciones del otro si. */
    var $observaciones;
    /** Almacena el contrato del otro si. */
    var $contrato;
    
    /**
     * Constructor de la clase.
     * @param type $id
     * @param type $descripcion
     * @param type $valor
     * @param type $fecha
     * @param type $documentoSoporte
     * @param type $observaciones
     * @param type $contrato
     */
    function COtroSi($id, $descripcion, $valor, $fecha, $documentoSoporte, 
                     $observaciones, $contrato) {
        $this->id = $id;
        $this->descripcion = $descripcion;
        $this->valor = $valor;
        $this->fecha = $fecha;
        $this->documentoSoporte = $documentoSoporte;
        $this->observaciones = $observaciones;
        $this->contrato = $contrato;
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

    function getFecha() {
        return $this->fecha;
    }

    function getDocumentoSoporte() {
        return $this->documentoSoporte;
    }

    function getObservaciones() {
        return $this->observaciones;
    }

    function getContrato() {
        return $this->contrato;
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

    function setFecha($fecha) {
        $this->fecha = $fecha;
    }

    function setDocumentoSoporte($documentoSoporte) {
        $this->documentoSoporte = $documentoSoporte;
    }

    function setObservaciones($observaciones) {
        $this->observaciones = $observaciones;
    }

    function setContrato($contrato) {
        $this->contrato = $contrato;
    }



}
