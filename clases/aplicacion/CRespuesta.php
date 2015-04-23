<?php

/**
 * Clase Plana Respuesta.
 * @package clases
 * @subpackage aplicacion
 * @author SERTIC SAS
 * @version 2014.10.11
 * @copyright SERTIC
 */
class CRespuesta {
    
    /** Corresponde al id de la respuesta. */
    var $idPregunta;
    /** Corresponde al id de la encuesta. */
    var $idEncuesta;
    /** Corresponde a la respuesta dada. */
    var $respuesta;
    
    /**
     * Constructor de la clase.
     * @param type $idPregunta
     * @param type $idEncuesta
     * @param type $respuesta
     */
    function CRespuesta($idPregunta, $idEncuesta, $respuesta) {
        $this->idPregunta = $idPregunta;
        $this->idEncuesta = $idEncuesta;
        $this->respuesta = $respuesta;
    }
    
    function getIdPregunta() {
        return $this->idPregunta;
    }

    function getIdEncuesta() {
        return $this->idEncuesta;
    }

    function getRespuesta() {
        return $this->respuesta;
    }

    function setIdPregunta($idPregunta) {
        $this->idPregunta = $idPregunta;
    }

    function setIdEncuesta($idEncuesta) {
        $this->idEncuesta = $idEncuesta;
    }

    function setRespuesta($respuesta) {
        $this->respuesta = $respuesta;
    }



    
}
