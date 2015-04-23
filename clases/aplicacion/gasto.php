<?php

class gasto{
    var $idGastosActividad;
    var $descripcion;
    var $valor;
    var $archivo;
    var $idTipoGasto;
    var $idActividad;
    var $estado;
    
    function __construct($idGastosActividad, $descripcion, $valor, $archivo, $idTipoGasto, $idActividad, $estado) {
        $this->idGastosActividad = $idGastosActividad;
        $this->descripcion = $descripcion;
        $this->valor = $valor;
        $this->archivo = $archivo;
        $this->idTipoGasto = $idTipoGasto;
        $this->idActividad = $idActividad;
        $this->estado = $estado;
    }
    
    function getIdGastosActividad() {
        return $this->idGastosActividad;
    }

    function getDescripcion() {
        return $this->descripcion;
    }

    function getValor() {
        return $this->valor;
    }

    function getArchivo() {
        return $this->archivo;
    }

    function getIdTipoGasto() {
        return $this->idTipoGasto;
    }

    function getIdActividad() {
        return $this->idActividad;
    }

    function getEstado() {
        return $this->estado;
    }

    function setIdGastosActividad($idGastosActividad) {
        $this->idGastosActividad = $idGastosActividad;
    }

    function setDescripcion($descripcion) {
        $this->descripcion = $descripcion;
    }

    function setValor($valor) {
        $this->valor = $valor;
    }

    function setArchivo($archivo) {
        $this->archivo = $archivo;
    }

    function setIdTipoGasto($idTipoGasto) {
        $this->idTipoGasto = $idTipoGasto;
    }

    function setIdActividad($idActividad) {
        $this->idActividad = $idActividad;
    }

    function setEstado($estado) {
        $this->estado = $estado;
    }

}
?>
