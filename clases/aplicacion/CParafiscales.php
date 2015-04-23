<?php

/**
 * Clase Plana Parafiscales.
 * @package clases
 * @subpackage aplicacion
 * @author SERTIC SAS
 * @version 2014.09.14
 * @copyright SERTIC
 */
class CParafiscales {
    
    /** Almacena el id del parafiscal. */
    var $idParafiscales = null;
    /** Almacena el periodo del parafiscal.*/
    var $periodo = null;
    /** Almacena la fecha realizacion comunicado del parafiscal. */
    var $fechaRealizacionComunicado = null;
    /** Almacena el comunicado entrega soportes del parafiscal. */
    var $comunicadoEntregaSoportes = null;
    /** Almacena la evaluacion del contenido del parafiscal. */
    var $evaluacionContenidoDocumento = null;
    /** Almacena la evaluacion del parafiscal. */
    var $evaluacionRevisorFiscal = null;
    /** Almacena el revisor del parafiscal. */
    var $usuario = null;
    /** Almacena la fecha comunicado interventoria del parafiscal. */
    var $fechaComunicadoInterventoria = null;
    /** Almacena el comunicado concepto interventoria del parafiscal. */
    var $comunicadoConceptoInterventoria = null;
    /** Almacena las observaciones del comunicado. */
    var $observaciones = null;
    
    /**
     * Constructor de la clase.
     * @param type $idParafiscales
     * @param type $periodo
     * @param type $fechaRealizacionComunicado
     * @param type $comunicadoEntregaSoportes
     * @param \CEstadoParafiscales $evaluacionContenidoDocumento
     * @param \CEstadoParafiscales $evaluacionRevisorFiscal
     * @param \CEstado $usuario
     * @param type $fechaComunicadoInterventoria
     * @param type $comunicadoConceptoInterventoria
     * @param type $observaciones
     */
    function CParafiscales($idParafiscales, $periodo, $fechaRealizacionComunicado, 
                           $comunicadoEntregaSoportes, $evaluacionContenidoDocumento, 
                           $evaluacionRevisorFiscal, $usuario, 
                           $fechaComunicadoInterventoria, 
                           $comunicadoConceptoInterventoria, $observaciones) {
        $this->idParafiscales = $idParafiscales;
        $this->periodo = $periodo;
        $this->fechaRealizacionComunicado = $fechaRealizacionComunicado;
        $this->comunicadoEntregaSoportes = $comunicadoEntregaSoportes;
        $this->evaluacionContenidoDocumento = $evaluacionContenidoDocumento;
        $this->evaluacionRevisorFiscal = $evaluacionRevisorFiscal;
        $this->usuario = $usuario;
        $this->fechaComunicadoInterventoria = $fechaComunicadoInterventoria;
        $this->comunicadoConceptoInterventoria = $comunicadoConceptoInterventoria;
        $this->observaciones = $observaciones;
    }
    
    /**
     * Obtiene el id del parafiscal.
     * @return type
     */
    public function getIdParafiscales() {
        return $this->idParafiscales;
    }

    /**
     * Obtiene el periodo del parafiscal.
     * @return type
     */
    public function getPeriodo() {
        return $this->periodo;
    }

    /**
     * Obtiene la fecha de realizacion del comunicado.
     * @return type
     */
    public function getFechaRealizacionComunicado() {
        return $this->fechaRealizacionComunicado;
    }

    /**
     * Obtiene el comunicado entrega soportes del comunicado.
     * @return type
     */
    public function getComunicadoEntregaSoportes() {
        return $this->comunicadoEntregaSoportes;
    }

    /**
     * Obtiene la evaluacion del contenido del parafiscal.
     * @return \CEstadoParafiscales
     */
    public function getEvaluacionContenidoDocumento() {
        return $this->evaluacionContenidoDocumento;
    }

    /**
     * Obtiene la evaluacion del revisor fiscal.
     * @return \CEstadoParafiscales
     */
    public function getEvaluacionRevisorFiscal() {
        return $this->evaluacionRevisorFiscal;
    }

    /**
     * Obtiene el usuario del parafiscal.
     * @return \CUsuario
     */
    public function getUsuario() {
        return $this->usuario;
    }

    /**
     * Obtiene el comunicado interventoria del parafiscal.
     * @return type
     */
    public function getFechaComunicadoInterventoria() {
        return $this->fechaComunicadoInterventoria;
    }

    /**
     * Obtiene el comunicado concepto interventoria del parafiscal.
     * @return type
     */
    public function getComunicadoConceptoInterventoria() {
        return $this->comunicadoConceptoInterventoria;
    }

    /**
     * Obtiene las observaciones del parafiscal.
     * @return type
     */
    public function getObservaciones() {
        return $this->observaciones;
    }

    /**
     * Asigna el id del parafiscal.
     * @param type $idParafiscales
     */
    public function setIdParafiscales($idParafiscales) {
        $this->idParafiscales = $idParafiscales;
    }

    /**
     * Asigna el periodo del parafiscal.
     * @param type $periodo
     */
    public function setPeriodo($periodo) {
        $this->periodo = $periodo;
    }

    /**
     * Asigna la fecha de realizacion del comunicado del parafiscal.
     * @param type $fechaRealizacionComunicado
     */
    public function setFechaRealizacionComunicado($fechaRealizacionComunicado) {
        $this->fechaRealizacionComunicado = $fechaRealizacionComunicado;
    }

    /**
     * ASigna el comunicado entrega soportes del parafiscal.
     * @param type $comunicadoEntregaSoportes
     */
    public function setComunicadoEntregaSoportes($comunicadoEntregaSoportes) {
        $this->comunicadoEntregaSoportes = $comunicadoEntregaSoportes;
    }

    /**
     * Asigna la evaluacion del contenido del documento del parafiscal.
     * @param \CEstadoParafiscales $evaluacionContenidoDocumento
     */
    public function setEvaluacionContenidoDocumento($evaluacionContenidoDocumento) {
        $this->evaluacionContenidoDocumento = $evaluacionContenidoDocumento;
    }

    /**
     * Asigna la evaluacion del revisor fiscal.
     * @param \CEstadoParafiscales $evaluacionRevisorFiscal
     */
    public function setEvaluacionRevisorFiscal($evaluacionRevisorFiscal) {
        $this->evaluacionRevisorFiscal = $evaluacionRevisorFiscal;
    }

    /**
     * Asigna el usuario del parafiscal.
     * @param \CUsuario $usuario
     */
    public function setUsuario($usuario) {
        $this->usuario = $usuario;
    }

    /**
     * Asigna la fecha comunicado interventoria del parafiscal.
     * @param type $fechaComunicadoInterventoria
     */
    public function setFechaComunicadoInterventoria($fechaComunicadoInterventoria) {
        $this->fechaComunicadoInterventoria = $fechaComunicadoInterventoria;
    }

    /**
     * Asigna el comunicado concepto interventoria del parafiscal.
     * @param type $comunicadoConceptoInterventoria
     */
    public function setComunicadoConceptoInterventoria($comunicadoConceptoInterventoria) {
        $this->comunicadoConceptoInterventoria = $comunicadoConceptoInterventoria;
    }

    /**
     * Asigna las observaciones del parafiscal.
     * @param type $observaciones
     */
    public function setObservaciones($observaciones) {
        $this->observaciones = $observaciones;
    }
}
