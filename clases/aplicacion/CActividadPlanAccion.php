<?php

/**
 * Clase Plana Actividad Plan de Accion.
 * @package clases
 * @subpackage aplicacion
 * @author SERTIC SAS
 * @version 2014.12.14
 * @copyright SERTIC
 */
class CActividadPlanAccion {
    
    /** Almacena el id de la actividad plan de accion. */
    var $id;
    /** Almacena la descripcion de la actividad plan de accion. */
    var $descripcion;
    /** Almacena la fecha de la actividad plan de accion. */
    var $fecha;
    /** Almacena los recursos de la actividad plan de accion. */
    var $recursos;
    /** Almacena la fecha cumplimiento de la actividad plan de accion. */
    var $fechaCumplimiento;
    /** Alamcena el soporte de la actividad plan de accion. */
    var $soporte;
    /** Almacena el plan de accion al que pertence la actividad. */
    var $planAccion;
    /** Almacena el usuario responsable de la actividad plan de accion. */
    var $usuario;
    
    /**
     * Constructor de la clase.
     * @param type $id
     * @param type $descripcion
     * @param type $fecha
     * @param type $recursos
     * @param type $planAccion
     * @param type $usuario
     * @param type $fechaCumplimiento
     * @param type $soporte
     */
    function CActividadPlanAccion($id, $descripcion, $fecha, $recursos, 
                                  $planAccion, $usuario, 
                                  $fechaCumplimiento = null, $soporte = null) {
        $this->id = $id;
        $this->descripcion = $descripcion;
        $this->fecha = $fecha;
        $this->recursos = $recursos;
        $this->fechaCumplimiento = $fechaCumplimiento;
        $this->soporte = $soporte;
        $this->planAccion = $planAccion;
        $this->usuario = $usuario;
    }
    
    function getId() {
        return $this->id;
    }

    function getDescripcion() {
        return $this->descripcion;
    }

    function getFecha() {
        return $this->fecha;
    }

    function getRecursos() {
        return $this->recursos;
    }

    function getFechaCumplimiento() {
        return $this->fechaCumplimiento;
    }

    function getSoporte() {
        return $this->soporte;
    }

    function getPlanAccion() {
        return $this->planAccion;
    }

    function getUsuario() {
        return $this->usuario;
    }

    function setId($id) {
        $this->id = $id;
    }

    function setDescripcion($descripcion) {
        $this->descripcion = $descripcion;
    }

    function setFecha($fecha) {
        $this->fecha = $fecha;
    }

    function setRecursos($recursos) {
        $this->recursos = $recursos;
    }

    function setFechaCumplimiento($fechaCumplimiento) {
        $this->fechaCumplimiento = $fechaCumplimiento;
    }

    function setSoporte($soporte) {
        $this->soporte = $soporte;
    }

    function setPlanAccion($planAccion) {
        $this->planAccion = $planAccion;
    }

    function setUsuario($usuario) {
        $this->usuario = $usuario;
    }   
}
