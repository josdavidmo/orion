<?php

/**
 * Clase Plana Bitacora.
 * @package clases
 * @subpackage aplicacion
 * @author SERTIC SAS
 * @version 2014.10.31
 * @copyright SERTIC
 */
class CRelacionTransporte {
    
    /** Corresponde al id de la relacion de transporte. */
    var $id;
    /** Corresponde al usuario de la relacion de transporte. */
    var $usuario;
    /** Corresponde al origen de la relacion de transporte. */
    var $origen;
    /** Corresponde al destino de la relacion de transporte. */
    var $destino;
    /** Corresponde al valor de la relacion de transporte. */
    var $valor;
    /** Corresponde a la fecha de la relacion de transporte. */
    var $fecha;
    
    function CRelacionTransporte($id, $usuario, $origen, $destino, $valor, $fecha) {
        $this->id = $id;
        $this->usuario = $usuario;
        $this->origen = $origen;
        $this->destino = $destino;
        $this->valor = $valor;
        $this->fecha = $fecha;
    }
    
    public function getId() {
        return $this->id;
    }

    public function getUsuario() {
        return $this->usuario;
    }

    public function getOrigen() {
        return $this->origen;
    }

    public function getDestino() {
        return $this->destino;
    }

    public function getValor() {
        return $this->valor;
    }

    public function getFecha() {
        return $this->fecha;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function setUsuario($usuario) {
        $this->usuario = $usuario;
    }

    public function setOrigen($origen) {
        $this->origen = $origen;
    }

    public function setDestino($destino) {
        $this->destino = $destino;
    }

    public function setValor($valor) {
        $this->valor = $valor;
    }

    public function setFecha($fecha) {
        $this->fecha = $fecha;
    }



    
}
