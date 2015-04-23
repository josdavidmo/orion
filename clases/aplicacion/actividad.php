<?php

class actividad{
    var $idActividad;
    var $idBitacora;
    var $fecha;
    var $fechaFin;
    var $descripcionActividadEjecutada;
    var $condicionesClimaticas;
    var $condicionesTopologicas;
    var $observaciones;
    var $idEstadoSalud;
    var $numCuadrillas;
    var $totalPersonas;
    var $totalPersonasContratadas;
    var $cumplimientoParafiscales;
    var $cumplimientoSenalizacion;
    var $cumplimientoEPP;
    var $cumplimientoCertificaciones;
    var $estado;
    
    function __construct($idActividad, $idBitacora, $fecha, $fechaFin, $descripcionActividadEjecutada, $condicionesClimaticas, $condicionesTopologicas, $observaciones, $idEstadoSalud, $numCuadrillas, $totalPersonas, $totalPersonasContratadas, $cumplimientoParafiscales, $cumplimientoSenalizacion, $cumplimientoEPP, $cumplimientoCertificaciones, $estado) {
        $this->idActividad = "$idActividad";
        $this->idBitacora = $idBitacora;
        $this->fecha = $fecha;
        $this->fechaFin = $fechaFin;
        $this->descripcionActividadEjecutada = $descripcionActividadEjecutada;
        $this->condicionesClimaticas = $condicionesClimaticas;
        $this->condicionesTopologicas = $condicionesTopologicas;
        $this->observaciones = $observaciones;
        $this->idEstadoSalud = $idEstadoSalud;
        $this->numCuadrillas = $numCuadrillas;
        $this->totalPersonas = $totalPersonas;
        $this->totalPersonasContratadas = $totalPersonasContratadas;
        $this->cumplimientoParafiscales = $cumplimientoParafiscales;
        $this->cumplimientoSenalizacion = $cumplimientoSenalizacion;
        $this->cumplimientoEPP = $cumplimientoEPP;
        $this->cumplimientoCertificaciones = $cumplimientoCertificaciones;
        $this->estado = $estado;
    }

    
    function getIdActividad() {
        return $this->idActividad;
    }

    function getIdBitacora() {
        return $this->idBitacora;
    }

    function getFecha() {
        return $this->fecha;
    }

    function getFechaFin() {
        return $this->fechaFin;
    }

    function getDescripcionActividadEjecutada() {
        return $this->descripcionActividadEjecutada;
    }

    function getCondicionesClimaticas() {
        return $this->condicionesClimaticas;
    }

    function getCondicionesTopologicas() {
        return $this->condicionesTopologicas;
    }

    function getObservaciones() {
        return $this->observaciones;
    }

    function getIdEstadoSalud() {
        return $this->idEstadoSalud;
    }

    function getNumCuadrillas() {
        return $this->numCuadrillas;
    }

    function getTotalPersonas() {
        return $this->totalPersonas;
    }

    function getTotalPersonasContratadas() {
        return $this->totalPersonasContratadas;
    }

    function getCumplimientoParafiscales() {
        return $this->cumplimientoParafiscales;
    }

    function getCumplimientoSenalizacion() {
        return $this->cumplimientoSenalizacion;
    }

    function getCumplimientoEPP() {
        return $this->cumplimientoEPP;
    }

    function getCumplimientoCertificaciones() {
        return $this->cumplimientoCertificaciones;
    }

    function getEstado() {
        return $this->estado;
    }

    function setIdActividad($idActividad) {
        $this->idActividad = "$idActividad";
    }

    function setIdBitacora($idBitacora) {
        $this->idBitacora = $idBitacora;
    }

    function setFecha($fecha) {
        $this->fecha = $fecha;
    }

    function setFechaFin($fechaFin) {
        $this->fechaFin = $fechaFin;
    }

    function setDescripcionActividadEjecutada($descripcionActividadEjecutada) {
        $this->descripcionActividadEjecutada = $descripcionActividadEjecutada;
    }

    function setCondicionesClimaticas($condicionesClimaticas) {
        $this->condicionesClimaticas = $condicionesClimaticas;
    }

    function setCondicionesTopologicas($condicionesTopologicas) {
        $this->condicionesTopologicas = $condicionesTopologicas;
    }

    function setObservaciones($observaciones) {
        $this->observaciones = $observaciones;
    }

    function setIdEstadoSalud($idEstadoSalud) {
        $this->idEstadoSalud = $idEstadoSalud;
    }

    function setNumCuadrillas($numCuadrillas) {
        $this->numCuadrillas = $numCuadrillas;
    }

    function setTotalPersonas($totalPersonas) {
        $this->totalPersonas = $totalPersonas;
    }

    function setTotalPersonasContratadas($totalPersonasContratadas) {
        $this->totalPersonasContratadas = $totalPersonasContratadas;
    }

    function setCumplimientoParafiscales($cumplimientoParafiscales) {
        $this->cumplimientoParafiscales = $cumplimientoParafiscales;
    }

    function setCumplimientoSenalizacion($cumplimientoSenalizacion) {
        $this->cumplimientoSenalizacion = $cumplimientoSenalizacion;
    }

    function setCumplimientoEPP($cumplimientoEPP) {
        $this->cumplimientoEPP = $cumplimientoEPP;
    }

    function setCumplimientoCertificaciones($cumplimientoCertificaciones) {
        $this->cumplimientoCertificaciones = $cumplimientoCertificaciones;
    }

    function setEstado($estado) {
        $this->estado = $estado;
    }
}
?>