<?php

/**
 * Clase Plana Hallazgos Pendientes.
 * @package clases
 * @subpackage aplicacion
 * @author SERTIC SAS
 * @version 2014.10.30
 * @copyright SERTIC
 */
class CHallazgosPendientes {
    
    /** Almacena el id del Hallazgo Pendiente. */
    var $id;
    /** Almacena la observacion del Hallazgo Pendiente. */
    var $observacion;
    /** Almacena el tipo del Hallazgo Pendiente. */
    var $tipo;
    /** Almacena la actividad del Hallazgo Pendiente. */
    var $actividad;
    /** Almacena la clasificacion del Hallazgo Pendiente. */
    var $clasificacion;
    /** Almacena el archivo del Hallazgo Pendiente. */
    var $archivo;
    /** Almacena la fecha respuesta del Hallazgo Pendiente. */
    var $fechaRespuesta;
    /** Almacena la observacion de la respuesta del Hallazgo Pendiente. */
    var $observacionRespuesta;
    /** Almacena el archivo de la respuesta del Hallazgo Pendiente. */
    var $archivoRespuesta;
    
    
    function __construct($id, $observacion, $tipo, $actividad, 
                         $clasificacion, $archivo, $fechaRespuesta = NULL, 
                         $observacionRespuesta = NULL, $archivoRespuesta = NULL) {
        $this->id = $id;
        $this->observacion = $observacion;
        $this->tipo = $tipo;
        $this->actividad = $actividad;
        $this->clasificacion = $clasificacion;
        $this->archivo = $archivo;
        $this->fechaRespuesta = $fechaRespuesta;
        $this->observacionRespuesta = $observacionRespuesta;
        $this->archivoRespuesta = $archivoRespuesta;
    }

    function getId() {
        return $this->id;
    }

    function getObservacion() {
        return $this->observacion;
    }

    function getTipo() {
        return $this->tipo;
    }

    function getActividad() {
        return $this->actividad;
    }

    function getClasificacion() {
        return $this->clasificacion;
    }

    function getArchivo() {
        return $this->archivo;
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

    function setId($id) {
        $this->id = $id;
    }

    function setObservacion($observacion) {
        $this->observacion = $observacion;
    }

    function setTipo($tipo) {
        $this->tipo = $tipo;
    }

    function setActividad($actividad) {
        $this->actividad = $actividad;
    }

    function setClasificacion($clasificacion) {
        $this->clasificacion = $clasificacion;
    }

    function setArchivo($archivo) {
        $this->archivo = $archivo;
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
