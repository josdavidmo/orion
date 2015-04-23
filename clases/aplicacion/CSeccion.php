<?php

/**
 * Clase plana que representa la estructura de una seccion.
 * @package clases.
 * @subpackage aplicacion.
 * @author Jose David Moreno Posada.
 * @version 1.0
 * @since 04/08/2014
 * @copyright SERTIC S.A.S
 */
class CSeccion {
    
    /** Almacena el id de la seccion. */
    var $idSeccion = null;
    /** Almacena el nombre de la seccion. */
    var $nombreSeccion = null;
    /** Almacena el numero de la seccion. */
    var $numero = null;
    /** Almacena el instrumento al que pertenece la seccion. */
    var $instrumento = null;
    
    /**
     * Constructor de la clase.
     * @param type $idSeccion
     * @param type $nombreSeccion
     * @param type $numero
     * @param \CInstrumento $instrumento
     */
    function CSeccion($idSeccion, $nombreSeccion, $numero, $instrumento) {
        $this->idSeccion = $idSeccion;
        $this->nombreSeccion = $nombreSeccion;
        $this->numero = $numero;
        $this->instrumento = $instrumento;
    }
    
    /**
     * Obtiene el id de la seccion.
     * @return type
     */
    public function getIdSeccion() {
        return $this->idSeccion;
    }

    /**
     * Obtiene el nombre de la seccion.
     * @return type
     */
    public function getNombreSeccion() {
        return $this->nombreSeccion;
    }

    /**
     * Obtiene el numero de la seccion.
     * @return type
     */
    public function getNumero() {
        return $this->numero;
    }

    /**
     * Obtiene el instrumento al que pertenece la seccion.
     * @return type
     */
    public function getInstrumento() {
        return $this->instrumento;
    }

    /**
     * Asigna el id de la seccion.
     * @param type $idSeccion
     */
    public function setIdSeccion($idSeccion) {
        $this->idSeccion = $idSeccion;
    }

    /**
     * Asigna el nombre de la seccion.
     * @param type $nombreSeccion
     */
    public function setNombreSeccion($nombreSeccion) {
        $this->nombreSeccion = $nombreSeccion;
    }

    /**
     * Asigna el numero de la seccion.
     * @param type $numero
     */
    public function setNumero($numero) {
        $this->numero = $numero;
    }

    /**
     * Asigna el instrumento al cual pertenece la seccion.
     * @param type $instrumento
     */
    public function setInstrumento($instrumento) {
        $this->instrumento = $instrumento;
    }

}
