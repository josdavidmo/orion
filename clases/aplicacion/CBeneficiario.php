<?php

/**
 * Clase Plana Beneficiario.
 * @package clases
 * @subpackage aplicacion
 * @author SERTIC SAS
 * @version 2014.09.21
 * @copyright SERTIC
 */
class CBeneficiario {
    
    /** Almacena el id del beneficiario.*/
    var $idBeneficiario = null;
    /** Almacena el codigo interventoria. */
    var $codigoInterventoria = null;
    /** Almacena el codigo del mintic. */
    var $codigoMintic = null;
    /** Almacena el operador. */
    var $codigoOperador = null;
    /** Almacena el nombre. */
    var $nombre = null;
    /** Almacena el msns. */
    var $msnm = null;
    /** Almacena la latitud en grados. */
    var $latitudGrados = null;
    /** Almacena la latitud en minutos. */
    var $latitudMinutos = null;
    /** Almacena la latitud en segundos. */
    var $latitudSegundos = null;
    /** Almacena la posicion en norte o sur. */
    var $south = null;
    /** Almacena la longitud en grados. */
    var $longitudGrados = null;
    /** Almacena la longitud en minutos. */
    var $longitudMinutos = null;
    /** Almacena la longitud en segundos. */
    var $longitudSegundos = null;
    /** Almacena la posicion en este u oeste.*/
    var $west = null;
	/** Almacena la fecha inicio del benficiario.*/
	var $fechaInicio = null;
    /** Almacena la meta del beneficiario. */
    var $meta = null;
    /** Almacena el estado del beneficiario. */
    var $estado = null;
    /** Almacena el dda del beneficiario. */
    var $dda = null;
    /** Almacena el grupo del beneficiario. */
    var $grupo = null;
    /** Almacena el centro poblado del beneficiario. */
    var $centroPoblado = null;
    /** Almacena el tipo de beneficiario. */
    var $tipo = null;
    var $observacion = null;
    
    function getObservacion() {
        return $this->observacion;
    }

    function setObservacion($observacion) {
        $this->observacion = $observacion;
    }

            
    function __construct($idBeneficiario, $codigoInterventoria, $codigoMintic, 
                         $codigoOperador, $nombre, $msnm, $latitudGrados, 
                         $latitudMinutos, $latitudSegundos, $south, 
                         $longitudGrados, $longitudMinutos, $longitudSegundos, 
                         $west, $fechaInicio, $meta, $estado, $dda, $grupo, 
                         $centroPoblado, $tipo, $observacion) {
        $this->idBeneficiario = $idBeneficiario;
        $this->codigoInterventoria = $codigoInterventoria;
        $this->codigoMintic = $codigoMintic;
        $this->codigoOperador = $codigoOperador;
        $this->nombre = $nombre;
        $this->msnm = $msnm;
        $this->latitudGrados = $latitudGrados;
        $this->latitudMinutos = $latitudMinutos;
        $this->latitudSegundos = $latitudSegundos;
        $this->south = $south;
        $this->longitudGrados = $longitudGrados;
        $this->longitudMinutos = $longitudMinutos;
        $this->longitudSegundos = $longitudSegundos;
        $this->west = $west;
        $this->fechaInicio = $fechaInicio;
        $this->meta = $meta;
        $this->estado = $estado;
        $this->dda = $dda;
        $this->grupo = $grupo;
        $this->centroPoblado = $centroPoblado;
        $this->tipo = $tipo;
        $this->observacion = $observacion;
    }

    
    public function getIdBeneficiario() {
        return $this->idBeneficiario;
    }

    public function getCodigoInterventoria() {
        return $this->codigoInterventoria;
    }

    public function getCodigoMintic() {
        return $this->codigoMintic;
    }

    public function getCodigoOperador() {
        return $this->codigoOperador;
    }

    public function getNombre() {
        return $this->nombre;
    }

    public function getMsnm() {
        return $this->msnm;
    }

    public function getLatitudGrados() {
        return $this->latitudGrados;
    }

    public function getLatitudSegundos() {
        return $this->latitudSegundos;
    }

    public function getSouth() {
        return $this->south;
    }

    public function getLongitudGrados() {
        return $this->longitudGrados;
    }

    public function getLongitudMinutos() {
        return $this->longitudMinutos;
    }

    public function getWest() {
        return $this->west;
    }

    /**
     * Obtiene la meta del beneficiario.
     * @return \CBasica
     */
    public function getMeta() {
        return $this->meta;
    }

    /**
     * Obtiene el estado del beneficiario.
     * @return \CBasica
     */
    public function getEstado() {
        return $this->estado;
    }

    /**
     * Obtiene el dda del beneficiario.
     * @return \CBasica
     */
    public function getDda() {
        return $this->dda;
    }

    /**
     * Obtiene el grupo del beneficiario.
     * @return \CBasica
     */
    public function getGrupo() {
        return $this->grupo;
    }

    /**
     * Obtiene el centro poblado del beneficiario.
     * @return \CPoblado.
     */
    public function getCentroPoblado() {
        return $this->centroPoblado;
    }

    /**
     * Obtiene el tipo de beneficiario.
     * @return \CBasica
     */
    public function getTipo() {
        return $this->tipo;
    }

    public function setIdBeneficiario($idBeneficiario) {
        $this->idBeneficiario = $idBeneficiario;
    }

    public function setCodigoInterventoria($codigoInterventoria) {
        $this->codigoInterventoria = $codigoInterventoria;
    }

    public function setCodigoMintic($codigoMintic) {
        $this->codigoMintic = $codigoMintic;
    }

    public function setCodigoOperador($codigoOperador) {
        $this->codigoOperador = $codigoOperador;
    }

    public function setNombre($nombre) {
        $this->nombre = $nombre;
    }

    public function setMsnm($msnm) {
        $this->msnm = $msnm;
    }

    public function setLatitudGrados($latitudGrados) {
        $this->latitudGrados = $latitudGrados;
    }

    public function setLatitudSegundos($latitudSegundos) {
        $this->latitudSegundos = $latitudSegundos;
    }

    public function setSouth($south) {
        $this->south = $south;
    }

    public function setLongitudGrados($longitudGrados) {
        $this->longitudGrados = $longitudGrados;
    }

    public function setLongitudMinutos($longitudMinutos) {
        $this->longitudMinutos = $longitudMinutos;
    }

    public function setWest($west) {
        $this->west = $west;
    }

    public function setMeta($meta) {
        $this->meta = $meta;
    }

    public function setEstado($estado) {
        $this->estado = $estado;
    }

    public function setDda($dda) {
        $this->dda = $dda;
    }

    public function setGrupo($grupo) {
        $this->grupo = $grupo;
    }

    public function setCentroPoblado($centroPoblado) {
        $this->centroPoblado = $centroPoblado;
    }

    public function setTipo($tipo) {
        $this->tipo = $tipo;
    }
    
    function getLatitudMinutos() {
        return $this->latitudMinutos;
    }

    function setLatitudMinutos($latitudMinutos) {
        $this->latitudMinutos = $latitudMinutos;
    }
    
    function getLongitudSegundos() {
        return $this->longitudSegundos;
    }

    function setLongitudSegundos($longitudSegundos) {
        $this->longitudSegundos = $longitudSegundos;
    }
	
	function setFechaInicio($fechaInicio) {
        $this->fechaInicio = $fechaInicio;
    }
	
	function getFechaInicio() {
        return $this->fechaInicio;
    }
    
}
