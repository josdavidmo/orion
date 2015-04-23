<?php

/**
 * Clase Plana Comunicado Respuesta.
 * @package clases
 * @subpackage aplicacion
 * @author SERTIC SAS
 * @version 2015.04.08
 * @copyright SERTIC
 */
class CComunicadoRespuesta {
    
    /** Almacena el identificador del comunicado. */
    var $comunicado;
    /** Almacena el identificador de la respuesta. */
    var $respuesta;
    
    /**
     * Constructor de la clase.
     * @param type $comunicado
     * @param type $respuesta
     */
    function CComunicadoRespuesta($comunicado, $respuesta) {
        $this->comunicado = $comunicado;
        $this->respuesta = $respuesta;
    }
    
    function getComunicado() {
        return $this->comunicado;
    }

    function getRespuesta() {
        return $this->respuesta;
    }

    function setComunicado($comunicado) {
        $this->comunicado = $comunicado;
    }

    function setRespuesta($respuesta) {
        $this->respuesta = $respuesta;
    }

}

