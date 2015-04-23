<?php

/**
 * Clase Plana Historial Producto.
 * @package clases
 * @subpackage aplicacion
 * @author SERTIC SAS
 * @version 2014.10.03
 * @copyright SERTIC
 */
class CHistorialProducto {
    
   /** Almacena el producto del historial producto. */
   var $producto;
   /** Almacena la bodega del historial producto. */
   var $bodega;
   /** Almacena la fecha envio del historial producto. */
   var $fechaEnvio;
   /** Almacena la fecha envio del historial producto. */
   var $beneficiario;
   
   /**
    * Constructor de la clase.
    * @param \CProducto $producto
    * @param \CBodega $bodega
    * @param type $fechaEnvio
    * @param \CBeneficiario $beneficiario
    */
   function CHistorialProducto($producto, $bodega, $fechaEnvio, $beneficiario) {
       $this->producto = $producto;
       $this->bodega = $bodega;
       $this->fechaEnvio = $fechaEnvio;
       $this->beneficiario = $beneficiario;
   }

   /**
    * Obtiene el producto del historial producto.
    * @return \CProductos
    */
   function getProducto() {
       return $this->producto;
   }

   /**
    * Obtiene la bodega del historial producto.
    * @return \CBodega
    */
   function getBodega() {
       return $this->bodega;
   }

   /**
    * Obtiene la fecha envio.
    * @return type
    */
   function getFechaEnvio() {
       return $this->fechaEnvio;
   }

   /**
    * Asigna el producto del historial producto.
    * @param \CProductos $producto
    */
   function setProducto($producto) {
       $this->producto = $producto;
   }

   /**
    * Asigna la bodega del historial producto.
    * @param \CBodega $bodega
    */
   function setBodega($bodega) {
       $this->bodega = $bodega;
   }

   /**
    * Asigna la fecha de envio del historial producto.
    * @param type $fechaEnvio
    */
   function setFechaEnvio($fechaEnvio) {
       $this->fechaEnvio = $fechaEnvio;
   }
   
   /**
    * Obtiene el beneficiario almacenado en la base de datos.
    * @return \CBeneficiario
    */
   function getBeneficiario() {
       return $this->beneficiario;
   }

   /**
    * Asigna un beneficiario.
    * @param \CBeneficiario $beneficiario
    */
   function setBeneficiario($beneficiario) {
       $this->beneficiario = $beneficiario;
   }
   
}

