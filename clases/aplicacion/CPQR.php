<?php

/**
 * Clase Plana Beneficiario.
 * @package clases
 * @subpackage aplicacion
 * @author SERTIC SAS
 * @version 2014.09.21
 * @copyright SERTIC
 */
class CPQR {
    
    /** Almacena el id del PQR. */
    var $id;
    /** Almacena la descripcion del requerimiento. */
    var $descripcionRequerimiento;
    /** Almacena la fecha reporte del PQR. */
    var $fechaReporte;
    /** Almcena la fecha de solucion del PQR. */
    var $fechaSolucion;
    /** Almacena el diagnostico del PQR. */
    var $diagnostico;
    /** Almacena la respuesta del PQR. */
    var $respuesta;
    /** Almacena el beneficiario del PQR. */
    var $beneficiario;
    
    /**
     * Constructor de la clase.
     * @param type $id
     * @param type $descripcionRequerimiento
     * @param type $fechaReporte
     * @param type $fechaSolucion
     * @param type $diagnostico
     * @param type $respuesta
     * @param type $beneficiario
     */
    public function CPQR($id, $descripcionRequerimiento, $fechaReporte, 
                         $fechaSolucion, $diagnostico, $respuesta, 
                         $beneficiario) {
        $this->id = $id;
        $this->descripcionRequerimiento = $descripcionRequerimiento;
        $this->fechaReporte = $fechaReporte;
        $this->fechaSolucion = $fechaSolucion;
        $this->diagnostico = $diagnostico;
        $this->respuesta = $respuesta;
        $this->beneficiario = $beneficiario;
    }
    
    public function getId() {
        return $this->id;
    }

    public function getDescripcionRequerimiento() {
        return $this->descripcionRequerimiento;
    }

    public function getFechaReporte() {
        return $this->fechaReporte;
    }

    public function getFechaSolucion() {
        return $this->fechaSolucion;
    }

    public function getDiagnostico() {
        return $this->diagnostico;
    }

    public function getRespuesta() {
        return $this->respuesta;
    }

    public function getBeneficiario() {
        return $this->beneficiario;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function setDescripcionRequerimiento($descripcionRequerimiento) {
        $this->descripcionRequerimiento = $descripcionRequerimiento;
    }

    public function setFechaReporte($fechaReporte) {
        $this->fechaReporte = $fechaReporte;
    }

    public function setFechaSolucion($fechaSolucion) {
        $this->fechaSolucion = $fechaSolucion;
    }

    public function setDiagnostico($diagnostico) {
        $this->diagnostico = $diagnostico;
    }

    public function setRespuesta($respuesta) {
        $this->respuesta = $respuesta;
    }

    public function setBeneficiario($beneficiario) {
        $this->beneficiario = $beneficiario;
    }



    
}
