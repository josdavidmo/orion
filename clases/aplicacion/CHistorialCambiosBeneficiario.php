<?php

/**
 * Clase Historial Cambios Beneficiario.
 * @package clases
 * @subpackage aplicacion
 * @author SERTIC SAS
 * @version 2014.10.26
 * @copyright SERTIC
 */
/**
 * Clase Historial Cambios Beneficiario.
 * Clase plana o entidad de Historial de Cambios, contiene la definicion y el manejo
 * de los atributos del historial de cambios de los beneficiarios a traves de la 
 * definicion de los mismos  y los metodos de set y get.
 * @see beneficiarios.php (@package modulos,@subpackage beneficiarios)
 * @see CBeneficiarioData.php (@package Clases,@subpackage datos)
 * @see CPlaneacionData.php (@package Clases,@subpackage datos)
 * @package clases
 * @subpackage aplicacion
 * @access public
 * @author SERTIC SAS
 * @since @version 2015.01.23
 * @copyright SERTIC SAS
 */
class CHistorialCambiosBeneficiario {
    
    /**
     * Almacena el id del historial de cambios.
     * @var Integer Id del historial de cambios. 
     * @access public.
     */
    var $idHistorialCambio;
    /**
     * Almacena la informacion del beneficiario1.
     * @var CBeneficiario Beneficiario 1. 
     * @access public.
     */
    var $beneficiario1;
    /**
     * Almacena la informacion del beneficiario2.
     * @var CBeneficiario Beneficiario 2. 
     * @access public.
     */
    var $beneficiario2;
    /**
     * Almacena tipo de cambio que se realiza.
     * @var string Tipo de Cambio ejecutado. 
     * @access public.
     */    
    var $tipoCambioBeneficiario;
    /**
     * Almacena en la que el cambio se hace efectivo..
     * @var Date Fecha en la que se ejecuta el cambio. 
     * @access public.
     */
    var $fecha;
    /**
     * Almacena la ruta del documento soporte del cambio.
     * @var string Documento soporte que respalda el cambio. 
     * @access public.
     */
    var $soporte;
    /**
     * Almacena las observaciones realizadas al cambio.
     * @var string Obervaciones. 
     * @access public.
     */
    var $observaciones;
    
    /**
     * Constructor de la clase, encargado de inicializar los atributos del 
     * historial de cambios del beneficiario y asignarles los valores de los 
     * parametros recibidos.
     * @param Integer $idHistorialCambio
     * @param CBeneficiario $beneficiario1
     * @param CBeneficiario $beneficiario2
     * @param string $tipoCambioBeneficiario
     * @param string $fecha
     * @param string $soporte
     * @param string $observaciones
     */
    function CHistorialCambiosBeneficiario($idHistorialCambio, $beneficiario1, 
                                           $beneficiario2, 
                                           $tipoCambioBeneficiario, $fecha, 
                                           $soporte, $observaciones) {
        $this->idHistorialCambio = $idHistorialCambio;
        $this->beneficiario1 = $beneficiario1;
        $this->beneficiario2 = $beneficiario2;
        $this->tipoCambioBeneficiario = $tipoCambioBeneficiario;
        $this->fecha = $fecha;
        $this->soporte = $soporte;
        $this->observaciones = $observaciones;
    }
    /**
     * Obtiene el Id del historial de cambio del beneficiario.
     * @return Integer Id del historial de cambio de beneficiario.
     */
    function getIdHistorialCambio() {
        return $this->idHistorialCambio;
    }
    /**
     * Obtiene la informacion del beneficiario 1 del cambio.
     * @return CBeneficiario Beneficiario 1.
     */
    function getBeneficiario1() {
        return $this->beneficiario1;
    }
    /**
     * Obtiene la informacion del beneficiario 2 del cambio.
     * @return CBeneficiario Beneficiario 2.
     */
    function getBeneficiario2() {
        return $this->beneficiario2;
    }
    /**
     * Obtiene el tipo de cambio realizado.
     * @return string Tipo de cambio efectuado.
     */
    function getTipoCambioBeneficiario() {
        return $this->tipoCambioBeneficiario;
    }
    /**
     * Obtiene la fecha de cuando fue efectuado el cambio.
     * @return string Fecha de ejecucion del cambio.
     */
    function getFecha() {
        return $this->fecha;
    }
    /**
     * Obtiene la ruta del documento soporte del cambio.
     * @return string Documento soporte que respalda el cambio.
     */
    function getSoporte() {
        return $this->soporte;
    }
    /**
     * Obtiene las observaciones del cambio.
     * @return string Observaciones del cambio.
     */
    function getObservaciones() {
        return $this->observaciones;
    }
    /**
     * Asigna el Id al historial de cambio.
     * @param Integer $idHistorialCambio Id del historial de cambio.
     */
    function setIdHistorialCambio($idHistorialCambio) {
        $this->idHistorialCambio = $idHistorialCambio;
    }
    /**
     * Asigna el Beneficiario1 del cambio.
     * @param CBeneficiario $beneficiario1 Beneficiario1 del cambio.
     */
    function setBeneficiario1($beneficiario1) {
        $this->beneficiario1 = $beneficiario1;
    }
    /**
     * Asigna el Beneficiario2 del cambio.
     * @param CBeneficiario $beneficiario2 Beneficiario2 del cambio.
     */
    function setBeneficiario2($beneficiario2) {
        $this->beneficiario2 = $beneficiario2;
    }
    /**
     * Asigna el tipo de cambio realizado.
     * @param string $tipoCambioBeneficiario Tipo de Cambio a Realizar.
     */
    function setTipoCambioBeneficiario($tipoCambioBeneficiario) {
        $this->tipoCambioBeneficiario = $tipoCambioBeneficiario;
    }
    /**
     * Asigna la fecha en la que se ejecuto el cambio.
     * @param string $fecha Fecha de ejecucion del cambio.
     */
    function setFecha($fecha) {
        $this->fecha = $fecha;
    }
    /**
     * Asigna la ruta del documento soporte del cambio.
     * @param string $soporte Documento soporte que respalda el cambio.
     */
    function setSoporte($soporte) {
        $this->soporte = $soporte;
    }
    /**
     * Asigna las observaciones al cambio.
     * @param string $observaciones Observaciones referentes al cambio.
     */
    function setObservaciones($observaciones) {
        $this->observaciones = $observaciones;
    }

}
