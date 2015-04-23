<?php

class anticipo{
    var $idAnticipo;
    var $fecha;
    var $valor;
    var $idBitacora;
    
    
    function __construct($idAnticipo, $fecha, $valor, $idBitacora) {
        $this->idAnticipo = $idAnticipo;
        $this->fecha = $fecha;
        $this->valor = $valor;
        $this->idBitacora = $idBitacora;
    }

    function getIdAnticipo() {
        return $this->idAnticipo;
    }

    function getFecha() {
        return $this->fecha;
    }

    function getValor() {
        return $this->valor;
    }

    function getIdBitacora() {
        return $this->idBitacora;
    }

    function setIdAnticipo($idAnticipo) {
        $this->idAnticipo = $idAnticipo;
    }

    function setFecha($fecha) {
        $this->fecha = $fecha;
    }

    function setValor($valor) {
        $this->valor = $valor;
    }

    function setIdBitacora($idBitacora) {
        $this->idBitacora = $idBitacora;
    }



}
?>
