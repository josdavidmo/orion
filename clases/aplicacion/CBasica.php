<?php

/**
 * Clase Plana Basica.
 * @package clases
 * @subpackage aplicacion
 * @author SERTIC SAS
 * @version 2014.09.18
 * @copyright SERTIC
 */
class CBasica {
    
   /** Almacena el id de la tabla basica de dos variables. */
   var $id;
   /** Almacena la descripcion de la tabla basica de dos variables. */
   var $descripcion;
   
   /**
    * Constructor de la clase.
    * @param type $id
    * @param type $descripcion
    */
   public function CBasica($id, $descripcion) {
       $this->id = $id;
       $this->descripcion = $descripcion;
   }
   
   public function getId() {
       return $this->id;
   }

   public function getDescripcion() {
       return $this->descripcion;
   }

   public function setId($id) {
       $this->id = $id;
   }

   public function setDescripcion($descripcion) {
       $this->descripcion = $descripcion;
   }

}
