<?php

/**
 * Clase Plana Observaciones.
 * @package clases
 * @subpackage aplicacion
 * @author SERTIC SAS
 * @version 2014.11.14
 * @copyright SERTIC
 */
class CObservaciones {
    
    /** Almacena el id de la tabla. */
    var $id;
    /** Almacena el periodo. */
    var $periodo;
    /** Almacena la descripcion. */
    var $descripcion;
    /** Almacena el autocontrol al que pertence la observacion. */
    var $autocontrol;
    /** Almacena el estado de la observacion. */
    var $estado;
    /** Almacena el control al que pertence la observacion. */
    var $control;
    
    
    /**
     * Constructor de la clase.
     * @param type $id
     * @param type $periodo
     * @param type $descripcion
     * @param type $autocontrol
     * @param type $estado
     * @param type $control
     */
    public function CObservaciones($id, $periodo, $descripcion, $autocontrol, 
                                   $estado, $control) {
        $this->id = $id;
        $this->periodo = $periodo;
        $this->descripcion = $descripcion;
        $this->autocontrol = $autocontrol;
        $this->estado = $estado;
        $this->control = $control;
    }

        public function getId() {
        return $this->id;
    }

    public function getPeriodo() {
        return $this->periodo;
    }

    public function getDescripcion() {
        return $this->descripcion;
    }

    public function getAutocontrol() {
        return $this->autocontrol;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function setPeriodo($periodo) {
        $this->periodo = $periodo;
    }

    public function setDescripcion($descripcion) {
        $this->descripcion = $descripcion;
    }

    public function setAutocontrol($autocontrol) {
        $this->autocontrol = $autocontrol;
    }
    
    public function getEstado() {
        return $this->estado;
    }

    public function setEstado($estado) {
        $this->estado = $estado;
    }
    
    public function getControl() {
        return $this->control;
    }

    public function setControl($control) {
        $this->control = $control;
    }
    
}
