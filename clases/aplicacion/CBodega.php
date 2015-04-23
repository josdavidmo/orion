<?php

/**
 * Clase Plana Bodega.
 * @package clases
 * @subpackage aplicacion
 * @author SERTIC SAS
 * @version 2014.09.14
 * @copyright SERTIC
 */
class CBodega {
    
    /** Maneja el id de la bodega. */
    var $idBodega = null;
    /** Maneja el codigo de la bodega. */
    var $codigo = null;
    /** Maneja el nombre de la bodega. */
    var $nombre = null;
    /** Maneja el tipo de bodega. */
    var $tipoBodega = null;
    /** Maneja la bodega a la que pertenece la bodega. */
    var $bodegaPadre = null;
    
    /**
     * Constructor de la clase.
     * @param type $idBodega
     * @param type $codigo
     * @param type $nombre
     * @param \CTipoBodega $tipoBodega
     * @param \CBodega $bodegaPadre
     */
    function CBodega($idBodega, $codigo, $nombre, $tipoBodega, $bodegaPadre) {
        $this->idBodega = $idBodega;
        $this->codigo = $codigo;
        $this->nombre = $nombre;
        $this->tipoBodega = $tipoBodega;
        $this->bodegaPadre = $bodegaPadre;
    }
    
    /**
     * Obtiene el id de la bodega.
     * @return type
     */
    public function getIdBodega() {
        return $this->idBodega;
    }

    /**
     * Obtiene el codigo de la bodega.
     * @return type
     */
    public function getCodigo() {
        return $this->codigo;
    }

    /**
     * Obtiene el nombre de la bodega.
     * @return type
     */
    public function getNombre() {
        return $this->nombre;
    }

    /**
     * Obtiene el tipo de bodega.
     * @return \CTipoBodega
     */
    public function getTipoBodega() {
        return $this->tipoBodega;
    }

    /**
     * Obtiene la subbodega de la bodega.
     * @return \CBodega
     */
    public function getBodegaPadre() {
        return $this->bodegaPadre;
    }

    /**
     * Asigna el id de la bodega.
     * @param type $idBodega
     */
    public function setIdBodega($idBodega) {
        $this->idBodega = $idBodega;
    }

    /**
     * Asigna el codigo de la bodega.
     * @param type $codigo
     */
    public function setCodigo($codigo) {
        $this->codigo = $codigo;
    }

    /**
     * Asigna el nombre de la bodega.
     * @param type $nombre
     */
    public function setNombre($nombre) {
        $this->nombre = $nombre;
    }

    /**
     * Asigna el tipo de bodega.
     * @param \CTipoBodega $tipoBodega
     */
    public function setTipoBodega($tipoBodega) {
        $this->tipoBodega = $tipoBodega;
    }

    /**
     * Asigna la subbodega a la que pertenece la bodega. 
     * @param \CBodega $bodegaPadre
     */
    public function setBodegaPadre($bodegaPadre) {
        $this->bodegaPadre = $bodegaPadre;
    }
 
}
