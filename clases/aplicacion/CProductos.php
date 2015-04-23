<?php

/**
 * Clase Plana Productos.
 * @package clases
 * @subpackage aplicacion
 * @author SERTIC SAS
 * @version 2014.09.18
 * @copyright SERTIC
 */
class CProductos {

    /** Almacena el id de un producto. */
    var $idProducto;

    /** Almacena el serial de un producto. */
    var $serial;

    /** Almacena el registro de un producto. */
    var $registroProducto;

    /** Almacena la descripcion de un producto. */
    var $descripcion;

    /** Almacena el estado de un producto. */
    var $estadoProducto;
    
    /** Almacena la fecha de envio */
    var $fechaEnvio;

    /**
     * Constructor de la clase.
     * @param type $idProducto
     * @param type $serial
     * @param \CRegistroProductos $registroProducto
     * @param type $descripcion
     * @param \CBasica $estadoProducto
     */
    function CProductos($idProducto, $serial, $registroProducto, $descripcion, 
                        $estadoProducto, $fechaEnvio) {
        $this->idProducto = $idProducto;
        $this->serial = $serial;
        $this->registroProducto = $registroProducto;
        $this->descripcion = $descripcion;
        $this->estadoProducto = $estadoProducto;
        $this->fechaEnvio = $fechaEnvio;
    }

    public function getIdProducto() {
        return $this->idProducto;
    }

    public function getSerial() {
        return $this->serial;
    }

    /**
     * Obtiene el registro de un producto.
     * @return \CRegistroProductos
     */
    public function getRegistroProducto() {
        return $this->registroProducto;
    }

    public function getDescripcion() {
        return $this->descripcion;
    }

    /**
     * Obtiene el estado de un producto.
     * @return \CBasica
     */
    public function getEstadoProducto() {
        return $this->estadoProducto;
    }

    public function setIdProducto($idProducto) {
        $this->idProducto = $idProducto;
    }

    public function setSerial($serial) {
        $this->serial = $serial;
    }

    /**
     * Asigna el registro del producto.
     * @param \CRegistroProductos $registroProducto
     */
    public function setRegistroProducto($registroProducto) {
        $this->registroProducto = $registroProducto;
    }

    public function setDescripcion($descripcion) {
        $this->descripcion = $descripcion;
    }

    /**
     * Asigna el estado producto.
     * @param \CBasica $estadoProducto
     */
    public function setEstadoProducto($estadoProducto) {
        $this->estadoProducto = $estadoProducto;
    }
    
    function getFechaEnvio() {
        return $this->fechaEnvio;
    }

    function setFechaEnvio($fechaEnvio) {
        $this->fechaEnvio = $fechaEnvio;
    }

}
