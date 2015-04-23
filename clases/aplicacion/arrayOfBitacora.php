<?php

class arrayOfBitacora{
    var $bitacoras;
    var $actividades;
    var $anticipos;
    var $hallazgos;
    var $registrosFotograficos;
    var $gastos;
    
    function __construct($bitacoras=null, $actividades=null, $anticipos=null, 
            $hallazgos=null, $registrosFotograficos=null, $gastos=null) {
        $this->bitacoras = $bitacoras;
        $this->actividades = $actividades;
        $this->anticipos = $anticipos;
        $this->hallazgos = $hallazgos;
        $this->registrosFotograficos = $registrosFotograficos;
        $this->gastos = $gastos;
    }
    function getBitacoras() {
        return $this->bitacoras;
    }

    function getActividades() {
        return $this->actividades;
    }

    function getAnticipos() {
        return $this->anticipos;
    }

    function getHallazgos() {
        return $this->hallazgos;
    }

    function getRegistrosFotograficos() {
        return $this->registrosFotograficos;
    }

    function getGastos() {
        return $this->gastos;
    }

    function setBitacoras($bitacoras) {
        $this->bitacoras = $bitacoras;
    }

    function setActividades($actividades) {
        $this->actividades = $actividades;
    }

    function setAnticipos($anticipos) {
        $this->anticipos = $anticipos;
    }

    function setHallazgos($hallazgos) {
        $this->hallazgos = $hallazgos;
    }

    function setRegistrosFotograficos($registrosFotograficos) {
        $this->registrosFotograficos = $registrosFotograficos;
    }

    function setGastos($gastos) {
        $this->gastos = $gastos;
    }



}


?>