<?php

/**
 * Clase plana que representa la estructura de un instrumento.
 * @package clases.
 * @subpackage aplicacion.
 * @author Jose David Moreno Posada.
 * @version 1.0.
 * @since 04/08/2014.
 * @copyright SERTIC S.A.S
 */
class CInstrumento {
    
    /** Corresponde al unico del instrumento. */
    var $id = null;
    /** Corresponde al nombre del instrumento. */
    var $nombreInstrumento = null;
    /** Corresponde al codigo unico del instrumento. */
    var $codigo = null;
    
    var $tipo=null;
    
    var $nivel=null;
    
    /**
     * Contructor de la clase.
     * @param type $id
     * @param type $nombreInstrumento
     * @param type $codigo
     */
    public function CInstrumento($id, $nombreInstrumento, $codigo, $tipo, $nivel) {
        $this->id = $id;
        $this->nombreInstrumento = $nombreInstrumento;
        $this->codigo = $codigo;
        $this->tipo = $tipo;
        $this->nivel = $nivel;
    }
    
    /**
     * Obtiene el id del instrumento.
     * @return type
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Obtiene el nombre del instrumento.
     * @return type
     */
    public function getNombreInstrumento() {
        return $this->nombreInstrumento;
    }

    /**
     * Asigna el id al instrumento.
     * @param type $id
     */
    public function setId($id) {
        $this->id = $id;
    }

    /**
     * Asigna el nombre al instrumento.
     * @param type $nombreInstrumento
     */
    public function setNombreInstrumento($nombreInstrumento) {
        $this->nombreInstrumento = $nombreInstrumento;
    }    
    
    /**
     * Obtiene el codigo del instrumento
     * @return type
     */
    public function getCodigo() {
        return $this->codigo;
    }

    /**
     * Asigna el codigo del instrumento.
     * @param type $codigo
     */
    public function setCodigo($codigo) {
        $this->codigo = $codigo;
    }
    
    public function getTipo() {
        return $this->tipo;
    }

    public function getNivel() {
        return $this->nivel;
    }

    public function setTipo($tipo) {
        $this->tipo = $tipo;
    }

    public function setNivel($nivel) {
        $this->nivel = $nivel;
    }


}
