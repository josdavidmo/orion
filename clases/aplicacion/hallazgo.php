<?php

class hallazgo{
    var $idHallazgosPendientes;
    var $observacion;
    var $archivo;
    var $idTipo;
    var $idActividad;
    var $fechaRespuesta;
    var $observacionRespuesta;
    var $archivoRespuesta;
    
    function __construct($idHallazgosPendientes, $observacion, $archivo, $idTipo, $idActividad, $fechaRespuesta, $observacionRespuesta, $archivoRespuesta) {
        $this->idHallazgosPendientes = $idHallazgosPendientes;
        $this->observacion = $observacion;
        $this->archivo = $archivo;
        $this->idTipo = $idTipo;
        $this->idActividad = $idActividad;
        $this->fechaRespuesta = $fechaRespuesta;
        $this->observacionRespuesta = $observacionRespuesta;
        $this->archivoRespuesta = $archivoRespuesta;
    }
    
    function getIdHallazgosPendientes() {
        return $this->idHallazgosPendientes;
    }

    function getObservacion() {
        return $this->observacion;
    }

    function getArchivo() {
        return $this->archivo;
    }

    function getIdTipo() {
        return $this->idTipo;
    }

    function getIdActividad() {
        return $this->idActividad;
    }

    function getFechaRespuesta() {
        return $this->fechaRespuesta;
    }

    function getObservacionRespuesta() {
        return $this->observacionRespuesta;
    }

    function getArchivoRespuesta() {
        return $this->archivoRespuesta;
    }

    function setIdHallazgosPendientes($idHallazgosPendientes) {
        $this->idHallazgosPendientes = $idHallazgosPendientes;
    }

    function setObservacion($observacion) {
        $this->observacion = $observacion;
    }

    function setArchivo($archivo) {
        $this->archivo = $archivo;
    }

    function setIdTipo($idTipo) {
        $this->idTipo = $idTipo;
    }

    function setIdActividad($idActividad) {
        $this->idActividad = $idActividad;
    }

    function setFechaRespuesta($fechaRespuesta) {
        $this->fechaRespuesta = $fechaRespuesta;
    }

    function setObservacionRespuesta($observacionRespuesta) {
        $this->observacionRespuesta = $observacionRespuesta;
    }

    function setArchivoRespuesta($archivoRespuesta) {
        $this->archivoRespuesta = $archivoRespuesta;
    }

}
?>

