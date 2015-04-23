<?php

/**
 * Clase Plana Tipo Bodega.
 * @package clases
 * @subpackage aplicacion
 * @author SERTIC SAS
 * @version 2014.09.12
 * @copyright SERTIC
 */
class CTipoBodega {
    
    /** Almacena el id del tipo de bodega. */
    var $idTipoBodega = null;
    /** Almacena la descripcion del tipo de bodega. */
    var $descripcion = null;
    
    /**
     * Constructor de la clase.
     * @param type $idTipoBodega
     * @param type $descripcion
     */
    function CTipoBodega($idTipoBodega, $descripcion) {
        $this->idTipoBodega = $idTipoBodega;
        $this->descripcion = $descripcion;
    }
    
    /**
     * Obtiene el id del tipo de bodega.
     * @return type
     */
    public function getIdTipoBodega() {
        return $this->idTipoBodega;
    }

    /**
     * Obtiene la descripcion del tipo de bodega.
     * @return type
     */
    public function getDescripcion() {
        return $this->descripcion;
    }

    /**
     * Asigna el id del tipo de bodega.
     * @param type $idTipoBodega
     */
    public function setIdTipoBodega($idTipoBodega) {
        $this->idTipoBodega = $idTipoBodega;
    }

    /**
     * Asigna la descripcion del tipo de bodega.
     * @param type $descripcion
     */
    public function setDescripcion($descripcion) {
        $this->descripcion = $descripcion;
    }



}
