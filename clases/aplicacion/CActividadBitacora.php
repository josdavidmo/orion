<?php

/**
 * Clase Plana Actividad Bitacora.
 * @package clases
 * @subpackage aplicacion
 * @author SERTIC SAS
 * @version 2014.12.10
 * @copyright SERTIC
 */
class CActividadBitacora {
    
    /** Almacena el id de la actividad. */
    var $id;
    /** Almacena la bitacora de la actividad. */
    var $bitacora;
    /** Almacena la fecha de realización de la actividad. */
    var $fecha;
    /** Almacena la fecha final de realización de la actividad. */
    var $fechaFin;
    /** Almacena la descripcion de las actividades ejecutadas. */
    var $descripcionActividadesEjecutadas;
    /** Almacena las condiciones topologicas de las actividad. */
    var $condicionesTopologicas;
    /** Almacena las condiciones climaticas en las que se realiza la actividad. */
    var $condicionesClimaticas;
    /** Almacena las observaciones de la actividad realizada. */
    var $observaciones;
    /** Almacena el estado de salud del usuario al escribir la bitacora. */
    var $estadoSalud;
    /** Almacena el número de cuadrillas reportado por el usuario al escribir la bitacora. */
    var $numeroCuadrillas;
    /** Almacena el total de personas reportado por el usuario al escribir la bitacora. */
    var $totalPersonas;
    /** Almacena el total de personas reportado por el usuario al escribir la bitacora. */
    var $totalPersonasContratadas;
    /** Almacena el estado del cumplimiento de parafiscales reportado por el usuario al escribir la bitacora. */
    var $cumplimientoParafiscales;
    /** Almacena el estado del cumplimiento de señalizacion reportado por el usuario al escribir la bitacora. */
    var $cumplimientoSenalizacion;
    /** Almacena el estado del cumplimiento de EPPs reportado por el usuario al escribir la bitacora. */
    var $cumplimientoEpp;
    /** Almacena el estado del cumplimiento de certificaciones reportado por el usuario al escribir la bitacora. */
    var $cumplimientoCertificaciones;
    /** Almacena el estado de la actividad. */
    var $estado;
    
    /**
     * Constructor de la clase.
     * @param type $id
     * @param type $bitacora
     * @param type $fecha
     * @param type $fechaFin
     * @param type $descripcionActividadesEjecutadas
     * @param type $condicionesTopologicas
     * @param type $condicionesClimaticas
     * @param type $observaciones
     * @param type $estadoSalud
     * @param type $numeroCuadrillas
     * @param type $totalPersonas
     * @param type $totalPersonasContratadas
     * @param type $cumplimientoParafiscales
     * @param type $cumplimientoSenalizacion
     * @param type $cumplimientoEpp
     * @param type $cumplimientoCertificaciones
     * @param type $estado
     */
    function CActividadBitacora($id, $bitacora, $fecha, $fechaFin,
                                $descripcionActividadesEjecutadas,
                                $condicionesTopologicas, 
                                $condicionesClimaticas,
                                $observaciones,
                                $estadoSalud, $numeroCuadrillas, 
                                $totalPersonas, $totalPersonasContratadas, 
                                $cumplimientoParafiscales, 
                                $cumplimientoSenalizacion, $cumplimientoEpp, 
                                $cumplimientoCertificaciones, $estado = null) {
        $this->id = $id;
        $this->bitacora = $bitacora;
        $this->fecha = $fecha;
        $this->fechaFin = $fechaFin;
        $this->descripcionActividadesEjecutadas = $descripcionActividadesEjecutadas;
        $this->condicionesTopologicas = $condicionesTopologicas;
        $this->condicionesClimaticas = $condicionesClimaticas;
        $this->observaciones = $observaciones;
        $this->estadoSalud = $estadoSalud;
        $this->numeroCuadrillas = $numeroCuadrillas;
        $this->totalPersonas = $totalPersonas;
        $this->totalPersonasContratadas = $totalPersonasContratadas;
        $this->cumplimientoParafiscales = $cumplimientoParafiscales;
        $this->cumplimientoSenalizacion = $cumplimientoSenalizacion;
        $this->cumplimientoEpp = $cumplimientoEpp;
        $this->cumplimientoCertificaciones = $cumplimientoCertificaciones; 
        $this->estado = $estado;
    }
    
    
    function getId() {
        return $this->id;
    }

    function getBitacora() {
        return $this->bitacora;
    }

    function getFecha() {
        return $this->fecha;
    }

    function getDescripcionActividadesEjecutadas() {
        return $this->descripcionActividadesEjecutadas;
    }

    function getCondicionesTopologicas() {
        return $this->condicionesTopologicas;
    }

    function getCondicionesClimaticas() {
        return $this->condicionesClimaticas;
    }

    function getObservaciones() {
        return $this->observaciones;
    }

    function getEstadoSalud() {
        return $this->estadoSalud;
    }

    function setId($id) {
        $this->id = $id;
    }

    function setBitacora($bitacora) {
        $this->bitacora = $bitacora;
    }

    function setFecha($fecha) {
        $this->fecha = $fecha;
    }

    function setDescripcionActividadesEjecutadas($descripcionActividadesEjecutadas) {
        $this->descripcionActividadesEjecutadas = $descripcionActividadesEjecutadas;
    }

    function setCondicionesTopologicas($condicionesTopologicas) {
        $this->condicionesTopologicas = $condicionesTopologicas;
    }

    function setCondicionesClimaticas($condicionesClimaticas) {
        $this->condicionesClimaticas = $condicionesClimaticas;
    }

    function setObservaciones($observaciones) {
        $this->observaciones = $observaciones;
    }

    function setEstadoSalud($estadoSalud) {
        $this->estadoSalud = $estadoSalud;
    }

    function getNumeroCuadrillas() {
        return $this->numeroCuadrillas;
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

    function getCumplimientoEpp() {
        return $this->cumplimientoEpp;
    }

    function getCumplimientoCertificaciones() {
        return $this->cumplimientoCertificaciones;
    }

    function setNumeroCuadrillas($numeroCuadrillas) {
        $this->numeroCuadrillas = $numeroCuadrillas;
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

    function setCumplimientoEpp($cumplimientoEpp) {
        $this->cumplimientoEpp = $cumplimientoEpp;
    }

    function setCumplimientoCertificaciones($cumplimientoCertificaciones) {
        $this->cumplimientoCertificaciones = $cumplimientoCertificaciones;
    }

    function getFechaFin() {
        return $this->fechaFin;
    }

    function setFechaFin($fechaFin) {
        $this->fechaFin = $fechaFin;
    }
    
    function getEstado() {
        return $this->estado;
    }

    function setEstado($estado) {
        $this->estado = $estado;
    }

}
