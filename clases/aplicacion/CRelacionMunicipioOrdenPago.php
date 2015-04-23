<?php

/**
 * Clase Plana que maneja la relacion municipio orden de pago.
 * @package clases
 * @subpackage aplicacion
 * @author SERTIC SAS
 * @version 2014.02.27
 * @copyright SERTIC
 */
class CRelacionMunicipioOrdenPago {
    
    /** Almacena el id de la relacion municipio orden pago. */
    var $id;
    /** Almacena el valor de la relacion municipio orden pago. */
    var $valor;
    /** Almacena la destinacion recursos de la relacion municipio orden pago. */
    var $destinacionRecursos;
    /** Almacena el municipio de la relacion municipio orden pago. */
    var $municipio;
    /** Almacena la orden de pago de la relacion. */
    var $ordenPago;
    
    /**
     * Constructor de la clase.
     * @param type $id
     * @param type $valor
     * @param type $destinacionRecursos
     * @param type $municipio
     * @param type $ordenPago
     */
    function CRelacionMunicipioOrdenPago($id, $valor, $destinacionRecursos, 
                                         $municipio, $ordenPago) {
        $this->id = $id;
        $this->valor = $valor;
        $this->destinacionRecursos = $destinacionRecursos;
        $this->municipio = $municipio;
        $this->ordenPago = $ordenPago;
    }
    
    function getId() {
        return $this->id;
    }

    function getValor() {
        return $this->valor;
    }

    function getDestinacionRecursos() {
        return $this->destinacionRecursos;
    }

    function getMunicipio() {
        return $this->municipio;
    }

    function getOrdenPago() {
        return $this->ordenPago;
    }

    function setId($id) {
        $this->id = $id;
    }

    function setValor($valor) {
        $this->valor = $valor;
    }

    function setDestinacionRecursos($destinacionRecursos) {
        $this->destinacionRecursos = $destinacionRecursos;
    }

    function setMunicipio($municipio) {
        $this->municipio = $municipio;
    }

    function setOrdenPago($ordenPago) {
        $this->ordenPago = $ordenPago;
    }



}
