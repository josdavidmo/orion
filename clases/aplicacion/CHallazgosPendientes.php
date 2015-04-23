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
    /** Almacena el archivo del Hallazgo Pendiente. */
    var $archivo;
    /** Almacena la fecha respuesta del Hallazgo Pendiente. */
    var $fechaRespuesta;
    /** Almacena la observacion de la respuesta del Hallazgo Pendiente. */
    var $observacionRespuesta;
    /** Almacena el archivo de la respuesta del Hallazgo Pendiente. */
    var $archivoRespuesta;
    
    
    /**
     * Constructor de la clase.
     * @param type $id
     * @param type $observacion
     * @param type $tipo
     * @param type $actividad
     * @param type $archivo
     * @param type $fechaRespuesta
     * @param type $observacionRespuesta
     * @param type $archivoRespuesta
     */
    function CHallazgosPendientes($id, $observacion, $tipo, $actividad, 
                                  $archivo, $fechaRespuesta = NULL, 
                                  $observacionRespuesta = NULL,
                                  $archivoRespuesta = NULL) {
        $this->id = $id;
        $this->observacion = $observacion;
        $this->tipo = $tipo;
        $this->actividad = $actividad;
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

    function getActividad() {
        return $this->actividad;
    }

    function getArchivo() {
        return $this->archivo;
    }

    function setId($id) {
        $this->id = $id;
    }

    function setObservacion($observacion) {
        $this->observacion = $observacion;
    }

    function setActividad($actividad) {
        $this->actividad = $actividad;
    }

    function setArchivo($archivo) {
        $this->archivo = $archivo;
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

    function setFechaRespuesta($fechaRespuesta) {
        $this->fechaRespuesta = $fechaRespuesta;
    }

    function setObservacionRespuesta($observacionRespuesta) {
        $this->observacionRespuesta = $observacionRespuesta;
    }

    function setArchivoRespuesta($archivoRespuesta) {
        $this->archivoRespuesta = $archivoRespuesta;
    }
    
    function getTipo() {
        return $this->tipo;
    }

    function setTipo($tipo) {
        $this->tipo = $tipo;
    }

}
