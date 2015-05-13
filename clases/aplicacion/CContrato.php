<?php

/**
 * Clase Plana Contrato.
 * @package clases
 * @subpackage aplicacion
 * @author SERTIC SAS
 * @version 2015.05.08
 * @copyright SERTIC
 */
class CContrato {

    /** Almacena el id del contrato. */
    var $idContrato;

    /** Almacena el numero del contrato. */
    var $numero;

    /** Almacena el objeto del contrato. */
    var $objeto;

    /** Almacena el valor del contrato. */
    var $valor;
	
	 /** Almacena el valor de Anticipo del contrato. */
    var $anticipo;

    /** Almacena el plazo del contrato. */
    var $plazo;

    /** Almacena la fecha inicio del contrato. */
    var $fechaInicio;

    /** Almacena la fecha fin del contrato. */
    var $fechaFin;

    /** Almacena el soporte del contrato. */
    var $soporte;

    /** Almacena el acta inicio del contrato. */
    var $moneda;
    
    function CContrato($idContrato, $numero, $objeto, $valor, $anticipo, $plazo, 
                       $fechaInicio, $fechaFin, $soporte, $moneda) {
        $this->idContrato = $idContrato;
        $this->numero = $numero;
        $this->objeto = $objeto;
        $this->valor = $valor;
		$this->anticipo = $anticipo;
        $this->plazo = $plazo;
        $this->fechaInicio = $fechaInicio;
        $this->fechaFin = $fechaFin;
        $this->soporte = $soporte;
        $this->moneda = $moneda;
    }
    
    function getIdContrato() {
        return $this->idContrato;
    }

    function getNumero() {
        return $this->numero;
    }

    function getObjeto() {
        return $this->objeto;
    }

    function getValor() {
        return $this->valor;
    }

	function getAnticipo() {
        return $this->anticipo;
    }
	
    function getPlazo() {
        return $this->plazo;
    }

    function getFechaInicio() {
        return $this->fechaInicio;
    }

    function getFechaFin() {
        return $this->fechaFin;
    }

    function getSoporte() {
        return $this->soporte;
    }

    function getMoneda() {
        return $this->moneda;
    }

    function setIdContrato($idContrato) {
        $this->idContrato = $idContrato;
    }

    function setNumero($numero) {
        $this->numero = $numero;
    }

    function setObjeto($objeto) {
        $this->objeto = $objeto;
    }

    function setValor($valor) {
        $this->valor = $valor;
    }

	function setAnticipo($anticipo) {
        $this->anticipo = $anticipo;
    }
	
    function setPlazo($plazo) {
        $this->plazo = $plazo;
    }

    function setFechaInicio($fechaInicio) {
        $this->fechaInicio = $fechaInicio;
    }

    function setFechaFin($fechaFin) {
        $this->fechaFin = $fechaFin;
    }

    function setSoporte($soporte) {
        $this->soporte = $soporte;
    }

    function setMoneda($moneda) {
        $this->moneda = $moneda;
    }

}
