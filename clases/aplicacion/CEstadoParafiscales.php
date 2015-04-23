<?php

/**
 * Clase Plana Estado Parafiscales.
 * @package clases
 * @subpackage aplicacion
 * @author SERTIC SAS
 * @version 2014.09.14
 * @copyright SERTIC
 */
class CEstadoParafiscales {

    /** Almacena el id del estado de parasfiscales. */
    var $idEstadoParasfiscales = null;
    /** Almacena la descripcion del estado de parasfiscales. */
    var $descripcion = null;
    
    /**
     * Constructor de la clase.
     * @param type $idEstadoParasfiscales
     * @param type $descripcion
     */
    function CEstadoParafiscales($idEstadoParasfiscales, $descripcion) {
        $this->idEstadoParasfiscales = $idEstadoParasfiscales;
        $this->descripcion = $descripcion;
    }
    
    /**
     * Obtiene el id del estado parafiscales.
     * @return type
     */
    public function getIdEstadoParasfiscales() {
        return $this->idEstadoParasfiscales;
    }

    /**
     * Obtiene la descripcion del estado parafiscales.
     * @return type
     */
    public function getDescripcion() {
        return $this->descripcion;
    }

    /**
     * Asigna el id del estado parafiscales.
     * @param type $idEstadoParasfiscales
     */
    public function setIdEstadoParasfiscales($idEstadoParasfiscales) {
        $this->idEstadoParasfiscales = $idEstadoParasfiscales;
    }

    /**
     * Asigna la descripcion del estado parafiscales.
     * @param type $descripcion
     */
    public function setDescripcion($descripcion) {
        $this->descripcion = $descripcion;
    }
}

?>

