<?php

/**
 * Clase Plana Productos.
 * @package clases
 * @subpackage aplicacion
 * @author SERTIC SAS
 * @version 2014.09.18
 * @copyright SERTIC
 */
class CRegistroProductos {

    /** Almacena el id del producto. */
    var $idRegistroProductos = null;

    /** Almacena la descripcion del producto. */
    var $descripcion = null;

    /** Almacena el valor unitario del producto. */
    var $valorUnitario = null;

    /** Almacena si el producto es un servicio o bien. */
    var $servicio = null;

    /** Almacena la cantidad de un producto. */
    var $cantidad = null;

    /** Almacena la familia del producto. */
    var $familia = null;

    /** Almacena la orden de pago del producto. */
    var $ordenPago = null;

    /**
     * Constructor de la clase.
     * @param type $idRegistroProductos
     * @param type $descripcion
     * @param type $valorUnitario
     * @param type $servicio
     * @param type $cantidad
     * @param type $familia
     * @param type $ordenPago
     */
    function CRegistroProductos($idRegistroProductos, $descripcion, $valorUnitario, $servicio, $cantidad, $familia, $ordenPago) {
        $this->idRegistroProductos = $idRegistroProductos;
        $this->descripcion = $descripcion;
        $this->valorUnitario = $valorUnitario;
        $this->servicio = $servicio;
        $this->cantidad = $cantidad;
        $this->familia = $familia;
        $this->ordenPago = $ordenPago;
    }

    public function getIdRegistroProductos() {
        return $this->idRegistroProductos;
    }

    public function getDescripcion() {
        return $this->descripcion;
    }

    public function getValorUnitario() {
        return $this->valorUnitario;
    }

    public function getCantidad() {
        return $this->cantidad;
    }

    /**
     * Obtiene la familia del producto.
     * @return \CFamilia
     */
    public function getFamilia() {
        return $this->familia;
    }

    /**
     * Obtiene la orden de pago del registro de productos.
     * @return \CRegistroProductos
     */
    public function getOrdenPago() {
        return $this->ordenPago;
    }

    public function setIdRegistroProductos($idRegistroProductos) {
        $this->idRegistroProductos = $idRegistroProductos;
    }

    public function setDescripcion($descripcion) {
        $this->descripcion = $descripcion;
    }

    public function setValorUnitario($valorUnitario) {
        $this->valorUnitario = $valorUnitario;
    }

    public function setCantidad($cantidad) {
        $this->cantidad = $cantidad;
    }

    public function setFamilia($familia) {
        $this->familia = $familia;
    }

    public function setOrdenPago($ordenPago) {
        $this->ordenPago = $ordenPago;
    }

    public function getServicio() {
        return $this->servicio;
    }

    public function setServicio($servicio) {
        $this->servicio = $servicio;
    }

}
