<?php

/**
 * Clase plana que representa la estructura de una pregunta.
 * @package clases.
 * @subpackage aplicacion.
 * @author Jose David Moreno Posada
 * @version 1.0
 * @since 04/08/2014
 * @copyright SERTIC S.A.S
 */
class CPregunta {
    
    /** Almacena el id de la pregunta. */
    var $idPregunta = null;
    /** Almacena el enunciado de la pregunta. */
    var $seccion = null;
    /** Almacena el tipo de pregunta. */
    var $tipoPregunta = null;
    /** Almacena el numero de pregunta. */
    var $numero = null;
    /** Almacena el requerimiento de la pregunta. */
    var $requerido = null;
    /** Almacena el enunciado de la pregunta. */
    var $enunciado = null;
    /** Almacena la descripcion de la pregunta. */
    var $descripcion = null;
    /** Almacena las opciones de respuesta de la pregunta. */
    var $opcionRespuesta = null;
    
    /**
     * Constructor de la clase.
     * @param type $idPregunta
     * @param \CSeccion $seccion
     * @param type $tipoPregunta
     * @param type $numero
     * @param type $requerido
     * @param type $enunciado
     * @param type $descripcion
     * @param type $opcionRespuesta
     */
    function CPregunta($idPregunta, $seccion, $tipoPregunta, $numero, 
                       $requerido, $enunciado, $descripcion, $opcionRespuesta) {
        $this->idPregunta = $idPregunta;
        $this->seccion = $seccion;
        $this->tipoPregunta = $tipoPregunta;
        $this->numero = $numero;
        $this->requerido = $requerido;
        $this->enunciado = $enunciado;
        $this->descripcion = $descripcion;
        $this->opcionRespuesta = $opcionRespuesta;
    }

    
    /**
     * Obtiene el id de la pregunta.
     * @return type
     */
    public function getIdPregunta() {
        return $this->idPregunta;
    }

    /**
     * Obtiene la seccion de la pregunta.
     * @return \CSeccion
     */
    public function getSeccion() {
        return $this->seccion;
    }

    /**
     * Obtiene el tipo de pregunta.
     * @return type
     */
    public function getTipoPregunta() {
        return $this->tipoPregunta;
    }

    /**
     * Obtiene el id de la pregunta.
     * @param type $idPregunta
     */
    public function setIdPregunta($idPregunta) {
        $this->idPregunta = $idPregunta;
    }

    /**
     * Asigna la seccion de la pregunta.
     * @param type $seccion
     */
    public function setSeccion($seccion) {
        $this->seccion = $seccion;
    }

    /**
     * Asigna el tipo de pregunta.
     * @param type $tipoPregunta
     */
    public function setTipoPregunta($tipoPregunta) {
        $this->tipoPregunta = $tipoPregunta;
    }
    
    /**
     * Obtiene si una pregunta es requerida o no.
     * @return type
     */
    public function isRequerido() {
        return $this->requerido;
    }

    /**
     * Asigna si una pregunta es requerida o no.
     * @param type $requerido
     */
    public function setRequerido($requerido) {
        $this->requerido = $requerido;
    }
    
    /**
     * Obtiene el numero de la pregunta.
     * @return type
     */
    public function getNumero() {
        return $this->numero;
    }

    /**
     * Asigna el numero de la pregunta.
     * @param type $numero
     */
    public function setNumero($numero) {
        $this->numero = $numero;
    }
    
    /**
     * Obtiene el enunciado de la pregunta.
     * @return type
     */
    public function getEnunciado() {
        return $this->enunciado;
    }

    /**
     * Asigna el enunciado de la pregunta.
     * @param type $enunciado
     */
    public function setEnunciado($enunciado) {
        $this->enunciado = $enunciado;
    }
    
    /**
     * Obtiene la descripcion de la pregunta.
     * @return type
     */
    public function getDescripcion() {
        return $this->descripcion;
    }

    /**
     * Obtiene las opciones de respuesta de la pregunta.
     * @return type
     */
    public function getOpcionRespuesta() {
        return $this->opcionRespuesta;
    }

    /**
     * Asigna la descripcion de la pregunta.
     * @param type $descripcion
     */
    public function setDescripcion($descripcion) {
        $this->descripcion = $descripcion;
    }

    /**
     * Asigna las opciones de respuesta de la pregunta.
     * @param type $opcionRespuesta
     */
    public function setOpcionRespuesta($opcionRespuesta) {
        $this->opcionRespuesta = $opcionRespuesta;
    }
    
}
